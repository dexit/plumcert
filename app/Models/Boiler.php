<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['property_id', 'make', 'model', 'serial_number', 'install_date', 'last_service_date', 'next_service_due'])]
class Boiler extends Model
{
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return [
            'install_date' => 'date',
            'last_service_date' => 'date',
            'next_service_due' => 'date',
        ];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function scopeDueForService(Builder $query): Builder
    {
        return $query->where('next_service_due', '<=', now()->addDays(30));
    }
}
