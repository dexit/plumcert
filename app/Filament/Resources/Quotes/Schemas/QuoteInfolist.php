<?php

namespace App\Filament\Resources\Quotes\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class QuoteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id'),
                TextEntry::make('customer.first_name')
                    ->label('Customer'),
                TextEntry::make('job.title')
                    ->label('Job'),
                TextEntry::make('subtotal')
                    ->money('GBP')
                    ->placeholder('-'),
                TextEntry::make('vat')
                    ->money('GBP')
                    ->placeholder('-'),
                TextEntry::make('total')
                    ->money('GBP')
                    ->placeholder('-'),
                TextEntry::make('valid_until')
                    ->placeholder('-'),
                TextEntry::make('notes')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
