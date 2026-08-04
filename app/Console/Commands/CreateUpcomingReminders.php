<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Boiler;
use App\Models\Certificate;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateUpcomingReminders extends Command
{
    protected $signature = 'reminders:generate';
    protected $description = 'Generate recurring maintenance & certificate-renewal reminders at staggered lead times';

    public function handle(): int
    {
        $boilerCount = $this->generateBoilerServiceReminders();
        $certCount   = $this->generateCertificateRenewalReminders();

        $this->info("Created {$boilerCount} boiler service reminder(s) and {$certCount} certificate renewal reminder(s).");

        \Illuminate\Support\Facades\Log::channel('plumcert')->info('reminders:generate completed', [
            'boiler_reminders' => $boilerCount,
            'cert_reminders'   => $certCount,
        ]);

        return self::SUCCESS;
    }

    /**
     * Lead times (days before the due date) at which a reminder should fire.
     * One reminder row is created per lead time per appliance/certificate.
     *
     * @return array<int, int>
     */
    private function leadDays(): array
    {
        return config('plumcert.reminder_lead_days', [30, 7, 0]);
    }

    private function generateBoilerServiceReminders(): int
    {
        $created = 0;
        $horizon = now()->addDays(max($this->leadDays()) + 1);

        $boilers = Boiler::whereNotNull('next_service_due')
            ->whereDate('next_service_due', '<=', $horizon)
            ->with('property.customer')
            ->get();

        foreach ($boilers as $boiler) {
            $dueAt = Carbon::parse($boiler->next_service_due);

            foreach ($this->leadDays() as $lead) {
                $fireAt = $this->resolveFireAt($dueAt, $lead);

                // skip if the firing window is in the past (don't backfill early-lead reminders)
                if ($fireAt === null) {
                    continue;
                }

                if ($this->reminderExists('boiler_id', $boiler->id, $lead, 'service')) {
                    continue;
                }

                $templateKey = ($lead === 0 || $dueAt->isPast()) ? 'service_overdue' : 'service_reminder';

                Reminder::create([
                    'boiler_id'    => $boiler->id,
                    'customer_id'  => $boiler->property?->customer_id,
                    'property_id'  => $boiler->property_id,
                    'type'         => 'service',
                    'title'        => "Annual Service Due — {$boiler->make} {$boiler->model}",
                    'description'  => "Service due on {$dueAt->format('d M Y')} ({$lead} day lead).",
                    'due_at'       => $fireAt,
                    'lead_days'    => $lead,
                    'template_key' => $templateKey,
                ]);

                $created++;
            }
        }

        return $created;
    }

    private function generateCertificateRenewalReminders(): int
    {
        $created = 0;
        $horizon = now()->addDays(max($this->leadDays()) + 1);

        // CP12 / gas safety certificates expire 12 months after issue.
        $certificates = Certificate::whereNotNull('issued_at')
            ->whereIn('type', ['cp12_homeowner', 'cp12_landlord', 'gas_service_record'])
            ->with(['customer', 'property'])
            ->get();

        foreach ($certificates as $cert) {
            $expiresAt = Carbon::parse($cert->issued_at)->addYear();

            // only those expiring within our look-ahead horizon
            if ($expiresAt->isAfter($horizon)) {
                continue;
            }

            foreach ($this->leadDays() as $lead) {
                $fireAt = $this->resolveFireAt($expiresAt, $lead);

                if ($fireAt === null) {
                    continue;
                }

                if ($this->reminderExists('certificate_id', $cert->id, $lead, 'annual_cert')) {
                    continue;
                }

                Reminder::create([
                    'certificate_id' => $cert->id,
                    'customer_id'    => $cert->customer_id,
                    'property_id'    => $cert->property_id,
                    'type'           => 'annual_cert',
                    'title'          => "Gas Safety Certificate Renewal — {$cert->certificate_number}",
                    'description'    => "Certificate expires on {$expiresAt->format('d M Y')} ({$lead} day lead).",
                    'due_at'         => $fireAt,
                    'lead_days'      => $lead,
                    'template_key'   => 'annual_cert_renewal',
                ]);

                $created++;
            }
        }

        return $created;
    }

    /**
     * Work out when a reminder for a given due date + lead time should fire.
     *
     * Early-lead reminders (lead > 0) are never backfilled — if their window
     * has passed, they are skipped (return null). The final lead=0 reminder is
     * clamped to today so genuinely overdue items still get chased now.
     */
    private function resolveFireAt(Carbon $dueAt, int $lead): ?Carbon
    {
        $fireAt = $dueAt->copy()->subDays($lead);

        if ($fireAt->isBefore(today())) {
            return $lead === 0 ? today() : null;
        }

        return $fireAt;
    }

    private function reminderExists(string $column, int $id, int $lead, string $type): bool
    {
        return Reminder::where($column, $id)
            ->where('type', $type)
            ->where('lead_days', $lead)
            ->exists();
    }
}
