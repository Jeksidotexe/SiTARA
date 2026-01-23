<?php

namespace App\Services;

use App\Models\LaporanLain;
use App\Models\LaporanSituasiDaerah;
use Illuminate\Support\Facades\Auth;
use App\Models\LaporanPilkadaSerentak;
use App\Models\LaporanKejadianMenonjol;
use App\Models\LaporanPenguatanIdeologi;
use App\Models\LaporanPelanggaranKampanye;

class VerificationService
{
    /**
     * Mendapatkan jumlah laporan pending untuk pimpinan secara Real-time.
     *
     * @return int
     */
    public function getPendingCount(): int
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'pimpinan' || !$user->id_wilayah) {
            return 0;
        }

        $pimpinanWilayahId = $user->id_wilayah;

        $reportModels = [
            LaporanSituasiDaerah::class,
            LaporanPilkadaSerentak::class,
            LaporanKejadianMenonjol::class,
            LaporanPelanggaranKampanye::class,
            LaporanPenguatanIdeologi::class,
            LaporanLain::class
        ];

        $countPending = 0;

        foreach ($reportModels as $model) {
            // Query dasar memfilter berdasarkan wilayah pimpinan
            $baseQuery = $model::query()->whereHas('operator', function ($query) use ($pimpinanWilayahId) {
                $query->where('id_wilayah', $pimpinanWilayahId);
            });

            // Menghitung yang statusnya pending/menunggu verifikasi
            $countPending += (clone $baseQuery)->pendingVerification()->count();
        }

        return $countPending;
    }
}
