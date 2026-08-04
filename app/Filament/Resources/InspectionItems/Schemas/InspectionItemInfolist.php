<?php

namespace App\Filament\Resources\InspectionItems\Schemas;

use App\Models\InspectionItem;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InspectionItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('category')
                ->label('Category')
                ->badge()
                ->formatStateUsing(fn (string $state): string => InspectionItem::CATEGORIES[$state] ?? $state),

            TextEntry::make('location')
                ->label('Location')
                ->placeholder('—'),

            TextEntry::make('make')
                ->label('Make')
                ->placeholder('—'),

            TextEntry::make('model')
                ->label('Model')
                ->placeholder('—'),

            TextEntry::make('serial')
                ->label('Serial Number')
                ->placeholder('—'),

            TextEntry::make('result')
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
                }),

            TextEntry::make('notes')
                ->label('Notes')
                ->placeholder('—')
                ->columnSpanFull(),

            TextEntry::make('data')
                ->label('Inspection Data')
                ->formatStateUsing(fn ($state): string => collect($state ?? [])
                    ->map(fn ($v, $k) => "$k: $v")
                    ->implode(', '))
                ->columnSpanFull()
                ->placeholder('No data recorded'),
        ]);
    }
}
