@extends('layouts.master')

@section('page', 'Laporan Bulanan - ' . $wilayah->nama_wilayah . ' - ' . $monthName . ' ' . $year)
@section('title', 'Daftar Laporan')

{{-- Include CSS DataTables jika belum ada di master layout --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('master/assets/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('master/assets/css/custom-datatables.css') }}">
    <link rel="stylesheet" href="{{ asset('master/assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('master/assets/css/select2-bootstrap-5-theme.min.css') }}">
@endpush

@section('content')
    <div class="row mb-3">
        <div class="col-lg-6 col-md-12 mb-3 mb-lg-0">
            <div>
                <h3 class="font-weight-bolder mb-1">Daftar Laporan - {{ $wilayah->nama_wilayah }}</h3>
                <p class="text-muted text-sm mb-0">Bulan: <strong>{{ $monthName }} {{ $year }}</strong></p>
            </div>
        </div>

        {{-- [PERBAIKAN] Tambahkan Filter Tipe Laporan & Tombol Kembali --}}
        <div class="col-lg-6 col-md-12 d-flex justify-content-lg-end align-items-center gap-2 flex-wrap">
            <div class="me-lg-2" style="min-width: 300px;">
                <label for="report-type-filter" class="form-label font-weight-bold ms-0 mb-1">Filter Tipe Laporan</label>
                <select class="form-select" id="report-type-filter" name="report_type">
                    <option value="all" selected>Semua Laporan</option>
                    @if (isset($reportTypes))
                        @foreach ($reportTypes as $key => $type)
                            <option value="{{ $key }}">{{ $type['title'] }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <a href="{{ route('laporan-bulanan.months', $wilayah->id_wilayah) }}?year={{ $year }}"
                class="btn btn-sm btn-secondary bg-gradient-secondary mb-0 mt-auto">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        {{-- [AKHIR PERBAIKAN] --}}
    </div>

    <div class="row mb-4">
        <div class="col-12 mb-md-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-laporan-bulanan" style="width:100%">
                            <thead>
                                {{-- [PERBAIKAN] Kembalikan header tabel ke versi asli --}}
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Pemda</th>
                                    <th>Pembangunan</th>
                                    <th>Publik</th>
                                    <th>Ideologi</th>
                                    <th>Politik</th>
                                    <th>Ekonomi</th>
                                    <th>Sosbud</th>
                                    <th>Hankam</th>
                                    <th width="10%"><i class="fa fa-cog"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- DataTables akan mengisi ini --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Include JS DataTables jika belum ada di master layout --}}
    <script src="{{ asset('master/assets/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ asset('master/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('master/assets/js/plugins/select2.min.js') }}"></script>

    <script>
        let table;

        $(function() {
            // Inisialisasi Select2 untuk filter
            $('#report-type-filter').select2({
                theme: 'bootstrap-5',
                // minimumResultsForSearch: Infinity
            });

            table = $('#table-laporan-bulanan').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,

                // [PERBAIKAN] Tambahkan objek ajax dengan 'data'
                ajax: {
                    url: '{{ route('laporan-bulanan.data', ['wilayah' => $wilayah->id_wilayah, 'month' => $month]) }}?year={{ $year }}',
                    data: function(d) {
                        // Kirim nilai filter dropdown ke controller
                        d.report_type = $('#report-type-filter').val();
                    }
                },
                // [AKHIR PERBAIKAN]

                // [PERBAIKAN] Kembalikan definisi kolom ke versi asli
                columns: [{
                        data: 'tanggal_laporan',
                        name: 'tanggal_laporan'
                    },
                    {
                        data: 'pemerintahan_daerah',
                        name: 'narasi_a',
                        orderable: false
                    },
                    {
                        data: 'program_pembangunan',
                        name: 'narasi_b',
                        orderable: false
                    },
                    {
                        data: 'pelayanan_publik',
                        name: 'narasi_c',
                        orderable: false
                    },
                    {
                        data: 'ideologi',
                        name: 'narasi_d',
                        orderable: false
                    },
                    {
                        data: 'politik',
                        name: 'narasi_e',
                        orderable: false
                    },
                    {
                        data: 'ekonomi',
                        name: 'narasi_f',
                        orderable: false
                    },
                    {
                        data: 'sosial_budaya',
                        name: 'narasi_g',
                        orderable: false
                    },
                    {
                        data: 'hankam',
                        name: 'narasi_h',
                        orderable: false
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        searchable: false,
                        sortable: false,
                        className: 'text-center'
                    },
                ],
                // [AKHIR PERBAIKAN]

                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    sEmptyTable: "Tidak ada data yang tersedia dalam tabel",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                }
            });

            // [TAMBAHKAN] Event listener untuk filter dropdown
            $('#report-type-filter').on('change', function() {
                table.ajax.reload(); // Muat ulang data tabel saat filter diubah
            });
        });
    </script>
@endpush
