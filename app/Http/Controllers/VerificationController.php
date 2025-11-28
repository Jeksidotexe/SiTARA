<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\WilayahUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\LaporanStatusUpdated;
use App\Models\LaporanSituasiDaerah;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use App\Models\LaporanPilkadaSerentak;
use App\Models\LaporanKejadianMenonjol;
use App\Models\LaporanPenguatanIdeologi;
use Yajra\DataTables\Facades\DataTables;
use App\Models\LaporanPelanggaranKampanye;
use App\Notifications\NotifikasiUntukOperator;

class VerificationController extends Controller
{

    /**
     * Peta model yang dapat diverifikasi.
     * Kini berisi nama model, route_base (untuk .show, .edit), dan judul.
     */
    private $verifiableModels = [
        'laporan-situasi-daerah' => [
            'class' => LaporanSituasiDaerah::class,
            'route_base' => 'laporan_situasi_daerah',
            'title' => 'Laporan Situasi Daerah'
        ],
        'laporan-pilkada-serentak' => [
            'class' => LaporanPilkadaSerentak::class,
            'route_base' => 'laporan_pilkada_serentak',
            'title' => 'Laporan Pilkada Serentak'
        ],
        'laporan-kejadian-menonjol' => [
            'class' => LaporanKejadianMenonjol::class,
            'route_base' => 'laporan_kejadian_menonjol',
            'title' => 'Laporan Kejadian Menonjol'
        ],
        'laporan-pelanggaran-kampanye' => [
            'class' => LaporanPelanggaranKampanye::class,
            'route_base' => 'laporan_pelanggaran_kampanye',
            'title' => 'Laporan Pelanggaran Kampanye'
        ],
        'laporan-penguatan-ideologi' => [
            'class' => LaporanPenguatanIdeologi::class,
            'route_base' => 'laporan_penguatan_ideologi',
            'title' => 'Laporan Penguatan Ideologi Pancasila dan Karakter'
        ],
    ];

    /**
     * Menyetujui sebuah laporan.
     *
     * @param string $reportType
     * @param int $id ID laporan
     * @return RedirectResponse
     */
    public function approve(Request $request, string $reportType, int $id): RedirectResponse
    {
        $report = $this->findReportOrFail($reportType, $id);
        $reportConfig = $this->verifiableModels[$reportType] ?? null;

        if (Auth::user()->role !== 'pimpinan') {
            abort(403, 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        if ($report->approve()) {
            try {
                $report->load('operator');
                if ($report->operator) {
                    $tanggalFormatted = $report->tanggal_laporan?->isoFormat('D MMMM YYYY') ?? '';
                    $message = 'Laporan Anda "' . Str::limit(strip_tags($report->judul), 50) . '" Tanggal ' . $tanggalFormatted . ' telah disetujui.';

                    $url = '#';
                    if ($reportConfig) {
                        $paramName = Str::singular(str_replace('-', '_', $reportType));
                        $url = route($reportConfig['route_base'] . '.show', [$paramName => $report->id_laporan, 'from' => 'notification']);
                    }

                    $report->operator->notify(new NotifikasiUntukOperator($report, $message, $url));
                    LaporanStatusUpdated::dispatch($report->id_laporan, $report->status_laporan);
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi approve ke operator: ' . $e->getMessage());
            }
            $newStatus = $request->input('status_wilayah_baru');
            if ($newStatus) {
                try {
                    $report->load('operator.wilayah');

                    if (!$report->operator) {
                        return redirect()->back()->with('error', 'Update Gagal: Relasi operator tidak ditemukan.');
                    }

                    if (!$report->operator->wilayah) {
                        return redirect()->back()->with('error', 'Update Gagal: Operator tidak memiliki data wilayah.');
                    }

                    $wilayah = $report->operator->wilayah;

                    $wilayah->update(['status_wilayah' => $newStatus]);

                    WilayahUpdated::dispatch($wilayah, 'updated');

                    $successMessage = 'Laporan disetujui DAN status wilayah berhasil diperbarui.';
                } catch (\Exception $e) {
                    Log::error('Gagal update status wilayah: ' . $e->getMessage());
                    return redirect()->back()->with('error', 'Update Gagal: Terjadi error. Cek log.');
                }
            }

            try {
                if (Auth::user()->role == 'pimpinan' && Auth::user()->id_wilayah) {
                    $cacheKey = 'pending_count_pimpinan_' . Auth::user()->id_wilayah;
                    Cache::forget($cacheKey);
                }
            } catch (\Exception $e) {
                Log::error('Gagal membersihkan cache pending count: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Laporan berhasil disetujui.');
        }

        return redirect()->back()->with('error', 'Gagal menyetujui laporan.');
    }

    /**
     * Meminta revisi untuk sebuah laporan.
     *
     * @param Request $request
     * @param string $reportType Tipe laporan
     * @param int $id ID laporan
     * @return RedirectResponse
     */
    public function requestRevision(Request $request, string $reportType, int $id): RedirectResponse
    {
        $request->validate([
            'catatan' => 'required|string|max:5000',
        ]);

        $report = $this->findReportOrFail($reportType, $id);

        $reportConfig = $this->verifiableModels[$reportType] ?? null;

        if (Auth::user()->role !== 'pimpinan') {
            abort(403, 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        if ($report->requestRevision($request->input('catatan'))) {
            try {
                $report->load('operator');
                if ($report->operator) {
                    $tanggalFormatted = $report->tanggal_laporan?->isoFormat('D MMMM YYYY') ?? '';
                    $message = 'Laporan Anda "' . Str::limit(strip_tags($report->judul), 50) . '" Tanggal ' . $tanggalFormatted . ' perlu direvisi.';

                    $url = '#';
                    if ($reportConfig) {
                        $paramName = Str::singular(str_replace('-', '_', $reportType));
                        $url = route($reportConfig['route_base'] . '.show', [$paramName => $report->id_laporan, 'from' => 'notification']);
                    }

                    $report->operator->notify(new NotifikasiUntukOperator($report, $message, $url));
                    LaporanStatusUpdated::dispatch($report->id_laporan, $report->status_laporan);
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi revisi: ' . $e->getMessage());
            }

            try {
                if (Auth::user()->role == 'pimpinan' && Auth::user()->id_wilayah) {
                    $cacheKey = 'pending_count_pimpinan_' . Auth::user()->id_wilayah;
                    Cache::forget($cacheKey);
                }
            } catch (\Exception $e) {
                Log::error('Gagal membersihkan cache pending count: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Permintaan revisi berhasil dikirim.');
        }

        return redirect()->back()->with('error', 'Gagal meminta revisi laporan.');
    }

    /**
     * Helper untuk mencari model laporan berdasarkan tipe dan ID.
     * Ini bagian penting untuk skalabilitas.
     *
     * @param string $reportType
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model & \App\Traits\Verifiable
     */
    private function findReportOrFail(string $reportType, int $id)
    {
        if (!isset($this->verifiableModels[$reportType])) {
            abort(404, 'Tipe laporan tidak ditemukan.');
        }

        $modelClass = $this->verifiableModels[$reportType]['class'];

        if (!in_array(\App\Traits\Verifiable::class, class_uses_recursive($modelClass))) {
            abort(500, 'Model tidak dapat diverifikasi.');
        }

        return $modelClass::findOrFail($id);
    }


    /**
     * Helper untuk membangun query Union dengan Join Operator.
     * Ini penting agar kolom 'operator_name' bisa dicari (searchable).
     */
    private function getMappedQueries($pimpinanWilayahId, $status = 'pending', $pimpinanId = null)
    {
        $queries = [];

        foreach ($this->verifiableModels as $key => $config) {
            $model = new $config['class'];
            $tableName = $model->getTable();

            $q = $config['class']::query()
                ->leftJoin('users', 'users.id_users', '=', "{$tableName}.id_operator")
                ->select([
                    "{$tableName}.id_laporan",
                    "{$tableName}.judul",
                    "{$tableName}.tanggal_laporan",
                    "{$tableName}.status_laporan",
                    "users.nama as operator_name",
                    DB::raw("'$key' as report_type_key")
                ]);

            if ($status === 'history') {
                $q->addSelect("{$tableName}.verified_at");
                $q->addSelect("{$tableName}.id_pimpinan");
            }

            if ($pimpinanWilayahId) {
                $q->where('users.id_wilayah', $pimpinanWilayahId);
            }

            if ($status === 'pending') {
                $q->whereNotIn("{$tableName}.status_laporan", ['disetujui', 'revisi']);
            } elseif ($status === 'history' && $pimpinanId) {
                $q->where("{$tableName}.id_pimpinan", $pimpinanId)
                    ->whereIn("{$tableName}.status_laporan", ['disetujui', 'revisi']);
            }

            $queries[] = $q;
        }

        return $queries;
    }

    /**
     * Menampilkan daftar laporan yang menunggu verifikasi.
     */
    public function pendingList()
    {

        return view('dashboard.verifikasi.pending');
    }

    /**
     * Menyediakan data untuk DataTables halaman pending verification.
     */
    public function pendingData()
    {
        $pimpinanWilayahId = Auth::user()->id_wilayah;

        $queries = $this->getMappedQueries($pimpinanWilayahId, 'pending');

        // Gabungkan Query
        $unionQuery = array_shift($queries);
        if ($unionQuery) {
            foreach ($queries as $q) {
                $unionQuery->unionAll($q);
            }
        } else {
            $unionQuery = \App\Models\User::whereRaw('1=0');
        }

        $wrappedQuery = DB::query()->fromSub($unionQuery, 'combined_table');

        $verifiableModelsMap = $this->verifiableModels;

        return DataTables::of($wrappedQuery)
            ->addIndexColumn()
            ->addColumn('report_type', function ($row) use ($verifiableModelsMap) {
                return $verifiableModelsMap[$row->report_type_key]['title'] ?? 'Laporan';
            })
            ->filterColumn('report_type', function ($query, $keyword) use ($verifiableModelsMap) {
                $matchedKeys = [];
                foreach ($verifiableModelsMap as $key => $config) {
                    if (stripos($config['title'], $keyword) !== false) {
                        $matchedKeys[] = $key;
                    }
                }
                if (!empty($matchedKeys)) {
                    $query->whereIn('report_type_key', $matchedKeys);
                } else {
                    $query->whereRaw('1 = 0');
                }
            })
            ->addColumn('operator_name', function ($row) {
                return $row->operator_name ?? '<span class="text-danger fst-italic">N/A</span>';
            })
            ->filterColumn('operator_name', function ($query, $keyword) {
                $query->where('operator_name', 'like', "%{$keyword}%");
            })
            ->editColumn('tanggal_laporan', function ($row) {
                return \Carbon\Carbon::parse($row->tanggal_laporan)->isoFormat('D MMMM YYYY');
            })
            ->filterColumn('tanggal_laporan', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(tanggal_laporan,'%d %M %Y') like ?", ["%{$keyword}%"]);
            })
            ->addColumn('action', function ($row) use ($verifiableModelsMap) {
                $config = $verifiableModelsMap[$row->report_type_key] ?? null;
                if (!$config) return '-';
                $paramName = str_replace('-', '_', $row->report_type_key);
                $showUrl = route($config['route_base'] . '.show', [$paramName => $row->id_laporan, 'from' => 'pending']);
                return '<a href="' . $showUrl . '" class="btn btn-sm btn-dark-blue bg-gradient-dark-blue"><i class="fa fa-search me-1"></i> Periksa</a>';
            })
            ->rawColumns(['operator_name', 'action'])
            ->make(true);
    }

    /**
     * Menampilkan daftar riwayat laporan yang sudah diverifikasi pimpinan.
     */
    public function historyList()
    {
        return view('dashboard.verifikasi.history');
    }

    /**
     * Menyediakan data untuk DataTables halaman history verification.
     */
    public function historyData()
    {
        $pimpinanId = Auth::id();
        $pimpinanWilayahId = Auth::user()->id_wilayah;

        $queries = $this->getMappedQueries($pimpinanWilayahId, 'history', $pimpinanId);

        $unionQuery = array_shift($queries);
        if ($unionQuery) {
            foreach ($queries as $q) {
                $unionQuery->unionAll($q);
            }
        } else {
            $unionQuery = \App\Models\User::selectRaw('null')->whereRaw('1=0');
        }

        $wrappedQuery = DB::query()->fromSub($unionQuery, 'combined_table');

        $wrappedQuery->orderBy('verified_at', 'desc');

        $verifiableModelsMap = $this->verifiableModels;

        return DataTables::of($wrappedQuery)
            ->addIndexColumn()
            ->addColumn('report_type', function ($row) use ($verifiableModelsMap) {
                return $verifiableModelsMap[$row->report_type_key]['title'] ?? 'Laporan';
            })
            ->filterColumn('report_type', function ($query, $keyword) use ($verifiableModelsMap) {
                $matchedKeys = [];
                foreach ($verifiableModelsMap as $key => $config) {
                    if (stripos($config['title'], $keyword) !== false) $matchedKeys[] = $key;
                }
                if (!empty($matchedKeys)) {
                    $query->whereIn('report_type_key', $matchedKeys);
                } else {
                    $query->whereRaw('1 = 0');
                }
            })
            ->addColumn('operator_name', function ($row) {
                return $row->operator_name ?? 'N/A';
            })
            ->filterColumn('operator_name', function ($query, $keyword) {
                $query->where('operator_name', 'like', "%{$keyword}%");
            })
            ->editColumn('tanggal_laporan', function ($row) {
                return \Carbon\Carbon::parse($row->tanggal_laporan)->isoFormat('D MMMM YYYY');
            })
            ->filterColumn('tanggal_laporan', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(tanggal_laporan,'%d %M %Y') like ?", ["%{$keyword}%"]);
            })
            ->editColumn('verified_at', function ($row) {
                return \Carbon\Carbon::parse($row->verified_at)->isoFormat('D MMMM YYYY');
            })
            ->filterColumn('verified_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(verified_at,'%d %M %Y') like ?", ["%{$keyword}%"]);
            })
            ->editColumn('status_laporan', function ($laporan) {
                if ($laporan->status_laporan == 'disetujui') {
                    return '<span class="badge badge-sm bg-gradient-success"><i class="fas fa-circle-check"></i> Disetujui</span>';
                } elseif ($laporan->status_laporan == 'revisi') {
                    return '<span class="badge badge-sm bg-gradient-warning"><i class="fas fa-exclamation-triangle"></i> Revisi</span>';
                }
                return $laporan->status_laporan;
            })
            ->filterColumn('status_laporan', function ($query, $keyword) {
                $query->where('status_laporan', 'like', "%{$keyword}%");
            })
            ->addColumn('action', function ($row) use ($verifiableModelsMap) {
                $config = $verifiableModelsMap[$row->report_type_key] ?? null;
                if (!$config) return '-';
                $paramName = str_replace('-', '_', $row->report_type_key);
                $showUrl = route($config['route_base'] . '.show', [$paramName => $row->id_laporan, 'from' => 'history']);
                return '<a href="' . $showUrl . '" class="btn btn-sm btn-dark-blue bg-gradient-dark-blue"><i class="fa fa-search me-1"></i> Lihat</a>';
            })
            ->rawColumns(['operator_name', 'status_laporan', 'action'])
            ->make(true);
    }
}
