<?php

namespace App\Filament\Resources\Jobs\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class JobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'first_name')
                    ->searchable()
                    ->preload()
                    ->default(fn () => request('customer_id') ? (int) request('customer_id') : null)
                    ->required(),
                Select::make('property_id')
                    ->relationship('property', 'address')
                    ->searchable()
                    ->preload(),
                Select::make('assigned_to_user_id')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'scheduled' => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('pending'),
                DateTimePicker::make('scheduled_at'),
                DateTimePicker::make('completed_at'),
            ]);
    }
}
