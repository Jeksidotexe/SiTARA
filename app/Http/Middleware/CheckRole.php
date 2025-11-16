<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string ...$roles Daftar role yang diizinkan (bisa lebih dari satu)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response // Gunakan spread operator (...)
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            // Bisa redirect ke login atau tampilkan error
            abort(403, 'Akses ditolak. Silakan login terlebih dahulu.');
            // return redirect('login');
        }

        // 2. Ambil role user yang sedang login
        $userRole = Auth::user()->role;

        // 3. Cek apakah role user ada di dalam daftar $roles yang diizinkan
        $allowed = false;
        foreach ($roles as $role) {
            // Explode jika role dikirim sebagai 'admin,operator' (fallback jika spread operator tidak bekerja sesuai harapan di versi PHP/Laravel tertentu)
            $allowedRoles = explode(',', $role);
            if (in_array($userRole, $allowedRoles)) {
                $allowed = true;
                break; // Hentikan loop jika role ditemukan
            }
        }

        // 4. Jika role TIDAK diizinkan, tampilkan error 403
        if (!$allowed) {
            abort(403, 'Anda tidak memiliki izin (' . $userRole . ') untuk mengakses halaman ini. Izin yang diperlukan: ' . implode(', ', $roles));
            // return redirect('/dashboard')->with('error', 'Akses ditolak.');
        }

        // 5. Jika role sesuai, lanjutkan request
        return $next($request);
    }
}
