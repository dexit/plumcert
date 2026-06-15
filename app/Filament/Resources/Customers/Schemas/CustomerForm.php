<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Filament\Forms\AddressLookup;
use App\Models\Customer;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category')
                    ->label('Customer Type')
                    ->options(Customer::CATEGORIES)
                    ->required()
                    ->default('homeowner')
                    ->live()
                    ->native(false),
                Select::make('business_type')
                    ->label('Business Type')
                    ->options(Customer::BUSINESS_TYPES)
                    ->visible(fn (Get $get) => $get('category') === 'commercial'),
                TextInput::make('contact_name')
                    ->label('Primary Contact Name')
                    ->visible(fn (Get $get) => in_array($get('category'), ['commercial', 'landlord'])),
                Select::make('company_id')
                    ->relationship('company', 'name'),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('title'),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name'),
                TextInput::make('company_name')
                    ->visible(fn (Get $get) => $get('category') === 'commercial'),
                AddressLookup::make('address'),
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
                TextInput::make('vat_number')
                    ->visible(fn (Get $get) => $get('category') === 'commercial'),
                TextInput::make('type')
                    ->required()
                    ->default('residential'),
                Select::make('preferred_channel')
                    ->label('Preferred Contact Channel')
                    ->options(['email' => 'Email', 'sms' => 'SMS', 'whatsapp' => 'WhatsApp'])
                    ->default('email')
                    ->native(false),
                Toggle::make('marketing_opt_in')
                    ->label('Marketing opt-in'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
