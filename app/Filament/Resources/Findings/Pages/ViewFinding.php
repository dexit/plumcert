<?php

namespace App\Filament\Resources\Findings\Pages;

use App\Filament\Resources\Findings\FindingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewFinding extends ViewRecord
{
    protected static string $resource = FindingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
