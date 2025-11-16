<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use App\Models\LaporanSituasiDaerah;
use App\Models\LaporanPilkadaSerentak; // [TAMBAHKAN]
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DaftarLaporanBulananController extends Controller
{
    /**
     * [SCALABILITY] Tentukan semua model laporan yang relevan di sini.
     */
    private $reportModels = [
        'laporan-situasi-daerah' => [
            'class' => LaporanSituasiDaerah::class,
            'title' => 'Laporan Situasi Daerah',
            'route_base' => 'laporan_situasi_daerah',
        ],
        'laporan-pilkada-serentak' => [
            'class' => LaporanPilkadaSerentak::class,
            'title' => 'Laporan Pilkada Serentak',
            'route_base' => 'laporan_pilkada_serentak',
        ],
        // Tambahkan model lain di sini
    ];

    /**
     * Menampilkan daftar wilayah untuk dipilih.
     * (LOGIKA SKALABEL SUDAH DITERAPKAN DI SINI)
     */
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $selectedYear = $request->input('year', now()->year);

        // [SCALABILITY] Ambil tahun unik dari SEMUA tabel laporan
        $yearQueries = [];
        foreach ($this->reportModels as $config) {
            $yearQueries[] = $config['class']::selectRaw('DISTINCT(YEAR(tanggal_laporan)) as year');
        }
        $query = array_shift($yearQueries);
        if ($query) { // Pastikan query tidak null jika $reportModels kosong
            foreach ($yearQueries as $q) {
                $query->union($q);
            }
            $availableYears = $query->orderBy('year', 'desc')->pluck('year');
        } else {
            $availableYears = collect();
        }

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        $carbonDate = Carbon::create($selectedYear);
        $daysInYear = $carbonDate->isLeapYear() ? 366 : 365;

        $wilayahs = Wilayah::whereHas('users', function ($query) {
            $query->whereIn('role', ['operator', 'pimpinan']);
        })
            ->distinct()
            ->orderBy('nama_wilayah')
            ->get();

        // [SCALABILITY] Hitung total laporan dari SEMUA model
        $wilayahs->each(function ($wilayah) use ($selectedYear, $daysInYear) {
            $totalCount = 0;
            foreach ($this->reportModels as $config) {
                $totalCount += $config['class']::where('status_laporan', 'disetujui')
                    ->whereYear('tanggal_laporan', $selectedYear)
                    ->whereHas('operator', function ($query) use ($wilayah) {
                        $query->where('id_wilayah', $wilayah->id_wilayah);
                    })
                    ->count();
            }

            $wilayah->report_count = $totalCount;
            $wilayah->target_days = $daysInYear;
            $wilayah->progress_percentage = ($daysInYear > 0) ? round(($totalCount / $daysInYear) * 100) : 0;
            if ($wilayah->progress_percentage > 100) {
                $wilayah->progress_percentage = 100;
            }
        });

        return view('dashboard.admin.daftar_laporan_bulanan.index', compact('wilayahs', 'availableYears', 'selectedYear'));
    }

    /**
     * Menampilkan daftar bulan untuk wilayah yang dipilih.
     * (LOGIKA SKALABEL SUDAH DITERAPKAN DI SINI)
     */
    public function showMonths(Request $request, Wilayah $wilayah)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $year = $request->input('year', now()->year);

        // [SCALABILITY] Ambil tanggal unik dari SEMUA tabel laporan
        $distinctReportDays = collect();
        foreach ($this->reportModels as $config) {
            $dates = $config['class']::where('status_laporan', 'disetujui')
                ->whereYear('tanggal_laporan', $year)
                ->whereHas('operator.wilayah', function ($query) use ($wilayah) {
                    $query->where('id_wilayah', $wilayah->id_wilayah);
                })
                ->selectRaw('DISTINCT(DATE(tanggal_laporan)) as report_date')
                ->pluck('report_date');

            $distinctReportDays = $distinctReportDays->merge($dates);
        }

        $distinctReportDays = $distinctReportDays->unique()->map(function ($date) {
            return Carbon::parse($date);
        });

        $monthsData = [];
        for ($i = 1; $i <= 12; $i++) {
            $date = Carbon::create($year, $i, 1);
            $monthName = $date->locale('id')->isoFormat('MMMM');
            $daysInMonth = $date->daysInMonth; // Target hari dalam bulan

            // [PERBAIKAN] Hapus logika dinamis untuk target hari
            // Cek jika tahun & bulan > tahun & bulan sekarang
            /*
            if ($date->gt(now()->endOfMonth())) {
                $daysInMonth = 0; // Target menjadi 0 jika bulan belum tercapai
            } elseif ($date->isSameMonth(now())) {
                $daysInMonth = now()->day; // Target adalah hari ini jika bulan berjalan
            }
            */

            $reportCountForMonth = $distinctReportDays->filter(function ($reportDate) use ($i) {
                return $reportDate->month == $i;
            })->count();

            $progressPercentage = ($daysInMonth > 0) ? round(($reportCountForMonth / $daysInMonth) * 100) : 0;
            if ($progressPercentage > 100) {
                $progressPercentage = 100;
            }

            $monthsData[$i] = [
                'name' => $monthName,
                'count' => $reportCountForMonth,
                'target' => $daysInMonth,
                'progress' => $progressPercentage,
            ];
        }

        return view('dashboard.admin.daftar_laporan_bulanan.months', compact('wilayah', 'year', 'monthsData'));
    }

    /**
     * Menampilkan halaman tabel laporan untuk wilayah dan bulan tertentu.
     */
    public function showReports(Request $request, Wilayah $wilayah, $month)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
        if ($month < 1 || $month > 12) {
            abort(404, 'Bulan tidak valid.');
        }

        $year = $request->input('year', now()->year);
        $monthName = Carbon::create($year, (int)$month)->locale('id')->isoFormat('MMMM');

        // [TAMBAHKAN] Kirim daftar tipe laporan ke view untuk filter
        $reportTypes = $this->reportModels;

        return view('dashboard.admin.daftar_laporan_bulanan.reports', compact('wilayah', 'month', 'year', 'monthName', 'reportTypes'));
    }

    /**
     * Menyiapkan data laporan untuk DataTables.
     */
    public function data(Request $request, Wilayah $wilayah, $month)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
        if ($month < 1 || $month > 12) {
            abort(404, 'Bulan tidak valid.');
        }

        $year = $request->input('year', now()->year);

        // [TAMBAHKAN] Ambil filter tipe laporan dari request
        $filterReportType = $request->input('report_type');

        // [SCALABILITY] Gabungkan SEMUA laporan menggunakan UNION
        $allQueries = collect();

        // Tentukan model yang akan di-query berdasarkan filter
        $modelsToQuery = [];
        if (!empty($filterReportType) && isset($this->reportModels[$filterReportType])) {
            // Jika memfilter spesifik, hanya ambil model itu
            $modelsToQuery[$filterReportType] = $this->reportModels[$filterReportType];
        } else {
            // Jika tidak ada filter (atau 'all'), ambil semua model
            $modelsToQuery = $this->reportModels;
        }

        foreach ($modelsToQuery as $key => $config) {
            $tipeLaporan = $config['title'];
            $modelClass = $config['class'];
            $routeBase = $config['route_base'];

            $query = $modelClass::with('operator.wilayah')
                ->where('status_laporan', 'disetujui')
                ->whereYear('tanggal_laporan', $year)
                ->whereMonth('tanggal_laporan', $month)
                ->whereHas('operator.wilayah', function ($query) use ($wilayah) {
                    $query->where('id_wilayah', $wilayah->id_wilayah);
                })
                // [PERBAIKAN] Pilih semua kolom narasi karena user bilang sama
                ->select(
                    'id_laporan',
                    'tanggal_laporan',
                    'judul',
                    'narasi_a',
                    'narasi_b',
                    'narasi_c',
                    'narasi_d',
                    'narasi_e',
                    'narasi_f',
                    'narasi_g',
                    'narasi_h', // <-- Kolom narasi dikembalikan
                    DB::raw("'$tipeLaporan' as tipe_laporan"),
                    DB::raw("'$routeBase' as route_base")
                );

            $allQueries->push($query);
        }

        // Jika tidak ada model yang dipilih (misal filter salah), return data kosong
        if ($allQueries->isEmpty()) {
            return DataTables::of(collect())->make(true);
        }

        $laporans = $allQueries->shift(); // Ambil query pertama
        if ($laporans) { // Pastikan $laporans tidak null
            foreach ($allQueries as $q) {
                $laporans->unionAll($q); // Gabungkan sisanya
            }
            $laporans->latest('tanggal_laporan'); // Urutkan setelah union
        } else {
            // Jika $allQueries awalnya kosong, $laporans akan null
            $laporans = collect();
        }
        // [AKHIR PERBAIKAN]

        return DataTables::of($laporans)
            ->editColumn('tanggal_laporan', function ($row) {
                return Carbon::parse($row->tanggal_laporan)->isoFormat('D MMMM YYYY');
            })
            // [PERBAIKAN] Kembalikan kolom narasi
            ->addColumn('pemerintahan_daerah', function ($row) {
                return Str::limit(strip_tags($row->narasi_a), 100);
            })
            ->addColumn('program_pembangunan', function ($row) {
                return Str::limit(strip_tags($row->narasi_b), 100);
            })
            ->addColumn('pelayanan_publik', function ($row) {
                return Str::limit(strip_tags($row->narasi_c), 100);
            })
            ->addColumn('ideologi', function ($row) {
                return Str::limit(strip_tags($row->narasi_d), 100);
            })
            ->addColumn('politik', function ($row) {
                return Str::limit(strip_tags($row->narasi_e), 100);
            })
            ->addColumn('ekonomi', function ($row) {
                return Str::limit(strip_tags($row->narasi_f), 100);
            })
            ->addColumn('sosial_budaya', function ($row) {
                return Str::limit(strip_tags($row->narasi_g), 100);
            })
            ->addColumn('hankam', function ($row) {
                return Str::limit(strip_tags($row->narasi_h), 100);
            })
            // Kolom 'judul' dan 'tipe_laporan' tidak perlu ditampilkan, tapi ada di data
            ->addColumn('aksi', function ($row) {
                // [PERBAIKAN] Gunakan $paramName yang benar
                $paramName = str_replace('-', '_', $row->route_base);
                $showUrl = route($row->route_base . '.show', [
                    $paramName => $row->id_laporan,
                    'from' => 'laporan-bulanan'
                ]);
                return '
                   <a href="' . $showUrl . '" class="btn btn-sm btn-dark-blue bg-gradient-dark-blue">
                       <i class="fa fa-eye"></i> Detail
                   </a>';
            })
            // [PERBAIKAN] Sesuaikan blok 'only'
            ->only([
                'tanggal_laporan',
                'pemerintahan_daerah',
                'program_pembangunan',
                'pelayanan_publik',
                'ideologi',
                'politik',
                'ekonomi',
                'sosial_budaya',
                'hankam',
                'aksi'
            ])
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
