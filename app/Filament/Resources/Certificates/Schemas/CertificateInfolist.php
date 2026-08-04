<?php

namespace App\Filament\Resources\Certificates\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CertificateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('certificate_number'),
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('customer.first_name')
                    ->label('Customer'),
                TextEntry::make('property.address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('boiler.make')
                    ->label('Boiler Make')
                    ->placeholder('-'),
                TextEntry::make('issuedBy.name')
                    ->label('Issued By')
                    ->placeholder('-'),
                TextEntry::make('issued_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('sent_at')
                    ->dateTime()
                    ->placeholder('-'),
                IconEntry::make('signed_by_engineer')
                    ->boolean(),
                IconEntry::make('signed_by_customer')
                    ->boolean(),
            ]);
    }
}
