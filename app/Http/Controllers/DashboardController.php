<?php

namespace App\Http\Controllers;

use App\Models\LaporanKejadianMenonjol;
use App\Models\LaporanPelanggaranKampanye;
use App\Models\LaporanPenguatanIdeologi;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Support\Facades\Auth;
use App\Models\LaporanSituasiDaerah;
use App\Models\LaporanPilkadaSerentak;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;
        $user = Auth::user();

        $reportModels = [
            LaporanSituasiDaerah::class,
            LaporanPilkadaSerentak::class,
            LaporanKejadianMenonjol::class,
            LaporanPelanggaranKampanye::class,
            LaporanPenguatanIdeologi::class,
        ];

        // --- DASHBOARD ADMIN ---
        if ($role == 'admin') {
            $totalLaporan = 0;
            foreach ($reportModels as $model) {
                $totalLaporan += $model::where('status_laporan', 'disetujui')->count();
            }
            $totalUsers = User::count();
            $totalWilayah = Wilayah::count();

            $laporanGlobalPerBulan = [];

            foreach ($reportModels as $model) {
                $dataPerModel = $model::selectRaw('MONTH(tanggal_laporan) as bulan, COUNT(*) as jumlah')
                    ->whereYear('tanggal_laporan', now()->year)
                    ->groupBy('bulan')
                    ->pluck('jumlah', 'bulan');

                foreach ($dataPerModel as $bulan => $jumlah) {
                    if (!isset($laporanGlobalPerBulan[$bulan])) {
                        $laporanGlobalPerBulan[$bulan] = 0;
                    }
                    $laporanGlobalPerBulan[$bulan] += $jumlah;
                }
            }
            $labelsBulanGlobal = [];
            $dataJumlahGlobal = [];
            for ($i = 1; $i <= 12; $i++) {
                try {
                    $labelsBulanGlobal[] = Carbon::create()->month($i)->locale('id')->isoFormat('MMM');
                } catch (\Exception $e) {
                    $labelsBulanGlobal[] = Carbon::create()->month($i)->format('M');
                }
                $dataJumlahGlobal[] = $laporanGlobalPerBulan[$i] ?? 0;
            }


            return view('dashboard.admin.index', compact(
                'totalLaporan',
                'totalUsers',
                'totalWilayah',
                'labelsBulanGlobal',
                'dataJumlahGlobal'
            ));
        }
        // --- DASHBOARD OPERATOR ---
        elseif ($role == 'operator') {

            $userId = $user->id_users;

            $allLaporanRevisi = collect();
            $allLaporanDisetujui = collect();
            $laporanMenunggu = 0;
            $totalLaporanOperator = 0;

            foreach ($reportModels as $model) {
                $allLaporanRevisi = $allLaporanRevisi->merge(
                    $model::where('id_operator', $userId)->needsRevision()->latest('verified_at')->limit(10)->get()
                );
                $allLaporanDisetujui = $allLaporanDisetujui->merge(
                    $model::where('id_operator', $userId)->with('pimpinan')->approved()->latest('verified_at')->limit(5)->get()
                );
                $laporanMenunggu += $model::where('id_operator', $userId)->pendingVerification()->count();
                $totalLaporanOperator += $model::where('id_operator', $userId)
                    ->where('status_laporan', 'disetujui')
                    ->count();
            }

            $laporanRevisi = $allLaporanRevisi->sortByDesc('verified_at')->take(10);
            $laporanDisetujui = $allLaporanDisetujui->sortByDesc('verified_at')->take(5);

            $laporanPerBulan = [];

            foreach ($reportModels as $model) {

                $dataPerModel = $model::selectRaw('MONTH(tanggal_laporan) as bulan, COUNT(*) as jumlah')
                    ->where('id_operator', $userId)
                    ->where('status_laporan', 'disetujui')
                    ->whereYear('tanggal_laporan', now()->year)
                    ->groupBy('bulan')
                    ->pluck('jumlah', 'bulan');

                foreach ($dataPerModel as $bulan => $jumlah) {
                    if (!isset($laporanPerBulan[$bulan])) {
                        $laporanPerBulan[$bulan] = 0;
                    }
                    $laporanPerBulan[$bulan] += $jumlah;
                }
            }

            $labelsBulan = [];
            $dataJumlah = [];
            for ($i = 1; $i <= 12; $i++) {
                try {
                    $labelsBulan[] = \Carbon\Carbon::create()->month($i)->locale('id')->isoFormat('MMMM');
                } catch (\Exception $e) {
                    $labelsBulan[] = \Carbon\Carbon::create()->month($i)->format('F');
                }
                $dataJumlah[] = $laporanPerBulan[$i] ?? 0;
            }

            return view('dashboard.operator.index', compact(
                'laporanRevisi',
                'laporanDisetujui',
                'laporanMenunggu',
                'totalLaporanOperator',
                'labelsBulan',
                'dataJumlah'
            ));
        }
        // --- DASHBOARD PIMPINAN ---
        elseif ($role == 'pimpinan') {
            $pimpinanWilayahId = $user->id_wilayah;

            $allLaporanPending = collect();
            $countPending = 0;
            $countApproved = 0;

            if ($pimpinanWilayahId) {
                foreach ($reportModels as $model) {
                    $baseQuery = $model::query()->whereHas('operator', function ($query) use ($pimpinanWilayahId) {
                        $query->where('id_wilayah', $pimpinanWilayahId);
                    });

                    $allLaporanPending = $allLaporanPending->merge(
                        (clone $baseQuery)->with('operator')->pendingVerification()->latest('created_at')->limit(10)->get()
                    );
                    $countPending += (clone $baseQuery)->pendingVerification()->count();
                    $countApproved += (clone $baseQuery)->approved()->count();
                }
            }

            $laporanPending = $allLaporanPending->sortByDesc('created_at')->take(10);

            return view('dashboard.pimpinan.index', compact(
                'laporanPending',
                'countPending',
                'countApproved',
            ));
        } else {
            abort(403, 'Role tidak dikenali.');
        }
    }

    /**
     * Data untuk peta Leaflet.
     */
    public function getMapData()
    {
        try {
            $wilayahData = Wilayah::select(
                'nama_wilayah',
                'status_wilayah',
                'latitude as lat',
                'longitude as lng'
            )
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get();

            return response()->json($wilayahData);
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Unknown column')) {
                return response()->json([
                    'error' => 'Kolom "latitude" atau "longitude" tidak ditemukan di tabel "wilayah". Silakan periksa nama kolom di database Anda.'
                ], 500);
            }
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
