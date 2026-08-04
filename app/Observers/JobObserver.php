<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Job;
use App\Services\TaskGenerator;

class JobObserver
{
    public function __construct(private TaskGenerator $tasks)
    {
    }

    public function created(Job $job): void
    {
        $this->tasks->generate(
            $job,
            'job',
            $job->type ?? null,
            $job->scheduled_at ?? now(),
        );
    }
}
