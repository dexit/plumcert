<?php

namespace App\Jobs;

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
        $boiler = $this->reminder->boiler;
        $email = $boiler->customer->email ?? null;

        if (!$email) {
            \Log::info("No email for boiler {$boiler->id}");
            return;
        }

        Mail::raw(
            "Service reminder for boiler {$boiler->model} - Service due: {$boiler->next_service_due}",
            function ($message) use ($email, $boiler) {
                $message->to($email)
                    ->subject("Service Reminder: {$boiler->model}");
            }
        );

        \Log::info("Reminder sent for boiler {$boiler->id} to {$email}");
    }
}
