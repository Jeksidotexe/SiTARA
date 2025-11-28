<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PengaturanWilayahController;
use App\Http\Controllers\DaftarLaporanBulananController;
use App\Http\Controllers\LaporanSituasiDaerahController;
use App\Http\Controllers\LaporanPilkadaSerentakController;
use App\Http\Controllers\LaporanKejadianMenonjolController;
use App\Http\Controllers\LaporanPenguatanIdeologiController;
use App\Http\Controllers\LaporanPelanggaranKampanyeController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])
    ->middleware('guest')
    ->name('password.request');

Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->middleware('guest')
    ->name('password.email');

Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('guest')
    ->name('password.update');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');

    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-one-as-read', [NotificationController::class, 'markOneAsRead'])
        ->name('notifications.markOneAsRead');

    Route::post('/wilayah/tindak-lanjut', [WilayahController::class, 'tindakLanjut'])
        ->name('wilayah.tindakLanjut');

    // --- Rute Admin ---
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/pengguna/data', [PenggunaController::class, 'data'])->name('pengguna.data');
        Route::resource('/pengguna', PenggunaController::class);
        Route::get('/wilayah/data', [WilayahController::class, 'data'])->name('wilayah.data');
        Route::resource('/wilayah', WilayahController::class);
        Route::get('/dashboard/map-data', [DashboardController::class, 'getMapData'])->name('dashboard.mapData');

        // Daftar Laporan Bulanan
        Route::prefix('dashboard/laporan-bulanan')->name('laporan-bulanan.')->group(function () {
            Route::get('/', [DaftarLaporanBulananController::class, 'index'])->name('index'); // Daftar Wilayah
            Route::get('/{wilayah}', [DaftarLaporanBulananController::class, 'showMonths'])->name('months'); // Daftar Bulan per Wilayah
            Route::get('/{wilayah}/{month}', [DaftarLaporanBulananController::class, 'showReports'])->name('reports'); // Halaman Tabel Laporan
            Route::get('/{wilayah}/{month}/data', [DaftarLaporanBulananController::class, 'data'])->name('data'); // Endpoint AJAX DataTables
        });
        // --- REKAPITULASI LAPORAN ---
        Route::prefix('dashboard/rekapitulasi')->name('rekapitulasi.')->group(function () {
            // Arahkan kedua menu ke halaman filter yang sama
            Route::get('/laporan', [RekapitulasiController::class, 'index'])->name('laporan');

            // Rute baru untuk memproses cetak PDF
            Route::get('/cetak', [RekapitulasiController::class, 'cetak'])->name('cetak');
        });
    });

    // --- Rute Operator ---
    Route::middleware(['role:operator'])->group(function () {
        Route::get('laporan_situasi_daerah/data', [LaporanSituasiDaerahController::class, 'data'])->name('laporan_situasi_daerah.data');
        Route::resource('laporan_situasi_daerah', LaporanSituasiDaerahController::class)->except(['show']);

        Route::get('laporan_pilkada_serentak/data', [LaporanPilkadaSerentakController::class, 'data'])->name('laporan_pilkada_serentak.data');
        Route::resource('laporan_pilkada_serentak', LaporanPilkadaSerentakController::class)->except(['show']);

        Route::get('laporan_kejadian_menonjol/data', [LaporanKejadianMenonjolController::class, 'data'])->name('laporan_kejadian_menonjol.data');
        Route::resource('laporan_kejadian_menonjol', LaporanKejadianMenonjolController::class)->except(['show']);

        Route::get('laporan_pelanggaran_kampanye/data', [LaporanPelanggaranKampanyeController::class, 'data'])->name('laporan_pelanggaran_kampanye.data');
        Route::resource('laporan_pelanggaran_kampanye', LaporanPelanggaranKampanyeController::class)->except(['show']);

        Route::get('laporan_penguatan_ideologi/data', [LaporanPenguatanIdeologiController::class, 'data'])->name('laporan_penguatan_ideologi.data');
        Route::resource('laporan_penguatan_ideologi', LaporanPenguatanIdeologiController::class)->except(['show']);

        // --- RUTE PENGATURAN WILAYAH ---
        Route::get('/pengaturan-wilayah/show', [PengaturanWilayahController::class, 'show'])
            ->name('pengaturan-wilayah.show');
        Route::post('/pengaturan-wilayah/update', [PengaturanWilayahController::class, 'update'])
            ->name('pengaturan-wilayah.update');
    });

    // --- Rute Pimpinan ---
    Route::middleware(['role:pimpinan'])->prefix('verifikasi')->name('verifikasi.')->group(function () {
        // Halaman daftar laporan menunggu verifikasi
        Route::get('/pending', [VerificationController::class, 'pendingList'])->name('pending');
        // Halaman daftar riwayat verifikasi
        Route::get('/history', [VerificationController::class, 'historyList'])->name('history');

        // Aksi verifikasi (sudah ada)
        Route::post('/{reportType}/{id}/approve', [VerificationController::class, 'approve'])->name('approve');
        Route::post('/{reportType}/{id}/revisi', [VerificationController::class, 'requestRevision'])->name('requestRevision');

        // Route DataTables untuk halaman pending & history (opsional)
        Route::get('/pending/data', [VerificationController::class, 'pendingData'])->name('pending.data');
        Route::get('/history/data', [VerificationController::class, 'historyData'])->name('history.data');
    });

    // --- Rute Umum ---
    Route::get('laporan_situasi_daerah/{laporan_situasi_daerah}', [LaporanSituasiDaerahController::class, 'show'])->name('laporan_situasi_daerah.show');
    Route::get('laporan_pilkada_serentak/{laporan_pilkada_serentak}', [LaporanPilkadaSerentakController::class, 'show'])->name('laporan_pilkada_serentak.show');
    Route::get('laporan_kejadian_menonjol/{laporan_kejadian_menonjol}', [LaporanKejadianMenonjolController::class, 'show'])->name('laporan_kejadian_menonjol.show');
    Route::get('laporan_pelanggaran_kampanye/{laporan_pelanggaran_kampanye}', [LaporanPelanggaranKampanyeController::class, 'show'])->name('laporan_pelanggaran_kampanye.show');
    Route::get('laporan_penguatan_ideologi/{laporan_penguatan_ideologi}', [LaporanPenguatanIdeologiController::class, 'show'])->name('laporan_penguatan_ideologi.show');

    Route::get('laporan_situasi_daerah/{id}/preview-pdf', [LaporanSituasiDaerahController::class, 'previewPdf'])
        ->name('laporan_situasi_daerah.previewPdf');
    Route::get('laporan_pilkada_serentak/{id}/preview-pdf', [LaporanPilkadaSerentakController::class, 'previewPdf'])
        ->name('laporan_pilkada_serentak.previewPdf');
    Route::get('laporan_kejadian_menonjol/{id}/preview-pdf', [LaporanKejadianMenonjolController::class, 'previewPdf'])
        ->name('laporan_kejadian_menonjol.previewPdf');
    Route::get('laporan_pelanggaran_kampanye/{id}/preview-pdf', [LaporanPelanggaranKampanyeController::class, 'previewPdf'])
        ->name('laporan_pelanggaran_kampanye.previewPdf');
    Route::get('laporan_penguatan_ideologi/{id}/preview-pdf', [LaporanPenguatanIdeologiController::class, 'previewPdf'])
        ->name('laporan_penguatan_ideologi.previewPdf');
});
