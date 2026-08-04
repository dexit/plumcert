<?php

namespace App\Filament\Resources\Certificates\Pages;

use App\Filament\Resources\Certificates\CertificateResource;
use App\Models\Certificate;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCertificate extends ViewRecord
{
    protected static string $resource = CertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(fn (Certificate $record) => route('certificate.pdf', $record))
                ->openUrlInNewTab(),

            Action::make('send_email')
                ->label('Email to Customer')
                ->icon('heroicon-o-envelope')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Send Certificate by Email')
                ->modalDescription(fn (Certificate $record) => "Send PDF to: " . ($record->customer?->email ?? 'no email on file'))
                ->action(fn (Certificate $record) => \Illuminate\Support\Facades\Http::post(
                    url('/api/v1/certificates/' . $record->id . '/email'),
                    ['email' => $record->customer?->email]
                ))
                ->visible(fn (Certificate $record) => filled($record->customer?->email)),

            EditAction::make(),
        ];
    }
}
