<?php

namespace Tests\Feature;

use App\Jobs\SendReminderJob;
use App\Mail\ServiceReminderMail;
use App\Models\Boiler;
use App\Models\Customer;
use App\Models\Property;
use App\Models\Reminder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReminderSendTest extends TestCase
{
    use RefreshDatabase;

    private function reminderFor(string $channel, array $customerAttrs = []): Reminder
    {
        $customer = Customer::create(array_merge([
            'first_name' => 'Send', 'last_name' => 'Test',
            'email' => 'send@example.com', 'mobile' => '07700900999',
        ], $customerAttrs));

        $property = Property::create(['customer_id' => $customer->id, 'address' => '3 Send St']);
        $boiler = Boiler::create([
            'property_id' => $property->id, 'make' => 'Ideal', 'model' => 'Logic',
            'next_service_due' => now()->addDays(7),
        ]);

        return Reminder::create([
            'boiler_id'    => $boiler->id,
            'customer_id'  => $customer->id,
            'property_id'  => $property->id,
            'type'         => 'service',
            'title'        => 'Service due',
            'due_at'       => now(),
            'channel'      => $channel,
            'template_key' => 'service_reminder',
        ]);
    }

    public function test_email_channel_sends_mailable_and_marks_sent(): void
    {
        Mail::fake();

        $reminder = $this->reminderFor('email');
        SendReminderJob::dispatchSync($reminder);

        Mail::assertSent(ServiceReminderMail::class);
        $this->assertNotNull($reminder->fresh()->sent_at);
    }

    public function test_sms_channel_marks_sent_via_log_driver(): void
    {
        config(['services.twilio.driver' => 'log']);
        Mail::fake();

        $reminder = $this->reminderFor('sms');
        SendReminderJob::dispatchSync($reminder);

        Mail::assertNothingSent();
        $this->assertNotNull($reminder->fresh()->sent_at);
    }

    public function test_no_email_does_not_mark_sent(): void
    {
        Mail::fake();

        $reminder = $this->reminderFor('email', ['email' => null]);
        SendReminderJob::dispatchSync($reminder);

        Mail::assertNothingSent();
        $this->assertNull($reminder->fresh()->sent_at);
    }
}
