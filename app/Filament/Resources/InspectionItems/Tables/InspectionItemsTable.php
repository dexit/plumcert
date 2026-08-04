<?php

namespace App\Filament\Resources\InspectionItems\Tables;

use App\Models\InspectionItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InspectionItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => InspectionItem::CATEGORIES[$state] ?? $state)
                    ->sortable(),

                TextColumn::make('location')
                    ->label('Location')
                    ->searchable(),

                TextColumn::make('make')
                    ->label('Make')
                    ->searchable(),

                TextColumn::make('model')
                    ->label('Model')
                    ->searchable(),

                TextColumn::make('result')
                    ->label('Result')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pass'    => 'success',
                        'fail'    => 'danger',
                        'id'      => 'danger',
                        'at_risk' => 'warning',
                        'na'      => 'gray',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pass'    => 'Pass',
                        'fail'    => 'Fail',
                        'at_risk' => 'At Risk',
                        'id'      => 'Immediately Dangerous',
                        'na'      => 'N/A',
                        default   => $state,
                    })
                    ->sortable(),

                TextColumn::make('job.title')
                    ->label('Job')
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->options(InspectionItem::CATEGORIES),

                SelectFilter::make('result')
                    ->options([
                        'pass'    => 'Pass',
                        'fail'    => 'Fail',
                        'at_risk' => 'At Risk',
                        'id'      => 'Immediately Dangerous',
                        'na'      => 'N/A',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
