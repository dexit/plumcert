<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download(Invoice $invoice)
    {
        return Pdf::loadView('certificates.invoice', [
            'certNo' => $invoice->invoice_number,
            'client' => [
                'name' => $invoice->customer?->first_name . ' ' . $invoice->customer?->last_name,
                'email' => $invoice->customer?->email,
            ],
            'lineItems' => $invoice->line_items ?? [],
            'subtotal' => $invoice->subtotal,
            'vat' => $invoice->vat,
            'total' => $invoice->total,
            'dueDate' => $invoice->due_date?->format('d M Y'),
        ])->stream("invoice-{$invoice->invoice_number}.pdf");
    }
}
