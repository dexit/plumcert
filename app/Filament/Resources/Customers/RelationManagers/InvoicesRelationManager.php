<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Invoices';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->searchable(),
                TextColumn::make('total')
                    ->money('gbp')
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->money('gbp'),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('paid_at')
                    ->dateTime()
                    ->placeholder('Unpaid'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('invoice_number')
                    ->required(),
                TextInput::make('total')
                    ->numeric()
                    ->prefix('£'),
                TextInput::make('vat')
                    ->numeric()
                    ->prefix('£'),
                TextInput::make('subtotal')
                    ->numeric()
                    ->prefix('£'),
                DatePicker::make('due_date'),
            ]);
    }

    public static function getRelationshipName(): string
    {
        return 'invoices';
    }
}
