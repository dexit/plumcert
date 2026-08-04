<?php

namespace App\Filament\Pages;

use App\Models\Job;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class JobCalendar extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Job Calendar';

    protected string $view = 'filament.pages.job-calendar';

    public int $year;

    public int $month;

    public function mount(): void
    {
        $this->year = (int) now()->year;
        $this->month = (int) now()->month;
    }

    public function previousMonth(): void
    {
        $cursor = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->year = $cursor->year;
        $this->month = $cursor->month;
    }

    public function nextMonth(): void
    {
        $cursor = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->year = $cursor->year;
        $this->month = $cursor->month;
    }

    public function goToday(): void
    {
        $this->year = (int) now()->year;
        $this->month = (int) now()->month;
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $first = Carbon::create($this->year, $this->month, 1)->startOfDay();
        $gridStart = $first->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $first->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $jobs = Job::query()
            ->whereNotNull('scheduled_at')
            ->whereBetween('scheduled_at', [$gridStart, $gridEnd])
            ->with('customer')
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy(fn (Job $job) => $job->scheduled_at->toDateString());

        $weeks = [];
        $week = [];

        foreach (CarbonPeriod::create($gridStart, $gridEnd) as $day) {
            $week[] = [
                'date'         => $day->copy(),
                'inMonth'      => $day->month === $this->month,
                'isToday'      => $day->isToday(),
                'jobs'         => $jobs->get($day->toDateString(), collect()),
            ];

            if (count($week) === 7) {
                $weeks[] = $week;
                $week = [];
            }
        }

        return [
            'weeks'      => $weeks,
            'monthLabel' => $first->format('F Y'),
            'weekdays'   => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        ];
    }
}
