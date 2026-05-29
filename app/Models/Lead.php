<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'email', 'phone', 'message', 'source', 'converted_customer_id', 'converted_at'])]
class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'converted_at' => 'datetime',
        ];
    }

    public function convertedCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'converted_customer_id');
    }

    public static function convertToCustomer(Lead $lead, Company $company, ?User $createdBy = null): Customer
    {
        return Customer::create([
            'company_id' => $company->id,
            'created_by' => $createdBy?->id,
            'first_name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
        ]);
    }
}
