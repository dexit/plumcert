<?php

namespace App\Filament\Resources\Jobs\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Components\Section;

/**
 * Pre-built standard forms per job category, with lookups, repeatable line
 * items and smart inputs. Each section appears only when its category is the
 * selected job type. Data is stored under form_data.<category>.
 */
class JobTypeSections
{
    /**
     * @return array<int, Section>
     */
    public static function all(): array
    {
        return [
            self::installation(),
            self::maintenance(),
            self::emergency(),
            self::heating(),
            self::plumbing(),
            self::custom(),
        ];
    }

    private static function visibleFor(string $type): \Closure
    {
        return fn (Get $get): bool => $get('type') === $type;
    }

    /** Shared materials/parts repeater used by several categories. */
    private static function materialsRepeater(string $statePath): Repeater
    {
        return Repeater::make($statePath)
            ->label('Materials / Parts Used')
            ->schema([
                TextInput::make('item')->required()->columnSpan(2),
                TextInput::make('qty')->numeric()->default(1)->minValue(0)->columnSpan(1),
                TextInput::make('unit_cost')->numeric()->prefix('£')->columnSpan(1),
            ])
            ->columns(4)
            ->addActionLabel('Add material')
            ->reorderable()
            ->collapsible()
            ->defaultItems(0);
    }

    private static function installation(): Section
    {
        return Section::make('Installation Details')
            ->visible(self::visibleFor('installation'))
            ->columns(2)
            ->schema([
                TextInput::make('form_data.installation.appliance_make')
                    ->label('Appliance Make')
                    ->datalist(['Worcester Bosch', 'Vaillant', 'Baxi', 'Ideal', 'Viessmann', 'Glow-worm', 'Potterton']),
                TextInput::make('form_data.installation.appliance_model')->label('Model'),
                TextInput::make('form_data.installation.gc_number')->label('GC Number')->placeholder('00-000-00'),
                TextInput::make('form_data.installation.serial')->label('Serial Number'),
                TextInput::make('form_data.installation.location')->label('Install Location')
                    ->datalist(['Kitchen', 'Utility', 'Airing Cupboard', 'Loft', 'Garage', 'Bathroom']),
                Select::make('form_data.installation.fuel')->label('Fuel')
                    ->options(['natural_gas' => 'Natural Gas', 'lpg' => 'LPG', 'oil' => 'Oil'])
                    ->native(false),
                self::materialsRepeater('form_data.installation.materials')->columnSpanFull(),
                Section::make('Commissioning Readings')
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('form_data.installation.gas_rate')->label('Gas Rate (m³/h)')->numeric(),
                        TextInput::make('form_data.installation.op_pressure')->label('Op. Pressure (mbar)')->numeric(),
                        TextInput::make('form_data.installation.flue_co')->label('Flue CO (ppm)')->numeric(),
                        TextInput::make('form_data.installation.co2_ratio')->label('CO/CO₂ ratio')->numeric(),
                    ]),
                Toggle::make('form_data.installation.benchmark_completed')->label('Benchmark checklist completed'),
                Toggle::make('form_data.installation.warranty_registered')->label('Manufacturer warranty registered'),
            ]);
    }

    private static function maintenance(): Section
    {
        return Section::make('Maintenance / Service')
            ->visible(self::visibleFor('maintenance'))
            ->columns(2)
            ->schema([
                TextInput::make('form_data.maintenance.appliance')->label('Appliance')
                    ->datalist(['Combi Boiler', 'System Boiler', 'Regular Boiler', 'Gas Fire', 'Water Heater', 'Cooker']),
                DatePicker::make('form_data.maintenance.next_service_due')->label('Next Service Due')
                    ->default(now()->addYear()),
                Repeater::make('form_data.maintenance.checklist')
                    ->label('Service Checklist')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('check')->label('Check')->required()->columnSpan(2),
                        Select::make('result')
                            ->options(['pass' => 'Pass', 'fail' => 'Fail', 'na' => 'N/A'])
                            ->default('pass')->native(false)->columnSpan(1),
                        TextInput::make('notes')->columnSpan(2),
                    ])
                    ->columns(5)
                    ->defaultItems(5)
                    ->default([
                        ['check' => 'Visual inspection of appliance', 'result' => 'pass'],
                        ['check' => 'Check operating pressure', 'result' => 'pass'],
                        ['check' => 'Flue flow & spillage test', 'result' => 'pass'],
                        ['check' => 'Combustion analysis', 'result' => 'pass'],
                        ['check' => 'Safety devices operation', 'result' => 'pass'],
                    ])
                    ->addActionLabel('Add check')
                    ->reorderable()
                    ->collapsible(),
                Section::make('Readings')
                    ->columns(4)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('form_data.maintenance.op_pressure')->label('Op. Pressure (bar)')->numeric(),
                        TextInput::make('form_data.maintenance.gas_rate')->label('Gas Rate (m³/h)')->numeric(),
                        TextInput::make('form_data.maintenance.co')->label('CO (ppm)')->numeric(),
                        TextInput::make('form_data.maintenance.co2')->label('CO₂ (%)')->numeric(),
                    ]),
            ]);
    }

    private static function emergency(): Section
    {
        return Section::make('Emergency Call-Out')
            ->visible(self::visibleFor('emergency'))
            ->columns(2)
            ->schema([
                Select::make('form_data.emergency.severity')->label('Severity')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'immediate' => 'Immediately Dangerous'])
                    ->native(false)->default('medium'),
                TextInput::make('form_data.emergency.arrived_at')->label('Arrived (time)')->placeholder('e.g. 14:25'),
                Textarea::make('form_data.emergency.fault_reported')->label('Fault Reported')->columnSpanFull()->rows(2),
                Textarea::make('form_data.emergency.diagnosis')->label('Diagnosis / Findings')->columnSpanFull()->rows(2),
                self::materialsRepeater('form_data.emergency.parts')->label('Parts Used')->columnSpanFull(),
                Toggle::make('form_data.emergency.made_safe')->label('Made safe / isolated'),
                Toggle::make('form_data.emergency.follow_up_required')->label('Follow-up visit required'),
                Toggle::make('form_data.emergency.warning_notice_issued')->label('Warning notice issued'),
            ]);
    }

    private static function heating(): Section
    {
        return Section::make('Heating System')
            ->visible(self::visibleFor('heating'))
            ->columns(2)
            ->schema([
                Select::make('form_data.heating.system_type')->label('System Type')
                    ->options(['combi' => 'Combi', 'system' => 'System', 'regular' => 'Regular / Heat-only'])
                    ->native(false),
                Select::make('form_data.heating.work_type')->label('Work Type')
                    ->options(['install' => 'Install', 'repair' => 'Repair', 'upgrade' => 'Upgrade', 'powerflush' => 'Power Flush'])
                    ->native(false),
                Repeater::make('form_data.heating.radiators')
                    ->label('Radiators')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('room')->required()->columnSpan(2),
                        TextInput::make('size')->placeholder('e.g. 600x1200')->columnSpan(1),
                        Select::make('valve')->options(['trv' => 'TRV', 'lockshield' => 'Lockshield', 'manual' => 'Manual'])
                            ->native(false)->columnSpan(1),
                        Toggle::make('balanced')->inline(false)->columnSpan(1),
                    ])
                    ->columns(5)
                    ->defaultItems(0)
                    ->addActionLabel('Add radiator')
                    ->reorderable()
                    ->collapsible(),
                TextInput::make('form_data.heating.flow_temp')->label('Flow Temp (°C)')->numeric(),
                TextInput::make('form_data.heating.return_temp')->label('Return Temp (°C)')->numeric(),
                Toggle::make('form_data.heating.inhibitor_added')->label('Inhibitor added'),
                Toggle::make('form_data.heating.system_balanced')->label('System balanced'),
            ]);
    }

    private static function plumbing(): Section
    {
        return Section::make('Plumbing Works')
            ->visible(self::visibleFor('plumbing'))
            ->columns(2)
            ->schema([
                Repeater::make('form_data.plumbing.work_items')
                    ->label('Work Items')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('type')
                            ->options(['leak' => 'Leak', 'install' => 'Install', 'repair' => 'Repair', 'replace' => 'Replace', 'unblock' => 'Unblock'])
                            ->required()->native(false)->columnSpan(1),
                        TextInput::make('location')->required()->columnSpan(1)
                            ->datalist(['Kitchen', 'Bathroom', 'En-suite', 'Utility', 'Outside Tap', 'Mains Stopcock']),
                        TextInput::make('description')->columnSpan(2),
                    ])
                    ->columns(4)
                    ->defaultItems(1)
                    ->addActionLabel('Add work item')
                    ->reorderable()
                    ->collapsible(),
                self::materialsRepeater('form_data.plumbing.materials')->columnSpanFull(),
                TextInput::make('form_data.plumbing.water_pressure')->label('Water Pressure (bar)')->numeric(),
                Toggle::make('form_data.plumbing.isolation_fitted')->label('Isolation valve fitted'),
                Toggle::make('form_data.plumbing.tested_no_leaks')->label('Tested — no leaks'),
            ]);
    }

    private static function custom(): Section
    {
        return Section::make('Custom Job')
            ->visible(self::visibleFor('custom'))
            ->description('Build your own field set for non-standard work.')
            ->schema([
                Repeater::make('form_data.custom.fields')
                    ->label('Custom Fields')
                    ->schema([
                        TextInput::make('label')->required()->columnSpan(1),
                        TextInput::make('value')->columnSpan(2),
                    ])
                    ->columns(3)
                    ->defaultItems(1)
                    ->addActionLabel('Add field')
                    ->reorderable(),
                Repeater::make('form_data.custom.checklist')
                    ->label('Checklist')
                    ->schema([
                        TextInput::make('task')->required()->columnSpan(3),
                        Toggle::make('done')->inline(false)->columnSpan(1),
                    ])
                    ->columns(4)
                    ->defaultItems(0)
                    ->addActionLabel('Add task'),
            ]);
    }
}
