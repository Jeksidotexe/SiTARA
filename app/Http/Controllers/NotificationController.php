<?php

namespace App\Http\Controllers;

use App\Models\User; // <-- TAMBAHKAN IMPORT INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        $user = Auth::user();

        // [PERBAIKAN]
        // Tambahkan pengecekan ini. Ini akan memberi tahu linter
        // bahwa $user adalah instance dari App\Models\User,
        // yang kita tahu memiliki trait Notifiable.
        if ($user instanceof User) {
            $user->unreadNotifications()->update(['read_at' => now()]);
            return response()->json(['success' => true]);
        }

        // Ini adalah fallback, meskipun seharusnya tidak akan pernah terpicu
        // jika middleware 'auth' Anda sudah benar.
        return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
    }

    /**
     * [METHOD BARU]
     * Menandai SATU notifikasi sebagai terbaca.
     */
    public function markOneAsRead(Request $request)
    {
        $request->validate([
            'id' => 'required|string', // ID notifikasi (UUID)
        ]);

        $user = Auth::user();

        if ($user instanceof User) {
            $notification = $user->unreadNotifications()
                ->where('id', $request->id)
                ->first();

            if ($notification) {
                $notification->markAsRead();
                return response()->json(['success' => true, 'message' => 'Notifikasi ditandai terbaca.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Notifikasi tidak ditemukan.'], 404);
    }
}
