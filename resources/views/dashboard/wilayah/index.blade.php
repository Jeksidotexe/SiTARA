@extends('layouts.master')

@section('page')
    Daftar Wilayah
@endsection

@section('title')
    Daftar Wilayah
@endsection

@section('content')
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Daftar Wilayah</h3>
            <p class="mb-4">
                Kelola semua daftar wilayah yang ada dalam sistem.
            </p>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <a href="{{ route('wilayah.create') }}" class="btn btn-dark glow-dark btn-sm">
                        <i class="fa fa-plus"></i> Tambah Wilayah
                    </a>
                </div>

                <div class="card-body p-3">
                    <div class="table-responsive p-0">
                        <table class="table table-striped" id="table-wilayah">
                            <thead>
                                <th width="5%">No</th>
                                <th>Nama Wilayah</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Status</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let table;

        $(function() {
            table = $('#table-wilayah').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: '{{ route('wilayah.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'nama_wilayah'
                    },
                    {
                        data: 'latitude'
                    },
                    {
                        data: 'longitude'
                    },
                    {
                        data: 'status_wilayah',
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
                title: 'Yakin ingin menghapus data ini?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fas fa-check-circle me-1"></i> Ya, hapus!',
                cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
                customClass: {
                    confirmButton: 'btn btn-dark bg-gradient-dark me-2',
                    cancelButton: 'btn btn-outline-dark ms-2'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(url, {
                            '_token': '{{ csrf_token() }}',
                            '_method': 'delete'
                        })
                        .done((response) => {
                            // table.ajax.reload(); // Tidak perlu lagi, event akan menangani
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
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
                                    confirmButton: 'btn btn-dark bg-gradient-dark'
                                },
                                buttonsStyling: false,
                                confirmButtonText: 'OK'
                            });
                        });
                }
            });
        }

        // [TAMBAHKAN BLOK INI UNTUK REALTIME]
        document.addEventListener('DOMContentLoaded', function() {
            // Pastikan Echo sudah di-load dari master layout Anda
            if (typeof window.Echo !== 'undefined') {
                console.log('[Reverb] Mendengarkan di channel publik: wilayah-updates');

                window.Echo.channel('wilayah-updates')
                    .listen('.WilayahUpdated', (e) => {
                        console.log('[Reverb] Menerima event WilayahUpdated:', e);

                        const dataTable = $('#table-wilayah').DataTable();

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
