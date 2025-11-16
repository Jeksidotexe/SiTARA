<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\NotifikasiUntukOperator;
use Illuminate\Support\Facades\Log;
use App\Events\LaporanStatusUpdated;
use App\Events\WilayahUpdated;
use App\Models\LaporanSituasiDaerah;
use App\Models\LaporanPilkadaSerentak;
use App\Models\LaporanKejadianMenonjol;
use App\Models\LaporanPelanggaranKampanye;
use App\Models\LaporanPenguatanIdeologi;

class VerificationController extends Controller
{

    /**
     * [SCALABILITY]
     * Peta model yang dapat diverifikasi.
     * Kini berisi nama model, route_base (untuk .show, .edit), dan judul.
     */
    private $verifiableModels = [
        'laporan-situasi-daerah' => [
            'class' => LaporanSituasiDaerah::class,
            'route_base' => 'laporan_situasi_daerah', // akan menjadi 'laporan_situasi_daerah.show'
            'title' => 'Laporan Situasi Daerah'
        ],
        'laporan-pilkada-serentak' => [ // <-- [TAMBAHKAN] Model baru
            'class' => LaporanPilkadaSerentak::class,
            'route_base' => 'laporan_pilkada_serentak', // akan menjadi 'laporan_pilkada_serentak.show'
            'title' => 'Laporan Pilkada Serentak'
        ],
        'laporan-kejadian-menonjol' => [ // <-- [TAMBAHKAN] Model baru
            'class' => LaporanKejadianMenonjol::class,
            'route_base' => 'laporan_kejadian_menonjol', // akan menjadi 'laporan_kejadian_menonjol.show'
            'title' => 'Laporan Kejadian Menonjol'
        ],
        'laporan-pelanggaran-kampanye' => [ // <-- [TAMBAHKAN] Model baru
            'class' => LaporanPelanggaranKampanye::class,
            'route_base' => 'laporan_pelanggaran_kampanye', // akan menjadi 'laporan_pelanggaran_kampanye.show'
            'title' => 'Laporan Pelanggaran Kampanye'
        ],
        'laporan-penguatan-ideologi' => [ // <-- [TAMBAHKAN] Model baru
            'class' => LaporanPenguatanIdeologi::class,
            'route_base' => 'laporan_penguatan_ideologi', // akan menjadi 'laporan_penguatan_ideologi.show'
            'title' => 'Laporan Penguatan Ideologi Pancasila dan Karakter'
        ],
    ];

    /**
     * Menyetujui sebuah laporan.
     *
     * @param string $reportType Tipe laporan (misal: 'laporan-situasi-daerah')
     * @param int $id ID laporan
     * @return RedirectResponse
     */
    public function approve(Request $request, string $reportType, int $id): RedirectResponse
    {
        $report = $this->findReportOrFail($reportType, $id);
        $reportConfig = $this->verifiableModels[$reportType] ?? null;

        // Optional: Authorization check (pastikan user adalah Pimpinan)
        if (Auth::user()->role !== 'pimpinan') {
            abort(403, 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        if ($report->approve()) {
            // === [KODE YANG PERLU DITAMBAHKAN] ===
            // === AWAL BLOK NOTIFIKASI APPROVE ===
            try {
                $report->load('operator'); // Pastikan relasi operator ter-load
                if ($report->operator) {
                    // 1. Ubah Pesannya
                    $tanggalFormatted = $report->tanggal_laporan?->isoFormat('D MMMM YYYY') ?? '';
                    $message = 'Laporan Anda "' . Str::limit(strip_tags($report->judul), 50) . '" Tanggal ' . $tanggalFormatted . ' telah disetujui.';
                    // [SCALABILITY] Buat URL dinamis
                    $url = '#'; // Fallback
                    if ($reportConfig) {
                        $paramName = Str::singular(str_replace('-', '_', $reportType));
                        $url = route($reportConfig['route_base'] . '.show', [$paramName => $report->id_laporan, 'from' => 'notification']);
                    }

                    $report->operator->notify(new NotifikasiUntukOperator($report, $message, $url));
                    LaporanStatusUpdated::dispatch($report->id_laporan, $report->status_laporan);
                }
            } catch (\Exception $e) {
                // Catat error jika gagal kirim notifikasi
                Log::error('Gagal mengirim notifikasi approve ke operator: ' . $e->getMessage());
            }
            // === AKHIR BLOK NOTIFIKASI APPROVE ===
            // === [AKHIR KODE TAMBAHAN] ===

            // === [MODIFIKASI LOGIKA WILAYAH] ===
            $newStatus = $request->input('status_wilayah_baru');
            if ($newStatus) {
                try {
                    $report->load('operator.wilayah');

                    if (!$report->operator) {
                        // GAGAL: Laporan tidak punya operator
                        return redirect()->back()->with('error', 'Update Gagal: Relasi operator tidak ditemukan.');
                    }

                    if (!$report->operator->wilayah) {
                        // GAGAL: Operator tidak punya wilayah
                        return redirect()->back()->with('error', 'Update Gagal: Operator tidak memiliki data wilayah.');
                    }

                    // [PERBAIKAN]
                    // 1. Ambil model wilayah
                    $wilayah = $report->operator->wilayah;

                    // 2. Update status. Observer akan otomatis set 'status_wilayah_updated_at'
                    $wilayah->update(['status_wilayah' => $newStatus]);

                    // 3. Dispatch event DENGAN model wilayah (ini FIX-nya)
                    WilayahUpdated::dispatch($wilayah, 'updated');
                    // [AKHIR PERBAIKAN]

                    // Jika berhasil, ubah pesan sukses
                    $successMessage = 'Laporan disetujui DAN status wilayah berhasil diperbarui.';
                } catch (\Exception $e) {
                    Log::error('Gagal update status wilayah: ' . $e->getMessage());
                    // GAGAL: Ada error database
                    return redirect()->back()->with('error', 'Update Gagal: Terjadi error. Cek log.');
                }
            }
            // === [AKHIR MODIFIKASI] ===

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
            'catatan' => 'required|string|max:5000', // Sesuaikan max length
        ]);

        $report = $this->findReportOrFail($reportType, $id);

        // [PERBAIKAN] Ambil konfigurasi model di sini agar bisa dipakai di notifikasi
        $reportConfig = $this->verifiableModels[$reportType] ?? null;

        // Optional: Authorization check
        if (Auth::user()->role !== 'pimpinan') {
            abort(403, 'Anda tidak memiliki izin untuk melakukan tindakan ini.');
        }

        if ($report->requestRevision($request->input('catatan'))) {
            // === AWAL BLOK NOTIFIKASI REVISI ===
            try {
                $report->load('operator'); // Pastikan relasi operator ter-load
                if ($report->operator) {
                    $tanggalFormatted = $report->tanggal_laporan?->isoFormat('D MMMM YYYY') ?? '';
                    $message = 'Laporan Anda "' . Str::limit(strip_tags($report->judul), 50) . '" Tanggal ' . $tanggalFormatted . ' perlu direvisi.';

                    // [PERBAIKAN] Buat URL dinamis berdasarkan $reportType, sama seperti di fungsi approve()
                    $url = '#'; // Fallback
                    if ($reportConfig) {
                        // Dapatkan nama parameter route (cth: 'laporan_pilkada_serentak')
                        $paramName = Str::singular(str_replace('-', '_', $reportType));
                        // Buat route ke halaman .edit (cth: 'laporan_pilkada_serentak.edit')
                        $url = route($reportConfig['route_base'] . '.show', [$paramName => $report->id_laporan, 'from' => 'notification']);
                    }
                    // [AKHIR PERBAIKAN]

                    $report->operator->notify(new NotifikasiUntukOperator($report, $message, $url));
                    LaporanStatusUpdated::dispatch($report->id_laporan, $report->status_laporan);
                }
            } catch (\Exception $e) {
                Log::error('Gagal mengirim notifikasi revisi: ' . $e->getMessage());
            }
            // === AKHIR BLOK NOTIFIKASI REVISI ===
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
        // [PERBAIKAN] Hapus deklarasi array $verifiableModels lokal dari sini.
        // Kita akan langsung menggunakan properti kelas '$this->verifiableModels'.

        // [PERBAIKAN] Ganti '$verifiableModels' menjadi '$this->verifiableModels'
        if (!isset($this->verifiableModels[$reportType])) {
            abort(404, 'Tipe laporan tidak ditemukan.');
        }

        // [PERBAIKAN] Ambil 'class' dari properti kelas '$this->verifiableModels'
        // Baris ini sekarang akan aman karena $this->verifiableModels[$reportType] adalah array.
        $modelClass = $this->verifiableModels[$reportType]['class'];

        // Pastikan model menggunakan trait Verifiable (opsional tapi baik)
        if (!in_array(\App\Traits\Verifiable::class, class_uses_recursive($modelClass))) {
            abort(500, 'Model tidak dapat diverifikasi.');
        }

        return $modelClass::findOrFail($id);
    }

    /**
     * Menampilkan daftar laporan yang menunggu verifikasi.
     */
    public function pendingList()
    {
        // Ambil data count untuk ringkasan (opsional)
        // $pendingCount = LaporanSituasiDaerah::query()->pendingVerification()->count();
        // $types = ['Laporan Situasi Daerah']; // Nanti bisa dinamis

        // Return view, data bisa diambil via AJAX DataTables atau langsung
        return view('dashboard.verifikasi.pending'); // Buat view ini
    }

    /**
     * Menyediakan data untuk DataTables halaman pending verification.
     */
    public function pendingData()
    {
        // [PERBAIKAN] Ambil ID Wilayah Pimpinan
        $pimpinanWilayahId = Auth::user()->id_wilayah;
        $queries = [];
        if ($pimpinanWilayahId) {
            foreach ($this->verifiableModels as $key => $config) {
                $queries[] = $config['class']::with('operator')
                    // [PERBAIKAN] Filter berdasarkan wilayah operator
                    ->whereHas('operator', function ($query) use ($pimpinanWilayahId) {
                        $query->where('id_wilayah', $pimpinanWilayahId);
                    })
                    ->pendingVerification()
                    ->select([
                        'id_laporan',
                        'judul',
                        'id_operator',
                        'tanggal_laporan',
                        'created_at',
                        \Illuminate\Support\Facades\DB::raw("'$key' as report_type_key") // Tambahkan tipe laporan
                    ]);
            }
        }

        // Gabungkan query pertama dengan sisanya
        $query = array_shift($queries);
        if ($query) {
            foreach ($queries as $q) {
                $query->unionAll($q);
            }
        }

        $verifiableModelsMap = $this->verifiableModels; // Untuk digunakan di dalam closure

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('report_type', function ($row) use ($verifiableModelsMap) {
                return $verifiableModelsMap[$row->report_type_key]['title'] ?? 'Laporan';
            })
            ->addColumn('operator_name', function ($row) {
                return $row->operator->nama ?? '<span class="text-danger fst-italic">N/A</span>';
            })
            ->editColumn('tanggal_laporan', function ($row) {
                return \Carbon\Carbon::parse($row->tanggal_laporan)->isoFormat('D MMMM YYYY');
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->isoFormat('D MMMM YY, HH:mm');
            })
            ->addColumn('action', function ($row) use ($verifiableModelsMap) {
                $config = $verifiableModelsMap[$row->report_type_key] ?? null;
                if (!$config) return '-';
                $paramName = str_replace('-', '_', $row->report_type_key);
                $showUrl = route($config['route_base'] . '.show', [$paramName => $row->id_laporan, 'from' => 'pending']);
                return '<a href="' . $showUrl . '" class="btn btn-sm btn-dark-blue bg-gradient-dark-blue"><i class="fa fa-search me-1"></i> Periksa</a>';
            })
            ->rawColumns(['operator_name', 'action'])
            ->toJson();
    }


    /**
     * Menampilkan daftar riwayat laporan yang sudah diverifikasi pimpinan.
     */
    public function historyList()
    {
        // Return view, data bisa diambil via AJAX DataTables atau langsung
        return view('dashboard.verifikasi.history'); // Buat view ini
    }

    /**
     * Menyediakan data untuk DataTables halaman history verification.
     */
    public function historyData()
    {
        $pimpinanId = Auth::id();
        // [PERBAIKAN] Ambil ID Wilayah Pimpinan
        $pimpinanWilayahId = Auth::user()->id_wilayah;
        $queries = [];
        if ($pimpinanWilayahId) {
            foreach ($this->verifiableModels as $key => $config) {
                $queries[] = $config['class']::with('operator')
                    ->where('id_pimpinan', $pimpinanId)
                    ->whereIn('status_laporan', ['disetujui', 'revisi'])
                    // [PERBAIKAN] DAN filter berdasarkan wilayah operator
                    ->whereHas('operator', function ($query) use ($pimpinanWilayahId) {
                        $query->where('id_wilayah', $pimpinanWilayahId);
                    })
                    ->select([
                        'id_laporan',
                        'judul',
                        'id_operator',
                        'tanggal_laporan',
                        'verified_at',
                        'status_laporan',
                        \Illuminate\Support\Facades\DB::raw("'$key' as report_type_key")
                    ]);
            }
        }

        $query = array_shift($queries);
        if ($query) {
            foreach ($queries as $q) {
                $query->unionAll($q);
            }
        } else {
            // [PERBAIKAN] Jika tidak ada query (Pimpinan tdk punya wilayah), buat query kosong
            $query = collect();
        }

        // Pastikan query ada sebelum memanggil latest()
        if ($query instanceof \Illuminate\Database\Query\Builder || $query instanceof \Illuminate\Database\Eloquent\Builder) {
            $query->latest('verified_at'); // Urutkan setelah union
        }

        $verifiableModelsMap = $this->verifiableModels;

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('report_type', function ($row) use ($verifiableModelsMap) {
                return $verifiableModelsMap[$row->report_type_key]['title'] ?? 'Laporan';
            })
            ->addColumn('operator_name', function ($row) {
                return $row->operator->nama ?? '<span class="text-danger fst-italic">N/A</span>';
            })
            ->editColumn('tanggal_laporan', function ($row) {
                return \Carbon\Carbon::parse($row->tanggal_laporan)->isoFormat('D MMMM YYYY');
            })
            ->editColumn('verified_at', function ($row) {
                // Gunakan format yang menyertakan jam, karena verifikasi perlu jam
                return \Carbon\Carbon::parse($row->verified_at)->isoFormat('D MMMM YYYY, HH:mm');
            })
            ->editColumn('created_at', function ($row) {
                return \Carbon\Carbon::parse($row->created_at)->isoFormat('D MMMM YY, HH:mm');
            })
            ->addColumn('action', function ($row) use ($verifiableModelsMap) {
                $config = $verifiableModelsMap[$row->report_type_key] ?? null;
                if (!$config) return '-';
                $paramName = str_replace('-', '_', $row->report_type_key);
                $showUrl = route($config['route_base'] . '.show', [$paramName => $row->id_laporan, 'from' => 'history']);
                return '<a href="' . $showUrl . '" class="btn btn-sm btn-dark-blue bg-gradient-dark-blue"><i class="fa fa-search me-1"></i> Lihat</a>';
            })
            ->rawColumns(['operator_name', 'action'])
            ->toJson();
    }
}
