<?php

namespace App\Jobs;

use App\Services\WaitlistService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanExpiredWaitlistJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(WaitlistService $waitlistService): void
    {
        try {
            // Limpiar entradas expiradas
            $cleanedCount = $waitlistService->cleanExpiredEntries();

            // Verificar notificaciones que expiraron (sin confirmar en 2h)
            $notificationExpiredCount = $waitlistService->checkNotificationExpiry();

            Log::info('Cleaned expired waitlist entries', [
                'expired_entries' => $cleanedCount,
                'expired_notifications' => $notificationExpiredCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to clean expired waitlist entries', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
