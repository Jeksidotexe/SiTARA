@extends('layouts.master')
@section('title', 'Dashboard')
@section('page', 'Home')

@section('content')
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-lg-12">
            <h3 class="font-weight-bolder text-uppercase mb-1">Pimpinan {{ Auth::user()->wilayah->nama_wilayah }}</h3>
            <p class="text-muted text-sm">Pastikan Anda mengikuti tata cara yang benar dalam verfikasi laporan.</p>
        </div>
    </div>

    <div class="row">
        {{-- KOLOM KIRI: Tabel Laporan Terbaru (Lebih Lebar) --}}
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

        {{-- KOLOM KANAN: Card Statistik (Vertikal) --}}
        <div class="col-lg-4">
            {{-- Card 1: Menunggu Verifikasi --}}
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

            {{-- Card 2: Laporan Disetujui --}}
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
@endsection
