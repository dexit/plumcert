<?php

namespace App\Filament\Resources\InspectionItems\Pages;

use App\Filament\Resources\InspectionItems\InspectionItemResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInspectionItem extends ViewRecord
{
    protected static string $resource = InspectionItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
