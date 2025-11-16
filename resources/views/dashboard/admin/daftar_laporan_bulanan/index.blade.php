@extends('layouts.master')

@section('page', 'Laporan Bulanan - Pilih Wilayah')
@section('title', 'Laporan Bulanan')

@push('styles')
    <style>
        .wilayah-card .card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease-in-out;
        }

        .progress-info-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
            white-space: nowrap;
        }

        .progress-info-container .progress-percentage {
            font-weight: bold;
            font-size: 0.8rem;
            color: #344767;
        }

        .progress-info-container .progress-count {
            font-weight: 500;
            font-size: 0.75rem;
            color: #6c757d;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-5">
        <div class="col-lg-12 d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h3 class="font-weight-bolder mb-1">Pilih Wilayah Laporan Bulanan</h3>
            </div>

            <div class="col-lg-6 col-md-8 col-sm-12 mt-4">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6 col-sm-12">
                        <div id="search-wilayah-form">
                            <label for="wilayah-search" class="form-label font-weight-bold ms-0">Cari Wilayah</label>
                            <input type="text" class="form-control py-2 px-3" style="height: 38px; background-color:#fff;" id="wilayah-search"
                                placeholder="Ketik nama wilayah...">
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <form action="{{ route('laporan-bulanan.index') }}" method="GET" id="year-filter-form"
                            class="mb-0">
                            <label for="year-select" class="form-label font-weight-bold ms-0">Pilih Tahun</label>
                            <select class="form-select" id="year-select" name="year">
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        @forelse ($wilayahs as $wilayah)
            <div class="col-xl-3 col-md-4 col-sm-6 mb-5 wilayah-card-wrapper"
                data-nama-wilayah="{{ $wilayah->nama_wilayah }}">
                <a href="{{ route('laporan-bulanan.months', $wilayah->id_wilayah) }}?year={{ $selectedYear }}"
                    class="text-decoration-none wilayah-card">
                    <div class="card">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1 text-center">
                                <h6 class="font-weight-bolder mb-1 mt-2 text-white">
                                    {{ Str::limit($wilayah->nama_wilayah, 25) }}
                                </h6>
                            </div>
                        </div>
                        <div class="card-body text-center py-3">
                            <p class="text-xs text-muted mb-1">Progress Laporan</p>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar progress-bar-striped bg-gradient-dark" role="progressbar"
                                    style="width: {{ $wilayah->progress_percentage }}%;"
                                    aria-valuenow="{{ $wilayah->progress_percentage }}" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>
                            <div class="progress-info-container mt-1">
                                <span class="progress-percentage">{{ $wilayah->progress_percentage }}%</span>
                                <span class="progress-count">{{ $wilayah->report_count }} /
                                    {{ $wilayah->target_days }} Laporan</span>
                            </div>
                            <hr class="dark horizontal my-2">
                            <p class="text-xs text-muted mb-0 d-flex align-items-center justify-content-center">
                                <i class="material-symbols-rounded opacity-6 me-1"
                                    style="font-size: 1rem;">arrow_forward</i>
                                Lihat Laporan
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-white" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> Belum ada data wilayah yang tersedia. Silakan tambahkan data wilayah
                    terlebih dahulu.
                </div>
            </div>
        @endforelse

        <div class="col-12" id="search-no-results" style="display: none;">
            <div class="alert alert-secondary text-white text-center" role="alert">
                <strong>Info!</strong> Wilayah dengan nama tersebut tidak ditemukan.
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#year-select').select2({
                theme: 'bootstrap-5',
                // minimumResultsForSearch: Infinity
            });

            $('#year-select').on('change.select2', function(e) {
                $('#year-filter-form').submit();
            });

            $('#wilayah-search').on('keyup', function() {
                let query = $(this).val().toLowerCase().trim();
                let visibleCount = 0;

                $('.wilayah-card-wrapper').each(function() {
                    let namaWilayah = $(this).data('nama-wilayah').toLowerCase();

                    if (namaWilayah.includes(query)) {
                        $(this).show();
                        visibleCount++;
                    } else {
                        $(this).hide();
                    }
                });

                if (visibleCount === 0 && $('.wilayah-card-wrapper').length > 0) {
                    $('#search-no-results').show();
                } else {
                    $('#search-no-results').hide();
                }
            });
        });
    </script>
@endpush
