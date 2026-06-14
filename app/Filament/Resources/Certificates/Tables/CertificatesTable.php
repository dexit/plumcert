<?php

namespace App\Filament\Resources\Certificates\Tables;

use App\Mail\CertificateMail;
use App\Models\Certificate;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class CertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('certificate_number')
                    ->label('Cert No.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->searchable(),
                TextColumn::make('customer.first_name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('property.address')
                    ->label('Property')
                    ->searchable(),
                TextColumn::make('issuedBy.name')
                    ->label('Issued By')
                    ->searchable(),
                TextColumn::make('issued_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->placeholder('Not sent')
                    ->sortable(),
                IconColumn::make('signed_by_engineer')
                    ->boolean(),
                IconColumn::make('signed_by_customer')
                    ->boolean(),
            ])
            ->defaultSort('issued_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'cp12_homeowner' => 'CP12 Homeowner',
                        'cp12_landlord' => 'CP12 Landlord',
                        'warning_notice' => 'Gas Warning Notice',
                        'installation_checklist' => 'Installation Checklist',
                        'gas_service_record' => 'Gas Service Record',
                        'minor_works' => 'Minor Works',
                        'disconnection' => 'Disconnection Notice',
                    ]),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('email')
                    ->label('Email to customer')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription('Send this certificate (with PDF if available) to the customer by email.')
                    ->action(function (Certificate $record) {
                        $record->loadMissing('customer');
                        $email = $record->customer?->email;

                        if (! $email) {
                            Notification::make()
                                ->title('No customer email on file')
                                ->danger()
                                ->send();
                            return;
                        }

                        Mail::to($email)->send(new CertificateMail($record, $email));
                        $record->update(['sent_at' => now()]);

                        Notification::make()
                            ->title("Certificate emailed to {$email}")
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
