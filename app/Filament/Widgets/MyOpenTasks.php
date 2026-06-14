<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyOpenTasks extends BaseWidget
{
    protected static ?string $heading = 'My Open Tasks';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder
    {
        return Task::query()
            ->open()
            ->where('assigned_to_user_id', Auth::id())
            ->with('taskable')
            ->orderByRaw("CASE WHEN due_at IS NULL THEN 1 ELSE 0 END")
            ->orderBy('due_at');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('title')
                    ->label('Task')
                    ->searchable()
                    ->wrap()
                    ->weight('bold'),

                TextColumn::make('taskable_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => class_basename($state)),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info'    => 'in_progress',
                    ]),

                TextColumn::make('due_at')
                    ->label('Due')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color(fn ($record) => $record->due_at && $record->due_at->isPast() ? 'danger' : null),

                TextColumn::make('description')
                    ->label('Notes')
                    ->limit(60)
                    ->wrap(),
            ])
            ->defaultSort('due_at')
            ->paginated([5, 10, 25]);
    }
}
