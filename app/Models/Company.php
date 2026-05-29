<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Fillable(['name', 'vat_number', 'gas_safe_registration', 'bank_details', 'address', 'postcode', 'logo_path'])]
class Company extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'bank_details' => 'array',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function jobs(): HasManyThrough
    {
        return $this->hasManyThrough(Job::class, Customer::class);
    }
}
