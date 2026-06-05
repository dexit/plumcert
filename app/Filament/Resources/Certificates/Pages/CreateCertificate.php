<?php

namespace App\Filament\Resources\Certificates\Pages;

use App\Filament\Resources\Certificates\CertificateResource;
use App\Filament\Resources\Certificates\Schemas\CertificateWizard;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Support\Str;

class CreateCertificate extends CreateRecord
{
    use HasWizard;

    protected static string $resource = CertificateResource::class;

    public function getSteps(): array
    {
        return CertificateWizard::steps();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate cert number if somehow blank
        if (empty($data['certificate_number'])) {
            $data['certificate_number'] = 'GS-' . strtoupper(Str::random(8));
        }

        // Default issued_at
        if (empty($data['issued_at'])) {
            $data['issued_at'] = now();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
