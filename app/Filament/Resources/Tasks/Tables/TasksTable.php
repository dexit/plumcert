<?php

namespace App\Filament\Resources\Tasks\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('taskable_type')
                    ->formatStateUsing(fn ($state) => class_basename($state)),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'done',
                        'warning' => 'in_progress',
                        'gray' => 'skipped',
                        'info' => 'pending',
                    ]),
                TextColumn::make('due_at')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($record) => $record->due_at?->isPast() && $record->status !== 'done' ? 'danger' : null),
                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->sortable(),
                IconColumn::make('photo_required')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                        'skipped' => 'Skipped',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('complete')
                    ->label('Mark Done')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'done')
                    ->action(fn ($record) => $record->markDone()),
            ])
            ->defaultSort('due_at')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
