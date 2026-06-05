<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CertificatesRelationManager extends RelationManager
{
    protected static string $relationship = 'certificates';

    protected static ?string $title = 'Certificates';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('certificate_number')
                    ->searchable(),
                BadgeColumn::make('type'),
                TextColumn::make('issued_at')
                    ->dateTime()
                    ->sortable(),
                BooleanColumn::make('signed_by_engineer'),
                BooleanColumn::make('signed_by_customer'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options([
                        'cp12_homeowner' => 'CP12 Homeowner',
                        'cp12_landlord' => 'CP12 Landlord',
                        'warning_notice' => 'Warning Notice',
                        'installation_checklist' => 'Installation Checklist',
                        'gas_service_record' => 'Gas Service Record',
                        'minor_works' => 'Minor Works',
                        'disconnection' => 'Disconnection',
                    ]),
                TextInput::make('certificate_number')
                    ->required(),
                DateTimePicker::make('issued_at'),
            ]);
    }

    public static function getRelationshipName(): string
    {
        return 'certificates';
    }
}
