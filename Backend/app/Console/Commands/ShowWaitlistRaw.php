<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowWaitlistRaw extends Command
{
    protected $signature = 'waitlist:show-raw {id}';
    protected $description = 'Show raw waitlist data';

    public function handle()
    {
        $id = $this->argument('id');
        $entry = DB::table('waitlist')->where('id', $id)->first();

        if (!$entry) {
            $this->error("Waitlist entry {$id} not found");
            return 1;
        }

        $this->info("RAW WAITLIST DATA:");
        foreach ((array)$entry as $key => $value) {
            $this->line("{$key}: " . ($value ?? 'NULL'));
        }

        return 0;
    }
}
