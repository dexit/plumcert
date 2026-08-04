<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['job_id', 'user_id', 'clocked_in_at', 'clocked_out_at', 'minutes', 'billable_rate', 'notes'])]
class TimeEntry extends Model
{
    protected function casts(): array
    {
        return [
            'clocked_in_at'  => 'datetime',
            'clocked_out_at' => 'datetime',
        ];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function durationMinutes(): int
    {
        if ($this->clocked_out_at) {
            return (int) $this->clocked_in_at->diffInMinutes($this->clocked_out_at);
        }
        return (int) $this->clocked_in_at->diffInMinutes(now());
    }

    public function durationFormatted(): string
    {
        $mins = $this->durationMinutes();
        $h = intdiv($mins, 60);
        $m = $mins % 60;
        return $h > 0 ? "{$h}h {$m}m" : "{$m}m";
    }
}
