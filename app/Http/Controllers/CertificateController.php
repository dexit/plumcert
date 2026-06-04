<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function download(Certificate $certificate)
    {
        return Pdf::loadView('certificates.template', ['certificate' => $certificate])
            ->stream("cert-{$certificate->certificate_number}.pdf");
    }
}
