<?php

namespace App\Filament\Pages;

use App\Models\Boiler;
use App\Models\Certificate;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class MaintenanceSchedule extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDateRange;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Maintenance Schedule';

    protected string $view = 'filament.pages.maintenance-schedule';

    public int $windowDays = 60;

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $horizon = now()->addDays($this->windowDays);

        $services = Boiler::whereNotNull('next_service_due')
            ->whereDate('next_service_due', '<=', $horizon)
            ->with('property.customer')
            ->orderBy('next_service_due')
            ->get()
            ->map(fn (Boiler $b) => [
                'customer' => trim(($b->property?->customer?->first_name ?? '') . ' ' . ($b->property?->customer?->last_name ?? '')) ?: '—',
                'address'  => $b->property?->address ?? '—',
                'item'     => trim("{$b->make} {$b->model}"),
                'due'      => Carbon::parse($b->next_service_due),
            ]);

        $certs = Certificate::whereNotNull('issued_at')
            ->whereIn('type', ['cp12_homeowner', 'cp12_landlord', 'gas_service_record'])
            ->with(['customer', 'property'])
            ->get()
            ->map(fn (Certificate $c) => [
                'customer' => trim(($c->customer?->first_name ?? '') . ' ' . ($c->customer?->last_name ?? '')) ?: '—',
                'address'  => $c->property?->address ?? '—',
                'item'     => $c->certificate_number,
                'due'      => Carbon::parse($c->issued_at)->addYear(),
            ])
            ->filter(fn ($c) => $c['due']->lte($horizon))
            ->sortBy('due')
            ->values();

        return [
            'services'        => $services,
            'certs'           => $certs,
            'overdueServices' => $services->filter(fn ($s) => $s['due']->isPast())->count(),
            'overdueCerts'    => $certs->filter(fn ($c) => $c['due']->isPast())->count(),
            'windowDays'      => $this->windowDays,
        ];
    }
}
