<?php

namespace App\Filament\Resources\Certificates\Schemas;

use App\Models\Boiler;
use App\Models\Customer;
use App\Models\InspectionItem;
use App\Models\Job;
use App\Models\Property;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CertificateWizard
{
    public static function steps(): array
    {
        return [
            self::stepTypeAndLookup(),
            self::stepInstallerAndSite(),
            self::stepCertDetails(),
            self::stepSignAndIssue(),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Wizard::make(self::steps())->columnSpanFull(),
        ]);
    }

    // ─────────────────────────────────────────
    // Step 1 — Certificate Type & Record Links
    // ─────────────────────────────────────────
    private static function stepTypeAndLookup(): Step
    {
        return Step::make('Type & Customer')
            ->description('Choose certificate type and link to customer/property')
            ->schema([
                Select::make('type')
                    ->label('Certificate Type')
                    ->options([
                        'cp12_homeowner'        => 'CP12 — Homeowner Gas Safety Record',
                        'cp12_landlord'         => 'CP12 — Landlord Gas Safety Record',
                        'warning_notice'        => 'Gas Warning Notice',
                        'installation_checklist'=> 'Installation / Commissioning Checklist',
                        'gas_service_record'    => 'Gas Service Record',
                        'minor_works'           => 'Minor Works Certificate',
                        'disconnection'         => 'Disconnection Notice',
                    ])
                    ->required()
                    ->live()
                    ->native(false)
                    ->columnSpanFull(),

                Select::make('job_id')
                    ->label('Link to Job (optional)')
                    ->relationship('job', 'title')
                    ->searchable()
                    ->preload()
                    ->default(fn () => request('job_id') ? (int) request('job_id') : null)
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set) {
                        if (! $state) return;
                        $job = Job::with(['customer', 'property', 'property.boilers'])->find($state);
                        if (! $job) return;
                        if ($job->customer_id) $set('customer_id', $job->customer_id);
                        if ($job->property_id) $set('property_id', $job->property_id);
                        if ($job->customer) {
                            $set('form_data.client.name', trim($job->customer->first_name . ' ' . $job->customer->last_name));
                            $set('form_data.client.tel', $job->customer->tel ?? $job->customer->mobile ?? '');
                            $set('form_data.client.email', $job->customer->email ?? '');
                        }
                        if ($job->property) {
                            $set('form_data.jobAddress.line1', $job->property->address ?? '');
                            $set('form_data.jobAddress.postcode', $job->property->postcode ?? '');
                            $set('form_data.jobAddress.city', $job->property->town ?? '');
                            $boiler = $job->property->boilers->first();
                            if ($boiler) {
                                $set('boiler_id', $boiler->id);
                                $set('form_data.appliances.0.make', $boiler->make ?? '');
                                $set('form_data.appliances.0.model', $boiler->model ?? '');
                            }
                        }

                        // Pull inspection items logged for this job into the appliances repeater
                        $items = InspectionItem::where('job_id', $state)
                            ->whereIn('category', ['gas_boiler', 'gas_appliance', 'heater'])
                            ->get();

                        if ($items->isNotEmpty()) {
                            $appliances = $items->values()->map(fn (InspectionItem $item, int $idx) => [
                                'make'          => $item->make ?? '',
                                'model'         => $item->model ?? '',
                                'serial'        => $item->serial ?? '',
                                'gc_number'     => $item->gc_number ?? '',
                                'location'      => $item->location ?? '',
                                'flue_type'     => $item->data['flue_type'] ?? '',
                                'op_pressure'   => (string) ($item->data['op_pressure_bar'] ?? ''),
                                'gas_rate'      => (string) ($item->data['gas_rate_m3h'] ?? ''),
                                'co_reading'    => (string) ($item->data['co_reading_ppm'] ?? ''),
                                'co2_percent'   => (string) ($item->data['co2_percent'] ?? ''),
                                'result'        => $item->result ?? 'pass',
                                'notes'         => $item->notes ?? '',
                            ])->all();

                            $set('form_data.appliances', $appliances);

                            // Mark safety checks based on CO/smoke alarm inspection items
                            $safetyChecks = [];
                            $hasCoAlarm = InspectionItem::where('job_id', $state)->where('category', 'co_alarm')->where('result', 'pass')->exists();
                            $hasSmokeAlarm = InspectionItem::where('job_id', $state)->where('category', 'smoke_alarm')->where('result', 'pass')->exists();
                            if ($hasCoAlarm) $safetyChecks[] = 'co_alarm_present';
                            if ($hasSmokeAlarm) $safetyChecks[] = 'smoke_alarm_present';
                            if (! empty($safetyChecks)) {
                                $set('form_data.safetyChecks', $safetyChecks);
                            }
                        }
                    })
                    ->columnSpanFull(),

                Select::make('customer_id')
                    ->label('Customer')
                    ->options(fn () => Customer::orderBy('first_name')->get()
                        ->mapWithKeys(fn ($c) => [$c->id => trim($c->first_name . ' ' . $c->last_name) . ($c->postcode ? " ({$c->postcode})" : '')]))
                    ->searchable()
                    ->required()
                    ->default(fn () => request('customer_id') ? (int) request('customer_id') : null)
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set) {
                        $set('property_id', null);
                        $set('boiler_id', null);
                        if (! $state) return;
                        $c = Customer::find($state);
                        if (! $c) return;
                        $set('form_data.client.name', trim($c->first_name . ' ' . $c->last_name));
                        $set('form_data.client.tel', $c->tel ?? $c->mobile ?? '');
                        $set('form_data.client.email', $c->email ?? '');
                    }),

                Select::make('property_id')
                    ->label('Property / Job Address')
                    ->options(fn (Get $get) => Property::where('customer_id', $get('customer_id') ?? 0)
                        ->get()->mapWithKeys(fn ($p) => [$p->id => $p->address . ($p->postcode ? ", {$p->postcode}" : '')]))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set) {
                        $set('boiler_id', null);
                        if (! $state) return;
                        $p = Property::find($state);
                        if (! $p) return;
                        $set('form_data.jobAddress.line1', $p->address ?? '');
                        $set('form_data.jobAddress.postcode', $p->postcode ?? '');
                        $set('form_data.jobAddress.city', $p->town ?? '');
                    }),

                Select::make('boiler_id')
                    ->label('Boiler / Appliance')
                    ->options(fn (Get $get) => Boiler::where('property_id', $get('property_id') ?? 0)
                        ->get()->mapWithKeys(fn ($b) => [$b->id => "{$b->make} {$b->model}" . ($b->serial ? " (S/N: {$b->serial})" : '')]))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set) {
                        if (! $state) return;
                        $b = Boiler::find($state);
                        if (! $b) return;
                        $set('form_data.appliances.0.make', $b->make ?? '');
                        $set('form_data.appliances.0.model', $b->model ?? '');
                        $set('form_data.appliances.0.type', 'Boiler');
                    }),

                Select::make('issued_by_user_id')
                    ->label('Attending Engineer')
                    ->relationship('issuedBy', 'name')
                    ->searchable()
                    ->preload()
                    ->default(fn () => Auth::id())
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set) {
                        if (! $state) return;
                        $u = User::with('company')->find($state);
                        if (! $u) return;
                        $set('form_data.installer.name', $u->name ?? '');
                        $set('form_data.installer.tel', $u->phone ?? '');
                        $set('form_data.installer.email', $u->email ?? '');
                        $set('form_data.installer.license', $u->gas_safe_id_card ?? $u->company?->gas_safe_registration ?? '');
                    }),
            ])
            ->columns(2);
    }

    // ─────────────────────────────────────────
    // Step 2 — Installer, Client & Site Details
    // ─────────────────────────────────────────
    private static function stepInstallerAndSite(): Step
    {
        return Step::make('Site & People')
            ->description('Confirm engineer, client and job address details')
            ->schema([
                Fieldset::make('Engineer / Installer')
                    ->schema([
                        TextInput::make('form_data.installer.name')
                            ->label('Engineer Name')
                            ->required(),
                        TextInput::make('form_data.installer.license')
                            ->label('Gas Safe Reg. No.'),
                        TextInput::make('form_data.installer.tel')
                            ->label('Engineer Tel')
                            ->tel(),
                        TextInput::make('form_data.installer.email')
                            ->label('Engineer Email')
                            ->email(),
                    ])
                    ->columns(2),

                Fieldset::make('Client')
                    ->schema([
                        TextInput::make('form_data.client.name')
                            ->label('Client Name'),
                        TextInput::make('form_data.client.tel')
                            ->label('Client Tel')
                            ->tel(),
                        TextInput::make('form_data.client.email')
                            ->label('Client Email')
                            ->email(),
                    ])
                    ->columns(3),

                Fieldset::make('Job Address')
                    ->schema([
                        TextInput::make('form_data.jobAddress.line1')
                            ->label('Address')
                            ->columnSpan(2),
                        TextInput::make('form_data.jobAddress.line2')
                            ->label('Address Line 2'),
                        TextInput::make('form_data.jobAddress.city')
                            ->label('Town / City'),
                        TextInput::make('form_data.jobAddress.postcode')
                            ->label('Postcode')
                            ->extraAttributes(['style' => 'text-transform:uppercase']),
                    ])
                    ->columns(3),
            ]);
    }

    // ─────────────────────────────────────────
    // Step 3 — Certificate-Specific Details
    // ─────────────────────────────────────────
    private static function stepCertDetails(): Step
    {
        return Step::make('Certificate Details')
            ->description('Fill in all inspection readings and findings')
            ->schema([

                // ── CP12 / Gas Service — Appliances Table ──
                Section::make('Appliances Inspected')
                    ->description('Add up to 6 appliances. Boiler pre-filled from selected record.')
                    ->schema([
                        Repeater::make('form_data.appliances')
                            ->label('')
                            ->schema([
                                TextInput::make('location')->label('Location'),
                                TextInput::make('type')->label('Type'),
                                TextInput::make('make')->label('Make'),
                                TextInput::make('model')->label('Model'),
                                TextInput::make('flue')->label('Flue'),
                                Select::make('inspected')->label('Inspected')
                                    ->options(['Y' => 'Yes', 'N' => 'No', 'NA' => 'N/A'])->native(false),
                                TextInput::make('opPressure')->label('Op. Press.'),
                                TextInput::make('heatInput')->label('Heat Input'),
                                TextInput::make('hcCo')->label('HC CO'),
                                TextInput::make('hcCo2')->label('HC CO₂'),
                                TextInput::make('lcCo')->label('LC CO'),
                                TextInput::make('lcCo2')->label('LC CO₂'),
                                Select::make('safe')->label('Safe?')
                                    ->options(['Y' => 'Yes', 'N' => 'No', 'NA' => 'N/A'])->native(false),
                                Select::make('vent')->label('Ventilation')
                                    ->options(['Y' => 'Pass', 'N' => 'Fail', 'NA' => 'N/A'])->native(false),
                                Select::make('fluVis')->label('Flue Visual')
                                    ->options(['Y' => 'Pass', 'N' => 'Fail', 'NA' => 'N/A'])->native(false),
                                Select::make('fluPerf')->label('Flue Perf.')
                                    ->options(['Y' => 'Pass', 'N' => 'Fail', 'NA' => 'N/A'])->native(false),
                                Select::make('srv')->label('Serviced')
                                    ->options(['Y' => 'Yes', 'N' => 'No', 'NA' => 'N/A'])->native(false),
                            ])
                            ->columns(4)
                            ->maxItems(6)
                            ->minItems(0)
                            ->reorderable(false)
                            ->addActionLabel('+ Add Appliance')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => in_array($get('type'), [
                        'cp12_homeowner', 'cp12_landlord', 'gas_service_record',
                    ]))
                    ->collapsible(),

                // ── CP12 — Defects ──
                Section::make('Defects Found')
                    ->schema([
                        Repeater::make('form_data.defects')
                            ->label('')
                            ->schema([
                                TextInput::make('appliance')->label('Appliance'),
                                TextInput::make('defect')->label('Defect')->columnSpan(2),
                                Select::make('classification')->label('Classification')
                                    ->options([
                                        'ID1' => 'ID1 — Immediately Dangerous',
                                        'ID2' => 'ID2 — At Risk',
                                        'AR'  => 'AR — Advisory',
                                    ])->native(false),
                                TextInput::make('action')->label('Action Required')->columnSpan(2),
                            ])
                            ->columns(3)
                            ->maxItems(6)
                            ->addActionLabel('+ Add Defect')
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => in_array($get('type'), ['cp12_homeowner', 'cp12_landlord']))
                    ->collapsible(),

                // ── CP12 / Gas Service — Safety Checks & Comments ──
                Section::make('Safety Checks & Comments')
                    ->schema([
                        CheckboxList::make('form_data.safetyChecks')
                            ->label('Safety Checks Carried Out')
                            ->options([
                                'pipework'    => 'Pipework soundness',
                                'burner'      => 'Burner condition',
                                'controls'    => 'Gas controls',
                                'flue'        => 'Flue integrity',
                                'ventilation' => 'Ventilation adequate',
                                'emergency'   => 'Emergency controls',
                                'pressure'    => 'Working pressure correct',
                            ])
                            ->columns(2)
                            ->columnSpanFull(),

                        Textarea::make('form_data.comments')
                            ->label('General Comments')
                            ->rows(3)
                            ->columnSpanFull(),

                        TextInput::make('form_data.alarmStatus')
                            ->label('CO / Smoke Alarm Status')
                            ->placeholder('e.g. CO alarm present and tested')
                            ->visible(fn (Get $get) => in_array($get('type'), ['cp12_homeowner', 'cp12_landlord'])),
                    ])
                    ->visible(fn (Get $get) => in_array($get('type'), [
                        'cp12_homeowner', 'cp12_landlord', 'gas_service_record',
                    ]))
                    ->collapsible()
                    ->columns(2),

                // ── Warning Notice ──
                Section::make('Gas Warning Notice Details')
                    ->schema([
                        Select::make('form_data.hazardClassification')
                            ->label('Hazard Classification')
                            ->options([
                                'ID1' => 'Immediately Dangerous (ID1) — Do not use',
                                'ID2' => 'At Risk (ID2) — Strongly advised not to use',
                            ])
                            ->native(false)
                            ->required(fn (Get $get) => $get('type') === 'warning_notice'),
                        Select::make('form_data.supplyIsolated')
                            ->label('Supply Isolated?')
                            ->options(['yes' => 'Yes — gas turned off', 'no' => 'No — customer advised'])
                            ->native(false),
                        Textarea::make('form_data.hazardDescription')
                            ->label('Hazard Description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('form_data.actionTaken')
                            ->label('Action Taken')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => $get('type') === 'warning_notice')
                    ->columns(2),

                // ── Gas Service Record ──
                Section::make('Service Record Details')
                    ->schema([
                        Repeater::make('form_data.serviceItems')
                            ->label('Service Items Checked')
                            ->schema([
                                TextInput::make('item')->label('Item')->columnSpan(2),
                                Select::make('result')->label('Result')
                                    ->options(['pass' => 'Pass', 'fail' => 'Fail', 'na' => 'N/A'])->native(false),
                                TextInput::make('notes')->label('Notes')->columnSpan(2),
                            ])
                            ->columns(3)
                            ->addActionLabel('+ Add Item')
                            ->reorderable(false)
                            ->columnSpanFull(),
                        TextInput::make('form_data.gasRate')->label('Gas Rate (m³/hr)'),
                        TextInput::make('form_data.workingPressure')->label('Working Pressure (mbar)'),
                        DatePicker::make('form_data.nextServiceDue')->label('Next Service Due'),
                    ])
                    ->visible(fn (Get $get) => $get('type') === 'gas_service_record')
                    ->collapsible()
                    ->columns(2),

                // ── Minor Works ──
                Section::make('Minor Works Details')
                    ->schema([
                        Textarea::make('form_data.worksDescription')
                            ->label('Works Carried Out')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('form_data.pressureTestResult')->label('Pressure Test (mbar)'),
                        TextInput::make('form_data.tightnessTest')->label('Tightness Test Result'),
                        Select::make('form_data.suitableForUse')
                            ->label('Suitable For Use?')
                            ->options(['yes' => 'Yes', 'no' => 'No — see notes'])
                            ->native(false),
                    ])
                    ->visible(fn (Get $get) => $get('type') === 'minor_works')
                    ->columns(2),

                // ── Installation / Commissioning Checklist ──
                Section::make('Commissioning Checklist')
                    ->schema([
                        CheckboxList::make('form_data.commissioningItems')
                            ->label('Items Completed')
                            ->options([
                                'visual'       => 'Visual inspection complete',
                                'gas_supply'   => 'Gas supply adequate',
                                'tightness'    => 'Tightness test passed',
                                'ventilation'  => 'Ventilation checked & adequate',
                                'flue'         => 'Flue checked & clear',
                                'controls'     => 'Controls set correctly',
                                'instructed'   => 'User instructed on operation',
                                'documentation'=> 'Documentation left with user',
                                'gas_rate'     => 'Gas rate checked',
                                'pressure'     => 'Working pressure verified',
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                        Textarea::make('form_data.installationNotes')
                            ->label('Installation Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (Get $get) => $get('type') === 'installation_checklist'),

                // ── Disconnection Notice ──
                Section::make('Disconnection Details')
                    ->schema([
                        Textarea::make('form_data.disconnectionReason')
                            ->label('Reason for Disconnection')
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('form_data.locationOfIsolation')
                            ->label('Location of Isolation Point'),
                        TextInput::make('form_data.sealingMethod')
                            ->label('Method of Sealing'),
                        Select::make('form_data.customerInformed')
                            ->label('Customer Informed?')
                            ->options(['yes' => 'Yes', 'no' => 'No — left notice'])
                            ->native(false),
                    ])
                    ->visible(fn (Get $get) => $get('type') === 'disconnection')
                    ->columns(2),
            ]);
    }

    // ─────────────────────────────────────────
    // Step 4 — Sign & Issue
    // ─────────────────────────────────────────
    private static function stepSignAndIssue(): Step
    {
        return Step::make('Sign & Issue')
            ->description('Set certificate number, date and acknowledgement')
            ->schema([
                TextInput::make('certificate_number')
                    ->label('Certificate Number')
                    ->default(fn () => 'GS-' . strtoupper(Str::random(8)))
                    ->required()
                    ->maxLength(32)
                    ->unique('certificates', 'certificate_number', ignoreRecord: true)
                    ->helperText('Auto-generated — edit if needed'),

                DateTimePicker::make('issued_at')
                    ->label('Date / Time Issued')
                    ->default(now())
                    ->required(),

                TextInput::make('form_data.issuedBy')
                    ->label('Engineer Printed Name'),

                TextInput::make('form_data.receivedBy')
                    ->label('Customer Acknowledgement (Print Name)'),

                Toggle::make('signed_by_engineer')
                    ->label('Engineer has signed the certificate')
                    ->default(false),

                Toggle::make('signed_by_customer')
                    ->label('Customer has acknowledged receipt')
                    ->default(false),

                Placeholder::make('summary')
                    ->label('Summary')
                    ->content(fn (Get $get): string => collect([
                        'Type: ' . (self::typeLabel($get('type') ?? '')),
                        'Cert No: ' . ($get('certificate_number') ?? '—'),
                        'Date: ' . ($get('issued_at') ?? now()->format('d/m/Y H:i')),
                    ])->implode(' · '))
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    private static function typeLabel(string $type): string
    {
        return match ($type) {
            'cp12_homeowner'         => 'CP12 Homeowner',
            'cp12_landlord'          => 'CP12 Landlord',
            'warning_notice'         => 'Gas Warning Notice',
            'installation_checklist' => 'Installation Checklist',
            'gas_service_record'     => 'Gas Service Record',
            'minor_works'            => 'Minor Works',
            'disconnection'          => 'Disconnection Notice',
            default                  => $type,
        };
    }
}
