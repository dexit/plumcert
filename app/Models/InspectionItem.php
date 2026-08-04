<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable(['job_id', 'certificate_id', 'property_id', 'category', 'location', 'make', 'model', 'serial', 'gc_number', 'result', 'data', 'notes'])]
class InspectionItem extends Model
{
    use HasFactory;

    public const CATEGORIES = [
        'gas_appliance' => 'Gas Appliance',
        'gas_boiler'    => 'Gas Boiler',
        'heater'        => 'Heater',
        'plumbing'      => 'Plumbing',
        'radiators'     => 'Radiators',
        'co_alarm'      => 'Carbon Monoxide Alarm',
        'smoke_alarm'   => 'Smoke Alarm',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    public function scopeCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }
}
