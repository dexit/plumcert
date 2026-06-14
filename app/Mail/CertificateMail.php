<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Certificate;
use App\Models\EmailTemplate;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;

class CertificateMail extends Mailable
{
    /** @var array{subject:string, body:string}|null */
    private ?array $rendered = null;

    public function __construct(public Certificate $certificate, public string $toEmail)
    {
        $template = EmailTemplate::findByKey('certificate_delivery');
        $this->rendered = $template?->render($this->buildVariables());
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->rendered['subject'] ?? 'Your Gas Safety Certificate — Plumcert',
        );
    }

    public function content(): Content
    {
        if ($this->rendered) {
            return new Content(htmlString: $this->rendered['body']);
        }

        return new Content(view: 'emails.certificate');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if (!$this->certificate->pdf_path || !Storage::disk('local')->exists("public/{$this->certificate->pdf_path}")) {
            return [];
        }

        return [
            Attachment::fromPath(storage_path('app/public/' . $this->certificate->pdf_path))
                ->as("certificate-{$this->certificate->certificate_number}.pdf")
                ->withMime('application/pdf'),
        ];
    }

    /**
     * @return array<string, string|int|null>
     */
    private function buildVariables(): array
    {
        $cert = $this->certificate->loadMissing(['customer', 'property', 'boiler']);
        $customer = $cert->customer;
        $property = $cert->property;
        $boiler   = $cert->boiler;

        return [
            'customer_name'    => $customer ? trim("{$customer->first_name} {$customer->last_name}") : '',
            'contact_name'     => $customer->contact_name ?? '',
            'company_name'     => $customer->company_name ?? '',
            'appliance'        => $boiler ? trim("{$boiler->make} {$boiler->model}") : '',
            'appliance_make'   => $boiler->make ?? '',
            'appliance_model'  => $boiler->model ?? '',
            'property_address' => $property?->address ?? '',
            'cert_type'        => self::certTypeLabel($cert->type),
            'cert_number'      => $cert->certificate_number ?? '',
            'booking_url'      => config('app.url') . '/book',
            'business'         => config('app.name', 'Plumcert'),
            'phone'            => config('mail.support_phone', '0800 000 0000'),
        ];
    }

    private static function certTypeLabel(?string $type): string
    {
        return match ($type) {
            'cp12_homeowner'         => 'CP12 Homeowner Gas Safety Record',
            'cp12_landlord'          => 'CP12 Landlord Gas Safety Record',
            'warning_notice'         => 'Gas Warning Notice',
            'installation_checklist' => 'Installation / Commissioning Checklist',
            'gas_service_record'     => 'Gas Service Record',
            'minor_works'            => 'Minor Works Certificate',
            'disconnection'          => 'Disconnection Notice',
            default                  => 'Gas Safety Certificate',
        };
    }
}
