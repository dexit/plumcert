<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['job_id', 'customer_id', 'property_id', 'boiler_id', 'issued_by_user_id', 'certificate_number', 'form_data', 'issued_at', 'sent_at', 'signed_by_engineer', 'signed_by_customer'])]
class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'form_data' => 'array',
            'issued_at' => 'datetime',
            'sent_at' => 'datetime',
            'signed_by_engineer' => 'boolean',
            'signed_by_customer' => 'boolean',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function boiler(): BelongsTo
    {
        return $this->belongsTo(Boiler::class);
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }

    public static function generateCertNo(): string
    {
        $nextId = (self::max('id') ?? 0) + 1;
        return str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);
    }
}
