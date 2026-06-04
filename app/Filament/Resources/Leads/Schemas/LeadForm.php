<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('postcode'),
                Select::make('service_type')
                    ->options([
                        'Gas Safety Certificate' => 'Gas Safety Certificate',
                        'Boiler Service' => 'Boiler Service',
                        'Boiler Install' => 'Boiler Install',
                        'Gas Repair' => 'Gas Repair',
                        'Other' => 'Other',
                    ]),
                Textarea::make('message')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'converted' => 'Converted',
                        'lost' => 'Lost',
                    ])
                    ->required()
                    ->default('new'),
                Select::make('source')
                    ->options([
                        'website' => 'Website',
                        'phone' => 'Phone',
                        'referral' => 'Referral',
                        'social' => 'Social Media',
                        'other' => 'Other',
                    ]),
            ]);
    }
}
