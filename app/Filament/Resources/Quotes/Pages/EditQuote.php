<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Actions\AiGenerateQuoteAction;
use App\Filament\Resources\Quotes\QuoteResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditQuote extends EditRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            AiGenerateQuoteAction::make(),
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
