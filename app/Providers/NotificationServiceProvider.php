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
        View::composer('layouts.navbar', function ($view) {
            $user = Auth::user();
            if ($user instanceof User) {
                $unreadNotifications = $user->unreadNotifications();

                $view->with('unreadCount', $unreadNotifications->count());

                $view->with('notifications', $unreadNotifications->latest()->take(5)->get());
            } else {
                $view->with('unreadCount', 0);
                $view->with('notifications', collect());
            }
        });
    }
}
