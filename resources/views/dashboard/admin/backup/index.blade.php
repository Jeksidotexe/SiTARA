@extends('layouts.master')
@section('title', 'Backup Data')
@section('page', 'Backup Data')
@push('styles')
    <style>
        .card-backup {
            border: none;
            box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.05);
            border-radius: 1rem;
            transition: all 0.3s ease;
        }

        .file-icon-wrapper {
            /* width: 42px;
                height: 42px;
                background-color: #fff3e0;
                color: #ff9800;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center; */
            font-size: 20px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background-color: #e9ecef;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            backdrop-filter: blur(4px);
        }

        @keyframes spin-slow {
            0% {
                transform: rotate(360deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        .icon-spin {
            animation: spin-slow 2s linear infinite;
        }

        div:where(.swal2-container) {
            z-index: 9999 !important;
        }

        div:where(.swal2-container) div:where(.swal2-popup) {
            border-radius: 1rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            padding: 2rem !important;
        }

        .swal2-loader {
            border-color: #344767 rgba(0, 0, 0, 0) #344767 rgba(0, 0, 0, 0) !important;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="font-weight-bolder mb-0 text-dark">Backup Data</h3>
            <p class="text-muted text-sm mb-0">Manajemen cadangan database via Google Drive.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">

        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-backup">
                <div class="card-header pb-0">
                    <form action="{{ route('backup.run') }}" method="POST" id="form-run-backup">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-dark bg-gradient-dark">
                            <i class="fas fa-cloud-upload-alt me-2"></i> Buat Backup Baru
                        </button>
                    </form>
                </div>
                <div class="card-body px-4 pt-4 pb-3">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 w-100" id="table-backup">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Nama File</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Ukuran</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Waktu Backup</th>
                                    <th width="15"
                                        class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        <i class="fa fa-cog"></i>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($files as $file)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="file-icon-wrapper me-3">
                                                    <i class="fas fa-file-archive text-info"></i>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-0 text-sm text-dark font-weight-bold">{{ $file['name'] }}
                                                    </h6>
                                                    <span class="text-xs text-secondary">Google Drive</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold text-dark">{{ $file['size'] }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-xs font-weight-bold text-dark">
                                                    {{ \Carbon\Carbon::createFromTimestamp($file['raw_date'])->setTimezone('Asia/Jakarta')->locale('id')->diffForHumans() }}
                                                </span>
                                                <span class="text-xxs text-secondary">
                                                    {{ \Carbon\Carbon::createFromTimestamp($file['raw_date'])->setTimezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i') }}
                                                    WIB
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('backup.download', ['path' => $file['path']]) }}"
                                                class="action-btn text-dark me-1" data-bs-toggle="tooltip" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>

                                            <button type="button"
                                                class="action-btn text-danger border-0 bg-transparent btn-delete"
                                                data-path="{{ $file['path'] }}" data-bs-toggle="tooltip" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#table-backup').DataTable({
                responsive: true,
                language: {
                    search: "Cari Backup:",
                    lengthMenu: "Tampilkan _MENU_ file",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ file",
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>',
                        next: '<i class="fas fa-angle-right"></i>',
                        previous: '<i class="fas fa-angle-left"></i>'
                    },
                    zeroRecords: "Tidak ada file backup ditemukan",
                    emptyTable: "Belum ada backup tersedia di Google Drive"
                },
                order: [],
                columnDefs: [{
                    orderable: false,
                    targets: 3
                }]
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })

            $('#form-run-backup').on('submit', function() {
                Swal.fire({
                    title: '',
                    html: `
                        <div class="d-flex flex-column align-items-center">
                            <div class="mb-3">
                                <span class="material-symbols-rounded text-info icon-spin" style="font-size: 4rem;">
                                    sync
                                </span>
                            </div>
                            <h5 class="text-dark font-weight-bold mb-1">Sedang Mencadangkan...</h5>
                            <p class="text-sm text-secondary mb-0">
                                Mengompresi database dan mengunggah ke Google Drive.
                            </p>
                            <small class="text-xs text-muted mt-2">Estimasi waktu: 1-2 menit</small>
                        </div>
                    `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    width: 400,
                    didOpen: () => {}
                });
            });

            $('.btn-delete').on('click', function() {
                let path = $(this).data('path');
                let deleteUrl = "{{ route('backup.destroy') }}?path=" + encodeURIComponent(path);

                Swal.fire({
                    title: 'Hapus Backup?',
                    html: "File akan dihapus permanen dari <br><b>Google Drive</b>.",
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
                        Swal.fire({
                            title: '',
                            html: `
                                <div class="d-flex flex-column align-items-center">
                                    <div class="mb-3">
                                        <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;"></div>
                                    </div>
                                    <h5 class="text-dark font-weight-bold mb-1">Menghapus...</h5>
                                    <p class="text-sm text-secondary mb-0">Mohon tunggu sebentar.</p>
                                </div>
                            `,
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            width: 350
                        });

                        let form = $('#delete-form');
                        form.attr('action', deleteUrl);
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
