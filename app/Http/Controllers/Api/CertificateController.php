<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'customer_id' => 'required|exists:customers,id',
            'property_id' => 'required|exists:properties,id',
            'boiler_id' => 'required|exists:boilers,id',
            'form_data' => 'nullable|json',
        ]);

        $cert_no = 'CERT-' . Str::upper(Str::random(10));

        $certificate = Certificate::create([
            'cert_no' => $cert_no,
            'type' => $validated['type'],
            'customer_id' => $validated['customer_id'],
            'property_id' => $validated['property_id'],
            'boiler_id' => $validated['boiler_id'],
            'form_data' => $validated['form_data'] ?? '{}',
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('certificates.template', ['certificate' => $certificate]);
        $path = "certificates/{$certificate->id}.pdf";
        Storage::disk('local')->put("public/{$path}", $pdf->output());

        $certificate->update(['pdf_path' => $path]);

        return response()->json([
            'certificate' => $certificate,
            'pdf_url' => "/storage/{$path}",
        ], 201);
    }

    public function show(Certificate $certificate)
    {
        return response()->json($certificate);
    }

    public function update(Request $request, Certificate $certificate)
    {
        $validated = $request->validate([
            'form_data' => 'sometimes|json',
        ]);

        $certificate->update($validated);
        return response()->json($certificate);
    }

    public function destroy(Certificate $certificate)
    {
        if ($certificate->pdf_path) {
            Storage::disk('local')->delete("public/{$certificate->pdf_path}");
        }
        $certificate->delete();
        return response()->json(null, 204);
    }

    public function pdf(Certificate $certificate)
    {
        if ($certificate->pdf_path && Storage::disk('local')->exists("public/{$certificate->pdf_path}")) {
            return response()->file(storage_path("app/public/{$certificate->pdf_path}"));
        }

        return response()->json(['error' => 'PDF not found'], 404);
    }

    public function email(Certificate $certificate, Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        // Stub: send email to customer
        return response()->json(['message' => 'Email queued', 'email' => $validated['email']]);
    }

    public function sign(Certificate $certificate, Request $request)
    {
        $validated = $request->validate([
            'signature' => 'required|string', // base64
        ]);

        $data = base64_decode($validated['signature']);
        $path = "signatures/{$certificate->id}.png";
        Storage::disk('local')->put("public/{$path}", $data);

        $certificate->update(['signature_path' => $path]);

        return response()->json(['message' => 'Signed', 'signature_path' => $path]);
    }
}
