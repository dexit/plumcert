<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class InvoiceForm
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
                Select::make('quote_id')
                    ->relationship('quote', 'id')
                    ->searchable()
                    ->preload(),
                TextInput::make('invoice_number')
                    ->required(),
                TextInput::make('subtotal')
                    ->numeric()
                    ->prefix('£'),
                TextInput::make('vat')
                    ->numeric()
                    ->prefix('£'),
                TextInput::make('total')
                    ->numeric()
                    ->prefix('£'),
                TextInput::make('paid_amount')
                    ->numeric()
                    ->prefix('£'),
                DatePicker::make('due_date'),
                DateTimePicker::make('paid_at'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
