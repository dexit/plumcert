<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends SMS and WhatsApp messages via Twilio, with a 'log' fallback driver
 * for local/dev use (no credentials required). Selected by config
 * services.twilio.driver (SMS_DRIVER env).
 */
class SmsSender
{
    /**
     * Send a plain-text message to a phone number.
     *
     * @param  'sms'|'whatsapp'  $channel
     */
    public function send(string $to, string $message, string $channel = 'sms'): bool
    {
        $to = $this->normalise($to);
        if ($to === '') {
            Log::channel('plumcert')->warning("SmsSender: no destination number for {$channel} message");
            return false;
        }

        $driver = config('services.twilio.driver', 'log');

        return $driver === 'twilio'
            ? $this->sendViaTwilio($to, $message, $channel)
            : $this->sendViaLog($to, $message, $channel);
    }

    private function sendViaLog(string $to, string $message, string $channel): bool
    {
        Log::channel('plumcert')->info("[{$channel}→{$to}] {$message}");

        return true;
    }

    private function sendViaTwilio(string $to, string $message, string $channel): bool
    {
        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');

        if (! $sid || ! $token) {
            Log::channel('plumcert')->warning('SmsSender: Twilio driver selected but credentials missing — falling back to log.');
            return $this->sendViaLog($to, $message, $channel);
        }

        if ($channel === 'whatsapp') {
            $from = 'whatsapp:' . config('services.twilio.whatsapp_from');
            $to   = 'whatsapp:' . $to;
        } else {
            $from = config('services.twilio.from');
        }

        try {
            $response = Http::asForm()
                ->withBasicAuth($sid, $token)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => $from,
                    'To'   => $to,
                    'Body' => $message,
                ]);

            if (! $response->successful()) {
                Log::channel('plumcert')->error("SmsSender: Twilio {$channel} send failed", ['status' => $response->status(), 'body' => $response->body()]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::channel('plumcert')->error("SmsSender: Twilio {$channel} exception — {$e->getMessage()}");
            return false;
        }
    }

    private function normalise(string $number): string
    {
        $number = preg_replace('/[^\d+]/', '', trim($number)) ?? '';

        // Convert UK national 0xxxx to +44 international form.
        if (str_starts_with($number, '0')) {
            $number = '+44' . substr($number, 1);
        }

        return $number;
    }
}
