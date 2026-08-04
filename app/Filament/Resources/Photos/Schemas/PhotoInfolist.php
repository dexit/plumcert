<?php

namespace App\Filament\Resources\Photos\Schemas;

use App\Models\Photo;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PhotoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('path')
                    ->label('Image'),
                TextEntry::make('caption'),
                TextEntry::make('phase')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'start' => 'info',
                        'during' => 'warning',
                        'finish' => 'success',
                        'defect' => 'danger',
                        default => 'gray',
                    }),
                TextEntry::make('photoable_type'),
                TextEntry::make('uploadedBy.name')
                    ->label('Uploaded By'),
                TextEntry::make('created_at')
                    ->dateTime(),
            ]);
    }
}
