@extends('layouts.master')

{{-- Judul Halaman --}}
@section('page', 'Laporan Pending')
@section('title', 'Menunggu Verifikasi')

@section('content')
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Laporan Menunggu Verifikasi</h3>
            <p class="mb-4">
                Daftar laporan yang membutuhkan tinjauan dan persetujuan Anda.
            </p>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12 mb-md-0 mb-4">
            <div class="card">
                {{-- Tombol Buat Laporan Baru dihapus, pimpinan tidak membuat laporan --}}
                <div class="card-body p-3">
                    <div class="table-responsive p-0">
                        <table class="table table-striped" id="table-laporan-pending">
                            <thead>
                                <th width="5%">No</th>
                                <th>Tipe Laporan</th>
                                <th>Nama Operator</th>
                                <th>Tanggal Laporan</th>
                                <th>Tanggal Dibuat</th>
                                <th width="15%"><i class="fa fa-cog"></i></th>
                            </thead>
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
            // Inisialisasi DataTables
            table = $('#table-laporan-pending').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{{ route('verifikasi.pending.data') }}', // <-- PERUBAHAN DI SINI
                columns: [
                    // Kolom disesuaikan dengan VerificationController@pendingData
                    {
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'report_type',
                        name: 'report_type'
                    },
                    {
                        data: 'operator_name',
                        name: 'operator.nama'
                    },
                    {
                        data: 'tanggal_laporan',
                        name: 'tanggal_laporan'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        searchable: false,
                        sortable: false
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
        });

        // Fungsi deleteData dihapus karena tidak ada tombol hapus di sini
    </script>
@endpush
