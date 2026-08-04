<?php

namespace App\Filament\Widgets;

use App\Models\Job;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentJobs extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(Job::query()->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('customer.first_name'),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->dateTime(),
            ]);
    }
}
