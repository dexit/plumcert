<?php

namespace App\Filament\Resources\Invoices\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InvoiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('invoice_number'),
                TextEntry::make('customer.first_name')
                    ->label('Customer'),
                TextEntry::make('subtotal')
                    ->money('GBP')
                    ->placeholder('-'),
                TextEntry::make('vat')
                    ->money('GBP')
                    ->placeholder('-'),
                TextEntry::make('total')
                    ->money('GBP')
                    ->placeholder('-'),
                TextEntry::make('paid_amount')
                    ->money('GBP')
                    ->placeholder('-'),
                TextEntry::make('due_date')
                    ->placeholder('-'),
                TextEntry::make('paid_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
