<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function stripe(Request $request)
    {
        $payload = $request->all();

        if ($payload['type'] === 'payment_intent.succeeded') {
            $metadata = $payload['data']['object']['metadata'] ?? [];
            if (isset($metadata['invoice_id'])) {
                $invoice = Invoice::find($metadata['invoice_id']);
                if ($invoice) {
                    $invoice->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }
}
