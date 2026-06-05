<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Task;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TaskInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),
                TextEntry::make('taskable_type'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'done' => 'success',
                        'in_progress' => 'warning',
                        'skipped' => 'gray',
                        'pending' => 'info',
                        default => 'info',
                    }),
                TextEntry::make('due_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('assignedTo.name')
                    ->label('Assigned To'),
                IconEntry::make('photo_required')
                    ->boolean(),
            ]);
    }
}
