<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function create(): View
    {
        return view('public.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'postcode' => 'nullable|string',
            'message' => 'required|string',
        ]);

        // Save to leads (placeholder)
        // Lead::create([...$validated, 'source' => 'contact']);

        return redirect()->route('home')->with('success', 'Message sent successfully.');
    }
}
