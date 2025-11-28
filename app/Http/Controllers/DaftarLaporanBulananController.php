<?php

namespace App\Http\Controllers;

use App\Models\LaporanKejadianMenonjol;
use App\Models\LaporanPelanggaranKampanye;
use App\Models\LaporanPenguatanIdeologi;
use App\Models\Wilayah;
use App\Models\LaporanSituasiDaerah;
use App\Models\LaporanPilkadaSerentak;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DaftarLaporanBulananController extends Controller
{
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
        'laporan-kejadian-menonjol' => [
            'class' => LaporanKejadianMenonjol::class,
            'title' => 'Laporan Kejadian Menonjol',
            'route_base' => 'laporan_kejadian_menonjol',
        ],
        'laporan-pelanggaran-kampanye' => [
            'class' => LaporanPelanggaranKampanye::class,
            'title' => 'Laporan Pelanggaran Kampanye',
            'route_base' => 'laporan_pelanggaran_kampanye',
        ],
        'laporan-penguatan-ideologi' => [
            'class' => LaporanPenguatanIdeologi::class,
            'title' => 'Laporan Penguatan Ideologi',
            'route_base' => 'laporan_penguatan_ideologi',
        ],
    ];

    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $selectedYear = $request->input('year', now()->year);

        $yearQueries = [];
        foreach ($this->reportModels as $config) {
            $yearQueries[] = $config['class']::selectRaw('DISTINCT(YEAR(tanggal_laporan)) as year');
        }
        $query = array_shift($yearQueries);
        if ($query) {
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

    public function showMonths(Request $request, Wilayah $wilayah)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }

        $year = $request->input('year', now()->year);

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
            $daysInMonth = $date->daysInMonth;

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

        $reportTypes = $this->reportModels;

        return view('dashboard.admin.daftar_laporan_bulanan.reports', compact('wilayah', 'month', 'year', 'monthName', 'reportTypes'));
    }

    public function data(Request $request, Wilayah $wilayah, $month)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');
        }
        if ($month < 1 || $month > 12) {
            abort(404, 'Bulan tidak valid.');
        }

        $year = $request->input('year', now()->year);
        $filterReportType = $request->input('report_type');

        $allQueries = collect();
        $modelsToQuery = [];

        if (!empty($filterReportType) && isset($this->reportModels[$filterReportType])) {
            $modelsToQuery[$filterReportType] = $this->reportModels[$filterReportType];
        } else {
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
                    'narasi_h',
                    DB::raw("'$tipeLaporan' as tipe_laporan"),
                    DB::raw("'$routeBase' as route_base")
                );

            $allQueries->push($query);
        }

        if ($allQueries->isEmpty()) {
            return DataTables::of(collect())->make(true);
        }

        $laporans = $allQueries->shift();
        if ($laporans) {
            foreach ($allQueries as $q) {
                $laporans->unionAll($q);
            }
            $laporans->latest('tanggal_laporan');
        } else {
            $laporans = collect();
        }

        return DataTables::of($laporans)
            ->editColumn('tanggal_laporan', function ($row) {
                return Carbon::parse($row->tanggal_laporan)->isoFormat('D MMMM YYYY');
            })
            // --- MULAI BAGIAN FORMAT KOLOM NARASI ---
            ->addColumn('pemerintahan_daerah', function ($row) {
                $text = strip_tags($row->narasi_a);
                // Cek jika kosong, null, atau hanya strip
                if (empty(trim($text)) || trim($text) === '-') {
                    return '<span class="text-muted fst-italic small" style="opacity: 0.7;">-- Nihil --</span>';
                }
                return '<span title="' . e($text) . '" style="cursor:help;">' . Str::limit($text, 50) . '</span>';
            })
            ->addColumn('program_pembangunan', function ($row) {
                $text = strip_tags($row->narasi_b);
                if (empty(trim($text)) || trim($text) === '-') {
                    return '<span class="text-muted fst-italic small" style="opacity: 0.7;">-- Nihil --</span>';
                }
                return '<span title="' . e($text) . '" style="cursor:help;">' . Str::limit($text, 50) . '</span>';
            })
            ->addColumn('pelayanan_publik', function ($row) {
                $text = strip_tags($row->narasi_c);
                if (empty(trim($text)) || trim($text) === '-') {
                    return '<span class="text-muted fst-italic small" style="opacity: 0.7;">-- Nihil --</span>';
                }
                return '<span title="' . e($text) . '" style="cursor:help;">' . Str::limit($text, 50) . '</span>';
            })
            ->addColumn('ideologi', function ($row) {
                $text = strip_tags($row->narasi_d);
                if (empty(trim($text)) || trim($text) === '-') {
                    return '<span class="text-muted fst-italic small" style="opacity: 0.7;">-- Nihil --</span>';
                }
                return '<span title="' . e($text) . '" style="cursor:help;">' . Str::limit($text, 50) . '</span>';
            })
            ->addColumn('politik', function ($row) {
                $text = strip_tags($row->narasi_e);
                if (empty(trim($text)) || trim($text) === '-') {
                    return '<span class="text-muted fst-italic small" style="opacity: 0.7;">-- Nihil --</span>';
                }
                return '<span title="' . e($text) . '" style="cursor:help;">' . Str::limit($text, 50) . '</span>';
            })
            ->addColumn('ekonomi', function ($row) {
                $text = strip_tags($row->narasi_f);
                if (empty(trim($text)) || trim($text) === '-') {
                    return '<span class="text-muted fst-italic small" style="opacity: 0.7;">-- Nihil --</span>';
                }
                return '<span title="' . e($text) . '" style="cursor:help;">' . Str::limit($text, 50) . '</span>';
            })
            ->addColumn('sosial_budaya', function ($row) {
                $text = strip_tags($row->narasi_g);
                if (empty(trim($text)) || trim($text) === '-') {
                    return '<span class="text-muted fst-italic small" style="opacity: 0.7;">-- Nihil --</span>';
                }
                return '<span title="' . e($text) . '" style="cursor:help;">' . Str::limit($text, 50) . '</span>';
            })
            ->addColumn('hankam', function ($row) {
                $text = strip_tags($row->narasi_h);
                if (empty(trim($text)) || trim($text) === '-') {
                    return '<span class="text-muted fst-italic small" style="opacity: 0.7;">-- Nihil --</span>';
                }
                return '<span title="' . e($text) . '" style="cursor:help;">' . Str::limit($text, 50) . '</span>';
            })
            // --- AKHIR BAGIAN FORMAT KOLOM NARASI ---
            ->addColumn('aksi', function ($row) {
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
            ->rawColumns([
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
            ->make(true);
    }
}
