<?php

namespace Tests\Unit;

use App\Services\SmsSender;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SmsSenderTest extends TestCase
{
    public function test_log_driver_sends_and_returns_true(): void
    {
        config(['services.twilio.driver' => 'log']);

        Log::shouldReceive('channel')->with('plumcert')->andReturnSelf();
        Log::shouldReceive('info')->once();

        $this->assertTrue((new SmsSender)->send('07700900123', 'Hello', 'sms'));
    }

    public function test_empty_number_returns_false(): void
    {
        Log::shouldReceive('channel')->with('plumcert')->andReturnSelf();
        Log::shouldReceive('warning')->once();

        $this->assertFalse((new SmsSender)->send('', 'Hello'));
    }

    public function test_whatsapp_channel_is_accepted(): void
    {
        config(['services.twilio.driver' => 'log']);

        Log::shouldReceive('channel')->with('plumcert')->andReturnSelf();
        Log::shouldReceive('info')->once();

        $this->assertTrue((new SmsSender)->send('07700900123', 'Hi', 'whatsapp'));
    }
}
