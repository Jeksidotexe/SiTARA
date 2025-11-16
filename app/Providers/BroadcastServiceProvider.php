<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast; // <-- Pastikan ini ada
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
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
        // BARIS INI AKAN MEMBUAT ENDPOINT /broadcasting/auth
        Broadcast::routes();

        // BARIS INI AKAN MEMUAT routes/channels.php ANDA
        require base_path('routes/channels.php');
    }
}
