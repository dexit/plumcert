<?php

namespace App\Filament\Resources\Reminders\Tables;

use App\Jobs\SendReminderJob;
use App\Models\Reminder;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RemindersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer.first_name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('property.address')
                    ->label('Property')
                    ->searchable(),
                TextColumn::make('boiler.make')
                    ->label('Boiler')
                    ->searchable(),
                TextColumn::make('due_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->placeholder('Not sent')
                    ->sortable(),
            ])
            ->defaultSort('due_at')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('sendNow')
                    ->label(fn (Reminder $record) => $record->sent_at ? 'Resend' : 'Send now')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription(fn (Reminder $record) => 'Send this reminder now via its ' . ($record->channel ?? 'email') . ' channel.')
                    ->action(function (Reminder $record) {
                        SendReminderJob::dispatchSync($record);

                        Notification::make()
                            ->title('Reminder dispatched')
                            ->body('Sent via ' . ($record->channel ?? 'email') . '.')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
