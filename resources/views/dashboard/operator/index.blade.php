@extends('layouts.master')
@section('title', 'Dashboard')
@section('page', 'Home')

@section('content')
    <div class="row mb-4">
        <div class="col-lg-12">
            <h3 class="font-weight-bolder text-uppercase mb-1">Operator Pemerintah {{ Auth::user()->wilayah->nama_wilayah }}
            </h3>
            <p class="text-muted text-sm">Pastikan Anda mengikuti tata cara yang benar dalam pengisian data.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-lg-0 mb-4">
            <div class="card card-body border-radius-lg shadow-sm mb-4">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-muted">Total Laporan</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $totalLaporanOperator ?? 0 }}
                                <span class="text-sm font-weight-light text-muted ms-1">Laporan</span>
                            </h5>
                        </div>
                        <hr class="dark horizontal my-2">
                        <p class="mb-0 pt-1 text-sm text-secondary">Semua tipe laporan.</p>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded text-lg opacity-10">list_alt</i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-body border-radius-lg shadow-sm mb-4">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-muted">Menunggu Verifikasi</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $laporanMenunggu ?? 0 }}
                                <span class="text-sm font-weight-light text-muted ms-1">Laporan</span>
                            </h5>
                        </div>
                        <hr class="dark horizontal my-2">
                        <p class="mb-0 pt-1 text-sm text-secondary">Laporan menunggu verifikasi.</p>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded text-lg opacity-10">pending_actions</i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-body border-radius-lg shadow-sm">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-muted">Laporan Perlu Revisi</p>
                            <h5 class="font-weight-bolder mb-0 text-dark">
                                <span id="operator-revisi-count">{{ $laporanRevisi->count() ?? 0 }}</span>
                                <span class="text-sm font-weight-light text-muted ms-1">Laporan</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded text-lg opacity-10">edit_note</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-2">
                <a class="text-sm text-dark font-weight-bold mb-0 pt-1" href="#laporan-revisi-list">
                    Lihat Laporan Revisi <i class="fas fa-arrow-circle-down ms-1"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card shadow-sm h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="mb-0 font-weight-bold d-flex align-items-center">
                        <i class="material-symbols-rounded text-dark me-2 fs-5">bar_chart</i>
                        Jumlah Laporan per Bulan ({{ now()->year }})
                    </h6>
                    <p class="text-sm text-muted mt-1 mb-0">
                        Grafik total laporan yang Anda buat setiap bulan.
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart h-100">
                        <canvas id="chart-laporan-bulanan" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-6 mb-lg-0 mb-4">
            <div class="card shadow-sm h-100 scroll-smooth" id="laporan-revisi-list">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0 font-weight-bold">Laporan Perlu Revisi</h6>
                        <span class="badge bg-gradient-warning text-xxs px-2 rounded-pill mb-1">
                            {{ $laporanRevisi->count() }} Pending
                        </span>
                    </div>
                </div>
                <div class="card-body p-3" style="max-height: 450px; overflow-y: auto;">

                    @if (!isset($laporanRevisi) || $laporanRevisi->isEmpty())
                        <div class="text-center py-5">
                            <div class="icon icon-shape icon-md bg-gray-100 shadow-none text-center border-radius-lg mb-3">
                                <i class="material-symbols-rounded text-success opacity-10 fs-4">check_circle</i>
                            </div>
                            <h6 class="text-sm font-weight-normal text-muted">Tidak ada revisi saat ini.</h6>
                        </div>
                    @else
                        <ul class="list-group">
                            @foreach ($laporanRevisi as $laporan)
                                @php
                                    $editUrl = '#';
                                    $showUrl = '#';
                                    if ($laporan instanceof \App\Models\LaporanSituasiDaerah) {
                                        $editUrl = route('laporan_situasi_daerah.edit', [
                                            'laporan_situasi_daerah' => $laporan->id_laporan,
                                        ]);
                                        $showUrl = route('laporan_situasi_daerah.show', [
                                            'laporan_situasi_daerah' => $laporan->id_laporan,
                                            'from' => 'dashboard',
                                        ]);
                                    } elseif ($laporan instanceof \App\Models\LaporanPilkadaSerentak) {
                                        $editUrl = route('laporan_pilkada_serentak.edit', [
                                            'laporan_pilkada_serentak' => $laporan->id_laporan,
                                        ]);
                                        $showUrl = route('laporan_pilkada_serentak.show', [
                                            'laporan_pilkada_serentak' => $laporan->id_laporan,
                                            'from' => 'dashboard',
                                        ]);
                                    } elseif ($laporan instanceof \App\Models\LaporanKejadianMenonjol) {
                                        $editUrl = route('laporan_kejadian_menonjol.edit', [
                                            'laporan_kejadian_menonjol' => $laporan->id_laporan,
                                        ]);
                                        $showUrl = route('laporan_kejadian_menonjol.show', [
                                            'laporan_kejadian_menonjol' => $laporan->id_laporan,
                                            'from' => 'dashboard',
                                        ]);
                                    } elseif ($laporan instanceof \App\Models\LaporanPelanggaranKampanye) {
                                        $editUrl = route('laporan_pelanggaran_kampanye.edit', [
                                            'laporan_pelanggaran_kampanye' => $laporan->id_laporan,
                                        ]);
                                        $showUrl = route('laporan_pelanggaran_kampanye.show', [
                                            'laporan_pelanggaran_kampanye' => $laporan->id_laporan,
                                            'from' => 'dashboard',
                                        ]);
                                    } elseif ($laporan instanceof \App\Models\LaporanPenguatanIdeologi) {
                                        $editUrl = route('laporan_penguatan_ideologi.edit', [
                                            'laporan_penguatan_ideologi' => $laporan->id_laporan,
                                        ]);
                                        $showUrl = route('laporan_penguatan_ideologi.show', [
                                            'laporan_penguatan_ideologi' => $laporan->id_laporan,
                                            'from' => 'dashboard',
                                        ]);
                                    }
                                @endphp

                                <li
                                    class="list-group-item border-0 d-flex p-3 mb-2 bg-gray-100 border-radius-lg revision-item align-items-center">
                                    <div class="d-flex flex-column">
                                        <h6 class="mb-1 text-sm text-dark text-truncate" style="max-width: 200px;">
                                            {{ $laporan->judul }}</h6>

                                        <div class="d-flex align-items-center text-xs text-muted mb-1">
                                            <i class="material-symbols-rounded text-xs me-1 text-warning">warning</i>
                                            <span class="font-weight-bold me-1">Catatan:</span>
                                            {{ Str::limit($laporan->catatan ?? 'Harap perbaiki data.', 30) }}
                                        </div>

                                        <span class="text-xs text-secondary">
                                            {{ $laporan->verified_at?->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="ms-auto text-end d-flex align-items-center">
                                        <a class="btn btn-success bg-gradient-success btn-sm px-2 py-1 mb-0 mx-2"
                                            href="{{ $showUrl }}">
                                            <i class="material-symbols-rounded text-sm position-relative">visibility</i>
                                            Detail
                                        </a>
                                        <a class="btn btn-warning bg-gradient-warning text-dark btn-sm px-2 py-1 mb-0"
                                            href="{{ $editUrl }}">
                                            <i class="material-symbols-rounded text-sm position-relative">build</i>
                                            Perbaiki
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header pb-0 p-3">
                    <h6 class="mb-0 font-weight-bold">Aktivitas Laporan</h6>
                </div>
                <div class="card-body p-3" style="max-height: 450px; overflow-y: auto;">

                    @if (!isset($laporanDisetujui) || $laporanDisetujui->isEmpty())
                        <div class="text-center py-5">
                            <h6 class="text-sm font-weight-normal text-muted">Belum ada riwayat persetujuan.</h6>
                        </div>
                    @else
                        <div class="timeline timeline-one-side">
                            @foreach ($laporanDisetujui as $laporan)
                                @php
                                    $showUrl = '#';
                                    if ($laporan instanceof \App\Models\LaporanSituasiDaerah) {
                                        $showUrl = route('laporan_situasi_daerah.show', [
                                            'laporan_situasi_daerah' => $laporan->id_laporan,
                                            'from' => 'dashboard',
                                        ]);
                                    } elseif ($laporan instanceof \App\Models\LaporanPilkadaSerentak) {
                                        $showUrl = route('laporan_pilkada_serentak.show', [
                                            'laporan_pilkada_serentak' => $laporan->id_laporan,
                                            'from' => 'dashboard',
                                        ]);
                                    } elseif ($laporan instanceof \App\Models\LaporanKejadianMenonjol) {
                                        $showUrl = route('laporan_kejadian_menonjol.show', [
                                            'laporan_kejadian_menonjol' => $laporan->id_laporan,
                                            'from' => 'dashboard',
                                        ]);
                                    } elseif ($laporan instanceof \App\Models\LaporanPelanggaranKampanye) {
                                        $showUrl = route('laporan_pelanggaran_kampanye.show', [
                                            'laporan_pelanggaran_kampanye' => $laporan->id_laporan,
                                            'from' => 'dashboard',
                                        ]);
                                    } elseif ($laporan instanceof \App\Models\LaporanPenguatanIdeologi) {
                                        $showUrl = route('laporan_penguatan_ideologi.show', [
                                            'laporan_penguatan_ideologi' => $laporan->id_laporan,
                                            'from' => 'dashboard',
                                        ]);
                                    }
                                @endphp

                                <div class="timeline-block mb-3">
                                    <span class="timeline-step">
                                        <i class="material-symbols-rounded text-success text-gradient">check_circle</i>
                                    </span>
                                    <div class="timeline-content">
                                        <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $laporan->judul }}</h6>
                                        <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                            {{ $laporan->verified_at?->format('d M Y, H:i') }}
                                        </p>
                                        <p class="text-sm mt-2 mb-2">
                                            Disetujui oleh <span
                                                class="font-weight-bold text-dark">{{ $laporan->pimpinan->nama ?? 'Pimpinan' }}</span>.
                                        </p>
                                        <a href="{{ $showUrl }}"
                                            class="btn btn-success bg-gradient-success btn-sm px-3 py-1 mb-0 mt-1">
                                            Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chart-laporan-bulanan');
            const labelsBulan = @json($labelsBulan ?? []);
            const dataJumlah = @json($dataJumlah ?? []);

            if (ctx && labelsBulan.length > 0 && dataJumlah.length > 0) {
                const chartContext = ctx.getContext('2d');
                const darkBlueColor = 'rgba(58, 65, 111, 0.8)';
                const lightBlueColor = 'rgba(58, 65, 111, 1)';

                new Chart(chartContext, {
                    type: 'bar',
                    data: {
                        labels: labelsBulan,
                        datasets: [{
                            label: 'Jumlah Laporan',
                            data: dataJumlah,
                            backgroundColor: function(context) {
                                const chart = context.chart;
                                const {
                                    ctx,
                                    chartArea
                                } = chart;
                                if (!chartArea) return lightBlueColor;
                                const gradient = ctx.createLinearGradient(0, chartArea.bottom,
                                    0, chartArea.top);
                                gradient.addColorStop(0, lightBlueColor);
                                gradient.addColorStop(1, darkBlueColor);
                                return gradient;
                            },
                            borderColor: darkBlueColor,
                            borderWidth: 1,
                            borderRadius: 5,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false, // Penting agar tinggi mengikuti container
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: '#344767',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        if (context.parsed.y !== null) label += context.parsed.y +
                                            ' Laporan';
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    drawBorder: false,
                                    display: true,
                                    drawOnChartArea: true,
                                    drawTicks: false,
                                    borderDash: [5, 5],
                                    color: 'rgba(0, 0, 0, .1)'
                                },
                                ticks: {
                                    display: true,
                                    padding: 10,
                                    color: '#6c757d',
                                    font: {
                                        size: 11,
                                        family: "Inter",
                                        style: 'normal',
                                        lineHeight: 2
                                    },
                                    callback: function(value) {
                                        if (value % 1 === 0) return value;
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    drawBorder: false,
                                    display: false,
                                    drawOnChartArea: false,
                                    drawTicks: false
                                },
                                ticks: {
                                    display: true,
                                    color: '#6c757d',
                                    padding: 10,
                                    font: {
                                        size: 11,
                                        family: "Inter",
                                        style: 'normal',
                                        lineHeight: 2
                                    }
                                }
                            }
                        }
                    }
                });
            } else if (ctx) {
                const chartContainer = ctx.closest('.chart');
                if (chartContainer) {
                    chartContainer.innerHTML =
                        '<div class="alert alert-light text-center border m-0 py-3"><i class="material-symbols-rounded text-muted d-block mb-1">signal_cellular_nodata</i><span class="text-sm text-muted">Belum ada data laporan yang cukup untuk menampilkan grafik.</span></div>';
                }
            }
        });
    </script>
@endpush
