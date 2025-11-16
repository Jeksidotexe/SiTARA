@extends('layouts.master')

@section('page', 'Laporan Bulanan - Pilih Bulan')
@section('title', 'Laporan Bulanan - ' . $wilayah->nama_wilayah)

@section('content')
    <div class="row mb-5">
        <div class="col-lg-12 d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h3 class="font-weight-bolder mb-1">Pilih Bulan untuk Wilayah {{ $wilayah->nama_wilayah }}</h3>
            </div>
            {{-- Tambahkan parameter ?year= ke route --}}
            <a href="{{ route('laporan-bulanan.index') }}?year={{ $year }}"
                class="btn btn-sm btn-secondary bg-gradient-secondary mb-0 mt-2 mt-md-0">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke halaman sebelumnya
            </a>
        </div>
    </div>

    <div class="row mt-4">
        {{-- Ganti loop menggunakan $monthsData --}}
        @foreach ($monthsData as $monthNumber => $month)
            <div class="col-xl-3 col-md-4 col-sm-6 mb-5">
                {{-- Tambahkan parameter ?year= ke route --}}
                <a href="{{ route('laporan-bulanan.reports', ['wilayah' => $wilayah->id_wilayah, 'month' => $monthNumber]) }}?year={{ $year }}"
                    class="text-decoration-none month-card">
                    <div class="card">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1 text-center">
                                <h6 class="font-weight-bolder mb-1 mt-2 text-white">
                                    {{ $month['name'] }} {{-- Ambil nama dari array --}}
                                </h6>
                            </div>
                        </div>
                        {{-- Ubah card-body --}}
                        <div class="card-body py-3">
                            {{-- TAMBAHKAN BLOK PROGRESS BAR --}}
                            <div class="px-3">
                                <p class="text-xs text-muted mb-1 text-start">Progress Laporan Harian</p>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar progress-bar-striped bg-gradient-secondary" role="progressbar"
                                        style="width: {{ $month['progress'] }}%;" aria-valuenow="{{ $month['progress'] }}"
                                        aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <p class="text-xs text-muted mt-1 mb-0 d-flex justify-content-between">
                                    <span class="font-weight-bold">{{ $month['progress'] }}%</span>
                                    <span class="font-weight-bold">{{ $month['count'] }} / {{ $month['target'] }}
                                        Hari</span>
                                </p>
                            </div>
                            {{-- AKHIR BLOK PROGRESS BAR --}}

                            <hr class="dark horizontal my-2">
                            <p class="text-xs text-muted mb-0 d-flex align-items-center justify-content-center">
                                <i class="material-symbols-rounded opacity-6 me-1" style="font-size: 1rem;">summarize</i>
                                Lihat Laporan
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endsection
