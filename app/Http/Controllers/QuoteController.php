<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    public function download(Quote $quote)
    {
        return Pdf::loadView('certificates.quote', [
            'certNo' => 'Q-' . str_pad((string) $quote->id, 5, '0', STR_PAD_LEFT),
            'client' => [
                'name' => $quote->customer?->first_name . ' ' . $quote->customer?->last_name,
                'email' => $quote->customer?->email,
            ],
            'lineItems' => $quote->line_items ?? [],
            'subtotal' => $quote->subtotal,
            'vat' => $quote->vat,
            'total' => $quote->total,
            'validUntil' => $quote->valid_until?->format('d M Y'),
        ])->stream("quote-Q{$quote->id}.pdf");
    }
}
