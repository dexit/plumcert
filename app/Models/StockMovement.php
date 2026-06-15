<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['part_id', 'job_id', 'user_id', 'type', 'quantity', 'unit_cost', 'reference', 'notes'])]
class StockMovement extends Model
{
    public function part(): BelongsTo { return $this->belongsTo(Part::class); }
    public function job(): BelongsTo { return $this->belongsTo(Job::class, 'job_id', 'id')->withDefault(); }
    public function user(): BelongsTo { return $this->belongsTo(User::class)->withDefault(); }
}
