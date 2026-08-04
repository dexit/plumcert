<?php

namespace App\Http\Controllers;

use App\Models\Lead;
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

        Lead::create([...$validated, 'source' => 'website', 'status' => 'new']);

        return redirect()->route('home')->with('success', 'Booking request received! We will be in touch within 2 hours.');
    }
}
