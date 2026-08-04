<?php

namespace App\Http\Controllers;

use App\Models\Lead;
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

        Lead::create(['name'=>$validated['name'], 'email'=>$validated['email'], 'phone'=>$validated['phone'], 'message'=>$validated['message'], 'source'=>'contact', 'status'=>'new']);

        return redirect()->route('home')->with('success', 'Message sent successfully.');
    }
}
