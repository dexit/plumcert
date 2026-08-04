<x-filament-panels::page>
    <div class="space-y-6">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Upcoming appliance services and certificate renewals due within the next {{ $windowDays }} days.
        </p>

        {{-- Boiler / appliance services --}}
        <x-filament::section>
            <x-slot name="heading">
                Appliance Services Due
                @if($overdueServices > 0)
                    <span class="ml-2 text-sm font-medium text-danger-600">({{ $overdueServices }} overdue)</span>
                @endif
            </x-slot>

            @if($services->isEmpty())
                <p class="text-sm text-gray-500">No services due in this window.</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 pr-4">Customer</th>
                            <th class="py-2 pr-4">Property</th>
                            <th class="py-2 pr-4">Appliance</th>
                            <th class="py-2">Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $row)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-2 pr-4">{{ $row['customer'] }}</td>
                                <td class="py-2 pr-4">{{ $row['address'] }}</td>
                                <td class="py-2 pr-4">{{ $row['item'] }}</td>
                                <td class="py-2 @if($row['due']->isPast()) text-danger-600 font-medium @endif">
                                    {{ $row['due']->format('d M Y') }}
                                    @if($row['due']->isPast()) (overdue) @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-filament::section>

        {{-- Certificate renewals --}}
        <x-filament::section>
            <x-slot name="heading">
                Certificate Renewals Due
                @if($overdueCerts > 0)
                    <span class="ml-2 text-sm font-medium text-danger-600">({{ $overdueCerts }} expired)</span>
                @endif
            </x-slot>

            @if($certs->isEmpty())
                <p class="text-sm text-gray-500">No certificate renewals due in this window.</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-200 dark:border-gray-700">
                            <th class="py-2 pr-4">Cert No.</th>
                            <th class="py-2 pr-4">Customer</th>
                            <th class="py-2 pr-4">Property</th>
                            <th class="py-2">Expires</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($certs as $row)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="py-2 pr-4 font-mono">{{ $row['item'] }}</td>
                                <td class="py-2 pr-4">{{ $row['customer'] }}</td>
                                <td class="py-2 pr-4">{{ $row['address'] }}</td>
                                <td class="py-2 @if($row['due']->isPast()) text-danger-600 font-medium @endif">
                                    {{ $row['due']->format('d M Y') }}
                                    @if($row['due']->isPast()) (expired) @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
