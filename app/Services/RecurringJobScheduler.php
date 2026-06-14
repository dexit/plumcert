<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Certificate;
use App\Models\Job;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Schedules the next recurring compliance visit for a certificate.
 *
 * Used both by CertificateObserver (live creation) and JobSeeder (demo data),
 * so the recurrence rules live in one place and stay config-driven.
 */
class RecurringJobScheduler
{
    /**
     * Create the next recurring follow-up job for a certificate, if applicable.
     * Returns the created Job, or null when the certificate does not recur or a
     * matching follow-up already exists.
     */
    public function scheduleFor(Certificate $certificate): ?Job
    {
        $months = $this->recurrenceMonths($certificate);

        if ($months <= 0 || ! $certificate->customer_id) {
            return null;
        }

        $issuedAt = $certificate->issued_at ? Carbon::parse($certificate->issued_at) : now();
        $dueAt = $issuedAt->copy()->addMonths($months)->setTime(9, 0);

        if ($this->followUpExists($certificate, $dueAt)) {
            return null;
        }

        $job = Job::create([
            'customer_id'                => $certificate->customer_id,
            'property_id'                => $certificate->property_id,
            'assigned_to_user_id'        => $certificate->issued_by_user_id,
            'type'                       => 'annual_service',
            'title'                      => 'Recurring Gas Safety Check — due ' . $dueAt->format('M Y'),
            'description'                => "Auto-scheduled from certificate {$certificate->certificate_number} ({$months}-month cycle).",
            'status'                     => 'scheduled',
            'scheduled_at'               => $dueAt,
            'is_recurring'               => true,
            'recurs_from_certificate_id' => $certificate->id,
        ]);

        Log::channel('plumcert')->info('Scheduled recurring follow-up job', [
            'certificate_id' => $certificate->id,
            'job_id'         => $job->id,
            'due_at'         => $dueAt->toDateTimeString(),
            'months'         => $months,
        ]);

        return $job;
    }

    /**
     * Resolve the recurrence interval: per-certificate override first, else the
     * configured default for the certificate type.
     */
    public function recurrenceMonths(Certificate $certificate): int
    {
        if (! is_null($certificate->recurrence_months)) {
            return (int) $certificate->recurrence_months;
        }

        return (int) (config('plumcert.cert_recurrence')[$certificate->type] ?? 0);
    }

    private function followUpExists(Certificate $certificate, Carbon $dueAt): bool
    {
        return Job::query()
            ->where('customer_id', $certificate->customer_id)
            ->where('type', 'annual_service')
            ->where('is_recurring', true)
            ->whereBetween('scheduled_at', [$dueAt->copy()->subDays(7), $dueAt->copy()->addDays(7)])
            ->when($certificate->property_id, fn ($q) => $q->where('property_id', $certificate->property_id))
            ->exists();
    }
}
