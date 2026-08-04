<?php

namespace App\Providers;

use App\Models\Certificate;
use App\Models\Job;
use App\Models\Quote;
use App\Observers\CertificateObserver;
use App\Observers\JobObserver;
use App\Observers\QuoteObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Job::observe(JobObserver::class);
        Quote::observe(QuoteObserver::class);
        Certificate::observe(CertificateObserver::class);
    }
}
