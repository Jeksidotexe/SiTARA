<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Definisikan aturan validasi yang lebih lengkap
        $credentials = $request->validate([
            'email' => 'required|email|string',
            'password' => 'required|string|min:8', // Tambahkan aturan min:8
        ], [
            // 2. Tambahkan pesan custom untuk setiap aturan
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal harus 8 karakter.',
        ]);

        // 3. Coba lakukan autentikasi dengan kredensial yang sudah tervalidasi
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard')->with('success', 'Berhasil masuk.');
        }

        // 4. Jika gagal, lemparkan ValidationException untuk pesan yang proper
        throw ValidationException::withMessages([
            'email' => 'Kombinasi email dan password tidak cocok.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
