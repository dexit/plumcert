<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Certificate;
use App\Services\RecurringJobScheduler;

/**
 * When a gas safety certificate / service record is issued, schedule the next
 * recurring compliance visit automatically (interval is config-driven and can
 * be overridden per certificate). See App\Services\RecurringJobScheduler.
 */
class CertificateObserver
{
    public function __construct(private RecurringJobScheduler $scheduler)
    {
    }

    public function created(Certificate $certificate): void
    {
        $this->scheduler->scheduleFor($certificate);
    }

    public function updated(Certificate $certificate): void
    {
        // Schedule the follow-up once the certificate actually gains an issue
        // date or its recurrence interval changes.
        if ($certificate->wasChanged('issued_at') || $certificate->wasChanged('recurrence_months')) {
            $this->scheduler->scheduleFor($certificate);
        }
    }
}
