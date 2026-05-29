<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->relationship('company', 'name'),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('title'),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name'),
                TextInput::make('company_name'),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('postcode'),
                TextInput::make('town'),
                TextInput::make('county'),
                TextInput::make('tel')
                    ->tel(),
                TextInput::make('mobile'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('vat_number'),
                TextInput::make('type')
                    ->required()
                    ->default('residential'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
