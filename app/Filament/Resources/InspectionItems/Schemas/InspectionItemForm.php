<?php

namespace App\Filament\Resources\InspectionItems\Schemas;

use App\Models\InspectionItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InspectionItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // ── Common Fields ──────────────────────────────────────────
            Section::make('Inspection Details')
                ->schema([
                    Select::make('job_id')
                        ->label('Job')
                        ->relationship('job', 'title')
                        ->searchable()
                        ->preload()
                        ->default(fn () => request('job_id') ? (int) request('job_id') : null),

                    Select::make('category')
                        ->label('Category')
                        ->options(InspectionItem::CATEGORIES)
                        ->required()
                        ->live()
                        ->native(false),

                    TextInput::make('location')
                        ->label('Location'),

                    TextInput::make('make')
                        ->label('Make'),

                    TextInput::make('model')
                        ->label('Model'),

                    TextInput::make('serial')
                        ->label('Serial Number'),

                    TextInput::make('gc_number')
                        ->label('GC Number')
                        ->visible(fn (Get $get) => in_array($get('category'), ['gas_appliance', 'gas_boiler'])),

                    Select::make('result')
                        ->label('Result')
                        ->options([
                            'pass'   => 'Pass',
                            'fail'   => 'Fail',
                            'at_risk'=> 'At Risk',
                            'id'     => 'Immediately Dangerous',
                            'na'     => 'N/A',
                        ])
                        ->required()
                        ->default('na')
                        ->native(false),

                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // ── Gas Appliance ──────────────────────────────────────────
            Section::make('Gas Appliance Details')
                ->schema([
                    TextInput::make('data.appliance_type')
                        ->label('Appliance Type'),

                    Select::make('data.flue_type')
                        ->label('Flue Type')
                        ->options([
                            'open_flue'   => 'Open Flue',
                            'room_sealed' => 'Room Sealed',
                            'flueless'    => 'Flueless',
                        ])
                        ->native(false),

                    TextInput::make('data.operating_pressure')
                        ->label('Operating Pressure (mbar)')
                        ->numeric(),

                    TextInput::make('data.heat_input')
                        ->label('Heat Input (kW)')
                        ->numeric(),

                    TextInput::make('data.combustion_co')
                        ->label('Combustion CO (ppm)')
                        ->numeric(),

                    TextInput::make('data.combustion_co2')
                        ->label('Combustion CO₂ (%)')
                        ->numeric(),

                    TextInput::make('data.co_co2_ratio')
                        ->label('CO/CO₂ Ratio')
                        ->numeric(),

                    Select::make('data.safety_devices')
                        ->label('Safety Devices')
                        ->options(['pass' => 'Pass', 'fail' => 'Fail'])
                        ->native(false),

                    Select::make('data.ventilation')
                        ->label('Ventilation')
                        ->options(['adequate' => 'Adequate', 'inadequate' => 'Inadequate', 'na' => 'N/A'])
                        ->native(false),

                    Select::make('data.flue_flow_test')
                        ->label('Flue Flow Test')
                        ->options(['pass' => 'Pass', 'fail' => 'Fail', 'na' => 'N/A'])
                        ->native(false),

                    Select::make('data.spillage_test')
                        ->label('Spillage Test')
                        ->options(['pass' => 'Pass', 'fail' => 'Fail', 'na' => 'N/A'])
                        ->native(false),
                ])
                ->columns(3)
                ->visible(fn (Get $get) => $get('category') === 'gas_appliance'),

            // ── Gas Boiler ─────────────────────────────────────────────
            Section::make('Gas Boiler Details')
                ->schema([
                    Select::make('data.boiler_type')
                        ->label('Boiler Type')
                        ->options([
                            'combi'   => 'Combi',
                            'system'  => 'System',
                            'regular' => 'Regular',
                        ])
                        ->native(false),

                    Select::make('data.fuel')
                        ->label('Fuel')
                        ->options([
                            'natural_gas' => 'Natural Gas',
                            'lpg'         => 'LPG',
                        ])
                        ->native(false),

                    TextInput::make('data.operating_pressure')
                        ->label('Operating Pressure (mbar)')
                        ->numeric(),

                    TextInput::make('data.gas_rate')
                        ->label('Gas Rate (m³/hr)')
                        ->numeric(),

                    TextInput::make('data.flow_temp')
                        ->label('Flow Temperature (°C)')
                        ->numeric(),

                    TextInput::make('data.return_temp')
                        ->label('Return Temperature (°C)')
                        ->numeric(),

                    TextInput::make('data.inlet_pressure')
                        ->label('Inlet Pressure (mbar)')
                        ->numeric(),

                    TextInput::make('data.burner_pressure')
                        ->label('Burner Pressure (mbar)')
                        ->numeric(),

                    TextInput::make('data.combustion_ratio')
                        ->label('Combustion Ratio')
                        ->numeric(),

                    TextInput::make('data.co_ppm')
                        ->label('CO (ppm)')
                        ->numeric(),

                    TextInput::make('data.co2_percent')
                        ->label('CO₂ (%)')
                        ->numeric(),

                    Select::make('data.flue_integrity')
                        ->label('Flue Integrity')
                        ->options(['pass' => 'Pass', 'fail' => 'Fail'])
                        ->native(false),

                    Select::make('data.condensate_disposal')
                        ->label('Condensate Disposal')
                        ->options(['ok' => 'OK', 'fault' => 'Fault', 'na' => 'N/A'])
                        ->native(false),

                    Select::make('data.case_seal')
                        ->label('Case Seal')
                        ->options(['pass' => 'Pass', 'fail' => 'Fail'])
                        ->native(false),

                    Toggle::make('data.serviced')
                        ->label('Serviced'),
                ])
                ->columns(3)
                ->visible(fn (Get $get) => $get('category') === 'gas_boiler'),

            // ── Heater ─────────────────────────────────────────────────
            Section::make('Heater Details')
                ->schema([
                    Select::make('data.heater_type')
                        ->label('Heater Type')
                        ->options([
                            'gas_fire'    => 'Gas Fire',
                            'water_heater'=> 'Water Heater',
                            'warm_air'    => 'Warm Air',
                            'space_heater'=> 'Space Heater',
                        ])
                        ->native(false),

                    TextInput::make('data.location_detail')
                        ->label('Location Detail'),

                    TextInput::make('data.operating_pressure')
                        ->label('Operating Pressure (mbar)')
                        ->numeric(),

                    Select::make('data.flue_condition')
                        ->label('Flue Condition')
                        ->options(['good' => 'Good', 'poor' => 'Poor', 'na' => 'N/A'])
                        ->native(false),

                    Select::make('data.oxygen_depletion_device')
                        ->label('Oxygen Depletion Device')
                        ->options(['present' => 'Present', 'absent' => 'Absent', 'na' => 'N/A'])
                        ->native(false),

                    Toggle::make('data.guard_fitted')
                        ->label('Guard Fitted'),

                    TextInput::make('data.co_reading')
                        ->label('CO Reading (ppm)')
                        ->numeric(),
                ])
                ->columns(3)
                ->visible(fn (Get $get) => $get('category') === 'heater'),

            // ── Plumbing ───────────────────────────────────────────────
            Section::make('Plumbing Details')
                ->schema([
                    Select::make('data.system_type')
                        ->label('System Type')
                        ->options([
                            'mains'  => 'Mains',
                            'gravity'=> 'Gravity',
                            'combi'  => 'Combi',
                        ])
                        ->native(false),

                    TextInput::make('data.stopcock_location')
                        ->label('Stopcock Location'),

                    TextInput::make('data.water_pressure')
                        ->label('Water Pressure (bar)')
                        ->numeric(),

                    Toggle::make('data.visible_leaks')
                        ->label('Visible Leaks'),

                    Select::make('data.pipe_material')
                        ->label('Pipe Material')
                        ->options([
                            'copper'  => 'Copper',
                            'plastic' => 'Plastic',
                            'lead'    => 'Lead',
                            'steel'   => 'Steel',
                        ])
                        ->native(false),

                    TextInput::make('data.hot_water_temp')
                        ->label('Hot Water Temperature (°C)')
                        ->numeric(),

                    Select::make('data.expansion_vessel')
                        ->label('Expansion Vessel')
                        ->options(['ok' => 'OK', 'fault' => 'Fault', 'na' => 'N/A'])
                        ->native(false),

                    Textarea::make('data.notes_plumbing')
                        ->label('Plumbing Notes')
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->visible(fn (Get $get) => $get('category') === 'plumbing'),

            // ── Radiators ──────────────────────────────────────────────
            Section::make('Radiators')
                ->schema([
                    TextInput::make('data.radiator_count')
                        ->label('Radiator Count')
                        ->numeric(),

                    Toggle::make('data.trv_fitted')
                        ->label('TRV Fitted'),

                    Toggle::make('data.balanced')
                        ->label('Balanced'),

                    Toggle::make('data.cold_spots')
                        ->label('Cold Spots'),

                    Toggle::make('data.bled')
                        ->label('Bled'),

                    Select::make('data.inhibitor_present')
                        ->label('Inhibitor Present')
                        ->options(['yes' => 'Yes', 'no' => 'No', 'unknown' => 'Unknown'])
                        ->native(false),

                    TextInput::make('data.largest_room_kw')
                        ->label('Largest Room (kW)')
                        ->numeric(),
                ])
                ->columns(3)
                ->visible(fn (Get $get) => $get('category') === 'radiators'),

            // ── Carbon Monoxide Alarm ──────────────────────────────────
            Section::make('Carbon Monoxide Alarm')
                ->schema([
                    Toggle::make('data.alarm_present')
                        ->label('Alarm Present'),

                    TextInput::make('data.location_detail')
                        ->label('Location Detail'),

                    TextInput::make('data.make_model')
                        ->label('Make / Model'),

                    DatePicker::make('data.manufacture_date')
                        ->label('Manufacture Date'),

                    DatePicker::make('data.expiry_date')
                        ->label('Expiry Date'),

                    Select::make('data.test_button')
                        ->label('Test Button')
                        ->options(['pass' => 'Pass', 'fail' => 'Fail'])
                        ->native(false),

                    Select::make('data.battery_type')
                        ->label('Battery Type')
                        ->options(['sealed' => 'Sealed', 'replaceable' => 'Replaceable'])
                        ->native(false),

                    Select::make('data.compliant_bs')
                        ->label('BS EN 50291 Compliant')
                        ->options(['yes' => 'Yes', 'no' => 'No'])
                        ->native(false),
                ])
                ->columns(2)
                ->visible(fn (Get $get) => $get('category') === 'co_alarm'),

            // ── Smoke Alarm ────────────────────────────────────────────
            Section::make('Smoke Alarm')
                ->schema([
                    Toggle::make('data.alarm_present')
                        ->label('Alarm Present'),

                    TextInput::make('data.location_detail')
                        ->label('Location Detail'),

                    Select::make('data.alarm_type')
                        ->label('Alarm Type')
                        ->options([
                            'ionisation' => 'Ionisation',
                            'optical'    => 'Optical',
                            'heat'       => 'Heat',
                            'combined'   => 'Combined',
                        ])
                        ->native(false),

                    Select::make('data.power_source')
                        ->label('Power Source')
                        ->options([
                            'mains'        => 'Mains',
                            'battery'      => 'Battery',
                            'mains_battery'=> 'Mains + Battery',
                        ])
                        ->native(false),

                    DatePicker::make('data.manufacture_date')
                        ->label('Manufacture Date'),

                    DatePicker::make('data.expiry_date')
                        ->label('Expiry Date'),

                    Select::make('data.test_button')
                        ->label('Test Button')
                        ->options(['pass' => 'Pass', 'fail' => 'Fail'])
                        ->native(false),

                    Toggle::make('data.interlinked')
                        ->label('Interlinked'),
                ])
                ->columns(2)
                ->visible(fn (Get $get) => $get('category') === 'smoke_alarm'),
        ]);
    }
}
