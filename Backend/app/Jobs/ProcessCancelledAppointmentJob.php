<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\WaitlistService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCancelledAppointmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $appointment;

    /**
     * Create a new job instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    /**
     * Execute the job.
     */
    public function handle(WaitlistService $waitlistService): void
    {
        try {
            Log::info('Processing cancelled appointment for waitlist', [
                'appointment_id' => $this->appointment->id,
                'appointment_date' => $this->appointment->starts_at,
                'appointment_service_id' => $this->appointment->service_id,
                'appointment_barber_id' => $this->appointment->barber_id,
            ]);

            // Buscar si hay alguien en lista de espera que coincida
            $waitlistEntry = $waitlistService->findMatchingWaitlist($this->appointment);

            Log::info('Waitlist search result', [
                'found' => $waitlistEntry ? 'YES' : 'NO',
                'waitlist_id' => $waitlistEntry?->id,
                'waitlist_email' => $waitlistEntry?->email,
            ]);

            if ($waitlistEntry) {
                // Preparar información del slot disponible
                $availableSlot = [
                    'date' => $this->appointment->starts_at,
                    'time' => $this->appointment->starts_at->format('H:i'),
                    'barber_id' => $this->appointment->barber_id,
                ];

                // Notificar al cliente
                $waitlistService->notifyWaitlistEntry($waitlistEntry, $availableSlot);

                Log::info('Waitlist notification sent for cancelled appointment', [
                    'appointment_id' => $this->appointment->id,
                    'waitlist_id' => $waitlistEntry->id,
                ]);
            } else {
                Log::info('No matching waitlist entry found for cancelled appointment', [
                    'appointment_id' => $this->appointment->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to process cancelled appointment for waitlist', [
                'appointment_id' => $this->appointment->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
