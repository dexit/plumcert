<?php

namespace App\Filament\Resources\Photos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PhotosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')
                    ->label('Image'),
                TextColumn::make('caption')
                    ->searchable(),
                BadgeColumn::make('phase')
                    ->colors([
                        'info' => 'start',
                        'warning' => 'during',
                        'success' => 'finish',
                        'danger' => 'defect',
                    ]),
                BadgeColumn::make('photoable_type')
                    ->formatStateUsing(fn ($state) => class_basename($state)),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('phase')
                    ->options([
                        'start' => 'Start',
                        'during' => 'During',
                        'finish' => 'Finish',
                        'defect' => 'Defect',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
