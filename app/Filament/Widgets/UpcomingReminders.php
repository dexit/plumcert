<?php

namespace App\Filament\Widgets;

use App\Models\Reminder;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingReminders extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Reminder::whereNull('sent_at')->orderBy('due_at')->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('customer.first_name'),
                Tables\Columns\TextColumn::make('due_at')
                    ->dateTime(),
            ]);
    }
}
