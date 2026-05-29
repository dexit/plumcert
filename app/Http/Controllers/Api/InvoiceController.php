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
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

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
            'amount' => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'due_date' => 'sometimes|date',
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
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
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
