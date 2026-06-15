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
            ->columns(2)
            ->components([
                Select::make('property_id')
                    ->relationship('property', 'id')
                    ->required(),
                TextInput::make('make')
                    ->required()
                    ->datalist(['Worcester Bosch', 'Vaillant', 'Baxi', 'Ideal', 'Viessmann', 'Glow-worm', 'Potterton', 'Ariston', 'Ferroli', 'Alpha']),
                TextInput::make('model')
                    ->required(),
                TextInput::make('serial')
                    ->label('Serial Number'),
                TextInput::make('gc_number')
                    ->label('GC Number')
                    ->placeholder('00-000-000')
                    ->helperText('Format: 00-000-000 (from Gas Safe Register)')
                    ->rule('regex:/^\d{2}-\d{3}-\d{3}$/')
                    ->validationMessages(['regex' => 'GC number must be in format 00-000-000']),
                Select::make('fuel_type')
                    ->label('Fuel Type')
                    ->options(['natural_gas' => 'Natural Gas', 'lpg' => 'LPG', 'oil' => 'Oil'])
                    ->native(false),
                DatePicker::make('install_date')->label('Install Date'),
                DatePicker::make('last_service_date')->label('Last Serviced'),
                DatePicker::make('next_service_due')->label('Next Service Due'),
            ]);
    }
}
