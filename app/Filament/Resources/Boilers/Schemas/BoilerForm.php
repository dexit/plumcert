<?php

namespace App\Filament\Resources\Boilers\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BoilerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('property_id')
                    ->relationship('property', 'id')
                    ->required(),
                TextInput::make('make')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('serial'),
                TextInput::make('gc_number'),
                DatePicker::make('install_date'),
                DatePicker::make('last_service_date'),
                DatePicker::make('next_service_due'),
            ]);
    }
}
