<?php

namespace App\Filament\Resources\Findings;

use App\Filament\Resources\Findings\Pages\CreateFinding;
use App\Filament\Resources\Findings\Pages\EditFinding;
use App\Filament\Resources\Findings\Pages\ListFindings;
use App\Filament\Resources\Findings\Pages\ViewFinding;
use App\Filament\Resources\Findings\Schemas\FindingForm;
use App\Filament\Resources\Findings\Schemas\FindingInfolist;
use App\Filament\Resources\Findings\Tables\FindingsTable;
use App\Models\Finding;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FindingResource extends Resource
{
    protected static ?string $model = Finding::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return FindingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FindingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FindingsTable::configure($table);
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
            'index' => ListFindings::route('/'),
            'create' => CreateFinding::route('/create'),
            'view' => ViewFinding::route('/{record}'),
            'edit' => EditFinding::route('/{record}/edit'),
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
