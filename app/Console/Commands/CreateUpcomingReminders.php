<?php

declare(strict_types=1);

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
            ->with('property')
            ->get();

        foreach ($boilers as $boiler) {
            $boiler->load('property');

            Reminder::create([
                'boiler_id' => $boiler->id,
                'customer_id' => $boiler->property?->customer_id,
                'property_id' => $boiler->property_id,
                'title' => "Annual Boiler Service Due — {$boiler->make} {$boiler->model}",
                'description' => "Service due on {$boiler->next_service_due->format('d M Y')}",
                'due_at' => $boiler->next_service_due,
                'sent_at' => null,
            ]);
        }

        $this->info("Created reminders for {$boilers->count()} boilers.");
    }
}
