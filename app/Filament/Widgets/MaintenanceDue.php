<?php

namespace App\Filament\Widgets;

use App\Models\Boiler;
use App\Models\Reminder;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MaintenanceDue extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $servicesDue    = Boiler::whereDate('next_service_due', '<=', now()->addDays(30))->count();
        $servicesOverdue = Boiler::whereDate('next_service_due', '<', today())->count();
        $remindersToSend = Reminder::pending()->where('due_at', '<=', now()->addDays(7))->count();
        $myOpenTasks    = Task::open()->where('assigned_to_user_id', auth()->id())->count();
        $overdueTasksCount = Task::due()->where('assigned_to_user_id', auth()->id())->count();

        return [
            Stat::make('Services Due (30 days)', $servicesDue)
                ->description("({$servicesOverdue} already overdue)")
                ->descriptionColor($servicesOverdue > 0 ? 'danger' : 'success')
                ->color($servicesOverdue > 0 ? 'danger' : 'warning')
                ->icon('heroicon-o-wrench-screwdriver'),

            Stat::make('Reminders to Send', $remindersToSend)
                ->description('Due within 7 days')
                ->color($remindersToSend > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-bell'),

            Stat::make('My Open Tasks', $myOpenTasks)
                ->description("{$overdueTasksCount} overdue")
                ->descriptionColor($overdueTasksCount > 0 ? 'danger' : 'gray')
                ->color($overdueTasksCount > 0 ? 'danger' : 'primary')
                ->icon('heroicon-o-clipboard-document-check'),
        ];
    }
}
