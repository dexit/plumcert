<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'sku', 'category', 'supplier', 'unit_cost', 'sell_price', 'stock_quantity', 'min_stock_level', 'unit', 'notes'])]
class Part extends Model
{
    use HasFactory, SoftDeletes;

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    public function margin(): float
    {
        if ($this->unit_cost == 0) return 0;
        return round((($this->sell_price - $this->unit_cost) / $this->unit_cost) * 100, 1);
    }
}
