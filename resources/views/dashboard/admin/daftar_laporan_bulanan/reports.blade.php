@extends('layouts.master')

@section('page', 'Laporan Bulanan - ' . $wilayah->nama_wilayah . ' - ' . $monthName . ' ' . $year)
@section('title', 'Daftar Laporan')

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
    </div>

    <div class="row mb-4">
        <div class="col-12 mb-md-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-laporan-bulanan" style="width:100%">
                            <thead>
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
                                    <th><i class="fa fa-cog"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let table;

        $(function() {
            $('#report-type-filter').select2({
                theme: 'bootstrap-5',
            });

            table = $('#table-laporan-bulanan').DataTable({
                responsive: true,
                scrollX: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('laporan-bulanan.data', ['wilayah' => $wilayah->id_wilayah, 'month' => $month]) }}?year={{ $year }}',
                    data: function(d) {
                        d.report_type = $('#report-type-filter').val();
                    }
                },

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

                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    sEmptyTable: "Tidak ada data yang tersedia dalam tabel",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                }
            });

            $('#report-type-filter').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush
