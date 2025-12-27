<?php

namespace App\Console\Commands;

use App\Notifications\SlotAvailableNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class TestEmail extends Command
{
    protected $signature = 'test:email {to}';
    protected $description = 'Send a test email to verify SMTP configuration';

    public function handle()
    {
        $to = $this->argument('to');
        
        $this->info("Sending test email to: {$to}");
        $this->info("Using SMTP: " . config('mail.mailers.smtp.host'));
        $this->info("From: " . config('mail.from.address'));

        try {
            // Crear waitlist de prueba
            $testWaitlist = new \App\Models\Waitlist([
                'id' => 999,
                'client_name' => 'Prueba',
                'client_email' => $to,
                'client_phone' => '1234567890',
                'service_id' => 2,
                'preferred_date' => now()->addDay(),
                'status' => 'WAITING',
            ]);

            $testWaitlist->exists = true; // Simular que existe en DB
            
            // Datos de slot disponible
            $testSlot = [
                'date' => now()->addDay(),
                'time' => '14:30',
                'barber_id' => 1,
            ];

            // Enviar notificación
            Notification::route('mail', $to)
                ->notify(new SlotAvailableNotification($testWaitlist, $testSlot));

            $this->info("✓ Email sent successfully!");
            $this->info("Check your inbox (and spam folder) at: {$to}");
            
        } catch (\Exception $e) {
            $this->error("✗ Email failed: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
