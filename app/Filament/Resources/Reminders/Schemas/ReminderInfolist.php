<?php

namespace App\Filament\Resources\Reminders\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ReminderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('customer.first_name')
                    ->label('Customer'),
                TextEntry::make('property.address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('boiler.make')
                    ->label('Boiler Make')
                    ->placeholder('-'),
                TextEntry::make('due_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('sent_at')
                    ->dateTime()
                    ->placeholder('Not sent'),
            ]);
    }
}
