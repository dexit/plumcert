<?php

namespace App\Jobs;

use App\Models\Invoice;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessStripeWebhookJob extends ProcessWebhookJob
{
    public function handle()
    {
        $payload = $this->webhookCall->payload;

        if ($payload['type'] ?? null === 'payment_intent.succeeded') {
            $sessionId = $payload['data']['object']['metadata']['session_id'] ?? null;
            if ($sessionId) {
                $invoice = Invoice::where('stripe_session_id', $sessionId)->first();
                if ($invoice) {
                    $invoice->update(['paid_at' => now()]);
                }
            }
        }
    }
}
