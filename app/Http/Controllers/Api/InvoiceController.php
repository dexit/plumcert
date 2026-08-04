<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Invoice::paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'job_id' => 'nullable|exists:service_jobs,id',
            'quote_id' => 'nullable|exists:quotes,id',
            'line_items' => 'nullable|array',
            'subtotal' => 'nullable|numeric',
            'vat' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $invoice_number = 'INV-' . str_pad(Invoice::max('id') + 1, 5, '0', STR_PAD_LEFT);
        $validated['invoice_number'] = $invoice_number;

        $invoice = Invoice::create($validated);
        return response()->json($invoice, 201);
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'line_items' => 'sometimes|array',
            'subtotal' => 'sometimes|numeric',
            'vat' => 'sometimes|numeric',
            'total' => 'sometimes|numeric',
            'paid_amount' => 'sometimes|numeric',
            'due_date' => 'sometimes|date',
            'notes' => 'sometimes|string',
        ]);

        $invoice->update($validated);
        return response()->json($invoice);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return response()->json(null, 204);
    }

    public function markPaid(Invoice $invoice, Request $request)
    {
        $paid_amount = $invoice->paid_amount ?? $invoice->total;
        $invoice->update([
            'paid_at' => now(),
            'paid_amount' => $paid_amount,
        ]);

        return response()->json($invoice);
    }

    public function stripeLink(Invoice $invoice)
    {
        // Stub: return Stripe checkout URL
        return response()->json([
            'stripe_url' => 'https://checkout.stripe.com/pay/stub',
        ]);
    }
}
