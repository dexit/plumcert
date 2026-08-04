<?php
namespace App\Filament\Resources\Parts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PartForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->required()->columnSpanFull(),
            TextInput::make('sku')->label('SKU / Part Number')->unique(ignoreRecord: true),
            Select::make('category')->options([
                'boiler_parts' => 'Boiler Parts',
                'pipework'     => 'Pipework',
                'fittings'     => 'Fittings',
                'tools'        => 'Tools',
                'consumables'  => 'Consumables',
            ])->native(false),
            TextInput::make('supplier'),
            Select::make('unit')->options(['each'=>'Each','metre'=>'Metre','litre'=>'Litre','box'=>'Box','pair'=>'Pair'])->native(false)->default('each'),
            TextInput::make('unit_cost')->label('Cost Price (£)')->numeric()->prefix('£')->default(0),
            TextInput::make('sell_price')->label('Sell Price (£)')->numeric()->prefix('£')->default(0),
            TextInput::make('stock_quantity')->label('Current Stock')->numeric()->default(0)->integer(),
            TextInput::make('min_stock_level')->label('Min Stock Level')->numeric()->default(1)->integer(),
            Textarea::make('notes')->columnSpanFull()->rows(2),
        ]);
    }
}
