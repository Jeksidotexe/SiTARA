<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan ini di-import
use Symfony\Component\HttpFoundation\Response;

class MarkNotificationAsRead
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah ada parameter ?mark_as_read= di URL
        if ($request->has('mark_as_read')) {
            /** @var User|null $user */
            $user = Auth::user();
            $notificationId = $request->query('mark_as_read'); // Ambil nilainya

            if ($user && $notificationId) {
                // Cari notifikasi HANYA milik user yang sedang login
                $notification = $user->notifications()->find($notificationId);

                if ($notification) {
                    // Tandai sebagai sudah dibaca
                    $notification->markAsRead();
                }
            }
        }

        // Lanjutkan ke halaman tujuan
        return $next($request);
    }
}
