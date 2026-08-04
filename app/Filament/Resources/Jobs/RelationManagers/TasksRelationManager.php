<?php

namespace App\Filament\Resources\Jobs\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = 'Tasks';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'done',
                        'warning' => 'in_progress',
                        'gray' => 'skipped',
                        'info' => 'pending',
                    ]),
                TextColumn::make('due_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('photo_required')
                    ->boolean(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('complete')
                    ->label('Mark Done')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'done')
                    ->action(fn ($record) => $record->markDone()),
                DeleteAction::make(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'done' => 'Done',
                        'skipped' => 'Skipped',
                    ])
                    ->default('pending'),
                DateTimePicker::make('due_at'),
                Toggle::make('photo_required'),
                Select::make('assigned_to_user_id')
                    ->relationship('assignedTo', 'name')
                    ->searchable(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
