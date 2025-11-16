<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PengaturanWilayahController extends Controller
{
    // Path dasar di 'storage/app/public/'
    private $path_kop = 'wilayah/kop_surat';
    private $path_ttd = 'wilayah/tanda_tangan';
    private $disk = 'public';

    /**
     * Helper untuk meng-handle upload file, menghapus yang lama, dan mengembalikan path baru.
     */
    private function handleUpload(Request $request, Wilayah $wilayah, $fileField, $oldPath, $storagePath)
    {
        if (!$request->hasFile($fileField)) {
            return $oldPath;
        }

        if ($oldPath) {
            Storage::disk($this->disk)->delete($oldPath);
        }

        $file = $request->file($fileField);

        $saneNamaWilayah = Str::slug($wilayah->nama_wilayah, '_');

        $fileName = $saneNamaWilayah . '_' . $fileField . '.' . $file->getClientOriginalExtension();

        $newPath = $file->storeAs($storagePath, $fileName, $this->disk);

        return $newPath;
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

        return response()->json([
            'nama_wilayah' => $wilayah->nama_wilayah,
            'kop_surat_url' => $wilayah->kop_surat ? Storage::url($wilayah->kop_surat) : null,
            'tanda_tangan_url' => $wilayah->tanda_tangan ? Storage::url($wilayah->tanda_tangan) : null,
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
            'kop_surat' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maks 2MB
            'tanda_tangan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maks 2MB
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
            $updatedMessages = []; // [BARU] Kita akan lacak apa yang di-update

            // Proses upload Kop Surat
            if ($request->hasFile('kop_surat')) {
                $updateData['kop_surat'] = $this->handleUpload(
                    $request,
                    $wilayah,
                    'kop_surat',
                    $wilayah->kop_surat,
                    $this->path_kop
                );
                $updatedMessages[] = 'Kop Surat'; // [BARU] Tandai
            }

            // Proses upload Tanda Tangan
            if ($request->hasFile('tanda_tangan')) {
                $updateData['tanda_tangan'] = $this->handleUpload(
                    $request,
                    $wilayah,
                    'tanda_tangan',
                    $wilayah->tanda_tangan,
                    $this->path_ttd
                );
                $updatedMessages[] = 'Tanda Tangan'; // [BARU] Tandai
            }

            // [LOGIKA PESAN BARU]
            $message = 'Tidak ada perubahan yang disimpan.'; // Pesan default jika tidak ada file

            // Hanya update database jika ada data baru
            if (!empty($updateData)) {
                $wilayah->update($updateData);

                // Buat pesan dinamis
                $message = implode(' dan ', $updatedMessages) . ' berhasil diperbarui.';
            }

            return response()->json([
                'message' => $message, // <-- Kirim pesan dinamis
                'kop_surat_url' => $wilayah->kop_surat ? Storage::url($wilayah->kop_surat) : null,
                'tanda_tangan_url' => $wilayah->tanda_tangan ? Storage::url($wilayah->tanda_tangan) : null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating wilayah settings: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}
