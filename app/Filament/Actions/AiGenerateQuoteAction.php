<?php
namespace App\Filament\Actions;

use App\Services\AiAssistant;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;

class AiGenerateQuoteAction
{
    public static function make(): Action
    {
        return Action::make('aiGenerateQuote')
            ->label('AI Generate Items')
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->form([
                Textarea::make('job_description')
                    ->label('Describe the job')
                    ->placeholder('e.g. Annual boiler service on Worcester Bosch 30i, plus replace faulty thermostat')
                    ->required()
                    ->rows(3),
                Select::make('job_type')
                    ->label('Job Type')
                    ->options([
                        'maintenance'  => 'Maintenance / Service',
                        'installation' => 'Installation',
                        'emergency'    => 'Emergency Call-Out',
                        'plumbing'     => 'Plumbing',
                        'heating'      => 'Heating',
                        'general'      => 'General',
                    ])
                    ->default('maintenance')
                    ->native(false),
            ])
            ->action(function (array $data, $record) {
                $ai = app(AiAssistant::class);
                $items = $ai->generateQuoteItems($data['job_description'], $data['job_type']);

                if (empty($items)) {
                    \Filament\Notifications\Notification::make()
                        ->title('AI unavailable')
                        ->body('Configure ANTHROPIC_API_KEY in .env to enable AI features.')
                        ->warning()
                        ->send();
                    return;
                }

                // Merge AI items into existing line_items on the quote
                $existing = $record->line_items ?? [];
                $record->update(['line_items' => array_merge($existing, $items)]);

                \Filament\Notifications\Notification::make()
                    ->title('AI generated ' . count($items) . ' line items')
                    ->success()
                    ->send();
            });
    }
}
