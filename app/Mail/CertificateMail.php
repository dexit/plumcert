<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Certificate;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;

class CertificateMail extends Mailable
{
    public function __construct(public Certificate $certificate, public string $toEmail)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Gas Safety Certificate — Plumcert');
    }

    public function content(): Content
    {
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
}
