<?php
namespace App\Filament\Widgets;

use App\Models\Part;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockAlert extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Low Stock Parts';

    public static function canView(): bool
    {
        return Part::where('stock_quantity', '<=', \DB::raw('min_stock_level'))->exists();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Part::query()->whereColumn('stock_quantity', '<=', 'min_stock_level')->orderBy('stock_quantity'))
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('sku')->placeholder('—'),
                TextColumn::make('category')->badge(),
                TextColumn::make('stock_quantity')->label('In Stock')->badge()->color('danger'),
                TextColumn::make('min_stock_level')->label('Min Level'),
                TextColumn::make('supplier')->placeholder('—'),
            ]);
    }
}
