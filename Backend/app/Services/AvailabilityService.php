<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    private int $businessStartHour;
    private int $businessEndHour;
    private int $slotInterval;

    public function __construct()
    {
        $this->businessStartHour = (int) config('app.business_start_hour', 10);
        $this->businessEndHour = (int) config('app.business_end_hour', 20);
        $this->slotInterval = (int) config('app.slot_interval_minutes', 30);
    }

    /**
     * Get available time slots for a specific date and service
     */
    public function getAvailableSlots(string $date, ?int $serviceId = null, ?int $barberId = null): array
    {
        $service = $serviceId ? Service::find($serviceId) : null;
        $serviceDuration = $service ? $service->duration_minutes : 30;

        // If no barber specified, get first active barber
        if (!$barberId) {
            $barber = \App\Models\Barber::where('active', true)->first();
            $barberId = $barber?->id;
        }

        if (!$barberId) {
            return []; // No barbers available
        }

        $allSlots = $this->generateAllTimeSlots();
        $existingAppointments = $this->getActiveAppointmentsForDate($date, $barberId);

        $availableSlots = [];

        foreach ($allSlots as $slotTime) {
            if ($this->isSlotAvailable($slotTime, $serviceDuration, $date, $existingAppointments)) {
                $availableSlots[] = [
                    'time' => $slotTime,
                    'available' => true,
                ];
            }
        }

        return $availableSlots;
    }

    /**
     * Generate all possible time slots for the day
     */
    private function generateAllTimeSlots(): array
    {
        $slots = [];
        $currentTime = Carbon::createFromTime($this->businessStartHour, 0);
        $endTime = Carbon::createFromTime($this->businessEndHour, 0);

        while ($currentTime->lt($endTime)) {
            $slots[] = $currentTime->format('H:i');
            $currentTime->addMinutes($this->slotInterval);
        }

        return $slots;
    }

    /**
     * Get all active appointments for a specific date
     */
    private function getActiveAppointmentsForDate(string $date, int $barberId): Collection
    {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->endOfDay();

        return Appointment::with('service')
            ->where('barber_id', $barberId)
            ->whereBetween('starts_at', [$startOfDay, $endOfDay])
            ->whereIn('status', ['PENDING', 'CONFIRMED'])
            ->get();
    }

    /**
     * Check if a specific slot is available
     */
    private function isSlotAvailable(
        string $slotTime,
        int $serviceDuration,
        string $date,
        Collection $existingAppointments
    ): bool {
        $slotStart = Carbon::createFromFormat('Y-m-d H:i', "$date $slotTime");
        $slotEnd = $slotStart->copy()->addMinutes($serviceDuration);

        foreach ($existingAppointments as $appointment) {
            $appointmentStart = Carbon::parse($appointment->starts_at);
            $appointmentEnd = Carbon::parse($appointment->ends_at);

            // Check for overlap: slot overlaps with appointment if:
            // - slot starts before appointment ends AND
            // - slot ends after appointment starts
            if ($slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate if a specific time slot is available for booking
     */
    public function validateSlotAvailability(string $date, string $time, int $serviceId, ?int $barberId = null): bool
    {
        $service = Service::findOrFail($serviceId);

        // Resolve barber id (fallback to first active barber)
        if (!$barberId) {
            $barber = \App\Models\Barber::where('active', true)->first();
            $barberId = $barber?->id;
        }
        if (!$barberId) {
            return false;
        }

        // Build the same availability check used for listings, but scoped to one time
        $existingAppointments = $this->getActiveAppointmentsForDate($date, $barberId);
        $serviceDuration = $service->duration_minutes ?? (int) config('app.slot_interval_minutes', 30);

        return $this->isSlotAvailable($time, $serviceDuration, $date, $existingAppointments);
    }
}
