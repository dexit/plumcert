<?php

namespace App\Filament\Resources\Jobs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class JobInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer.first_name')->label('Customer'),
                TextEntry::make('property.address')->label('Property'),
                TextEntry::make('assignedTo.name')->label('Assigned To'),
                TextEntry::make('title')->columnSpanFull(),
                TextEntry::make('description')->columnSpanFull(),
                TextEntry::make('status')->badge(),
                TextEntry::make('scheduled_at')->dateTime(),
                TextEntry::make('completed_at')->dateTime()->placeholder('Not completed'),
                TextEntry::make('created_at')->dateTime(),
            ]);
    }
}
