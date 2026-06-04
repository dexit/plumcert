<?php

namespace App\Filament\Resources\Certificates\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('job_id')
                    ->relationship('job', 'title')
                    ->searchable()
                    ->preload(),
                Select::make('customer_id')
                    ->relationship('customer', 'first_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('property_id')
                    ->relationship('property', 'address')
                    ->searchable()
                    ->preload(),
                Select::make('boiler_id')
                    ->relationship('boiler', 'make')
                    ->searchable()
                    ->preload(),
                Select::make('issued_by_user_id')
                    ->relationship('issuedBy', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('type')
                    ->options([
                        'cp12_homeowner' => 'CP12 Homeowner',
                        'cp12_landlord' => 'CP12 Landlord',
                        'warning_notice' => 'Gas Warning Notice',
                        'installation_checklist' => 'Installation Checklist',
                        'gas_service_record' => 'Gas Service Record',
                        'minor_works' => 'Minor Works',
                        'disconnection' => 'Disconnection Notice',
                    ])
                    ->required(),
                TextInput::make('certificate_number')
                    ->required(),
                DateTimePicker::make('issued_at'),
                DateTimePicker::make('sent_at'),
            ]);
    }
}
