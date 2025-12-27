<?php

namespace App\Console\Commands;

use App\Jobs\CleanExpiredWaitlistJob;
use Illuminate\Console\Command;

class CleanExpiredWaitlistCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'waitlist:clean';

    /**
     * The console command description.
     */
    protected $description = 'Clean expired waitlist entries and notification timeouts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Cleaning expired waitlist entries...');

        CleanExpiredWaitlistJob::dispatch();

        $this->info('Job dispatched successfully!');

        return 0;
    }
}
