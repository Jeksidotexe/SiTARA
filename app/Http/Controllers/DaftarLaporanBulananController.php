<?php

namespace App\Http\Controllers;

use App\Models\LaporanKejadianMenonjol;
use App\Models\LaporanLain;
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
        'laporan-lain' => [
            'class' => LaporanLain::class,
            'title' => 'Laporan Lain-Lain',
            'route_base' => 'laporan_lain'
        ]
    ];

    public function index(Request $request)
    {
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
        $month = (int) $month;
        if ($month < 1 || $month > 12) {
            abort(404, 'Bulan tidak valid.');
        }

        $year = (int) $request->input('year', now()->year);
        $filterReportType = $request->input('report_type');

        $modelsToQuery = [];
        if (!empty($filterReportType) && isset($this->reportModels[$filterReportType])) {
            $modelsToQuery[$filterReportType] = $this->reportModels[$filterReportType];
        } else {
            $modelsToQuery = $this->reportModels;
        }

        $resultCollection = collect();

        foreach ($modelsToQuery as $key => $config) {
            $modelClass = $config['class'];
            $routeBase = $config['route_base'];

            $data = $modelClass::with('operator.wilayah')
                ->where('status_laporan', 'disetujui')
                ->whereYear('tanggal_laporan', $year)
                ->whereMonth('tanggal_laporan', $month)
                ->whereHas('operator.wilayah', function ($q) use ($wilayah) {
                    $q->where('id_wilayah', $wilayah->id_wilayah);
                })
                ->latest('tanggal_laporan')
                ->get();

            $transformed = $data->map(function ($item) use ($routeBase) {
                return [
                    'id_laporan'          => $item->id_laporan,
                    'tanggal_laporan'     => $item->tanggal_laporan,
                    'tanggal_display'     => Carbon::parse($item->tanggal_laporan)->isoFormat('D MMMM YYYY'),
                    'route_base'          => $routeBase,

                    'pemerintahan_daerah' => $this->cleanText($item->narasi_a),
                    'program_pembangunan' => $this->cleanText($item->narasi_b),
                    'pelayanan_publik'    => $this->cleanText($item->narasi_c),
                    'ideologi'            => $this->cleanText($item->narasi_d),
                    'politik'             => $this->cleanText($item->narasi_e),
                    'ekonomi'             => $this->cleanText($item->narasi_f),
                    'sosial_budaya'       => $this->cleanText($item->narasi_g),
                    'hankam'              => $this->cleanText($item->narasi_h),
                ];
            });

            $resultCollection = $resultCollection->concat($transformed);
        }

        return DataTables::of($resultCollection)
            ->editColumn('tanggal_laporan', function ($row) {
                return $row['tanggal_display'];
            })
            ->editColumn('pemerintahan_daerah', fn($row) => $this->renderNarasiColumn($row['pemerintahan_daerah']))
            ->editColumn('program_pembangunan', fn($row) => $this->renderNarasiColumn($row['program_pembangunan']))
            ->editColumn('pelayanan_publik',    fn($row) => $this->renderNarasiColumn($row['pelayanan_publik']))
            ->editColumn('ideologi',            fn($row) => $this->renderNarasiColumn($row['ideologi']))
            ->editColumn('politik',             fn($row) => $this->renderNarasiColumn($row['politik']))
            ->editColumn('ekonomi',             fn($row) => $this->renderNarasiColumn($row['ekonomi']))
            ->editColumn('sosial_budaya',       fn($row) => $this->renderNarasiColumn($row['sosial_budaya']))
            ->editColumn('hankam',              fn($row) => $this->renderNarasiColumn($row['hankam']))

            ->addColumn('aksi', function ($row) {
                $paramName = str_replace('-', '_', $row['route_base']);
                $showUrl = route($row['route_base'] . '.show', [
                    $paramName => $row['id_laporan'],
                    'from' => 'laporan-bulanan'
                ]);

                return '
                    <a href="' . $showUrl . '" class="btn btn-sm btn-dark-blue bg-gradient-dark-blue">
                        <i class="fa fa-eye"></i> Detail
                    </a>';
            })
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

    /**
     * Helper untuk membersihkan teks dari HTML tags dan spasi berlebih.
     * Digunakan untuk keperluan Search Indexing.
     */
    private function cleanText($text)
    {
        if (empty($text) || trim($text) === '-') {
            return '';
        }
        return trim(strip_tags($text));
    }

    /**
     * Helper untuk merender tampilan kolom narasi (HTML).
     * Menerapkan escaping (e()) untuk mencegah XSS pada atribut title.
     */
    private function renderNarasiColumn($cleanText)
    {
        if (empty($cleanText)) {
            return '<span class="text-muted fst-italic small" style="opacity: 0.7;">-- Nihil --</span>';
        }

        $safeText = e($cleanText);
        $shortText = Str::limit($cleanText, 50);

        return '<span title="' . $safeText . '" style="cursor:help;">' . $shortText . '</span>';
    }
}
