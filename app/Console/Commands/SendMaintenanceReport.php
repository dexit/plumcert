<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Boiler;
use App\Models\Certificate;
use App\Models\Reminder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendMaintenanceReport extends Command
{
    protected $signature = 'reminders:report {--email= : Override recipient email address}';
    protected $description = 'Email a weekly maintenance summary (services due, certificates expiring, reminders pending) to the admin';

    public function handle(): int
    {
        $horizon = now()->addDays(30);

        $servicesDue = Boiler::whereNotNull('next_service_due')
            ->whereDate('next_service_due', '<=', $horizon)
            ->with('property.customer')
            ->orderBy('next_service_due')
            ->get();

        $certsExpiring = Certificate::whereNotNull('issued_at')
            ->whereIn('type', ['cp12_homeowner', 'cp12_landlord', 'gas_service_record'])
            ->with(['customer', 'property'])
            ->get()
            ->filter(fn (Certificate $c) => Carbon::parse($c->issued_at)->addYear()->lte($horizon))
            ->sortBy(fn (Certificate $c) => Carbon::parse($c->issued_at)->addYear());

        $remindersPending = Reminder::pending()
            ->where('due_at', '<=', $horizon)
            ->count();

        $recipient = $this->option('email')
            ?? User::where('role', 'admin')->value('email')
            ?? config('mail.from.address');

        if (! $recipient) {
            $this->error('No admin recipient email found.');
            return self::FAILURE;
        }

        $summary = [
            'services_due'      => $servicesDue,
            'certs_expiring'    => $certsExpiring,
            'reminders_pending' => $remindersPending,
            'generated_at'      => now(),
        ];

        Mail::send('emails.maintenance-report', $summary, function ($message) use ($recipient) {
            $message->to($recipient)
                ->subject('Weekly Maintenance Report — ' . now()->format('d M Y'));
        });

        $this->info(sprintf(
            'Maintenance report sent to %s — %d services due, %d certificates expiring, %d reminders pending.',
            $recipient,
            $servicesDue->count(),
            $certsExpiring->count(),
            $remindersPending,
        ));

        return self::SUCCESS;
    }
}
