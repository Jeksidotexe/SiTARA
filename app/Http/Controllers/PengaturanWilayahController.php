<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PengaturanWilayahController extends Controller
{
    // Path diletakkan di dalam 'storage' agar tersimpan di public/storage/wilayah/...
    private $path_kop = 'storage/wilayah/kop_surat';
    private $path_ttd = 'storage/wilayah/tanda_tangan';

    /**
     * Helper untuk meng-handle upload file, memindahkannya langsung ke folder public fisik.
     */
    private function handleUpload(Request $request, Wilayah $wilayah, $fileField, $oldPath, $storagePath)
    {
        if (!$request->hasFile($fileField)) {
            return $oldPath;
        }

        // 1. Hapus file lama secara fisik (jika ada) untuk menghindari penumpukan file
        if ($oldPath) {
            $oldFile = Str::startsWith($oldPath, 'storage/') ? public_path($oldPath) : public_path('storage/' . $oldPath);
            if (file_exists($oldFile) && is_file($oldFile)) {
                @unlink($oldFile);
            }
        }

        // 2. Siapkan file baru
        $file = $request->file($fileField);

        // Membersihkan nama wilayah dari spasi dan karakter aneh (misal: "Kota Pontianak" jadi "kota_pontianak")
        $saneNamaWilayah = Str::slug($wilayah->nama_wilayah, '_');

        // Nama file diatur murni berdasarkan nama wilayah dan jenis inputannya
        $fileName = $saneNamaWilayah . '_' . $fileField . '.' . $file->getClientOriginalExtension();

        // 3. Pindahkan file LANGSUNG ke direktori fisik public
        $file->move(public_path($storagePath), $fileName);

        // 4. Simpan path yang sudah disesuaikan ke database
        return $storagePath . '/' . $fileName;
    }

    /**
     * Menampilkan data wilayah saat ini (untuk modal).
     */
    public function show()
    {
        $operator = Auth::user();
        $wilayah = Wilayah::find($operator->id_wilayah);

        if (!$wilayah) {
            return response()->json(['error' => 'Wilayah tidak ditemukan.'], 404);
        }

        // Otomatis menyesuaikan path jika data lama di database tidak memiliki prefix "storage/"
        $kopUrl = null;
        if ($wilayah->kop_surat) {
            $pathKop = Str::startsWith($wilayah->kop_surat, 'storage/') ? $wilayah->kop_surat : 'storage/' . $wilayah->kop_surat;
            $kopUrl = asset($pathKop);
        }

        $ttdUrl = null;
        if ($wilayah->tanda_tangan) {
            $pathTtd = Str::startsWith($wilayah->tanda_tangan, 'storage/') ? $wilayah->tanda_tangan : 'storage/' . $wilayah->tanda_tangan;
            $ttdUrl = asset($pathTtd);
        }

        return response()->json([
            'nama_wilayah' => $wilayah->nama_wilayah,
            'kop_surat_url' => $kopUrl,
            'tanda_tangan_url' => $ttdUrl,
        ]);
    }

    /**
     * Update data kop surat dan tanda tangan.
     */
    public function update(Request $request)
    {
        $operator = Auth::user();
        $wilayah = Wilayah::find($operator->id_wilayah);

        if (!$wilayah) {
            return response()->json(['message' => 'Wilayah tidak ditemukan.'], 404);
        }

        // Validasi
        $validator = Validator::make($request->all(), [
            'kop_surat' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tanda_tangan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            '*.image' => 'File harus berupa gambar.',
            '*.mimes' => 'Format gambar harus: jpeg, png, jpg',
            '*.max' => 'Ukuran gambar tidak boleh lebih dari 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $updateData = [];
            $updatedMessages = [];

            // Upload Kop Surat
            if ($request->hasFile('kop_surat')) {
                $updateData['kop_surat'] = $this->handleUpload(
                    $request,
                    $wilayah,
                    'kop_surat',
                    $wilayah->kop_surat,
                    $this->path_kop
                );
                $updatedMessages[] = 'Kop Surat';
            }

            // Upload Tanda Tangan
            if ($request->hasFile('tanda_tangan')) {
                $updateData['tanda_tangan'] = $this->handleUpload(
                    $request,
                    $wilayah,
                    'tanda_tangan',
                    $wilayah->tanda_tangan,
                    $this->path_ttd
                );
                $updatedMessages[] = 'Tanda Tangan';
            }

            $message = 'Tidak ada perubahan yang disimpan.';

            // Update Database
            if (!empty($updateData)) {
                $wilayah->update($updateData);
                $message = implode(' dan ', $updatedMessages) . ' berhasil diperbarui.';
            }

            // Format URL response dengan Anti-Cache bawaan untuk memperbarui preview otomatis
            $kopUrl = null;
            if ($wilayah->kop_surat) {
                $pathKop = Str::startsWith($wilayah->kop_surat, 'storage/') ? $wilayah->kop_surat : 'storage/' . $wilayah->kop_surat;
                $kopUrl = asset($pathKop) . '?t=' . time();
            }

            $ttdUrl = null;
            if ($wilayah->tanda_tangan) {
                $pathTtd = Str::startsWith($wilayah->tanda_tangan, 'storage/') ? $wilayah->tanda_tangan : 'storage/' . $wilayah->tanda_tangan;
                $ttdUrl = asset($pathTtd) . '?t=' . time();
            }

            return response()->json([
                'message' => $message,
                'kop_surat_url' => $kopUrl,
                'tanda_tangan_url' => $ttdUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating wilayah settings: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
