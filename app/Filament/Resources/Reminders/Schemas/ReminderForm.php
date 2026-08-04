<?php

namespace App\Filament\Resources\Reminders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ReminderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'first_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('property_id')
                    ->relationship('property', 'address')
                    ->searchable()
                    ->preload(),
                Select::make('boiler_id')
                    ->relationship('boiler', 'make')
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
                DateTimePicker::make('due_at')
                    ->required(),
                DateTimePicker::make('sent_at'),
            ]);
    }
}
