<?php

namespace App\Filament\Resources\Jobs\Schemas;

use App\Models\Boiler;
use App\Models\Job;
use App\Models\Property;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Schema;

class JobForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('type')
                    ->label('Job Type')
                    ->options(Job::TYPES)
                    ->required()
                    ->native(false)
                    ->live()
                    ->default('maintenance'),

                Select::make('status')
                    ->options([
                        'open'        => 'Open',
                        'scheduled'   => 'Scheduled',
                        'in_progress' => 'In Progress',
                        'completed'   => 'Completed',
                        'cancelled'   => 'Cancelled',
                    ])
                    ->required()
                    ->native(false)
                    ->default('open'),

                Select::make('customer_id')
                    ->label('Customer')
                    ->relationship('customer', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->first_name . ' ' . $record->last_name)
                    ->searchable()
                    ->preload()
                    ->default(fn () => request('customer_id') ? (int) request('customer_id') : null)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('property_id', null);
                    }),

                Select::make('property_id')
                    ->label('Property')
                    ->options(function (Get $get) {
                        $customerId = $get('customer_id');
                        if (! $customerId) {
                            return [];
                        }
                        return Property::where('customer_id', $customerId)
                            ->get()
                            ->pluck('address', 'id');
                    })
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::prefillBoilerData($get, $set);
                    }),

                Select::make('assigned_to_user_id')
                    ->label('Assigned To')
                    ->relationship('assignedTo', 'name')
                    ->searchable()
                    ->preload(),

                DateTimePicker::make('scheduled_at')->label('Scheduled At'),

                TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->columnSpanFull()
                    ->rows(2),

                DateTimePicker::make('completed_at')->label('Completed At'),

                ...JobTypeSections::all(),

                Textarea::make('notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    private static function prefillBoilerData(Get $get, Set $set): void
    {
        $propertyId = $get('property_id');
        $type       = $get('type');

        if (! $propertyId || ! in_array($type, ['installation', 'maintenance', 'heating'], true)) {
            return;
        }

        $boiler = Boiler::where('property_id', $propertyId)->latest()->first();

        if (! $boiler) {
            return;
        }

        $prefix = "form_data.{$type}";

        if ($type === 'installation') {
            $set("{$prefix}.appliance_make",  $boiler->make  ?? '');
            $set("{$prefix}.appliance_model", $boiler->model ?? '');
            $set("{$prefix}.serial",          $boiler->serial_number ?? '');
            $set("{$prefix}.gc_number",       $boiler->gc_number ?? '');
        } elseif ($type === 'maintenance') {
            $set("{$prefix}.appliance", trim(($boiler->make ?? '') . ' ' . ($boiler->model ?? '')));
        } elseif ($type === 'heating') {
            // no direct boiler prefill for heating system type
        }
    }
}
