<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCancelledAppointmentJob;
use App\Models\Appointment;
use Illuminate\Console\Command;

class TestWaitlistJob extends Command
{
    protected $signature = 'waitlist:test-job {appointment_id}';
    protected $description = 'Test the waitlist job with a cancelled appointment';

    public function handle()
    {
        $appointmentId = $this->argument('appointment_id');
        $appointment = Appointment::find($appointmentId);

        if (!$appointment) {
            $this->error("Appointment {$appointmentId} not found");
            return 1;
        }

        $this->info("Running ProcessCancelledAppointmentJob for appointment {$appointmentId}");
        $this->info("Date: {$appointment->date}, Service ID: {$appointment->service_id}");

        try {
            ProcessCancelledAppointmentJob::dispatchSync($appointment);
            $this->info("Job executed successfully!");
            
            // Check waitlist status
            $this->info("\nChecking waitlist entries...");
            $waitlist = \App\Models\Waitlist::where('preferred_date', $appointment->date)
                ->where('service_id', $appointment->service_id)
                ->where('status', 'NOTIFIED')
                ->first();
            
            if ($waitlist) {
                $this->info("✓ Waitlist entry found and notified: {$waitlist->email}");
            } else {
                $this->warn("× No waitlist entry was notified");
            }
            
        } catch (\Exception $e) {
            $this->error("Job failed: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
