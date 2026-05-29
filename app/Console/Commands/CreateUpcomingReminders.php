<?php

namespace App\Console\Commands;

use App\Models\Boiler;
use App\Models\Reminder;
use Illuminate\Console\Command;

class CreateUpcomingReminders extends Command
{
    protected $signature = 'reminders:generate';
    protected $description = 'Generate service reminders for boilers due within 30 days';

    public function handle()
    {
        $now = now();
        $thresholdDate = $now->copy()->addDays(30);

        $boilers = Boiler::whereBetween('next_service_due', [$now, $thresholdDate])
            ->whereDoesntHave('reminders', function ($query) {
                $query->whereNull('sent_at');
            })
            ->get();

        foreach ($boilers as $boiler) {
            Reminder::create([
                'boiler_id' => $boiler->id,
                'due_at' => $boiler->next_service_due,
                'sent_at' => null,
            ]);
        }

        $this->info("Created reminders for {$boilers->count()} boilers.");
    }
}
