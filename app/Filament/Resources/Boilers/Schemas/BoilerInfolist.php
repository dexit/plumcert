<?php

namespace App\Filament\Resources\Boilers\Schemas;

use App\Models\Boiler;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BoilerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('property.id')
                    ->label('Property'),
                TextEntry::make('make'),
                TextEntry::make('model'),
                TextEntry::make('serial')
                    ->placeholder('-'),
                TextEntry::make('gc_number')
                    ->placeholder('-'),
                TextEntry::make('install_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('last_service_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('next_service_due')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Boiler $record): bool => $record->trashed()),
            ]);
    }
}
