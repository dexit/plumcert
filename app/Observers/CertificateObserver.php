<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Certificate;
use App\Models\Job;
use Illuminate\Support\Carbon;

/**
 * When a gas safety certificate / service record is issued, schedule the next
 * annual visit automatically so recurring compliance work is never missed.
 */
class CertificateObserver
{
    /**
     * Certificate types that recur on a 12-month cycle.
     */
    private const RECURRING_TYPES = [
        'cp12_homeowner',
        'cp12_landlord',
        'gas_service_record',
    ];

    public function created(Certificate $certificate): void
    {
        $this->scheduleNextVisit($certificate);
    }

    public function updated(Certificate $certificate): void
    {
        // If a certificate only gains its issued_at on update (e.g. signed later),
        // schedule the follow-up then.
        if ($certificate->wasChanged('issued_at') && $certificate->issued_at) {
            $this->scheduleNextVisit($certificate);
        }
    }

    private function scheduleNextVisit(Certificate $certificate): void
    {
        if (! in_array($certificate->type, self::RECURRING_TYPES, true)) {
            return;
        }

        if (! $certificate->customer_id) {
            return;
        }

        $issuedAt = $certificate->issued_at ? Carbon::parse($certificate->issued_at) : now();
        $dueAt = $issuedAt->copy()->addYear()->setTime(9, 0);

        // Idempotency: don't create a duplicate recurring follow-up for the same
        // property within a few days of the computed due date.
        $exists = Job::query()
            ->where('customer_id', $certificate->customer_id)
            ->where('type', 'annual_service')
            ->where('is_recurring', true)
            ->whereBetween('scheduled_at', [$dueAt->copy()->subDays(7), $dueAt->copy()->addDays(7)])
            ->when($certificate->property_id, fn ($q) => $q->where('property_id', $certificate->property_id))
            ->exists();

        if ($exists) {
            return;
        }

        Job::create([
            'customer_id'         => $certificate->customer_id,
            'property_id'         => $certificate->property_id,
            'assigned_to_user_id' => $certificate->issued_by_user_id,
            'type'                => 'annual_service',
            'title'               => 'Annual Gas Safety Check (recurring) — due ' . $dueAt->format('M Y'),
            'description'         => "Auto-scheduled from certificate {$certificate->certificate_number}.",
            'status'              => 'scheduled',
            'scheduled_at'        => $dueAt,
            'is_recurring'        => true,
            'recurs_from_certificate_id' => $certificate->id,
        ]);
    }
}
