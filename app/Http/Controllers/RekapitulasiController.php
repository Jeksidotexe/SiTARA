<?php

namespace App\Http\Controllers;

use App\Models\LaporanKejadianMenonjol;
use App\Models\LaporanPelanggaranKampanye;
use App\Models\LaporanPenguatanIdeologi;
use App\Models\LaporanPilkadaSerentak;
use App\Models\LaporanSituasiDaerah;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RekapitulasiController extends Controller
{
    // ... (properti $reportModels, $reportTitles, $sectionTitles, $sectionKeys tetap sama)
    protected $reportModels = [
        'situasi_daerah' => LaporanSituasiDaerah::class,
        'pilkada_serentak' => LaporanPilkadaSerentak::class,
        'kejadian_menonjol' => LaporanKejadianMenonjol::class,
        'pelanggaran_kampanye' => LaporanPelanggaranKampanye::class,
        'penguatan_ideologi' => LaporanPenguatanIdeologi::class,
    ];
    protected $reportTitles = [
        'situasi_daerah' => 'Laporan Situasi Daerah',
        'pilkada_serentak' => 'Laporan Pilkada Serentak',
        'kejadian_menonjol' => 'Laporan Kejadian Menonjol',
        'pelanggaran_kampanye' => 'Laporan Pelanggaran Kampanye',
        'penguatan_ideologi' => 'Laporan Penguatan Ideologi',
    ];
    protected $sectionTitles = [
        'a' => 'A. PENYELENGGARAAN PEMERINTAH DAERAH',
        'b' => 'B. PELAKSANAAN PROGRAM PEMBANGUNAN',
        'c' => 'C. PELAYANAN PUBLIK',
        'd' => 'D. IDEOLOGI',
        'e' => 'E. POLITIK',
        'f' => 'F. EKONOMI',
        'g' => 'G. SOSIAL BUDAYA',
        'h' => 'H. HANKAM',
    ];
    protected $sectionKeys = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];

    /**
     * Menampilkan halaman filter untuk cetak laporan gabungan.
     * (Tidak ada perubahan di fungsi index)
     */
    public function index()
    {
        $user = Auth::user();
        $wilayahs = collect();
        $myWilayah = null;

        if ($user->role == 'admin') {
            $wilayahs = Wilayah::orderBy('nama_wilayah')->get();
        } else {
            if ($user->id_wilayah) {
                $myWilayah = Wilayah::find($user->id_wilayah);
            }
        }
        $availableYears = $this->getAvailableYears();

        return view('dashboard.rekapitulasi.index', [
            'reportTitles' => $this->reportTitles,
            'wilayahs' => $wilayahs,
            'myWilayah' => $myWilayah,
            'availableYears' => $availableYears,
        ]);
    }

    /**
     * Membuat dan men-stream PDF laporan gabungan.
     */
    public function cetak(Request $request)
    {
        $user = Auth::user();
        $modelKeys = array_keys($this->reportModels);

        // [UPDATED] Validasi baru tanpa 'filter_mode'
        $rules = [
            'tipe_laporan' => ['required', Rule::in($modelKeys)],
            // Validasi kondisional: 'tanggal' diisi, ATAU ('bulan' DAN 'tahun' diisi)
            'tanggal' => 'nullable|required_without_all:bulan,tahun|date',
            'bulan' => 'nullable|required_without:tanggal|integer|between:1,12',
            'tahun' => 'nullable|required_without:tanggal|integer|min:2020',
        ];

        // Validasi wilayah
        if ($user->role == 'admin') {
            $rules['id_wilayah'] = 'required|exists:wilayah,id_wilayah';
            $id_wilayah = $request->input('id_wilayah');
        } else {
            $id_wilayah = $user->id_wilayah;
            if (!$id_wilayah) {
                return redirect()->back()->with('error', 'Akun Anda tidak terdaftar di wilayah manapun.');
            }
        }

        $validator = Validator::make($request->all(), $rules, [
            'tanggal.required_without_all' => 'Anda harus memilih filter Harian (Tanggal) atau Bulanan (Bulan & Tahun).',
            'bulan.required_without' => 'Untuk filter bulanan, Bulan wajib diisi.',
            'tahun.required_without' => 'Untuk filter bulanan, Tahun wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 1. Ambil Data Wilayah (Kop & TTD)
        $wilayah = Wilayah::find($id_wilayah);
        if (!$wilayah) {
            return redirect()->back()->with('error', 'Wilayah tidak ditemukan.');
        }

        // 2. Tentukan Model Laporan
        $modelKey = $request->input('tipe_laporan');
        $modelClass = $this->reportModels[$modelKey];
        $id_operators = $wilayah->users()->where('role', 'operator')->pluck('id_users');

        // 3. [UPDATED] Logika Query
        $query = $modelClass::whereIn('id_operator', $id_operators)
            ->where('status_laporan', 'disetujui');

        // UTAMAKAN FILTER HARIAN JIKA DIISI
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_laporan', $request->input('tanggal'));
        } else if ($request->filled('bulan') && $request->filled('tahun')) { // JIKA TIDAK, GUNAKAN FILTER BULANAN
            $query->whereYear('tanggal_laporan', $request->input('tahun'))
                ->whereMonth('tanggal_laporan', $request->input('bulan'));
        } else {
            // Ini seharusnya ditangkap oleh validator, tapi sebagai fallback
            return redirect()->back()->with('error', 'Silakan pilih filter tanggal atau filter bulanan.')->withInput();
        }

        $reports = $query->orderBy('tanggal_laporan', 'asc')->get();

        // 4. [UPDATED] Siapkan data filter untuk PDF
        $filters = [
            'tipe_laporan' => $this->reportTitles[$modelKey],
        ];

        $fileNameTgl = now()->format('Y-m-d'); // Bagian nama file

        if ($request->filled('tanggal')) {
            $filters['tanggal'] = Carbon::parse($request->input('tanggal'))->isoFormat('D MMMM YYYY');
            $fileNameTgl = $request->input('tanggal');
        } else if ($request->filled('bulan') && $request->filled('tahun')) {
            $filters['bulan'] = Carbon::create()->month((int) $request->input('bulan'))->isoFormat('MMMM');
            $filters['tahun'] = $request->input('tahun');
            $fileNameTgl = $filters['bulan'] . '-' . $filters['tahun'];
        }

        $data = [
            'wilayah' => $wilayah,
            'reports' => $reports,
            'filters' => $filters,
            'sectionTitles' => $this->sectionTitles,
            'sectionKeys' => $this->sectionKeys,
        ];

        // 5. Generate PDF
        @ini_set('max_execution_time', 300);
        $pdf = App::make('dompdf.wrapper');

        $pdf->loadView('dashboard.rekapitulasi.cetak', $data)
            ->setPaper('a4', 'portrait');

        $fileName = sprintf(
            'Laporan_%s_%s_%s.pdf',
            str_replace(' ', '_', $this->reportTitles[$modelKey]),
            str_replace(' ', '_', $wilayah->nama_wilayah),
            $fileNameTgl
        );

        return $pdf->stream($fileName);
    }


    /**
     * Helper: Mendapatkan daftar tahun yang memiliki laporan.
     */
    private function getAvailableYears(): array
    {
        $years = new \Illuminate\Support\Collection();
        foreach ($this->reportModels as $model) {
            $years = $years->merge(
                $model::selectRaw('YEAR(tanggal_laporan) as year')
                    ->distinct()
                    ->pluck('year')
            );
        }
        $distinctYears = $years->filter()->unique()->sortDesc();
        if ($distinctYears->isEmpty()) {
            return [now()->year];
        }
        return $distinctYears->toArray();
    }
}
