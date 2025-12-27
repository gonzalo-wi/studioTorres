<?php

namespace App\Console\Commands;

use App\Models\Waitlist;
use Illuminate\Console\Command;

class CheckWaitlistStatus extends Command
{
    protected $signature = 'waitlist:check {id}';
    protected $description = 'Check waitlist entry status';

    public function handle()
    {
        $id = $this->argument('id');
        $entry = Waitlist::find($id);

        if (!$entry) {
            $this->error("Waitlist entry {$id} not found");
            return 1;
        }

        $this->info("WAITLIST ENTRY STATUS:");
        $this->info("ID: {$entry->id}");
        $this->info("Email: {$entry->email}");
        $this->info("Service ID: {$entry->service_id}");
        $this->info("Preferred Date: {$entry->preferred_date}");
        $this->info("Status: {$entry->status}");
        $this->info("Notified At: " . ($entry->notified_at ?? 'NULL'));
        $this->info("Created At: {$entry->created_at}");

        return 0;
    }
}
