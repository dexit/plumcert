<?php

namespace App\Console\Commands;

use App\Jobs\SendReminderJob;
use App\Models\Reminder;
use Illuminate\Console\Command;

class ProcessDueReminders extends Command
{
    protected $signature = 'reminders:process';
    protected $description = 'Process and send due service reminders';

    public function handle()
    {
        $reminders = Reminder::where('due_at', '<=', now())
            ->whereNull('sent_at')
            ->get();

        foreach ($reminders as $reminder) {
            SendReminderJob::dispatch($reminder);
            $reminder->update(['sent_at' => now()]);
        }

        $this->info("Dispatched {$reminders->count()} reminders.");
    }
}
