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

#[Fillable(['customer_id', 'property_id', 'assigned_to_user_id', 'type', 'form_data', 'title', 'description', 'status', 'is_recurring', 'recurs_from_certificate_id', 'scheduled_at', 'completed_at', 'notes'])]
class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'service_jobs';

    /**
     * Pre-built job categories, each with its own standard form.
     */
    public const TYPES = [
        'installation' => 'Installation',
        'maintenance'  => 'Maintenance / Service',
        'emergency'    => 'Emergency Call-Out',
        'heating'      => 'Heating',
        'plumbing'     => 'Plumbing',
        'custom'       => 'Custom',
        'other'        => 'Other',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
            'is_recurring' => 'boolean',
            'form_data'    => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function findings(): HasMany
    {
        return $this->hasMany(Finding::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function tasks(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Task::class, 'taskable')->orderBy('sort_order');
    }

    public function photos(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Photo::class, 'photoable');
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class, 'job_id')->orderBy('clocked_in_at');
    }

        public function inspectionItems(): HasMany
    {
        return $this->hasMany(InspectionItem::class);
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }
}
