<?php
namespace App\Filament\Resources\Parts;

use App\Filament\Resources\Parts\Schemas\PartForm;
use App\Filament\Resources\Parts\Tables\PartsTable;
use App\Models\Part;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PartResource extends Resource
{
    protected static ?string $model = Part::class;
    protected static string|BackedEnum|null $navigationIcon  = Heroicon::OutlinedArchiveBox;
    protected static string|\UnitEnum|null $navigationGroup = 'Operations';
    protected static ?int    $navigationSort  = 5;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PartForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PartsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListParts::route('/'),
            'create' => Pages\CreatePart::route('/create'),
            'edit'   => Pages\EditPart::route('/{record}/edit'),
        ];
    }
}
