<?php

namespace App\Filament\Resources\Jobs\Pages;

use App\Filament\Resources\Certificates\CertificateResource;
use App\Filament\Resources\Jobs\JobResource;
use App\Models\Job;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewJob extends ViewRecord
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_cert')
                ->label('New Certificate')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->url(fn (Job $record) =>
                    CertificateResource::getUrl('create')
                    . '?' . http_build_query(['job_id' => $record->id])
                ),
            EditAction::make(),
        ];
    }
}
