<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $footer = '<hr style="border:none;border-top:1px solid #ddd;margin:24px 0">'
            . '<p style="font-size:12px;color:#888">{{ business }} — Gas Safe Registered Engineers<br>'
            . 'Call us on {{ phone }} · <a href="{{ booking_url }}">Book online</a></p>';

        $templates = [
            [
                'key'         => 'service_reminder',
                'name'        => 'Annual Service Reminder',
                'subject'     => 'Your {{ appliance }} service is due on {{ due_date }}',
                'description' => 'Sent ahead of an appliance/boiler annual service becoming due.',
                'from_name'   => 'Plumcert Gas Safety',
                'body_html'   => '<div style="font-family:Arial,sans-serif;color:#222">'
                    . '<h2 style="color:#1a3a6b">Annual Service Reminder</h2>'
                    . '<p>Dear {{ customer_name }},</p>'
                    . '<p>Our records show your <strong>{{ appliance }}</strong> at {{ property_address }} '
                    . 'is due for its annual service on <strong>{{ due_date }}</strong> ({{ days_until }} days away).</p>'
                    . '<p>Regular servicing keeps your appliance safe, efficient and within warranty.</p>'
                    . '<p style="margin:24px 0"><a href="{{ booking_url }}" '
                    . 'style="background:#FFD700;color:#1a3a6b;padding:12px 24px;border-radius:4px;'
                    . 'text-decoration:none;font-weight:bold">Book Your Service</a></p>'
                    . $footer . '</div>',
            ],
            [
                'key'         => 'service_overdue',
                'name'        => 'Service Overdue Notice',
                'subject'     => 'OVERDUE: {{ appliance }} service was due {{ due_date }}',
                'description' => 'Sent when an appliance service is past its due date.',
                'from_name'   => 'Plumcert Gas Safety',
                'body_html'   => '<div style="font-family:Arial,sans-serif;color:#222">'
                    . '<h2 style="color:#b00020">Service Overdue</h2>'
                    . '<p>Dear {{ customer_name }},</p>'
                    . '<p>Your <strong>{{ appliance }}</strong> at {{ property_address }} was due for service on '
                    . '<strong>{{ due_date }}</strong> and is now overdue.</p>'
                    . '<p>An unserviced gas appliance can be unsafe. Please book as soon as possible.</p>'
                    . '<p style="margin:24px 0"><a href="{{ booking_url }}" '
                    . 'style="background:#b00020;color:#fff;padding:12px 24px;border-radius:4px;'
                    . 'text-decoration:none;font-weight:bold">Book Now</a></p>'
                    . $footer . '</div>',
            ],
            [
                'key'         => 'annual_cert_renewal',
                'name'        => 'Gas Safety Certificate Renewal',
                'subject'     => 'Your Gas Safety Certificate expires on {{ due_date }}',
                'description' => 'Sent to landlords/homeowners when a CP12 / gas safety certificate is due for renewal.',
                'from_name'   => 'Plumcert Gas Safety',
                'body_html'   => '<div style="font-family:Arial,sans-serif;color:#222">'
                    . '<h2 style="color:#1a3a6b">Certificate Renewal Due</h2>'
                    . '<p>Dear {{ customer_name }},</p>'
                    . '<p>Your <strong>{{ cert_type }}</strong> (No. {{ cert_number }}) for {{ property_address }} '
                    . 'is due to expire on <strong>{{ due_date }}</strong>.</p>'
                    . '<p>Landlords are legally required to hold a valid gas safety certificate for each rented property. '
                    . 'Book your renewal inspection now to stay compliant.</p>'
                    . '<p style="margin:24px 0"><a href="{{ booking_url }}" '
                    . 'style="background:#FFD700;color:#1a3a6b;padding:12px 24px;border-radius:4px;'
                    . 'text-decoration:none;font-weight:bold">Renew Certificate</a></p>'
                    . $footer . '</div>',
            ],
            [
                'key'         => 'certificate_delivery',
                'name'        => 'Certificate Delivery',
                'subject'     => 'Your {{ cert_type }} ({{ cert_number }})',
                'description' => 'Sent with a freshly issued certificate PDF attached.',
                'from_name'   => 'Plumcert Gas Safety',
                'body_html'   => '<div style="font-family:Arial,sans-serif;color:#222">'
                    . '<h2 style="color:#1a3a6b">Your Certificate</h2>'
                    . '<p>Dear {{ customer_name }},</p>'
                    . '<p>Please find attached your <strong>{{ cert_type }}</strong> '
                    . '(Cert No. {{ cert_number }}) for {{ property_address }}.</p>'
                    . '<p>Keep this document safe — you may need it for insurance, tenancy or sale purposes.</p>'
                    . $footer . '</div>',
            ],
        ];

        foreach ($templates as $t) {
            EmailTemplate::updateOrCreate(['key' => $t['key']], $t);
        }
    }
}
