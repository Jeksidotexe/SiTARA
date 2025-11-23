<?php

namespace App\Services;

use App\Models\LaporanKejadianMenonjol;
use App\Models\LaporanPelanggaranKampanye;
use App\Models\LaporanPenguatanIdeologi;
use App\Models\LaporanSituasiDaerah;
use App\Models\LaporanPilkadaSerentak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class VerificationService
{
    /**
     * Mendapatkan dan men-cache jumlah laporan pending untuk pimpinan.
     *
     * @return int
     */
    public function getPendingCount(): int
    {
        $user = Auth::user();

        // 1. Pastikan user adalah pimpinan dan memiliki wilayah
        if (!$user || $user->role !== 'pimpinan' || !$user->id_wilayah) {
            return 0;
        }

        $pimpinanWilayahId = $user->id_wilayah;

        // 2. Definisikan cache key (HARUS SAMA DENGAN DI VerificationController)
        $cacheKey = 'pending_count_pimpinan_' . $pimpinanWilayahId;

        // 3. Gunakan Cache::remember() untuk efisiensi
        // Simpan selama 60 menit, atau sampai di-forget (dihapus) oleh VerificationController
        return Cache::remember($cacheKey, 60 * 60, function () use ($pimpinanWilayahId) {

            // 4. Salin logika perhitungan dari DashboardController Anda
            $reportModels = [
                LaporanSituasiDaerah::class,
                LaporanPilkadaSerentak::class,
                LaporanKejadianMenonjol::class,
                LaporanPelanggaranKampanye::class,
                LaporanPenguatanIdeologi::class,
            ];

            $countPending = 0;

            foreach ($reportModels as $model) {
                // Gunakan query() untuk memulai query builder pada model
                $baseQuery = $model::query()->whereHas('operator', function ($query) use ($pimpinanWilayahId) {
                    $query->where('id_wilayah', $pimpinanWilayahId);
                });

                // Pastikan model Anda memiliki scope 'pendingVerification()'
                // Jika tidak, ganti dengan: ->where('status_laporan', 'menunggu verifikasi')
                $countPending += (clone $baseQuery)->pendingVerification()->count();
            }

            return $countPending;
        });
    }
}
