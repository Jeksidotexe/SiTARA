<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class PenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wilayah = Wilayah::all();
        return view('dashboard.pengguna.index', compact('wilayah'));
    }

    /**
     * Menyiapkan data untuk DataTables.
     *
     */
    public function data()
    {
        $pengguna = User::with('wilayah')
            ->latest('id_users');

        return datatables()
            ->of($pengguna)
            ->addIndexColumn()
            ->editColumn('foto', function ($pengguna) {
                $url = $pengguna->foto ? Storage::url($pengguna->foto) : asset('images/default.jpg');
                return '<img src="' . $url . '" alt="Foto" width="50" class="img-thumbnail">';
            })
            ->editColumn('id_wilayah', function ($pengguna) {
                return $pengguna->wilayah->nama_wilayah ?? 'Belum Ditentukan';
            })
            ->addColumn('aksi', function ($pengguna) {
                return '
                <div class="d-flex justify-content-start gap-2">
                    <a href="' . route('pengguna.edit', $pengguna->id_users) . '" class="btn btn-sm btn-warning bg-gradient-warning text-dark"><i class="fa fa-edit"></i> Edit</a>
                    <button onclick="deleteData(`' . route('pengguna.destroy', $pengguna->id_users) . '`)" class="btn btn-sm btn-danger bg-gradient-danger"><i class="fa fa-trash"></i> Hapus</button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'foto'])
            ->make('true');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $wilayah = Wilayah::all();
        return view('dashboard.pengguna.create', compact('wilayah'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string',
            'id_wilayah' => 'required|exists:wilayah,id_wilayah',
            'role' => 'required|in:Admin,Operator,Pimpinan',
            'password' => 'required|string|min:8|confirmed',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $messages = [
            'required' => ':Attribute wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'email.email' => 'Format email tidak valid.',
            'id_wilayah.required' => 'Wilayah wajib dipilih.',
            'role.required' => 'Role wajib dipilih.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'foto.required' => 'Foto profil wajib diunggah.',
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
        $data['password'] = Hash::make($validatedData['password']);

        if ($request->hasFile('foto')) {
            $path = $validatedData['foto']->store('pengguna/images', 'public');
            $data['foto'] = $path;
        }

        try {
            User::create($data);
            return redirect()->route('pengguna.index')
                ->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pengguna = User::findOrFail($id);
        return response()->json($pengguna);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $pengguna = User::findOrFail($id);
        $wilayah = Wilayah::all();
        return view('dashboard.pengguna.edit', compact('pengguna', 'wilayah'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $pengguna = User::findOrFail($id);

        $rules = [
            'nama' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($pengguna->id_users, 'id_users'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($pengguna->id_users, 'id_users'),
            ],
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'required|string',
            'id_wilayah' => 'required|exists:wilayah,id_wilayah',
            'role' => 'required|in:Admin,Operator,Pimpinan',
            'password' => 'nullable|string|min:8|confirmed',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $messages = [
            'required' => ':Attribute wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan oleh pengguna lain.',
            'email.unique' => 'Email ini sudah terdaftar pada pengguna lain.',
            'email.email' => 'Format email tidak valid.',
            'id_wilayah.required' => 'Wilayah wajib dipilih.',
            'role.required' => 'Role wajib dipilih.',
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
            if ($pengguna->foto && Storage::disk('public')->exists($pengguna->foto)) {
                Storage::disk('public')->delete($pengguna->foto);
            }
            $path = $validatedData['foto']->store('pengguna/images', 'public');
            $data['foto'] = $path;
        }

        try {
            $pengguna->update($data);
            return redirect()->route('pengguna.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating user ' . $id . ': ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $pengguna = User::findOrFail($id);

            if ($pengguna->foto && Storage::disk('public')->exists($pengguna->foto)) {
                Storage::disk('public')->delete($pengguna->foto);
            }

            $pengguna->delete();

            return response()->json(['message' => 'Data berhasil dihapus.'], 200);
        } catch (QueryException $e) {
            Log::error('Error deleting user ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Tidak dapat menghapus data karena terkait dengan data lain.'], 500);
        } catch (\Exception $e) {
            Log::error('Error deleting user ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data.'], 500);
        }
    }
}
