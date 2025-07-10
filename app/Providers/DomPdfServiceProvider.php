<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Barryvdh\DomPDF\Facade\Pdf;

class DomPdfServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('pdf', function ($app) {
            return new \Barryvdh\DomPDF\PDF($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // PDF facade için alias tanımlama
        $this->app->alias('pdf', \Barryvdh\DomPDF\PDF::class);
    }
}
