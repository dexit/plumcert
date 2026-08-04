<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\ServiceReminderMail;
use App\Models\EmailTemplate;
use App\Models\Reminder;
use App\Services\SmsSender;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReminderJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reminder $reminder)
    {
    }

    public function handle(SmsSender $sms): void
    {
        $reminder = $this->reminder->load(['boiler.property.customer', 'customer', 'property', 'certificate']);
        $customer = $reminder->customer ?? $reminder->boiler?->property?->customer;

        $channel = $reminder->channel ?? 'email';

        $sent = match ($channel) {
            'sms', 'whatsapp' => $this->sendText($reminder, $customer, $channel, $sms),
            default           => $this->sendEmail($reminder, $customer),
        };

        if ($sent) {
            $reminder->update(['sent_at' => now()]);
        }
    }

    private function sendEmail(Reminder $reminder, $customer): bool
    {
        $email = $customer?->email;
        if (! $email) {
            Log::channel('plumcert')->info("Reminder {$reminder->id}: no email address — skipped.");
            return false;
        }

        Mail::to($email)->send(new ServiceReminderMail($reminder));
        Log::channel('plumcert')->info("Reminder {$reminder->id} emailed to {$email}.");

        return true;
    }

    private function sendText(Reminder $reminder, $customer, string $channel, SmsSender $sms): bool
    {
        $number = $customer?->mobile ?? $customer?->tel;
        if (! $number) {
            Log::channel('plumcert')->info("Reminder {$reminder->id}: no mobile number for {$channel} — skipped.");
            return false;
        }

        return $sms->send($number, $this->buildText($reminder), $channel);
    }

    /**
     * Build a short plain-text message — uses the configured template's body
     * (stripped to text) when available, else the reminder title + due date.
     */
    private function buildText(Reminder $reminder): string
    {
        if ($reminder->template_key && $template = EmailTemplate::findByKey($reminder->template_key)) {
            $rendered = $template->render([
                'customer_name'    => $reminder->customer?->first_name ?? '',
                'appliance'        => $reminder->boiler ? trim("{$reminder->boiler->make} {$reminder->boiler->model}") : 'your appliance',
                'due_date'         => $reminder->due_at?->format('d M Y') ?? '',
                'property_address' => $reminder->property?->address ?? '',
                'business'         => config('app.name', 'Plumcert'),
            ]);

            $text = trim(html_entity_decode(strip_tags($rendered['body'])));
            $text = preg_replace('/\s+/', ' ', $text) ?? $text;

            return mb_substr($text, 0, 300);
        }

        $due = $reminder->due_at?->format('d M Y') ?? '';

        return trim("{$reminder->title} — due {$due}. " . config('app.name', 'Plumcert'));
    }
}
