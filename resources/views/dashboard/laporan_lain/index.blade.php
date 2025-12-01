@extends('layouts.master')
@section('page', 'Laporan Lain-Lain')
@section('title', 'Laporan Lain-Lain')

@section('content')
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Laporan Lain-Lain</h3>
            <p class="mb-4">
                Kelola semua Data Laporan Lain-Lain.
            </p>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <a href="{{ route('laporan_lain.create') }}" class="btn btn-sm btn-dark bg-gradient-dark">
                        <i class="fa fa-plus"></i> Buat Laporan Baru
                    </a>
                </div>

                <div class="card-body p-3">
                    <div class="table-responsive p-0">
                        <table class="table table-striped" id="table-laporan" style="width: 100%">
                            <thead>
                                <th width="5">No</th>
                                <th>Nama Operator</th>
                                <th>Tanggal Laporan</th>
                                <th>Status Laporan</th>
                                <th width="15"><i class="fa fa-cog"></i></th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let table;

        $(function() {
            table = $('#table-laporan').DataTable({
                responsive: false,
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: '{{ route('laporan_lain.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'operator',
                        name: 'operator.nama'
                    },
                    {
                        data: 'tanggal_laporan',
                        name: 'tanggal_laporan'
                    },
                    {
                        data: 'status_laporan',
                        name: 'status_laporan'
                    },
                    {
                        data: 'aksi',
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

        function deleteData(url) {
            Swal.fire({
                title: 'Yakin ingin menghapus laporan ini?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check-circle"></i> Ya, hapus!',
                cancelButtonText: '<i class="fas fa-times"></i> Batal',
                customClass: {
                    confirmButton: 'btn btn-sm btn-dark bg-gradient-dark me-2',
                    cancelButton: 'btn btn-sm btn-secondary bg-gradient-secondary ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(url, {
                            '_token': '{{ csrf_token() }}',
                            '_method': 'delete'
                        })
                        .done((response) => {
                            table.ajax.reload();
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message || 'Laporan berhasil dihapus.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true
                            });
                        })
                        .fail((jqXHR, textStatus, errorThrown) => {
                            let errorMessage = 'Tidak dapat menghapus data.';
                            if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                                errorMessage = jqXHR.responseJSON.message;
                            }
                            Swal.fire({
                                title: 'Gagal!',
                                text: errorMessage,
                                icon: 'error',
                                customClass: {
                                    confirmButton: 'btn btn-sm btn-dark bg-gradient-dark'
                                },
                                buttonsStyling: false,
                                confirmButtonText: 'OK'
                            });
                        });
                }
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.Echo !== 'undefined') {

                console.log('[Reverb] Mendengarkan di channel publik: laporan-updates');
                window.Echo.channel('laporan-updates')
                    .listen('.LaporanUpdated', (e) => {

                        console.log('[Reverb] Menerima event LaporanUpdated:', e);
                        const dataTable = $('#table-laporan').DataTable();

                        if (dataTable) {
                            dataTable.ajax.reload();
                        }
                    });
            } else {
                console.error('Laravel Echo not initialized. Pastikan master layout memuatnya.');
            }
        });
    </script>
@endpush
