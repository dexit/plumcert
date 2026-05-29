<?php

namespace App\Filament\Resources\Boilers\Pages;

use App\Filament\Resources\Boilers\BoilerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBoilers extends ListRecords
{
    protected static string $resource = BoilerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
