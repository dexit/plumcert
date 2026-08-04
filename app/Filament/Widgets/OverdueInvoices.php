<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueInvoices extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Invoice::whereNull('paid_at')->where('due_date', '<', today())->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number'),
                Tables\Columns\TextColumn::make('customer.first_name'),
                Tables\Columns\TextColumn::make('total')
                    ->money('GBP'),
                Tables\Columns\TextColumn::make('due_date')
                    ->date(),
            ]);
    }
}
