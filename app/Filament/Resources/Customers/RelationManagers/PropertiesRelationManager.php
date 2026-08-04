<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PropertiesRelationManager extends RelationManager
{
    protected static string $relationship = 'properties';

    protected static ?string $title = 'Properties';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('address')
                    ->searchable(),
                TextColumn::make('postcode')
                    ->searchable(),
                TextColumn::make('town'),
                TextColumn::make('county'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(),
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
                Textarea::make('address')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('postcode'),
                TextInput::make('town'),
                TextInput::make('county'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function getRelationshipName(): string
    {
        return 'properties';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
