<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentService
{
    private AvailabilityService $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    /**
     * Create a new appointment
     */
    public function createAppointment(array $data): Appointment
    {
        // Validate service exists and is active
        $service = Service::active()->findOrFail($data['service_id']);

        // Get barber from request or first active barber
        $barberId = $data['barber_id'] ?? null;
        if ($barberId) {
            $barber = \App\Models\Barber::where('active', true)->findOrFail($barberId);
        } else {
            $barber = \App\Models\Barber::where('active', true)->firstOrFail();
        }

        // Validate slot availability
        if (!$this->availabilityService->validateSlotAvailability(
            $data['date'],
            $data['time'],
            $data['service_id'],
            $barber->id
        )) {
            throw new \Exception('El horario seleccionado ya no está disponible');
        }

        // Create appointment with generated public code
        $appointment = DB::transaction(function () use ($data, $service, $barber) {
            $startsAt = \Carbon\Carbon::parse($data['date'] . ' ' . $data['time']);
            $endsAt = $startsAt->copy()->addMinutes($service->duration_minutes);

            return Appointment::create([
                'public_code' => Appointment::generatePublicCode(),
                'client_name' => $data['client_name'],
                'client_phone' => $data['client_phone'],
                'client_email' => $data['client_email'] ?? null,
                'barber_id' => $barber->id,
                'service_id' => $data['service_id'],
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'notes' => $data['notes'] ?? null,
                'status' => 'PENDING',
            ]);
        });

        Log::info('New appointment created', [
            'appointment_id' => $appointment->id,
            'public_code' => $appointment->public_code,
            'client' => $appointment->client_name,
        ]);

        // Load related models; Barber has no 'user' relation in current model
        return $appointment->load(['service', 'barber']);
    }

    /**
     * Update appointment status (and optionally reschedule)
     */
    public function updateAppointmentStatus(Appointment $appointment, array $data): Appointment
    {
        DB::transaction(function () use ($appointment, $data) {
            // If rescheduling (date/time provided), validate new slot
            if (isset($data['date']) && isset($data['time'])) {
                // Skip availability check if cancelling
                if ($data['status'] !== 'CANCELLED') {
                    // Temporarily remove this appointment from availability check
                    $originalStatus = $appointment->status;
                    $appointment->update(['status' => 'CANCELLED']);

                    $isAvailable = $this->availabilityService->validateSlotAvailability(
                        $data['date'],
                        $data['time'],
                        $appointment->service_id
                    );

                    // Restore original status if validation fails
                    if (!$isAvailable) {
                        $appointment->update(['status' => $originalStatus]);
                        throw new \Exception('El nuevo horario no está disponible');
                    }
                }

                $appointment->date = $data['date'];
                $appointment->time = $data['time'];
            }

            $appointment->status = $data['status'];
            $appointment->save();
        });

        Log::info('Appointment status updated', [
            'appointment_id' => $appointment->id,
            'new_status' => $appointment->status,
        ]);

        return $appointment->fresh('service');
    }

    /**
     * Get appointment by public code
     */
    public function getAppointmentByPublicCode(string $publicCode): ?Appointment
    {
        return Appointment::with(['service', 'barber'])
            ->where('public_code', $publicCode)
            ->first();
    }

    /**
     * Get filtered appointments for admin
     */
    public function getFilteredAppointments(array $filters = [])
    {
        $query = Appointment::with(['service', 'barber'])->orderBy('starts_at', 'desc');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from'])) {
            $query->whereDate('starts_at', '>=', $filters['from']);
        }

        if (isset($filters['to'])) {
            $query->whereDate('starts_at', '<=', $filters['to']);
        }

        return $query->paginate(20);
    }
}
