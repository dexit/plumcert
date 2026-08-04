<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use App\Filament\Resources\Jobs\JobResource;
use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_job')
                ->label('New Job')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->url(fn (Customer $record) =>
                    JobResource::getUrl('create')
                    . '?' . http_build_query(['customer_id' => $record->id])
                ),
            EditAction::make(),
        ];
    }
}
