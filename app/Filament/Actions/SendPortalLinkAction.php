<?php
namespace App\Filament\Actions;

use App\Models\Customer;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendPortalLinkAction
{
    public static function make(): Action
    {
        return Action::make('sendPortalLink')
            ->label('Send Portal Link')
            ->icon('heroicon-o-link')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Send Customer Portal Link')
            ->modalDescription('This will email the customer a secure 30-day link to view their certificates and request a service booking.')
            ->action(function ($record) {
                /** @var Customer $customer */
                $customer = $record;
                $link = URL::temporarySignedRoute('portal.show', now()->addDays(30), ['customer' => $customer->id]);

                Mail::send([], [], function ($message) use ($customer, $link) {
                    $message->to($customer->email ?? '')
                        ->subject('Your Plumcert Customer Portal')
                        ->html(view('emails.portal-link', compact('customer', 'link'))->render());
                });

                \Filament\Notifications\Notification::make()
                    ->title('Portal link sent')
                    ->body('Email sent to ' . ($customer->email ?? 'customer'))
                    ->success()
                    ->send();
            });
    }
}
