<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['customer_id', 'job_id', 'line_items', 'subtotal', 'vat', 'total', 'valid_until', 'notes'])]
class Quote extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'line_items' => 'array',
            'subtotal' => 'decimal:2',
            'vat' => 'decimal:2',
            'total' => 'decimal:2',
            'valid_until' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function convertToInvoice(): Invoice
    {
        return Invoice::create([
            'customer_id' => $this->customer_id,
            'job_id' => $this->job_id,
            'quote_id' => $this->id,
            'line_items' => $this->line_items,
            'subtotal' => $this->subtotal,
            'vat' => $this->vat,
            'total' => $this->total,
            'due_date' => now()->addDays(30)->toDateString(),
        ]);
    }

    public function recalculate(): void
    {
        $items = $this->line_items ?? [];
        $subtotal = collect($items)->sum('total') ?? 0;
        $vat = $subtotal * 0.20;

        $this->update([
            'subtotal' => $subtotal,
            'vat' => $vat,
            'total' => $subtotal + $vat,
        ]);
    }
}
