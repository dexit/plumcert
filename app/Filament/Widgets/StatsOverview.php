<?php

namespace App\Filament\Widgets;

use App\Models\Certificate;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Job;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Customer::count()),
            Stat::make('Open Jobs', Job::whereNotIn('status', ['completed', 'cancelled'])->count()),
            Stat::make('Certificates This Month', Certificate::whereMonth('created_at', now()->month)->count()),
            Stat::make('Unpaid Invoices', Invoice::whereNull('paid_at')->count()),
        ];
    }
}
