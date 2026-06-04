<?php

namespace App\Filament\Resources\Findings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FindingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('description')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('severity')
                    ->badge(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('job.title')
                    ->label('Job')
                    ->placeholder('-'),
                TextEntry::make('submittedBy.name')
                    ->label('Submitted By')
                    ->placeholder('-'),
                TextEntry::make('featured')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
