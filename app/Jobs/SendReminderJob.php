<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\ServiceReminderMail;
use App\Models\Reminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendReminderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reminder $reminder)
    {
    }

    public function handle()
    {
        $boiler = $this->reminder->load(['boiler.property.customer', 'customer', 'property'])->boiler;
        $email = $this->reminder->customer?->email ?? $boiler?->property?->customer?->email;

        if (!$email) {
            \Log::info("No email for boiler {$boiler?->id}");
            return;
        }

        Mail::to($email)->send(new ServiceReminderMail($this->reminder));

        $this->reminder->update(['sent_at' => now()]);

        \Log::info("Reminder sent for boiler {$boiler?->id} to {$email}");
    }
}
