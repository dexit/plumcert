<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Quote;
use App\Services\TaskGenerator;

class QuoteObserver
{
    public function __construct(private TaskGenerator $tasks)
    {
    }

    public function created(Quote $quote): void
    {
        $this->tasks->generate($quote, 'quote', null, now());
    }
}
