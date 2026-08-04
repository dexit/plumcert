<?php
namespace App\Filament\Resources\Parts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PartsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('sku')->searchable()->placeholder('—'),
                TextColumn::make('category')->badge()
                    ->color(fn ($state) => match($state) {
                        'boiler_parts' => 'warning',
                        'pipework'     => 'info',
                        'fittings'     => 'gray',
                        'tools'        => 'purple',
                        'consumables'  => 'success',
                        default        => 'gray',
                    })->placeholder('—'),
                TextColumn::make('stock_quantity')->label('Stock')
                    ->badge()
                    ->color(fn ($state, $record) => $record->isLowStock() ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('unit_cost')->label('Cost')->money('GBP')->sortable(),
                TextColumn::make('sell_price')->label('Sell')->money('GBP')->sortable(),
                TextColumn::make('supplier')->searchable()->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('category')->options([
                    'boiler_parts' => 'Boiler Parts',
                    'pipework'     => 'Pipework',
                    'fittings'     => 'Fittings',
                    'tools'        => 'Tools',
                    'consumables'  => 'Consumables',
                ]),
            ])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
