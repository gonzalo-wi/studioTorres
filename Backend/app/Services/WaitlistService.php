<?php

namespace App\Services;

use App\Models\Waitlist;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Barber;
use App\Notifications\SlotAvailableNotification;
use App\Notifications\AppointmentConfirmedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class WaitlistService
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    /**
     * Agregar cliente a la lista de espera
     */
    public function addToWaitlist(array $data): Waitlist
    {
        // Validar que el servicio existe
        $service = Service::findOrFail($data['service_id']);
        
        // Si especificó barbero, validar que existe
        if (!empty($data['barber_id'])) {
            Barber::findOrFail($data['barber_id']);
        }

        // Establecer fecha de expiración (7 días por defecto)
        $expiresAt = Carbon::parse($data['preferred_date'])->addDays(7);

        return Waitlist::create([
            'client_name' => $data['client_name'],
            'client_phone' => $data['client_phone'],
            'client_email' => $data['client_email'] ?? null,
            'service_id' => $data['service_id'],
            'preferred_date' => $data['preferred_date'],
            'preferred_time_start' => $data['preferred_time_start'] ?? null,
            'preferred_time_end' => $data['preferred_time_end'] ?? null,
            'barber_id' => $data['barber_id'] ?? null,
            'status' => 'WAITING',
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Buscar entradas de lista de espera que coincidan con un slot liberado
     */
    public function findMatchingWaitlist(Appointment $cancelledAppointment): ?Waitlist
    {
        $date = $cancelledAppointment->starts_at->format('Y-m-d');
        $time = $cancelledAppointment->starts_at->format('H:i');

        // Buscar en lista de espera ordenado por antigüedad (FIFO)
        $waitlistEntries = Waitlist::waiting()
            ->forDate($date)
            ->forService($cancelledAppointment->service_id)
            ->with(['service', 'barber'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Encontrar el primero que coincida con preferencias de horario
        foreach ($waitlistEntries as $entry) {
            // Si especificó barbero, debe coincidir
            if ($entry->barber_id && $entry->barber_id !== $cancelledAppointment->barber_id) {
                continue;
            }

            // Verificar si el horario coincide con sus preferencias
            if ($entry->matchesTimePreference($time)) {
                return $entry;
            }
        }

        return null;
    }

    /**
     * Notificar a un cliente de la lista de espera
     */
    public function notifyWaitlistEntry(Waitlist $waitlistEntry, array $availableSlot): void
    {
        try {
            // Si tiene email, enviar notificación
            if ($waitlistEntry->client_email) {
                Notification::route('mail', $waitlistEntry->client_email)
                    ->notify(new SlotAvailableNotification($waitlistEntry, $availableSlot));
            }

            // Marcar como notificado
            $waitlistEntry->markAsNotified();

            Log::info('Waitlist notification sent', [
                'waitlist_id' => $waitlistEntry->id,
                'client_email' => $waitlistEntry->client_email,
                'slot' => $availableSlot,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send waitlist notification', [
                'waitlist_id' => $waitlistEntry->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Convertir entrada de lista de espera en turno
     */
    public function convertToAppointment(Waitlist $waitlistEntry, array $slotData): Appointment
    {
        // Crear el turno
        $appointment = Appointment::create([
            'public_code' => Appointment::generatePublicCode(),
            'client_name' => $waitlistEntry->client_name,
            'client_phone' => $waitlistEntry->client_phone,
            'client_email' => $waitlistEntry->client_email,
            'barber_id' => $slotData['barber_id'],
            'service_id' => $waitlistEntry->service_id,
            'starts_at' => $slotData['starts_at'],
            'ends_at' => $slotData['ends_at'],
            'status' => 'CONFIRMED',
            'notes' => 'Convertido desde lista de espera',
        ]);

        // Cargar relaciones para el email
        $appointment->load(['service', 'barber.user']);

        // Enviar email de confirmación
        if ($appointment->client_email) {
            try {
                Notification::route('mail', $appointment->client_email)
                    ->notify(new AppointmentConfirmedNotification($appointment));
                    
                Log::info('Confirmation email sent for waitlist conversion', [
                    'appointment_id' => $appointment->id,
                    'public_code' => $appointment->public_code,
                    'client_email' => $appointment->client_email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send confirmation email', [
                    'appointment_id' => $appointment->id,
                    'error' => $e->getMessage(),
                ]);
                // No lanzar excepción para no bloquear la conversión
            }
        }

        // Marcar entrada de waitlist como convertida
        $waitlistEntry->markAsConverted();

        return $appointment;
    }

    /**
     * Limpiar entradas expiradas
     */
    public function cleanExpiredEntries(): int
    {
        $expired = Waitlist::where('status', 'WAITING')
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expired as $entry) {
            $entry->markAsExpired();
        }

        return $expired->count();
    }

    /**
     * Verificar si un turno fue notificado hace más de 2 horas sin confirmar
     */
    public function checkNotificationExpiry(): int
    {
        $expiredNotifications = Waitlist::where('status', 'NOTIFIED')
            ->where('notified_at', '<', now()->subHours(2))
            ->get();

        $count = 0;
        foreach ($expiredNotifications as $entry) {
            // Volver a estado WAITING para ofrecerlo al siguiente
            $entry->update(['status' => 'WAITING', 'notified_at' => null]);
            $count++;
        }

        return $count;
    }

    /**
     * Obtener estadísticas de lista de espera
     */
    public function getStats(): array
    {
        return [
            'waiting' => Waitlist::where('status', 'WAITING')->count(),
            'notified' => Waitlist::where('status', 'NOTIFIED')->count(),
            'converted' => Waitlist::where('status', 'CONVERTED')->count(),
            'expired' => Waitlist::where('status', 'EXPIRED')->count(),
        ];
    }
}
