<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    public function create(): View
    {
        return view('public.book');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'postcode' => 'required|string',
            'service_type' => 'required|string',
            'message' => 'nullable|string',
        ]);

        // Save to leads (placeholder)
        // Lead::create($validated);

        return redirect()->route('home')->with('success', 'Booking request submitted.');
    }
}
