<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use App\Events\LaporanStatusUpdated;
use App\Models\LaporanLain;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NotifikasiUntukPimpinan;

class LaporanLainController extends Controller
{
    // [REFAKTOR] Daftar field yang di-looping
    private $fileFields = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];

    // [REFAKTOR] Daftar field teks untuk $request->only()
    private $textFields = [
        'deskripsi',
        'penutup',
        'narasi_a',
        'narasi_b',
        'narasi_c',
        'narasi_d',
        'narasi_e',
        'narasi_f',
        'narasi_g',
        'narasi_h'
    ];

    // [REFAKTOR] Nama 'cantik' untuk pesan error yang spesifik
    private $fieldTitles = [
        'a' => 'A. Penyelenggaraan Pemerintah Daerah',
        'b' => 'B. Pelaksanaan Program Pembangunan',
        'c' => 'C. Pelayanan Publik',
        'd' => 'D. Ideologi',
        'e' => 'E. Politik',
        'f' => 'F. Ekonomi',
        'g' => 'G. Sosial Budaya',
        'h' => 'H. Hankam',
    ];

    // [REFAKTOR] Konfigurasi path untuk Storage facade
    private $storageDiskPath = 'laporan_lain/file'; // Path relatif di dalam disk
    private $publicAccessPath = 'storage/laporan_lain/file'; // Path untuk disimpan di DB
    private $diskName = 'public'; // Nama disk di config/filesystems.php

    /**
     * [REFAKTOR] Helper untuk mengunggah file menggunakan Storage facade.
     * Lebih bersih dan memanfaatkan sistem file Laravel.
     */
    private function uploadFile(UploadedFile $file): string
    {
        // Simpan ke 'public' disk di folder 'laporan_lain/file'
        // store() otomatis menghasilkan hashName
        $path = $file->store($this->storageDiskPath, $this->diskName);

        // $path akan berisi 'laporan_lain/file/hash.ext'
        $fileName = basename($path);

        // Kembalikan path yang bisa diakses publik (untuk disimpan di DB)
        return $this->publicAccessPath . '/' . $fileName;
    }

    /**
     * [REFAKTOR] Helper untuk menghapus file menggunakan Storage facade.
     */
    private function deleteFile(?string $filePath): void
    {
        if (!$filePath) {
            return;
        }

        // $filePath dari DB adalah 'storage/laporan_lain/file/hash.ext'
        // Kita perlu 'laporan_lain/file/hash.ext' untuk dihapus
        $fileName = basename($filePath);
        $storagePath = $this->storageDiskPath . '/' . $fileName;

        if (Storage::disk($this->diskName)->exists($storagePath)) {
            Storage::disk($this->diskName)->delete($storagePath);
        }
    }

    /**
     * [REFAKTOR] Helper baru untuk membangun aturan validasi file secara dinamis.
     * Ini menghilangkan repetisi di store() dan update()
     * dan memberikan pesan error yang spesifik.
     */
    private function buildFileValidationRules(): array
    {
        $rules = [];
        $messages = [];
        $fileRule = 'file|mimes:jpg,jpeg,png|max:2048'; // 2MB

        foreach ($this->fileFields as $field) {
            $fileKey = "file_$field"; // cth: "file_a"
            // Ambil nama 'cantik' dari properti, fallback ke nama generik
            $friendlyName = $this->fieldTitles[$field] ?? "Bagian $field";

            // Aturan Validasi
            $rules[$fileKey] = 'nullable|array';
            $rules["$fileKey.*"] = $fileRule;

            // [REFAKTOR] Pesan Error Spesifik Sesuai Permintaan
            $messages["$fileKey.*.file"]  = "Upload untuk '$friendlyName' harus berupa file yang valid.";
            $messages["$fileKey.*.mimes"] = "Format file untuk '$friendlyName' tidak didukung (Hanya: jpg, jpeg dan png).";
            $messages["$fileKey.*.max"]   = "Ukuran file untuk '$friendlyName' terlalu besar (Maks 2MB).";
        }

        // Tambahkan aturan untuk 'deleted_files' (digunakan di update)
        $rules['deleted_files'] = 'nullable|array';
        $rules['deleted_files.*'] = 'nullable|array';
        $rules['deleted_files.*.*'] = 'string';

        return ['rules' => $rules, 'messages' => $messages];
    }

    public function previewPdf(string $id)
    {
        $laporan = LaporanLain::with(['operator.wilayah'])->findOrFail($id);

        $wilayah = $laporan->operator->wilayah ?? null;
        if (!$wilayah) {
            return redirect()->back()->with('error', 'Data wilayah tidak ditemukan pada operator ini.');
        }

        $reports = collect([$laporan]);
        $filters = [
            'tipe_laporan' => 'Lain-Lain',
            'tanggal'      => Carbon::parse($laporan->tanggal_laporan)->isoFormat('D MMMM YYYY'),
            'bulan'        => null,
            'tahun'        => null,
        ];
        $sectionKeys = $this->fileFields;
        $sectionTitles = $this->fieldTitles;

        $fileFields = $this->fileFields;
        $fieldTitles = $this->fieldTitles;

        $pdf = Pdf::loadView('dashboard.laporan_lain.preview_pdf', compact(
            'laporan',
            'reports',
            'wilayah',
            'filters',
            'sectionKeys',
            'sectionTitles',
            'fileFields',
            'fieldTitles'
        ));

        $pdf->setPaper('A4', 'portrait');

        $fileName = 'Laporan_Lain_Lain_' . Carbon::parse($laporan->tanggal_laporan)->format('d_m_Y') . '.pdf';
        return $pdf->stream($fileName);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.laporan_lain.index');
    }

    /**
     * Provide data for DataTables.
     */
    public function data()
    {
        $user = Auth::user();

        // [PERBAIKAN] Mulai query builder
        $query = LaporanLain::with('operator')
            ->latest('id_laporan');

        // [PERBAIKAN] Terapkan filter berdasarkan Role Operator
        // Route ini dilindungi oleh middleware 'role:operator' di web.php
        $query->where('id_operator', $user->id_users);

        return datatables()
            ->of($query) // [PERBAIKAN] Gunakan $query
            ->addIndexColumn()
            ->addColumn('operator', function ($laporan) {
                return $laporan->operator->nama ?? '<span class="text-danger">Operator Dihapus</span>';
            })
            ->filterColumn('operator', function ($query, $keyword) {
                $query->whereHas('operator', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('tanggal_laporan', function ($laporan) {
                return Carbon::parse($laporan->tanggal_laporan)->isoFormat('D MMMM YYYY');
            })
            ->filterColumn('tanggal_laporan', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(tanggal_laporan,'%d %M %Y') like ?", ["%{$keyword}%"]);
            })
            ->editColumn('status_laporan', function ($laporan) {
                if ($laporan->status_laporan == 'disetujui') {
                    return '<span class="badge badge-sm bg-gradient-success"><i class="fas fa-circle-check"></i> Disetujui</span>';
                } elseif ($laporan->status_laporan == 'revisi') {
                    return '<span class="badge badge-sm bg-gradient-warning"><i class="fas fa-exclamation-triangle"></i> Revisi</span>';
                } else {
                    return '<span class="badge badge-sm bg-gradient-info"><i class="fas fa-hourglass-half"></i> Menunggu Verifikasi</span>';
                }
            })
            ->addColumn('aksi', function ($laporan) {
                // [MODIFIKASI] Tambahkan $showUrl
                $editUrl = route('laporan_lain.edit', ['laporan_lain' => $laporan->id_laporan]);
                $showUrl = route('laporan_lain.show', ['laporan_lain' => $laporan->id_laporan]);
                $deleteUrl = route('laporan_lain.destroy', ['laporan_lain' => $laporan->id_laporan]);

                // [MODIFIKASI] Tambahkan tombol "Lihat"
                return '
                <div class="d-flex justify-content-start gap-2">
                <a href="' . $editUrl . '" class="btn btn-sm btn-dark bg-gradient-dark"><i class="fa fa-edit"></i> Edit</a>
                <a href="' . $showUrl . '" class="btn btn-sm btn-dark-blue bg-gradient-dark-blue"><i class="fa fa-eye"></i> Lihat</a>
                    <button onclick="deleteData(`' . $deleteUrl . '`)" class="btn btn-sm btn-danger bg-gradient-danger"><i class="fa fa-trash"></i> Hapus</button>
                </div>
                ';
            })
            ->rawColumns(['operator', 'status_laporan', 'aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.laporan_lain.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * [REFAKTOR] Menggunakan helper validasi dinamis.
     * [REFAKTOR] Logika rollback yang lebih bersih.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Dapatkan aturan & pesan validasi file yang dinamis
        $fileValidation = $this->buildFileValidationRules();

        // 2. Tentukan aturan & pesan statis (non-file)
        $staticRules = [
            'deskripsi' => 'required|string',
            'penutup'   => 'required|string',
            'narasi_a' => 'nullable|string',
            'narasi_b' => 'nullable|string',
            'narasi_c' => 'nullable|string',
            'narasi_d' => 'nullable|string',
            'narasi_e' => 'nullable|string',
            'narasi_f' => 'nullable|string',
            'narasi_g' => 'nullable|string',
            'narasi_h' => 'nullable|string',
        ];
        $staticMessages = [
            'deskripsi.required' => 'Deskripsi laporan wajib diisi.',
            'penutup.required'   => 'Penutup laporan wajib diisi.',
            '*.string'           => 'Input ini harus berupa teks.',
        ];

        // 3. Gabungkan semua aturan dan pesan
        $allRules = array_merge($staticRules, $fileValidation['rules']);
        $allMessages = array_merge($staticMessages, $fileValidation['messages']);

        // 4. Validasi request
        $validatedData = $request->validate($allRules, $allMessages);

        // Untuk melacak semua file yang diupload di request ini (untuk rollback)
        $uploadedFilePaths = [];

        try {
            // Tambahkan data otomatis
            $validatedData['id_operator'] = Auth::id();
            $validatedData['tanggal_laporan'] = now();
            $validatedData['judul'] = 'Laporan Lain-Lain';

            // Handle File Uploads
            foreach ($this->fileFields as $field) {
                $fileKey = "file_$field";
                $paths = [];
                if ($request->hasFile($fileKey)) {
                    foreach ($request->file($fileKey) as $file) {
                        if ($file instanceof UploadedFile && $file->isValid()) {
                            $path = $this->uploadFile($file);
                            $paths[] = $path;
                            $uploadedFilePaths[] = $path; // Catat untuk rollback
                        }
                    }
                }
                // Simpan sebagai array (akan di-encode ke JSON oleh Model)
                $validatedData[$fileKey] = $paths;
            }

            // [MODIFIKASI] Simpan laporan yang baru dibuat ke variabel
            $laporanBaru = LaporanLain::create($validatedData);

            // === AWAL BLOK NOTIFIKASI ===
            try {
                // 1. Dapatkan ID Wilayah Operator
                $operatorWilayahId = Auth::user()->id_wilayah;

                // 2. Cari Pimpinan di Wilayah yang SAMA
                $pimpinanUsers = User::where('role', 'pimpinan')
                    ->where('id_wilayah', $operatorWilayahId)
                    ->get();

                // 3. Hanya kirim jika Pimpinan ditemukan di wilayah tsb
                if ($pimpinanUsers->isNotEmpty()) {
                    $tanggalFormatted = $laporanBaru->tanggal_laporan?->isoFormat('D MMMM YYYY') ?? '';
                    $message = 'Laporan baru "' . Str::limit(strip_tags($laporanBaru->judul), 50) . '" Tanggal ' . $tanggalFormatted . ' menunggu verifikasi.';
                    $url = route('laporan_lain.show', ['laporan_lain' => $laporanBaru->id_laporan, 'from' => 'notification']);

                    Notification::send($pimpinanUsers, new NotifikasiUntukPimpinan($laporanBaru, $message, $url));
                    LaporanStatusUpdated::dispatch($laporanBaru->id_laporan, $laporanBaru->status_laporan);
                } else {
                    Log::warning("Notifikasi (store) Laporan ID {$laporanBaru->id_laporan} tidak terkirim: Tidak ada Pimpinan di Wilayah ID {$operatorWilayahId}.");
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi laporan baru: ' . $e->getMessage());
            }
            // === AKHIR BLOK NOTIFIKASI ===

            return redirect()->route('laporan_lain.index')
                ->with('success', 'Laporan berhasil ditambahkan dan menunggu verifikasi.');
        } catch (\Exception $e) {
            Log::error('Error storing laporan: ' . $e->getMessage());

            // Jika terjadi error, hapus SEMUA file yang baru saja di-upload
            foreach ($uploadedFilePaths as $path) {
                $this->deleteFile($path);
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan laporan.')
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     * [BARU] Menampilkan halaman detail laporan
     */
    public function show(LaporanLain $laporan_lain)
    {
        $laporan = $laporan_lain;
        $laporan->loadMissing('operator');

        return view('dashboard.laporan_lain.show', compact(
            'laporan',
        ));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $laporan = LaporanLain::findOrFail($id);
        return view('dashboard.laporan_lain.edit', compact('laporan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * [REFAKTOR] Menggunakan helper validasi dinamis.
     * [REFAKTOR] Menggunakan properti $textFields.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $laporan = LaporanLain::findOrFail($id);
        $originalStatus = $laporan->status_laporan; // <-- Simpan status sebelum update

        // --- Authorization Check (PENTING!) ---
        // Pastikan hanya operator yang membuat atau admin yang bisa mengedit
        if (! (Auth::id() == $laporan->id_operator || Auth::user()->role == 'admin')) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit laporan ini.');
        }
        // Jika laporan sudah disetujui, mungkin operator tidak boleh edit lagi? (Opsional)
        if ($laporan->isApproved() && Auth::user()->role == 'operator') {
            return redirect()->back()->with('error', 'Laporan yang sudah disetujui tidak dapat diedit lagi.');
        }


        // 1. Validasi (Sama seperti sebelumnya)
        $fileValidation = $this->buildFileValidationRules();
        $staticRules = [
            'deskripsi' => 'required|string',
            'penutup'   => 'required|string',
            'narasi_a' => 'nullable|string',
            'narasi_b' => 'nullable|string',
            'narasi_c' => 'nullable|string',
            'narasi_d' => 'nullable|string',
            'narasi_e' => 'nullable|string',
            'narasi_f' => 'nullable|string',
            'narasi_g' => 'nullable|string',
            'narasi_h' => 'nullable|string',
        ];
        $staticMessages = [
            'deskripsi.required' => 'Deskripsi laporan wajib diisi.',
            'penutup.required'   => 'Penutup laporan wajib diisi.',
            '*.string'           => 'Input ini harus berupa teks.',
        ];

        // 3. Gabungkan semua aturan dan pesan
        $allRules = array_merge($staticRules, $fileValidation['rules']);
        $allMessages = array_merge($staticMessages, $fileValidation['messages']);

        // 4. Validasi request
        // $validatedData tidak diperlukan karena kita memprosesnya secara manual
        $request->validate($allRules, $allMessages);

        try {
            // Ambil data teks
            $updateData = $request->only($this->textFields);

            // === LOGIKA PERUBAHAN STATUS ===
            // Jika status ASLI adalah 'revisi', ubah kembali ke 'menunggu verifikasi'
            if ($originalStatus === LaporanLain::STATUS_REVISION) {
                $updateData['status_laporan'] = LaporanLain::STATUS_PENDING;
                // Kosongkan Pimpinan, Catatan, dan Tanggal Verifikasi sebelumnya
                $updateData['id_pimpinan'] = null;
                $updateData['catatan'] = null;
                $updateData['verified_at'] = null;

                // === AWAL BLOK NOTIFIKASI REVISI ===
                try {
                    // 1. Dapatkan ID Wilayah Operator (dari Auth, karena operator yg meng-update)
                    $operatorWilayahId = Auth::user()->id_wilayah;

                    // 2. Cari Pimpinan di Wilayah yang SAMA
                    $pimpinanUsers = User::where('role', 'pimpinan')
                        ->where('id_wilayah', $operatorWilayahId)
                        ->get();

                    if ($pimpinanUsers->isNotEmpty()) {
                        $tanggalFormatted = $laporan->tanggal_laporan?->isoFormat('D MMMM YYYY') ?? '';
                        $message = 'Laporan revisi "' . Str::limit(strip_tags($laporan->judul), 50) . '" Tanggal ' . $tanggalFormatted . ' telah dikirim ulang untuk diverifikasi.';
                        $url = route('laporan_lain.show', ['laporan_lain' => $laporan->id_laporan, 'from' => 'notification']);

                        Notification::send($pimpinanUsers, new NotifikasiUntukPimpinan($laporan, $message, $url));
                        LaporanStatusUpdated::dispatch($laporan->id_laporan, $laporan->status_laporan);
                    } else {
                        Log::warning("Notifikasi (update) Laporan ID {$laporan->id_laporan} tidak terkirim: Tidak ada Pimpinan di Wilayah ID {$operatorWilayahId}.");
                    }
                } catch (\Exception $e) {
                    Log::error('Gagal mengirim notifikasi laporan revisi: ' . $e->getMessage());
                }
                // === AKHIR BLOK NOTIFIKASI REVISI ===
            }
            // === AKHIR LOGIKA PERUBAHAN STATUS ===


            // Handle file updates (Sama seperti sebelumnya)
            $newlyUploadedPaths = [];
            $pathsToActuallyDelete = [];
            foreach ($this->fileFields as $field) {
                $fileKey = "file_$field";
                $currentPaths = $laporan->$fileKey ?? [];
                if (!is_array($currentPaths)) {
                    $currentPaths = [];
                }

                $deletedPaths = $request->input("deleted_files.$fileKey", []);
                if (!empty($deletedPaths)) {
                    $currentPaths = array_diff($currentPaths, $deletedPaths);
                    $pathsToActuallyDelete = array_merge($pathsToActuallyDelete, $deletedPaths);
                }

                $newPaths = [];
                if ($request->hasFile($fileKey)) {
                    foreach ($request->file($fileKey) as $file) {
                        if ($file instanceof UploadedFile && $file->isValid()) {
                            $path = $this->uploadFile($file);
                            $newPaths[] = $path;
                            $newlyUploadedPaths[] = $path;
                        }
                    }
                }
                $finalPaths = array_merge(array_values($currentPaths), $newPaths);
                $updateData[$fileKey] = $finalPaths;
            }

            // Update data di database
            $laporan->update($updateData);

            // Hapus file fisik dari disk
            foreach ($pathsToActuallyDelete as $path) {
                $this->deleteFile($path);
            }

            // Pesan sukses disesuaikan jika status diubah
            $successMessage = 'Laporan berhasil diperbarui.';
            if ($originalStatus === LaporanLain::STATUS_REVISION) {
                $successMessage = 'Laporan berhasil diperbarui dan dikirim ulang untuk verifikasi.';
            }

            return redirect()->route('laporan_lain.index')
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Error updating laporan ' . $id . ': ' . $e->getMessage());
            foreach ($newlyUploadedPaths as $path) {
                $this->deleteFile($path);
            }
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui laporan.')
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * [REFAKTOR] Logika tidak berubah, tapi kini memanggil helper deleteFile()
     * yang baru.
     */
    public function destroy(string $id)
    {
        try {
            $laporan = LaporanLain::findOrFail($id);

            // Hapus semua file terkait dari storage
            foreach ($this->fileFields as $field) {
                $fileKey = "file_$field";
                $files = $laporan->$fileKey ?? [];

                if (is_array($files)) {
                    foreach ($files as $path) {
                        $this->deleteFile($path);
                    }
                }
            }

            $laporan->delete();
            LaporanStatusUpdated::dispatch($laporan->id_laporan, 'deleted');

            return response()->json(['message' => 'Laporan berhasil dihapus.'], 200);
        } catch (QueryException $e) {
            Log::error('Error deleting laporan ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Tidak dapat menghapus data karena terkait dengan data lain.'], 500);
        } catch (\Exception $e) {
            Log::error('Error deleting laporan ' . $id . ': ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat menghapus laporan.'], 500);
        }
    }
}
