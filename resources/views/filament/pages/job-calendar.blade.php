<x-filament-panels::page>
    @php
        $statusColors = [
            'scheduled'   => 'bg-info-100 text-info-700 dark:bg-info-500/20 dark:text-info-300',
            'in_progress' => 'bg-warning-100 text-warning-700 dark:bg-warning-500/20 dark:text-warning-300',
            'completed'   => 'bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-300',
            'cancelled'   => 'bg-gray-100 text-gray-500 line-through dark:bg-gray-700 dark:text-gray-400',
        ];
    @endphp

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold text-gray-950 dark:text-white">{{ $monthLabel }}</h2>
        <div class="flex items-center gap-2">
            <x-filament::button size="sm" color="gray" wire:click="previousMonth" icon="heroicon-m-chevron-left">
                Prev
            </x-filament::button>
            <x-filament::button size="sm" color="gray" wire:click="goToday">
                Today
            </x-filament::button>
            <x-filament::button size="sm" color="gray" wire:click="nextMonth" icon="heroicon-m-chevron-right" icon-position="after">
                Next
            </x-filament::button>
        </div>
    </div>

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">
        {{-- Weekday header --}}
        <div class="grid grid-cols-7 border-b border-gray-200 dark:border-white/10">
            @foreach($weekdays as $day)
                <div class="px-2 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400">{{ $day }}</div>
            @endforeach
        </div>

        {{-- Weeks --}}
        @foreach($weeks as $week)
            <div class="grid grid-cols-7">
                @foreach($week as $cell)
                    <div @class([
                        'min-h-28 border-b border-r border-gray-100 dark:border-white/5 p-1.5 align-top',
                        'bg-gray-50/60 dark:bg-white/5' => ! $cell['inMonth'],
                    ])>
                        <div class="flex items-center justify-between mb-1">
                            <span @class([
                                'inline-flex h-6 w-6 items-center justify-center rounded-full text-xs',
                                'font-bold bg-primary-600 text-white' => $cell['isToday'],
                                'text-gray-400 dark:text-gray-600' => ! $cell['inMonth'] && ! $cell['isToday'],
                                'text-gray-700 dark:text-gray-300' => $cell['inMonth'] && ! $cell['isToday'],
                            ])>
                                {{ $cell['date']->day }}
                            </span>
                        </div>

                        <div class="space-y-1">
                            @foreach($cell['jobs']->take(4) as $job)
                                <a href="{{ \App\Filament\Resources\Jobs\JobResource::getUrl('edit', ['record' => $job]) }}"
                                   class="block truncate rounded px-1.5 py-0.5 text-xs {{ $statusColors[$job->status] ?? 'bg-gray-100 text-gray-700' }}"
                                   title="{{ $job->scheduled_at->format('H:i') }} — {{ $job->title }}">
                                    <span class="font-medium">{{ $job->scheduled_at->format('H:i') }}</span>
                                    {{ \Illuminate\Support\Str::limit($job->title, 22) }}
                                </a>
                            @endforeach

                            @if($cell['jobs']->count() > 4)
                                <span class="block px-1.5 text-xs text-gray-400">+{{ $cell['jobs']->count() - 4 }} more</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
