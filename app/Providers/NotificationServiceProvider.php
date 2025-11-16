<?php

namespace App\Providers;

use App\Models\User; // <-- TAMBAHKAN IMPORT INI
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Gunakan View Composer untuk membagikan data notifikasi ke 'layouts.navbar'
        View::composer('layouts.navbar', function ($view) {

            // [FIX] Ambil user sekali di atas
            $user = Auth::user();

            // [FIX] Ganti Auth::check() dengan pengecekan instanceof
            // Ini akan memberi tahu linter bahwa $user adalah model User Anda
            if ($user instanceof User) {

                // Ambil query builder notifikasi (lebih efisien)
                $unreadNotifications = $user->unreadNotifications();

                // 1. Dapatkan HANYA jumlah yang belum dibaca
                $view->with('unreadCount', $unreadNotifications->count());

                // 2. Dapatkan 5 notifikasi TERBARU YANG BELUM DIBACA
                // (Ini sudah benar dari kode Anda sebelumnya)
                $view->with('notifications', $unreadNotifications->latest()->take(5)->get());
            } else {
                // Fallback jika tidak login
                $view->with('unreadCount', 0);
                $view->with('notifications', collect());
            }
        });
    }
}
