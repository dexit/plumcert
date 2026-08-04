<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\Invoice;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Quote::paginate(20));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'title' => 'required|string',
            'amount' => 'required|numeric',
            'description' => 'nullable|string',
            'valid_until' => 'nullable|date',
        ]);

        $quote = Quote::create($validated);
        return response()->json($quote, 201);
    }

    public function show(Quote $quote)
    {
        return response()->json($quote);
    }

    public function update(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'amount' => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'valid_until' => 'sometimes|date',
        ]);

        $quote->update($validated);
        return response()->json($quote);
    }

    public function destroy(Quote $quote)
    {
        $quote->delete();
        return response()->json(null, 204);
    }

    public function send(Quote $quote, Request $request)
    {
        // Stub: send quote email
        return response()->json(['message' => 'Quote sent']);
    }

    public function convertToInvoice(Quote $quote)
    {
        $invoice = Invoice::create([
            'customer_id' => $quote->customer_id,
            'amount' => $quote->amount,
            'description' => $quote->description,
            'quote_id' => $quote->id,
        ]);

        return response()->json($invoice, 201);
    }
}
