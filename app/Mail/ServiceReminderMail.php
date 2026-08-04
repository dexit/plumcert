<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Reminder;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ServiceReminderMail extends Mailable
{
    private ?EmailTemplate $template = null;

    /** @var array{subject:string, body:string}|null */
    private ?array $rendered = null;

    public function __construct(public Reminder $reminder)
    {
        if ($reminder->template_key) {
            $this->template = EmailTemplate::findByKey($reminder->template_key);
            $this->rendered = $this->template?->render($this->buildVariables());
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->rendered['subject'] ?? ($this->reminder->title ?: 'Your Boiler Service is Due — Plumcert'),
        );
    }

    public function content(): Content
    {
        // Use the admin-customizable template when one is configured, else the static blade.
        if ($this->rendered) {
            return new Content(htmlString: $this->rendered['body']);
        }

        return new Content(view: 'emails.service-reminder');
    }

    /**
     * Build the {{ variable }} substitution context from the reminder's relations.
     *
     * @return array<string, string|int|null>
     */
    private function buildVariables(): array
    {
        $reminder = $this->reminder->loadMissing(['boiler.property.customer', 'customer', 'property', 'certificate']);
        $boiler   = $reminder->boiler;
        $customer = $reminder->customer ?? $boiler?->property?->customer;
        $property = $reminder->property ?? $boiler?->property;
        $cert     = $reminder->certificate;

        $dueAt = $reminder->due_at;

        return [
            'customer_name'    => $customer ? trim("{$customer->first_name} {$customer->last_name}") : '',
            'contact_name'     => $customer->contact_name ?? '',
            'company_name'     => $customer->business_type ?? '',
            'appliance'        => $boiler ? trim("{$boiler->make} {$boiler->model}") : 'your appliance',
            'appliance_make'   => $boiler->make ?? '',
            'appliance_model'  => $boiler->model ?? '',
            'property_address' => $property?->address ?? '',
            'due_date'         => $dueAt?->format('d M Y') ?? '',
            'days_until'       => $dueAt ? max(0, (int) now()->startOfDay()->diffInDays($dueAt, false)) : '',
            'last_service'     => $boiler?->last_service_date?->format('d M Y') ?? '',
            'cert_type'        => $cert?->type ?? '',
            'cert_number'      => $cert?->certificate_number ?? '',
            'booking_url'      => config('app.url') . '/book',
            'business'         => config('app.name', 'Plumcert'),
            'phone'            => config('mail.support_phone', '0800 000 0000'),
        ];
    }
}
