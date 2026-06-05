<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Reminder;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ServiceReminderMail extends Mailable
{
    public function __construct(public Reminder $reminder)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Boiler Service is Due — Plumcert');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.service-reminder');
    }
}
