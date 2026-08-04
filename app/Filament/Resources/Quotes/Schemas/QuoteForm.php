<?php

namespace App\Filament\Resources\Quotes\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class QuoteForm
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
                Select::make('job_id')
                    ->relationship('job', 'title')
                    ->searchable()
                    ->preload(),
                TextInput::make('subtotal')
                    ->numeric()
                    ->prefix('£'),
                TextInput::make('vat')
                    ->numeric()
                    ->prefix('£'),
                TextInput::make('total')
                    ->numeric()
                    ->prefix('£'),
                DatePicker::make('valid_until'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
