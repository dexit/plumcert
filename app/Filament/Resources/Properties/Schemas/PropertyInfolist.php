<?php

namespace App\Filament\Resources\Properties\Schemas;

use App\Models\Property;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PropertyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer.title')
                    ->label('Customer'),
                TextEntry::make('address')
                    ->columnSpanFull(),
                TextEntry::make('postcode')
                    ->placeholder('-'),
                TextEntry::make('town')
                    ->placeholder('-'),
                TextEntry::make('county')
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Property $record): bool => $record->trashed()),
            ]);
    }
}
