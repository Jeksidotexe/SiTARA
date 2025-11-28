@extends('layouts.master')
@section('title', 'Dashboard')
@section('page', 'Home')

@section('content')
    <div class="row mb-4 align-items-center">
        <div class="col-lg-8 col-md-7 col-12 mb-3 mb-md-0">
            <h3 class="font-weight-bolder text-uppercase mb-1">Pimpinan {{ Auth::user()->wilayah->nama_wilayah }}</h3>
            <p class="text-muted text-sm">Pastikan Anda mengikuti tata cara yang benar dalam verfikasi laporan.</p>
        </div>
        <div class="col-lg-4 col-md-5 col-12 text-md-end text-start">
            <div class="d-inline-block px-3 py-2 border-radius-lg">
                <p class="text-sm text-muted mb-0 font-weight-bold text-end" id="realtime-date">
                    Memuat tanggal...
                </p>
                <h4 class="font-weight-bolder mb-0 d-flex align-items-center justify-content-md-end text-dark">
                    <i class="material-symbols-rounded me-2 text-gradient text-dark fs-4">schedule</i>
                    <span id="realtime-clock" style="min-width: 110px;">00:00:00</span>
                    <span class="text-xs font-weight-normal ms-1 text-muted">WIB</span>
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-lg-0 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold d-flex align-items-center">
                            <i class="material-symbols-rounded me-2 fs-5">list_alt</i>
                            Laporan Terbaru Menunggu Verifikasi
                        </h6>
                        @if (isset($countPending) && $countPending > 0)
                            <a href="{{ route('verifikasi.pending') }}"
                                class="btn btn-sm btn-dark bg-gradient-dark mb-0 py-1 px-3">
                                Lihat Semua Pending <i class="fas fa-arrow-circle-right ms-1"></i>
                            </a>
                        @endif
                    </div>
                    <p class="text-sm text-muted mt-1 mb-2">
                        Tinjau dan ambil tindakan untuk laporan berikut.
                    </p>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <tbody id="pimpinan-pending-list-tbody">
                                @forelse ($laporanPending as $laporan)
                                    <tr>
                                        <td class="px-3 py-3 border-bottom {{ $loop->last ? 'border-bottom-0' : '' }}">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="icon icon-shape icon-sm shadow-sm border-radius-md bg-white text-center me-3 flex-shrink-0">
                                                    <i
                                                        class="material-symbols-rounded text-dark opacity-10 fs-5">assignment</i>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm font-weight-normal">
                                                        {{ $laporan->judul ?? 'Laporan Situasi Daerah' }}
                                                        <span class="text-xs text-muted ms-1">- Tanggal:
                                                            {{ $laporan->tanggal_laporan?->isoFormat('D MMMM YYYY') ?? 'N/A' }}</span>
                                                    </h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        <i class="fa fa-user opacity-6 me-1"></i>
                                                        {{ $laporan->operator->nama ?? 'N/A' }}
                                                        <span class="ms-2"><i class="fa fa-clock opacity-6 me-1"></i>
                                                            {{ $laporan->created_at?->diffForHumans() ?? '-' }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td
                                            class="align-middle text-end px-3 py-3 border-bottom {{ $loop->last ? 'border-bottom-0' : '' }}">
                                            @php
                                                $showUrl = '#';
                                                if ($laporan instanceof \App\Models\LaporanSituasiDaerah) {
                                                    $showUrl = route('laporan_situasi_daerah.show', [
                                                        'laporan_situasi_daerah' => $laporan->id_laporan,
                                                        'from' => 'pending',
                                                    ]);
                                                } elseif ($laporan instanceof \App\Models\LaporanPilkadaSerentak) {
                                                    $showUrl = route('laporan_pilkada_serentak.show', [
                                                        'laporan_pilkada_serentak' => $laporan->id_laporan,
                                                        'from' => 'pending',
                                                    ]);
                                                } elseif ($laporan instanceof \App\Models\LaporanKejadianMenonjol) {
                                                    $showUrl = route('laporan_kejadian_menonjol.show', [
                                                        'laporan_kejadian_menonjol' => $laporan->id_laporan,
                                                        'from' => 'pending',
                                                    ]);
                                                } elseif ($laporan instanceof \App\Models\LaporanPelanggaranKampanye) {
                                                    $showUrl = route('laporan_pelanggaran_kampanye.show', [
                                                        'laporan_pelanggaran_kampanye' => $laporan->id_laporan,
                                                        'from' => 'pending',
                                                    ]);
                                                } elseif ($laporan instanceof \App\Models\LaporanPenguatanIdeologi) {
                                                    $showUrl = route('laporan_penguatan_ideologi.show', [
                                                        'laporan_penguatan_ideologi' => $laporan->id_laporan,
                                                        'from' => 'pending',
                                                    ]);
                                                }
                                            @endphp
                                            <a href="{{ $showUrl }}"
                                                class="btn btn-dark-blue bg-gradient-dark-blue btn-sm px-3 py-1 my-auto shadow-sm mb-0">
                                                <i class="fa fa-search me-1"></i> Periksa
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="pimpinan-empty-state">
                                        <td colspan="2" class="text-center py-5">
                                            <div class="d-flex flex-column align-items-center">
                                                <i
                                                    class="material-symbols-rounded fs-1 mb-2 text-dark-blue">playlist_add_check</i>
                                                <span class="fw-bold">Tidak ada laporan yang perlu diverifikasi.</span>
                                                <span class="text-sm mt-1">Semua laporan sudah ditinjau.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-body border-radius-lg shadow-sm mb-4">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-muted">Menunggu Verifikasi</p>
                            <h5 class="font-weight-bolder mb-0">
                                <span id="pimpinan-pending-count">{{ $countPending ?? 0 }}</span>
                                <span class="text-sm font-weight-light text-muted ms-1">Laporan</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded text-lg opacity-10">pending_actions</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-2">
                <a class="text-sm text-secondary font-weight-normal mb-0 pt-1" href="{{ route('verifikasi.pending') }}">
                    Lihat Semua Laporan Pending <i class="fas fa-arrow-circle-right ms-1"></i>
                </a>
            </div>

            <div class="card card-body border-radius-lg shadow-sm">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-muted">Laporan Disetujui</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $countApproved ?? 0 }}
                                <span class="text-sm font-weight-light text-muted ms-1">Laporan</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded text-lg opacity-10">task_alt</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-2">
                <a class="text-sm text-secondary font-weight-normal mb-0 pt-1" href="{{ route('verifikasi.history') }}">
                    Lihat Riwayat Verifikasi <i class="fas fa-arrow-circle-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="mb-0 font-weight-bold d-flex align-items-center">
                        <i class="material-symbols-rounded me-2 fs-5">bar_chart</i>
                        Grafik Laporan Disetujui (Per Bulan {{ date('Y') }})
                    </h6>
                </div>
                <div class="card-body">
                    <div class="chart">
                        <canvas id="chart-laporan-pimpinan" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const labels = @json($labelsBulan ?? []);
            const data = @json($dataJumlah ?? []);
            const ctxElement = document.getElementById("chart-laporan-pimpinan");

            const currentMonthIndex = @json($currentMonthIndex);

            if (ctxElement && labels.length > 0) {
                const ctx = ctxElement.getContext("2d");

                const defaultColor = 'rgba(58, 65, 111, 0.6)';
                const highlightColor = 'rgba(70, 78, 130, 0.9)';

                const backgroundColors = data.map((_, index) =>
                    index === currentMonthIndex ? highlightColor : defaultColor
                );

                new Chart(ctx, {
                    type: "bar",
                    data: {
                        labels: labels,
                        datasets: [{
                            label: "Laporan Disetujui",
                            backgroundColor: backgroundColors,
                            data: data,
                            borderWidth: 0,
                            borderRadius: 4,
                            borderSkipped: false,
                            maxBarThickness: 40,
                            tension: 0.4,
                        }, ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                            },
                            tooltip: {
                                backgroundColor: 'rgb(38, 38, 38, 0.9)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y + ' Laporan';
                                        }
                                        if (context.dataIndex === currentMonthIndex) {
                                            label += ' (Saat Ini)';
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index',
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
                                        if (value % 1 === 0) {
                                            return value;
                                        }
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    drawBorder: false,
                                    display: false,
                                    drawOnChartArea: false,
                                    drawTicks: false,
                                },
                                ticks: {
                                    display: true,
                                    padding: 10,
                                    color: function(context) {
                                        return context.index === currentMonthIndex ? '#464E82' :
                                            '#6c757d';
                                    },
                                    font: {
                                        size: 11,
                                        family: "Inter",
                                        style: 'normal',
                                        lineHeight: 2,
                                        weight: function(context) {
                                            return context.index === currentMonthIndex ? 'bold' :
                                                'normal';
                                        },
                                    }
                                }
                            },
                        },
                    },
                });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cek apakah moment.js tersedia (sudah ada di master.blade.php)
            if (typeof moment !== 'undefined') {
                // Set locale ke Indonesia
                moment.locale('id');

                function updateTime() {
                    const now = moment();

                    // Update Jam (Format: 14:30:59)
                    const timeString = now.format('HH:mm:ss');
                    const clockElement = document.getElementById('realtime-clock');
                    if (clockElement) clockElement.innerText = timeString;

                    // Update Tanggal (Format: Senin, 25 November 2024)
                    const dateString = now.format('dddd, D MMMM YYYY');
                    const dateElement = document.getElementById('realtime-date');
                    if (dateElement) dateElement.innerText = dateString;
                }

                // Jalankan fungsi update setiap 1 detik
                setInterval(updateTime, 1000);

                // Jalankan segera saat load agar tidak ada delay tampilan
                updateTime();
            } else {
                console.error('Moment.js tidak ditemukan. Pastikan sudah di-load di master layout.');
            }
        });
    </script>
@endpush
