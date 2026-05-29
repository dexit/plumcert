<?php

namespace App\Filament\Resources\Boilers;

use App\Filament\Resources\Boilers\Pages\CreateBoiler;
use App\Filament\Resources\Boilers\Pages\EditBoiler;
use App\Filament\Resources\Boilers\Pages\ListBoilers;
use App\Filament\Resources\Boilers\Pages\ViewBoiler;
use App\Filament\Resources\Boilers\Schemas\BoilerForm;
use App\Filament\Resources\Boilers\Schemas\BoilerInfolist;
use App\Filament\Resources\Boilers\Tables\BoilersTable;
use App\Models\Boiler;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BoilerResource extends Resource
{
    protected static ?string $model = Boiler::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return BoilerForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BoilerInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BoilersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBoilers::route('/'),
            'create' => CreateBoiler::route('/create'),
            'view' => ViewBoiler::route('/{record}'),
            'edit' => EditBoiler::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
