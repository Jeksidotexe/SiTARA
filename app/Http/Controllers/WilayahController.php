<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;
use App\Events\WilayahUpdated;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class WilayahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.wilayah.index');
    }

    /**
     * Menyiapkan data untuk DataTables.
     */
    public function data()
    {
        $wilayah = Wilayah::withCount('users')->latest('id_wilayah');

        return datatables()
            ->of($wilayah)
            ->addIndexColumn()
            ->editColumn('latitude', function ($wilayah) {
                return $wilayah->latitude ? number_format($wilayah->latitude, 6) : '-';
            })
            ->editColumn('longitude', function ($wilayah) {
                return $wilayah->longitude ? number_format($wilayah->longitude, 6) : '-';
            })
            ->editColumn('status_wilayah', function ($wilayah) {
                if ($wilayah->status_wilayah == 'Aman') {
                    return '<span class="badge badge-sm bg-gradient-success">Aman</span>';
                } elseif ($wilayah->status_wilayah == 'Siaga') {
                    return '<span class="badge badge-sm bg-gradient-warning">Siaga</span>';
                } elseif ($wilayah->status_wilayah == 'Bahaya') {
                    return '<span class="badge badge-sm bg-gradient-danger">Bahaya</span>';
                } else {
                    return '<span class="badge badge-sm bg-gradient-secondary">Belum Diatur</span>';
                }
            })
            ->addColumn('aksi', function ($wilayah) {
                return '
                <div class="d-flex justify-content-start gap-2">
                    <a href="' . route('wilayah.edit', $wilayah->id_wilayah) . '" class="btn btn-sm btn-dark"><i class="fa fa-edit"></i> Edit</a>
                    <button onclick="deleteData(`' . route('wilayah.destroy', $wilayah->id_wilayah) . '`)" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus</button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'status_wilayah'])
            ->make('true');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.wilayah.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'nama_wilayah' => 'required|string|max:255|unique:wilayah,nama_wilayah',
            'latitude'     => 'nullable|numeric|between:-90,90',
            'longitude'    => 'nullable|numeric|between:-180,180',
        ];

        $messages = [
            'nama_wilayah.required' => 'Nama wilayah wajib diisi.',
            'nama_wilayah.unique' => 'Nama wilayah ini sudah ada.',
            'latitude.numeric'      => 'Latitude harus berupa angka.',
            'latitude.between'      => 'Latitude harus antara -90 dan 90.',
            'longitude.numeric'     => 'Longitude harus berupa angka.',
            'longitude.between'     => 'Longitude harus antara -180 dan 180.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $wilayah = Wilayah::create($validator->validated());
            WilayahUpdated::dispatch($wilayah, 'created');

            return redirect()->route('wilayah.index')
                ->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating wilayah: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Wilayah $wilayah)
    {
        return response()->json($wilayah);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wilayah $wilayah)
    {
        return view('dashboard.wilayah.edit', compact('wilayah'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wilayah $wilayah): RedirectResponse // [FIX] Gunakan Route Model Binding
    {
        $rules = [
            'nama_wilayah' => [
                'required',
                'string',
                'max:255',
                Rule::unique('wilayah', 'nama_wilayah')->ignore($wilayah->id_wilayah, 'id_wilayah'),
            ],
            'status_wilayah' => 'required|in:Aman,Siaga,Bahaya',
            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
        ];

        $messages = [
            'nama_wilayah.required' => 'Nama wilayah wajib diisi.',
            'nama_wilayah.unique' => 'Nama wilayah ini sudah digunakan.',
            'status_wilayah.required' => 'Status wilayah wajib dipilih.',
            'status_wilayah.in' => 'Status wilayah tidak valid.',
            'latitude.numeric'        => 'Latitude harus berupa angka.',
            'latitude.between'        => 'Latitude harus antara -90 dan 90.',
            'longitude.numeric'       => 'Longitude harus berupa angka.',
            'longitude.between'       => 'Longitude harus antara -180 dan 180.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $wilayah->update($validator->validated());

            WilayahUpdated::dispatch($wilayah, 'updated');

            return redirect()->route('wilayah.index')
                ->with('success', 'Data berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating wilayah ' . $wilayah->id_wilayah . ': ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wilayah $wilayah) // [FIX] Gunakan Route Model Binding
    {
        try {
            // [FIX] Perbaiki relasi 'user' menjadi 'users' (sesuai 'withCount')
            $wilayah->loadCount('users');

            if ($wilayah->users_count > 0) {
                return response()->json([
                    'message' => 'Gagal menghapus! Masih ada ' . $wilayah->users_count . ' pengguna terdaftar di wilayah ini.'
                ], 422);
            }

            $wilayah->delete();

            WilayahUpdated::dispatch($wilayah, 'deleted');

            return response()->json(['message' => 'Data berhasil dihapus.'], 200);
        } catch (QueryException $e) {
            Log::error('Error deleting wilayah ' . $wilayah->id_wilayah . ': ' . $e->getMessage());
            return response()->json(['message' => 'Tidak dapat menghapus data karena terkait dengan data lain.'], 500);
        } catch (\Exception $e) {
            Log::error('Error deleting wilayah ' . $wilayah->id_wilayah . ': ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus data.'], 500);
        }
    }

    /**
     * [METHOD BARU]
     * Menangani submit modal tindak lanjut dari Pimpinan.
     */
    public function tindakLanjut(Request $request)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'id_wilayah' => 'required|integer|exists:wilayah,id_wilayah',
            'status_wilayah' => 'required|in:Aman,Siaga,Bahaya',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Otorisasi (pastikan yang update adalah Pimpinan)
        if (Auth::user()->role !== 'pimpinan') {
            return response()->json(['message' => 'Anda tidak memiliki izin.'], 403);
        }

        try {
            // 3. Cari dan Update Wilayah
            $wilayah = Wilayah::find($request->id_wilayah);

            // Pengecekan tambahan: Pimpinan hanya boleh update wilayahnya sendiri
            if (Auth::user()->id_wilayah != $wilayah->id_wilayah) {
                return response()->json(['message' => 'Anda tidak memiliki izin untuk wilayah ini.'], 403);
            }

            $wilayah->update([
                'status_wilayah' => $request->status_wilayah
            ]);
            // Observer (WilayahObserver) akan otomatis meng-update `status_wilayah_updated_at`

            // 4. Kirim event ke peta
            WilayahUpdated::dispatch($wilayah, 'updated');

            return response()->json(['message' => 'Status wilayah berhasil diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Error tindak lanjut wilayah: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
