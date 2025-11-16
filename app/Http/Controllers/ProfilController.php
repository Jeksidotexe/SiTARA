<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfilController extends Controller
{
    /**
     * Menampilkan form edit profil untuk pengguna yang sedang login.
     */
    public function edit()
    {
        $user = Auth::user();

        return view('dashboard.profil.index', [
            'user' => $user
        ]);
    }

    /**
     * Update data profil pengguna yang sedang login.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $userId = $user->id_users;

        $rules = [
            'nama' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($userId, 'id_users'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'id_users'),
            ],
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Pesan kustom untuk validasi
        $messages = [
            'required' => ':Attribute wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan oleh pengguna lain.',
            'email.unique' => 'Email ini sudah terdaftar pada pengguna lain.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'foto.max' => 'Ukuran foto tidak boleh lebih dari 2MB.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validatedData = $validator->validated();

        $data = collect($validatedData)->except(['password', 'foto', 'password_confirmation'])->toArray();

        if (!empty($validatedData['password'])) {
            $data['password'] = Hash::make($validatedData['password']);
        }

        if ($request->hasFile('foto') && isset($validatedData['foto'])) {
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }
            $path = $validatedData['foto']->store('pengguna/images', 'public');
            $data['foto'] = $path;
        }

        try {
            $user->update($data);

            return redirect()->route('profil.edit')
                ->with('success', 'Profil berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating profile for user ' . $userId . ': ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui profil.')
                ->withInput();
        }
    }
}
