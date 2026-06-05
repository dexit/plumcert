<?php

namespace App\Filament\Resources\Quotes\Pages;

use App\Filament\Resources\Quotes\QuoteResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuote extends ViewRecord
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('download_pdf')
                ->label('Download PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn () => route('quote.pdf', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}
