<?php

namespace App\Filament\Resources\Properties\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BoilersRelationManager extends RelationManager
{
    protected static string $relationship = 'boilers';

    protected static ?string $title = 'Boilers';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('make')
                    ->searchable(),
                TextColumn::make('model')
                    ->searchable(),
                TextColumn::make('serial')
                    ->searchable(),
                TextColumn::make('gc_number'),
                TextColumn::make('install_date')
                    ->date(),
                TextColumn::make('last_service_date')
                    ->date(),
                TextColumn::make('next_service_due')
                    ->date()
                    ->color(fn ($state) => $state && $state < now()->toDateString() ? 'danger' : null),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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

    public static function getRelationshipName(): string
    {
        return 'boilers';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
