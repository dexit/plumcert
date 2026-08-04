<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class CustomerPortalController extends Controller
{
    public function show(Request $request, Customer $customer)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'This link has expired or is invalid.');
        }

        $certificates = Certificate::whereHas('job', fn ($q) => $q->where('customer_id', $customer->id))
            ->with(['job.property', 'job.boiler'])
            ->latest('issued_at')
            ->get();

        return view('portal.show', compact('customer', 'certificates'));
    }

    public function downloadCert(Request $request, Customer $customer, Certificate $certificate)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'This link has expired or is invalid.');
        }

        if ($certificate->job->customer_id !== $customer->id) {
            abort(403);
        }

        $pdf = app(\App\Services\PdfCertificate::class)->generate($certificate);
        $filename = 'certificate-' . $certificate->certificate_number . '.pdf';

        return response($pdf)->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=\"{$filename}\"");
    }

    public function bookingRequest(Request $request, Customer $customer)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'This link has expired or is invalid.');
        }

        $validated = $request->validate([
            'preferred_date' => 'required|date|after:today',
            'notes'          => 'nullable|string|max:500',
        ]);

        // Create a lead/booking note — stored as a reminder note for now
        \App\Models\Reminder::create([
            'customer_id'  => $customer->id,
            'type'         => 'booking_request',
            'template_key' => 'booking_request',
            'channel'      => 'email',
            'due_at'       => now()->addHour(),
            'message'      => 'Customer requested booking for ' . $validated['preferred_date'] . '. Notes: ' . ($validated['notes'] ?? 'None'),
        ]);

        return redirect()->back()->with('success', 'Booking request sent! We will be in touch shortly.');
    }
}
