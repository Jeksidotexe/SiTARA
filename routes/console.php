<?php

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CheckWilayahStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// [LANGKAH 2] Tambahkan penjadwalan Anda di sini
// Ini adalah pengganti dari Kernel.php
Schedule::command(CheckWilayahStatus::class)->hourly();
