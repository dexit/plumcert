<?php

namespace App\Filament\Resources\EmailTemplates\Schemas;

use App\Models\EmailTemplate;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class EmailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Template')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('key')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Unique system key. Do not change once in use (e.g. service_reminder).')
                            ->disabled(fn (?EmailTemplate $record) => $record !== null)
                            ->dehydrated(),

                        TextInput::make('subject')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->helperText('You may use {{ variable }} placeholders here too.'),

                        TextInput::make('from_name')
                            ->label('From name')
                            ->maxLength(255),

                        Toggle::make('active')
                            ->default(true)
                            ->inline(false),

                        Textarea::make('description')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Section::make('Email Body')
                    ->schema([
                        RichEditor::make('body_html')
                            ->label('')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Available Variables')
                    ->description('Insert any of these into the subject or body using {{ variable }} syntax.')
                    ->collapsible()
                    ->schema([
                        Text::make(new HtmlString(self::variableList()))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    private static function variableList(): string
    {
        $rows = collect(EmailTemplate::VARIABLES)
            ->map(fn ($desc, $key) => "<tr><td style=\"padding:2px 16px 2px 0;font-family:monospace;color:#b45309\">{{ {$key} }}</td><td style=\"padding:2px 0;color:#555\">{$desc}</td></tr>")
            ->implode('');

        return "<table style=\"font-size:13px\">{$rows}</table>";
    }
}
