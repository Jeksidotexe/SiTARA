<?php

namespace App\Providers;

use App\Models\Wilayah;
use App\Observers\WilayahObserver;
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
        Wilayah::observe(WilayahObserver::class);
    }
}
