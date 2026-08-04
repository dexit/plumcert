<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['trigger', 'applies_to', 'title', 'description', 'offset_days', 'sort_order', 'photo_required', 'active'])]
class TaskTemplate extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'photo_required' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function scopeFor(Builder $query, string $trigger, ?string $appliesTo = null): Builder
    {
        return $query->where('active', true)
            ->where('trigger', $trigger)
            ->where(function ($q) use ($appliesTo) {
                $q->whereNull('applies_to');
                if ($appliesTo) {
                    $q->orWhere('applies_to', $appliesTo);
                }
            })
            ->orderBy('sort_order');
    }
}
