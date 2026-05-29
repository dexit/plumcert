<?php

namespace App\Filament\Resources\Boilers\Pages;

use App\Filament\Resources\Boilers\BoilerResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewBoiler extends ViewRecord
{
    protected static string $resource = BoilerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
