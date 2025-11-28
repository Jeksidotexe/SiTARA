@extends('layouts.master')
@section('page', 'Akun Pengguna')
@section('title', 'Akun Pengguna')

@section('content')
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Akun Pengguna</h3>
            <p class="mb-4">
                Kelola semua akun pengguna yang terdaftar dalam sistem.
            </p>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-12 mb-md-0 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <a href="{{ route('pengguna.create') }}" class="btn btn-sm btn-dark bg-gradient-dark">
                        <i class="fa fa-plus"></i> Tambah Pengguna
                    </a>
                </div>

                <div class="card-body p-3">
                    <div class="table-responsive p-0">
                        <table class="table table-striped" style="width: 100%">
                            <thead>
                                <th width="5">No</th>
                                <th>Foto</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Wilayah</th>
                                <th>Role</th>
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
            table = $('.table').DataTable({
                responsive: false,
                scrollX: true,
                processing: true,
                serverSide: true,
                ajax: '{{ route('pengguna.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'foto',
                        searchable: false,
                        sortable: false
                    },
                    {
                        data: 'nama'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'id_wilayah'
                    },
                    {
                        data: 'role'
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
                            } else if (jqXHR.responseText) {
                                try {
                                    const err = JSON.parse(jqXHR.responseText);
                                    if (err.message) errorMessage = err.message;
                                } catch (e) {}
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
@endpush
