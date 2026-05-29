<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['customer_id', 'job_id', 'quote_id', 'invoice_number', 'line_items', 'subtotal', 'vat', 'total', 'paid_amount', 'due_date', 'paid_at', 'notes'])]
class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'line_items' => 'array',
            'subtotal' => 'decimal:2',
            'vat' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
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

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null && $this->balance() <= 0;
    }

    public function isOverdue(): bool
    {
        return $this->due_date < today() && !$this->isPaid();
    }

    public function balance(): float|int
    {
        return (float) ($this->total - ($this->paid_amount ?? 0));
    }
}
