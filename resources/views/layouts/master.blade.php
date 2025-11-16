<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('master') }}/assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="{{ asset('master') }}/assets/img/favicon.png">
    <title> @yield('title') | SiMANTEL</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ Auth::id() }}">
    @endauth
    <!-- Fonts and icons -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />

    <!-- Leaflet JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css'
        rel='stylesheet' />
    <!-- Nucleo Icons -->
    <link href="{{ asset('master') }}/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="{{ asset('master') }}/assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Material Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('master') }}/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
    <!-- Datatables -->
    <link rel="stylesheet" href="{{ asset('master/assets/css/dataTables.bootstrap5.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('master/assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('master/assets/css/select2-bootstrap-5-theme.min.css') }}">
    <!-- Costum Css Datatables -->
    <link rel="stylesheet" href="{{ asset('master/assets/css/custom-datatables.css') }}">
    <link rel="stylesheet" href="{{ asset('master/assets/css/custom.css') }}">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
    @push('styles')
        <style>
            .image-preview-container {
                position: relative;
                width: 100%;
                height: 150px;
                border: 2px dashed #ddd;
                border-radius: .5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #f8f9fa;
                overflow: hidden;
            }

            .image-preview-container img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
            }

            .image-preview-container .preview-text {
                color: #6c757d;
                font-size: 0.9rem;
            }

            .alert-container {
                display: none;
                padding: 0.75rem 1rem;
                margin-bottom: 1rem;
                font-size: 0.875rem;
            }
        </style>
    @endpush
    @stack('styles')
</head>

<body
    class="g-sidenav-show  bg-gray-100 @if (Auth::check()) @if (Auth::user()->role == 'admin') dashboard-admin @endif
        @if (Auth::user()->role == 'operator') dashboard-operator @endif
        @if (Auth::user()->role == 'pimpinan') dashboard-pimpinan @endif
    @endif">
    <div class="position-fixed end-3" style="top: 4.5rem; z-index: 1090; max-width: 350px;">
        <div id="materialToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <span class="material-symbols-rounded me-2" id="toastIcon"></span>
                <strong class="me-auto" id="toastTitle"></strong>
                <small id="toastTime"></small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastBody">
            </div>
        </div>
    </div>
    <!-- Sidebar -->
    @includeIf('layouts.sidebar')
    <!-- End Sidebar -->
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        @includeIf('layouts.navbar')
        <!-- End Navbar -->
        <div class="container-fluid py-2">

            <!-- Content -->
            @yield('content')
            <!-- End Content -->

            <!-- Footer -->
            @includeIf('layouts.footer')
            <!-- EndFooter -->
        </div>
    </main>

    <!-- Form Modal Kop Surat dan Tanda Tangan -->
    @auth
        @if (Auth::user()->role == 'operator')
            <div class="modal fade" id="modalPengaturanKop" tabindex="-1" role="dialog"
                aria-labelledby="modalPengaturanKopLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalPengaturanKopLabel">Pengaturan Kop Surat Wilayah</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="formPengaturanKop" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div id="kop-error-container" class="alert alert-danger alert-container" role="alert">
                                </div>
                                <p class="text-sm">Anda mengelola pengaturan untuk wilayah:
                                    <strong id="nama-wilayah-modal-kop">Memuat...</strong>
                                </p>
                                <div class="row">
                                    {{-- KOP SURAT --}}
                                    <div class="col-md-12">
                                        <label class="form-label">Kop Surat (Max: 2MB)</label>
                                        <div class="image-preview-container mb-2">
                                            <img src="" id="kop-surat-preview" alt="Preview Kop Surat"
                                                style="display: none;">
                                            <span id="kop-surat-text" class="preview-text">Tidak ada gambar</span>
                                        </div>

                                        <input class="form-control" type="file" name="kop_surat" id="kop_surat_input"
                                            accept="image/*"
                                            onchange="previewImage(this, 'kop-surat-preview', 'kop-surat-text')"
                                            style="display: none;">

                                        <label for="kop_surat_input" class="btn btn-dark-blue w-100 mb-0">
                                            <i class="material-symbols-rounded me-1"
                                                style="font-size: 1rem; vertical-align: middle;">upload_file</i>
                                            Pilih Gambar Kop Surat
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm bg-gradient-secondary"
                                    data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Batal</button>
                                <button type="submit" class="btn btn-sm bg-gradient-dark"><i
                                        class="fa fa-save me-1"></i>
                                    Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalPengaturanTtd" tabindex="-1" role="dialog"
                aria-labelledby="modalPengaturanTtdLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalPengaturanTtdLabel">Pengaturan Tanda Tangan Wilayah</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="formPengaturanTtd" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div id="ttd-error-container" class="alert alert-danger alert-container" role="alert">
                                </div>
                                <p class="text-sm">Anda mengelola pengaturan untuk wilayah:
                                    <strong id="nama-wilayah-modal-ttd">Memuat...</strong>
                                </p>
                                <div class="row">
                                    {{-- TANDA TANGAN --}}
                                    <div class="col-md-12">
                                        <label class="form-label">Tanda Tangan (Max: 2MB)</label>
                                        <div class="image-preview-container mb-2">
                                            <img src="" id="tanda-tangan-preview" alt="Preview Tanda Tangan"
                                                style="display: none;">
                                            <span id="tanda-tangan-text" class="preview-text">Tidak ada gambar</span>
                                        </div>

                                        <input class="form-control" type="file" name="tanda_tangan"
                                            id="tanda_tangan_input" accept="image/*"
                                            onchange="previewImage(this, 'tanda-tangan-preview', 'tanda-tangan-text')"
                                            style="display: none;">

                                        <label for="tanda_tangan_input" class="btn btn-dark-blue w-100 mb-0">
                                            <i class="material-symbols-rounded me-1"
                                                style="font-size: 1rem; vertical-align: middle;">upload_file</i>
                                            Pilih Gambar Tanda Tangan
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm bg-gradient-secondary"
                                    data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Batal</button>
                                <button type="submit" class="btn btn-sm bg-gradient-dark"><i
                                        class="fa fa-save me-1"></i>
                                    Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endauth
    <!-- Akhir Form Modal Kop Surat dan Tanda Tangan -->

    <!-- Form Modal Tindak Lanjut Wilayah -->
    @auth
        @if (Auth::user()->role == 'pimpinan')
            {{-- [MODAL BARU UNTUK TINDAK LANJUT WILAYAH] --}}
            <div class="modal fade" id="modalTindakLanjut" tabindex="-1" role="dialog"
                aria-labelledby="modalTindakLanjutLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTindakLanjutLabel">Tindak Lanjut Status Wilayah</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        {{-- Form akan di-handle oleh AJAX --}}
                        <form id="formTindakLanjut" action="{{ route('wilayah.tindakLanjut') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_wilayah" id="tindakLanjut_id_wilayah">

                            <div class="modal-body">
                                {{-- Container untuk pesan error AJAX --}}
                                <div id="tindakLanjutErrorContainer" class="alert alert-danger"
                                    style="display: none; padding: 0.75rem 1.25rem; font-size: 0.875rem;">
                                </div>

                                <p class="text-sm">Status wilayah ini perlu ditinjau ulang. Anda dapat
                                    mengabaikannya (akan diingatkan lagi 24 jam) atau mengubah statusnya
                                    sekarang.</p>

                                <div class="form-group mb-0">
                                    <label for="tindakLanjut_status_wilayah" class="form-label">Ubah Status
                                        Wilayah</label>
                                    <select name="status_wilayah" id="tindakLanjut_status_wilayah"
                                        class="form-select px-2" required>
                                        <option value="Aman">Aman</option>
                                        <option value="Siaga">Siaga</option>
                                        <option value="Bahaya">Bahaya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer">
                                {{-- Tombol "Abaikan" hanya menutup modal --}}
                                <button type="button" class="btn btn-sm bg-gradient-secondary"
                                    id="btnAbaikanTindakLanjut"><i class="fas fa-times me-1"></i>Abaikan</button>

                                <button type="submit" id="btnSubmitTindakLanjut" class="btn btn-sm bg-gradient-dark"><i
                                        class="fa fa-save me-1"></i>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endauth
    <!-- Akhir Form Modal Tindak Lanjut Wilayah -->

    <!--   jQuery   -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!--   Core JS Files   -->
    <script src="{{ asset('master') }}/assets/js/core/popper.min.js"></script>
    <script src="{{ asset('master') }}/assets/js/core/bootstrap.min.js"></script>
    <script src="{{ asset('master') }}/assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="{{ asset('master') }}/assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="{{ asset('master') }}/assets/js/plugins/chartjs.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    {{-- <script src="{{ asset('master') }}/assets/js/material-dashboard.min.js?v=3.2.0"></script> --}}

    <!-- Datatables -->
    <script src="{{ asset('master/assets/js/plugins/dataTables.min.js') }}"></script>
    <script src="{{ asset('master/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>

    <!-- Select2 -->
    <script src="{{ asset('master/assets/js/plugins/select2.min.js') }}"></script>

    <!-- TinyMCE -->
    <script src="{{ asset('master/assets/js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

    <script>
        function formatBytes(bytes, decimals = 2) {
            if (!bytes || bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            const i = bytes > 0 ? Math.floor(Math.log(bytes) / Math.log(k)) : 0;
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        function displayFileDetails(input, infoTextId, detailsContainerId, uploadBtnId, previewWrapperId) {
            const fileInfoText = document.getElementById(infoTextId);
            const detailsContainer = document.getElementById(detailsContainerId);
            const uploadBtn = document.getElementById(uploadBtnId);
            const fileNameSpan = document.getElementById(detailsContainerId.replace('uploadedFileDetails', 'fileName'));
            const fileTypeSpan = document.getElementById(detailsContainerId.replace('uploadedFileDetails', 'fileType'));
            const fileSizeSpan = document.getElementById(detailsContainerId.replace('uploadedFileDetails', 'fileSize'));
            const filePreviewIcon = document.getElementById(detailsContainerId.replace('uploadedFileDetails',
                'filePreviewIcon'));
            const viewBtn = document.getElementById(detailsContainerId.replace('uploadedFileDetails', 'viewBtn'));
            const previewWrapper = document.getElementById(previewWrapperId);
            const previewImage = document.getElementById('foto-preview');

            const defaultInfo = fileInfoText.dataset.defaultText || fileInfoText.textContent;
            if (!fileInfoText.dataset.defaultText) {
                fileInfoText.dataset.defaultText = defaultInfo;
            }

            const defaultPdfIcon = "{{ asset('images/default-pdf-icon.png') }}";
            const defaultImageIcon = "{{ asset('images/default-image-icon.png') }}";

            if (viewBtn && viewBtn._currentObjectURL) {
                URL.revokeObjectURL(viewBtn._currentObjectURL);
                viewBtn._currentObjectURL = null;
            }

            if (input.files && input.files.length > 0) {
                const file = input.files[0];
                const maxSize = 2 * 1024 * 1024;

                if (file.size > maxSize) {
                    showMaterialToast('Ukuran foto melebihi dari 2 MB.', 'danger');
                    deleteSelectedFile(input.id, infoTextId, detailsContainerId, uploadBtnId,
                        previewWrapperId);
                    return;
                }

                fileNameSpan.textContent = file.name;
                fileTypeSpan.textContent = file.type || 'N/A';
                fileSizeSpan.textContent = formatBytes(file.size);
                fileInfoText.style.display = 'none';
                detailsContainer.style.display = 'flex';
                detailsContainer.classList.add('has-file');

                if (uploadBtnId.includes('_create')) {
                    uploadBtn.classList.add('upload-success');
                    uploadBtn.innerHTML = '<i class="fa fa-check"></i> Upload Berhasil';
                }

                previewWrapper.style.display = 'none';
                previewWrapper.classList.remove('has-image-preview');
                if (previewImage) previewImage.src = '#';

                const reader = new FileReader();
                reader.onload = function(e) {
                    const objectURL = URL.createObjectURL(file);
                    if (viewBtn) viewBtn._currentObjectURL = objectURL;

                    if (file.type.startsWith('image/')) {
                        if (filePreviewIcon) filePreviewIcon.src = e.target.result;
                        if (previewImage) {
                            previewImage.src = e.target.result;
                            previewWrapper.style.display = 'block';
                            previewWrapper.classList.add('has-image-preview');
                        }
                    } else if (file.type === 'application/pdf') {
                        if (filePreviewIcon) filePreviewIcon.src = defaultPdfIcon;
                    } else {
                        if (filePreviewIcon) filePreviewIcon.src = defaultImageIcon;
                    }

                    if (viewBtn && viewBtn.tagName === 'A') {
                        viewBtn.href = objectURL;
                        viewBtn.classList.remove('disabled');
                        viewBtn.style.cursor = 'pointer';
                        viewBtn.style.opacity = '1';
                    } else if (viewBtn) {
                        viewBtn.disabled = false;
                        viewBtn.style.cursor = 'pointer';
                        viewBtn.style.opacity = '1';
                    }
                }
                reader.onerror = function() {
                    console.error("Gagal membaca file.");
                    deleteSelectedFile(input.id, infoTextId, detailsContainerId, uploadBtnId, previewWrapperId);
                };
                reader.readAsDataURL(file);

            } else {
                deleteSelectedFile(input.id, infoTextId, detailsContainerId, uploadBtnId, previewWrapperId);
            }
        }

        function deleteSelectedFile(inputId, infoTextId, detailsContainerId, uploadBtnId, previewWrapperId) {
            const input = document.getElementById(inputId);
            const fileInfoText = document.getElementById(infoTextId);
            const detailsContainer = document.getElementById(detailsContainerId);
            const uploadBtn = document.getElementById(uploadBtnId);
            const viewBtn = document.getElementById(detailsContainerId.replace('uploadedFileDetails', 'viewBtn'));
            const previewWrapper = document.getElementById(previewWrapperId);
            const previewImage = document.getElementById('foto-preview');
            const fileNameSpan = document.getElementById(detailsContainerId.replace('uploadedFileDetails', 'fileName'));
            const fileTypeSpan = document.getElementById(detailsContainerId.replace('uploadedFileDetails', 'fileType'));
            const fileSizeSpan = document.getElementById(detailsContainerId.replace('uploadedFileDetails', 'fileSize'));
            const filePreviewIcon = document.getElementById(detailsContainerId.replace('uploadedFileDetails',
                'filePreviewIcon'));
            const defaultImageIcon = "{{ asset('images/default-image-icon.png') }}";

            if (viewBtn && viewBtn._currentObjectURL) {
                URL.revokeObjectURL(viewBtn._currentObjectURL);
                viewBtn._currentObjectURL = null;
            }

            input.value = '';
            fileInfoText.textContent = fileInfoText.dataset.defaultText || fileInfoText.textContent;
            fileInfoText.style.display = 'block';
            detailsContainer.style.display = 'none';
            detailsContainer.classList.remove('has-file');

            if (fileNameSpan) fileNameSpan.textContent = '';
            if (fileTypeSpan) fileTypeSpan.textContent = '';
            if (fileSizeSpan) fileSizeSpan.textContent = '';
            if (filePreviewIcon) filePreviewIcon.src = defaultImageIcon;

            if (uploadBtnId.includes('_create')) {
                uploadBtn.classList.remove('upload-success');
                uploadBtn.innerHTML = `<i class="fa fa-upload"></i> Upload`;
            } else if (uploadBtnId.includes('_edit')) {
                uploadBtn.innerHTML = `<i class="fa fa-upload"></i> Upload Baru`;
            }

            if (viewBtn && viewBtn.tagName === 'A') {
                viewBtn.href = '#';
                viewBtn.classList.add('disabled');
                viewBtn.style.cursor = 'not-allowed';
                viewBtn.style.opacity = '0.5';
            } else if (viewBtn) {
                viewBtn.disabled = true;
                viewBtn.style.cursor = 'not-allowed';
                viewBtn.style.opacity = '0.5';
            }

            if (previewWrapper && previewImage) {
                previewWrapper.style.display = 'none';
                previewWrapper.classList.remove('has-image-preview');
                previewImage.src = '#';
            }
        }

        function handleFileChangeForEdit(input, infoTextId, previewWrapperId, previewId) {
            const fileInfoText = document.getElementById(infoTextId);
            const wrapper = document.getElementById(previewWrapperId);
            const preview = document.getElementById(previewId);

            if (!wrapper || !preview || !fileInfoText) {
                console.error("Required elements not found for edit preview.");
                return;
            }

            const initialSrc = wrapper.dataset.initialSrc || '#';

            if (input.files && input.files[0]) {
                const file = input.files[0];

                const fileType = file.type;
                if (!fileType.startsWith('image/')) {
                    alert("Hanya file gambar (JPG, PNG, GIF) yang dapat dipilih.");
                    input.value = '';
                    preview.src = initialSrc;
                    wrapper.style.display = (initialSrc && initialSrc !== '#') ? 'block' :
                        'none';
                    fileInfoText.style.display = 'block';
                    return;
                }

                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    showMaterialToast('Ukuran foto melebihi dari 2 MB.', 'danger');
                    input.value = '';
                    preview.src = initialSrc;
                    wrapper.style.display = (initialSrc && initialSrc !== '#') ? 'block' : 'none';
                    fileInfoText.style.display = 'block';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    wrapper.style.display = 'block';
                    fileInfoText.style.display = 'none';
                }
                reader.onerror = function() {
                    console.error("Gagal membaca gambar untuk preview.");
                    input.value = '';
                    preview.src = initialSrc;
                    wrapper.style.display = (initialSrc && initialSrc !== '#') ? 'block' : 'none';
                    fileInfoText.style.display = 'block';
                }
                reader.readAsDataURL(file);

            } else {
                preview.src = initialSrc;
                wrapper.style.display = (initialSrc && initialSrc !== '#') ? 'block' :
                    'none';
                fileInfoText.style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const editPreviewWrapper = document.getElementById('foto-preview-wrapper');
            const editPreviewImage = document.getElementById('foto-preview');
            const editFileInfoText = document.getElementById('fileInfoText_edit');

            if (editPreviewWrapper && editPreviewImage) {
                editPreviewWrapper.dataset.initialDisplay = window.getComputedStyle(editPreviewWrapper).display;
            }
            if (editFileInfoText) {
                editFileInfoText.dataset.initialDisplay = window.getComputedStyle(editFileInfoText)
                    .display;
                editFileInfoText.dataset.defaultText = editFileInfoText.textContent;
            }

            const createDetailsContainer = document.getElementById('uploadedFileDetails_create');
            const createPreviewWrapper = document.getElementById(
                'foto-preview-wrapper');
            const createFileInfoText = document.getElementById('fileInfoText_create');
            if (createDetailsContainer) {
                createDetailsContainer.dataset.initialDisplay = window.getComputedStyle(createDetailsContainer)
                    .display;
            }
            if (createPreviewWrapper) {
                createPreviewWrapper.dataset.initialDisplay = window.getComputedStyle(createPreviewWrapper).display;
                createPreviewWrapper.dataset.initialSrc = '#';
            }
            if (createFileInfoText) {
                createFileInfoText.dataset.initialDisplay = window.getComputedStyle(createFileInfoText).display;
                createFileInfoText.dataset.defaultText = createFileInfoText.textContent;
            }

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
            const passwordConfirmation = document.getElementById('password_confirmation');

            function addToggleListener(toggleButton, passwordInput) {
                if (toggleButton && passwordInput) {
                    toggleButton.addEventListener('click', function() {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' :
                            'password';
                        passwordInput.setAttribute('type', type);

                        const icon = this.querySelector('i');
                        if (icon) {
                            icon.classList.toggle('fa-eye');
                            icon.classList.toggle('fa-eye-slash');
                        }
                    });
                }
            }

            addToggleListener(togglePassword, password);
            addToggleListener(togglePasswordConfirmation, passwordConfirmation);
        });
        const toastEl = document.getElementById('materialToast');
        const toastTitle = document.getElementById('toastTitle');
        const toastBody = document.getElementById('toastBody');
        const toastIcon = document.getElementById('toastIcon');
        const toastTime = document.getElementById('toastTime');

        let materialToast;
        if (toastEl) {
            materialToast = new bootstrap.Toast(toastEl, {
                delay: 5000
            });
        }

        function showMaterialToast(message, type, headerTitle, headerTime = 'Baru saja', iconClass) {
            if (!materialToast) return;

            toastIcon.classList.remove('text-success', 'text-danger', 'text-warning', 'text-info');
            toastTitle.classList.remove('text-success', 'text-danger', 'text-warning', 'text-info');

            let iconColorClass = 'text-info';
            let finalHeaderTitle = headerTitle;
            let finalIconClass = iconClass;

            switch (type) {
                case 'success':
                    iconColorClass = 'text-success';
                    if (!finalIconClass) finalIconClass = 'check_circle';
                    if (!finalHeaderTitle) finalHeaderTitle = 'Berhasil';
                    break;
                case 'danger':
                    iconColorClass = 'text-danger';
                    if (!finalIconClass) finalIconClass = 'error';
                    if (!finalHeaderTitle) finalHeaderTitle = 'Error';
                    break;
                case 'warning':
                    iconColorClass = 'text-warning';
                    if (!finalIconClass) finalIconClass = 'warning';
                    if (!finalHeaderTitle) finalHeaderTitle = 'Peringatan';
                    break;
                case 'info':
                default:
                    iconColorClass = 'text-info';
                    if (!finalIconClass) finalIconClass = 'info';
                    if (!finalHeaderTitle) finalHeaderTitle = 'Informasi';
                    break;
            }

            toastIcon.classList.add(iconColorClass);

            if (type === 'danger') {
                toastTitle.classList.add(iconColorClass);
            }

            toastIcon.innerText = finalIconClass;
            toastTitle.innerText = finalHeaderTitle;
            toastTime.innerText = headerTime;
            toastBody.innerHTML = message;

            materialToast.show();
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                showMaterialToast("{{ session('success') }}", 'success');
            @endif

            @if (session('error'))
                showMaterialToast("{{ session('error') }}", 'danger');
            @endif

            @if ($errors->any())
                showMaterialToast("{{ $errors->first() }}", 'danger', 'Error');
            @endif
        });
    </script>

    @vite(['resources/js/app.js'])

    @push('scripts')
        {{-- === [TAMBAHKAN BLOK SCRIPT INI] === --}}
        @auth
            @if (Auth::user()->role == 'operator')
                <script>
                    // Fungsi untuk menampilkan preview gambar (INI TETAP SAMA, BISA DIPAKAI ULANG)
                    function previewImage(input, previewId, textId) {
                        const preview = document.getElementById(previewId);
                        const text = document.getElementById(textId);

                        if (input.files && input.files[0]) {
                            // Validasi ukuran di frontend
                            if (input.files[0].size > 2 * 1024 * 1024) { // 2MB
                                showMaterialToast('Ukuran file melebihi 2MB.', 'danger');
                                input.value = ''; // Hapus file
                                return;
                            }

                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                                text.style.display = 'none';
                            }
                            reader.readAsDataURL(input.files[0]);
                        }
                    }

                    // [BARU] Fungsi untuk me-reset modal Kop
                    function resetModalKop() {
                        $('#formPengaturanKop')[0].reset();
                        $('#kop-surat-preview').attr('src', '').hide();
                        $('#kop-surat-text').show();
                        $('#kop-error-container').hide();
                        $('#kop-error-list').html('');
                    }

                    // [BARU] Fungsi untuk me-reset modal Tanda Tangan
                    function resetModalTtd() {
                        $('#formPengaturanTtd')[0].reset();
                        $('#tanda-tangan-preview').attr('src', '').hide();
                        $('#tanda-tangan-text').show();
                        $('#ttd-error-container').hide();
                        $('#ttd-error-list').html('');
                    }

                    $(document).ready(function() {
                        // Inisialisasi instance modal Bootstrap
                        const modalKopElement = document.getElementById('modalPengaturanKop');
                        const modalKop = modalKopElement ? new bootstrap.Modal(modalKopElement) : null;

                        const modalTtdElement = document.getElementById('modalPengaturanTtd');
                        const modalTtd = modalTtdElement ? new bootstrap.Modal(modalTtdElement) : null;

                        // Inisialisasi tooltip untuk tombol navbar baru
                        const kopButton = document.getElementById('btn-pengaturan-kop');
                        if (kopButton) {
                            new bootstrap.Tooltip(kopButton);
                        }

                        const ttdButton = document.getElementById('btn-pengaturan-ttd');
                        if (ttdButton) {
                            new bootstrap.Tooltip(ttdButton);
                        }


                        // === LOGIKA MODAL KOP SURAT ===

                        // 1. Saat tombol Kop Surat diklik, ambil data terbaru
                        $('#btn-pengaturan-kop').on('click', function() {
                            resetModalKop();
                            $('#nama-wilayah-modal-kop').text('Memuat...');

                            $.ajax({
                                url: "{{ route('pengaturan-wilayah.show') }}",
                                type: 'GET',
                                success: function(data) {
                                    $('#nama-wilayah-modal-kop').text(data.nama_wilayah ||
                                        'Tidak terdaftar');

                                    if (data.kop_surat_url) {
                                        $('#kop-surat-preview').attr('src', data.kop_surat_url + '?t=' +
                                            new Date().getTime()).show();
                                        $('#kop-surat-text').hide();
                                    } else {
                                        $('#kop-surat-preview').hide();
                                        $('#kop-surat-text').show();
                                    }
                                },
                                error: function(err) {
                                    $('#nama-wilayah-modal-kop').text('Gagal memuat');
                                    showMaterialToast(err.responseJSON.message ||
                                        'Gagal memuat data wilayah.', 'danger');
                                }
                            });
                        });

                        // 2. Saat form Kop Surat disubmit
                        $('#formPengaturanKop').on('submit', function(e) {
                            e.preventDefault();
                            let formData = new FormData(this);
                            let submitButton = $(this).find('button[type="submit"]');
                            let originalButtonHtml = submitButton.html();

                            // Tampilkan loading
                            submitButton.prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...'
                            );
                            $('#kop-error-container').hide();
                            $('#kop-error-list').html('');

                            $.ajax({
                                url: "{{ route('pengaturan-wilayah.update') }}",
                                type: 'POST',
                                data: formData, // Hanya akan berisi kop_surat
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    submitButton.prop('disabled', false).html(originalButtonHtml);
                                    if (modalKop) modalKop.hide();
                                    showMaterialToast(data.message, 'success');

                                    // Perbarui preview dengan URL baru jika ada
                                    if (data.kop_surat_url) {
                                        $('#kop-surat-preview').attr('src', data.kop_surat_url + '?t=' +
                                            new Date().getTime());
                                    }
                                },
                                error: function(jqXHR) {
                                    submitButton.prop('disabled', false).html(originalButtonHtml);

                                    if (jqXHR.status === 422) { // Error validasi
                                        let errors = jqXHR.responseJSON.errors;
                                        let errorMessage = 'Terjadi kesalahan validasi.'; // Pesan default

                                        // Ambil pesan error pertama untuk 'kop_surat'
                                        if (errors.kop_surat && errors.kop_surat.length > 0) {
                                            errorMessage = errors.kop_surat.join('<br>');
                                        }

                                        // [BARU] Set pesan error sebagai teks/html langsung ke div container
                                        $('#kop-error-container').html(errorMessage);
                                        $('#kop-error-container').show();
                                    } else {
                                        showMaterialToast(jqXHR.responseJSON.message ||
                                            'Terjadi kesalahan.', 'danger');
                                    }
                                }
                            });
                        });

                        // === LOGIKA MODAL TANDA TANGAN ===

                        // 1. Saat tombol Tanda Tangan diklik, ambil data terbaru
                        $('#btn-pengaturan-ttd').on('click', function() {
                            resetModalTtd();
                            $('#nama-wilayah-modal-ttd').text('Memuat...');

                            $.ajax({
                                url: "{{ route('pengaturan-wilayah.show') }}",
                                type: 'GET',
                                success: function(data) {
                                    $('#nama-wilayah-modal-ttd').text(data.nama_wilayah ||
                                        'Tidak terdaftar');

                                    if (data.tanda_tangan_url) {
                                        $('#tanda-tangan-preview').attr('src', data.tanda_tangan_url +
                                            '?t=' + new Date().getTime()).show();
                                        $('#tanda-tangan-text').hide();
                                    } else {
                                        $('#tanda-tangan-preview').hide();
                                        $('#tanda-tangan-text').show();
                                    }
                                },
                                error: function(err) {
                                    $('#nama-wilayah-modal-ttd').text('Gagal memuat');
                                    showMaterialToast(err.responseJSON.message ||
                                        'Gagal memuat data wilayah.', 'danger');
                                }
                            });
                        });

                        // 2. Saat form Tanda Tangan disubmit
                        $('#formPengaturanTtd').on('submit', function(e) {
                            e.preventDefault();
                            let formData = new FormData(this);
                            let submitButton = $(this).find('button[type="submit"]');
                            let originalButtonHtml = submitButton.html();

                            submitButton.prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...'
                            );
                            $('#ttd-error-container').hide();
                            $('#ttd-error-list').html('');

                            $.ajax({
                                url: "{{ route('pengaturan-wilayah.update') }}",
                                type: 'POST',
                                data: formData, // Hanya akan berisi tanda_tangan
                                contentType: false,
                                processData: false,
                                success: function(data) {
                                    submitButton.prop('disabled', false).html(originalButtonHtml);
                                    if (modalTtd) modalTtd.hide();
                                    showMaterialToast(data.message, 'success');

                                    // Perbarui preview dengan URL baru jika ada
                                    if (data.tanda_tangan_url) {
                                        $('#tanda-tangan-preview').attr('src', data.tanda_tangan_url +
                                            '?t=' + new Date().getTime());
                                    }
                                },
                                error: function(jqXHR) {
                                    submitButton.prop('disabled', false).html(originalButtonHtml);

                                    if (jqXHR.status === 422) { // Error validasi
                                        let errors = jqXHR.responseJSON.errors;
                                        let errorMessage = 'Terjadi kesalahan validasi.'; // Pesan default

                                        // Ambil pesan error pertama untuk 'tanda_tangan'
                                        if (errors.tanda_tangan && errors.tanda_tangan.length > 0) {
                                            errorMessage = errors.tanda_tangan.join('<br>');
                                        }

                                        // [BARU] Set pesan error sebagai teks/html langsung ke div container
                                        $('#ttd-error-container').html(errorMessage);
                                        $('#ttd-error-container').show();
                                    } else {
                                        showMaterialToast(jqXHR.responseJSON.message ||
                                            'Terjadi kesalahan.', 'danger');
                                    }
                                }
                            });
                        });
                    });
                </script>
            @endif
        @endauth
        {{-- === [AKHIR BLOK SCRIPT] === --}}
    @endpush

    @push('scripts')
        @auth
            @if (Auth::user()->role == 'pimpinan')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const modalTindakLanjutEl = document.getElementById('modalTindakLanjut');
                        if (!modalTindakLanjutEl) return;

                        const modalTindakLanjut = new bootstrap.Modal(modalTindakLanjutEl);
                        const formTindakLanjut = $('#formTindakLanjut');
                        const errorContainer = $('#tindakLanjutErrorContainer');
                        const submitBtn = $('#btnSubmitTindakLanjut');
                        const hiddenWilayahId = $('#tindakLanjut_id_wilayah');
                        const statusSelect = $('#tindakLanjut_status_wilayah');

                        // [TAMBAHAN BARU] Targetkan tombol "Abaikan"
                        const btnAbaikan = $('#btnAbaikanTindakLanjut');

                        // --- BAGIAN 1: Menyadap klik notifikasi ---
                        document.body.addEventListener('click', function(e) {
                            const link = e.target.closest('a');
                            const notificationItem = e.target.closest('li.notification-item');

                            if (link && notificationItem && (link.href.includes('?follow_up=') || link.href
                                    .includes('&follow_up='))) {

                                e.preventDefault();

                                const url = new URL(link.href);
                                const wilayahId = url.searchParams.get('follow_up');
                                const notifId = notificationItem.dataset.id;

                                if (wilayahId && notifId) {
                                    errorContainer.hide().html('');
                                    submitBtn.prop('disabled', false).html(
                                        '<i class="fa fa-save me-1"></i> Simpan Perubahan');
                                    hiddenWilayahId.val(wilayahId);
                                    formTindakLanjut.data('notification-id', notifId);
                                    statusSelect.val('Aman');
                                    modalTindakLanjut.show();
                                }
                            }
                        });

                        // --- BAGIAN 2: Menangani submit form modal (Simpan Perubahan) ---
                        formTindakLanjut.on('submit', function(e) {
                            e.preventDefault();
                            submitBtn.prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...'
                            );
                            errorContainer.hide();

                            $.ajax({
                                url: formTindakLanjut.attr('action'),
                                method: 'POST',
                                data: formTindakLanjut.serialize(),
                                success: function(response) {
                                    modalTindakLanjut.hide();
                                    showMaterialToast(response.message, 'success');

                                    const notifId = formTindakLanjut.data('notification-id');
                                    if (notifId) {
                                        markNotificationAsReadUI(notifId);
                                        formTindakLanjut.data('notification-id', null);
                                    }
                                },
                                error: function(jqXHR) {
                                    let msg = 'Terjadi kesalahan. Silakan coba lagi.';
                                    if (jqXHR.status === 422) {
                                        msg = Object.values(jqXHR.responseJSON.errors).join(
                                            '<br>');
                                    } else if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                                        msg = jqXHR.responseJSON.message;
                                    }
                                    errorContainer.html(msg).show();
                                    submitBtn.prop('disabled', false).html(
                                        '<i class="fa fa-save me-1"></i> Simpan Perubahan');
                                }
                            });
                        });

                        // --- [BLOK BARU] ---
                        // --- BAGIAN 3: Menangani klik tombol "Abaikan" ---
                        btnAbaikan.on('click', function() {
                            // 1. Ambil ID notifikasi yang tersimpan
                            const notifId = formTindakLanjut.data('notification-id');

                            if (notifId) {
                                // 2. Panggil fungsi yang sama untuk menandai terbaca
                                //    Fungsi ini sudah menangani AJAX, badge, dan penghapusan <li>
                                markNotificationAsReadUI(notifId);

                                // 3. Reset data-id
                                formTindakLanjut.data('notification-id', null);
                            }

                            // 4. Tutup modal secara manual
                            modalTindakLanjut.hide();
                        });
                        // --- [AKHIR BLOK BARU] ---


                        // --- FUNGSI HELPER: Menandai notifikasi sebagai terbaca (di server dan UI) ---
                        function markNotificationAsReadUI(notifId) {
                            // 1. Kirim request ke server untuk update database
                            $.post('{{ route('notifications.markOneAsRead') }}', {
                                    id: notifId,
                                    _token: '{{ csrf_token() }}'
                                })
                                .done(function() {
                                    // 2. Hapus item notifikasi dari dropdown
                                    const itemToRemove = $('li.notification-item[data-id="' + notifId +
                                        '"]');
                                    if (itemToRemove.length) {
                                        itemToRemove.remove();
                                    }

                                    // 3. Update badge count
                                    const badge = $('#notification-badge');
                                    if (badge.length) {
                                        let currentCount = parseInt(badge.text()) || 0;
                                        if (currentCount > 0) {
                                            currentCount--;
                                            badge.text(currentCount);
                                        }

                                        // Jika count jadi 0, sembunyikan badge dan tombol 'mark all'
                                        if (currentCount === 0) {
                                            badge.hide();
                                            $('#mark-as-read-container').hide();
                                            $('#mark-as-read-divider').hide();
                                            $('#notification-empty-state').show();
                                        }
                                    }
                                })
                                .fail(function() {
                                    console.error('Gagal menandai notifikasi ' + notifId +
                                        ' sebagai terbaca.');
                                    // Jika gagal di server, setidaknya hapus dari UI agar tidak diklik lagi
                                    $('li.notification-item[data-id="' + notifId + '"]').remove();
                                });
                        }

                    });
                </script>
            @endif
        @endauth
    @endpush

    @stack('scripts')

    {{-- KODE LISTENER REAL-TIME --}}
    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const userId = document.querySelector("meta[name='user-id']").getAttribute('content');

                if (userId) {
                    console.log(`[Reverb] Mendengarkan di channel privat: App.Models.User.${userId}`);

                    window.Echo.private(`App.Models.User.${userId}`)
                        .notification((notification) => {
                            console.log('[Reverb] Menerima Notifikasi:', notification);

                            // 1. Tampilkan Toast (menggunakan fungsi Anda dari master.blade.php)
                            if (typeof showMaterialToast === 'function') {
                                showMaterialToast(notification.message, 'info', 'Notifikasi Baru');
                            }

                            // 2. Update Badge
                            updateNotificationBadge();

                            // 3. Tambahkan ke Dropdown
                            prependNotificationToDropdown(notification);

                            // 4. (Opsional Skalabilitas) Update data dashboard jika sedang dibuka
                            updateDashboardCounts(notification);

                            const markAsReadContainer = document.getElementById('mark-as-read-container');
                            const markAsReadDivider = document.getElementById('mark-as-read-divider');
                            if (markAsReadContainer) markAsReadContainer.style.display = 'list-item';
                            if (markAsReadDivider) markAsReadDivider.style.display = 'list-item';
                        });
                }

                function updateNotificationBadge() {
                    const badge = document.getElementById('notification-badge');
                    if (!badge) return;

                    badge.style.display = 'block';
                    let currentCount = parseInt(badge.innerText) || 0;
                    badge.innerText = currentCount + 1;
                }

                function prependNotificationToDropdown(notification) {
                    const dropdownMenu = document.getElementById('notification-dropdown-menu');
                    const emptyState = document.getElementById('notification-empty-state');

                    // [FIX 1] Ambil divider, BUKAN header
                    const divider = document.getElementById('mark-as-read-divider');
                    const header = dropdownMenu ? dropdownMenu.querySelector('.dropdown-header') : null;

                    if (!dropdownMenu) {
                        console.error("Elemen dropdown notifikasi tidak ditemukan.");
                        return;
                    }

                    if (emptyState) {
                        emptyState.style.display = 'none';
                    }

                    let iconHtml = '';
                    const message = notification.message || 'Notifikasi baru.';

                    if (message.includes('disetujui')) {
                        iconHtml =
                            '<div class="text-center me-2 d-flex align-items-center justify-content-center"><i class="material-symbols-rounded avatar avatar-sm text-success bg-gradient-light me-3 py-2">check_circle</i></div>';
                    } else if (message.includes('revisi')) {
                        iconHtml =
                            '<div class="text-center me-2 d-flex align-items-center justify-content-center"><i class="material-symbols-rounded avatar avatar-sm text-warning bg-gradient-light me-3 py-2">edit_note</i></div>';
                    } else {
                        iconHtml =
                            '<div class="text-center me-2 d-flex align-items-center justify-content-center"><i class="material-symbols-rounded avatar avatar-sm text-info bg-gradient-light me-3 py-2">campaign</i></div>';
                    }

                    // [LOGIKA URL BARU]
                    const url = notification.url || '#';
                    const notificationId = notification.notification_id; // <-- Ambil ID baru
                    let finalUrl = url;

                    if (notificationId) {
                        // Tambahkan parameter query ?mark_as_read=
                        finalUrl += (url.includes('?') ? '&' : '?') + 'mark_as_read=' + notificationId;
                    }

                    const newNotificationHtml = `
                        <li class="mb-2 notification-item" data-id="${notification.id || ''}">
                            <a class="dropdown-item border-radius-md bg-gray-100" href="${finalUrl}">
                                <div class="d-flex py-1">
                                    <div class="my-auto">${iconHtml}</div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="text-sm font-weight-normal mb-1">${message}</h6>
                                        <p class="text-xs text-secondary mb-0"><i class="fa fa-clock me-1"></i> Baru saja</p>
                                    </div>
                                </div>
                            </a>
                        </li>`;

                    // [FIX 1] Sisipkan SETELAH divider, atau setelah header jika divider tidak ada
                    if (divider) {
                        divider.insertAdjacentHTML('afterend', newNotificationHtml);
                    } else if (header) {
                        header.insertAdjacentHTML('afterend', newNotificationHtml);
                    }
                }

                function updateDashboardCounts(notification) {
                    // 1. Logika untuk Dashboard Pimpinan
                    // Ini akan menangkap 'NotifikasiUntukPimpinan' (baik laporan baru ATAU revisi)
                    if (document.body.classList.contains('dashboard-pimpinan') && notification.type.includes(
                            'NotifikasiUntukPimpinan')) {

                        // --- Bagian 1: Update Hitungan di Card ---
                        const pendingCountEl = document.getElementById('pimpinan-pending-count');
                        if (pendingCountEl) {
                            let currentCount = parseInt(pendingCountEl.innerText) || 0;
                            pendingCountEl.innerText = currentCount + 1;
                        }

                        // --- Bagian 2: Tambahkan Laporan ke Tabel "Laporan Terbaru" ---
                        const listBody = document.getElementById('pimpinan-pending-list-tbody');
                        const emptyState = document.getElementById('pimpinan-empty-state');

                        if (listBody) {
                            // Sembunyikan pesan "kosong" jika ada
                            if (emptyState) {
                                emptyState.style.display = 'none';
                            }

                            const url = notification.url || '#';
                            const notificationId = notification.notification_id; // <-- Ambil ID baru
                            let finalUrl = url;

                            if (notificationId) {
                                finalUrl += (url.includes('?') ? '&' : '?') + 'mark_as_read=' + notificationId;
                            }

                            // Buat elemen <tr> baru dengan highlight
                            const newRow = document.createElement('tr');
                            newRow.style.backgroundColor = '#f8f9fa'; // Warna highlight sementara

                            newRow.innerHTML = `
                                        <td class="px-3 py-3 border-bottom">
                                            <div class="d-flex align-items-center">
                                                <div class="icon icon-shape icon-sm shadow-sm border-radius-md bg-white text-center me-3 flex-shrink-0">
                                                    <i class="material-symbols-rounded text-dark opacity-10 fs-5">assignment</i>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm font-weight-normal">
                                                        ${notification.judul || 'Laporan Situasi Daerah'}
                                                        <span class="text-xs text-muted ms-1">- Tanggal: ${notification.tanggal_laporan_human || 'N/A'}</span>
                                                    </h6>
                                                    <p class="text-xs text-secondary mb-0">
                                                        <i class="fa fa-user opacity-6 me-1"></i> ${notification.operator_name || 'N/A'}
                                                        <span class="ms-2"><i class="fa fa-clock opacity-6 me-1"></i> ${notification.created_at_human || 'Baru saja'}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-end px-3 py-3 border-bottom">
                                            <a href="${finalUrl}" class="btn btn-dark-blue bg-gradient-dark-blue btn-sm px-3 py-1 my-auto shadow-sm mb-0">
                                                <i class="fa fa-search me-1"></i> Periksa
                                            </a>
                                        </td>
                                    `;

                            // Tambahkan baris baru ke paling atas tabel
                            listBody.prepend(newRow);
                        }
                    }

                    // 2. Logika untuk Dashboard Operator (Untuk notifikasi 'revisi')
                    if (document.body.classList.contains('dashboard-operator') && notification.type.includes(
                            'NotifikasiUntukOperator')) {
                        if (notification.message.includes('revisi')) {
                            const revisiCountEl = document.getElementById('operator-revisi-count');
                            if (revisiCountEl) {
                                let currentCount = parseInt(revisiCountEl.innerText) || 0;
                                revisiCountEl.innerText = currentCount + 1;
                            }
                            // Di sini Anda juga bisa menambahkan logika untuk prepend ke daftar "Perlu Revisi" di dashboard operator
                        }
                    }
                }
                // ===================================================================
                // AKHIR FUNGSI PENGGANTI
                // ===================================================================

            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const dropdownMenu = document.getElementById('notification-dropdown-menu');

                if (dropdownMenu) {
                    dropdownMenu.addEventListener('click', function(e) {
                        const markAsReadBtn = e.target.closest('#mark-all-as-read-btn');
                        if (!markAsReadBtn) return;

                        e.preventDefault();

                        const notificationBadge = document.getElementById('notification-badge');
                        const markAsReadContainer = document.getElementById('mark-as-read-container');
                        const markAsReadDivider = document.getElementById('mark-as-read-divider');

                        // [FIX 2] Pastikan kita MENEMUKAN empty state
                        const emptyState = document.getElementById('notification-empty-state');
                        const allNotificationItems = dropdownMenu.querySelectorAll('li.notification-item');

                        // 1. Sembunyikan badge, tombol, dan divider
                        if (notificationBadge) notificationBadge.style.display = 'none';
                        if (markAsReadContainer) markAsReadContainer.style.display = 'none';
                        if (markAsReadDivider) markAsReadDivider.style.display = 'none';

                        // 2. Sembunyikan SEMUA item notifikasi
                        allNotificationItems.forEach(item => {
                            item.style.display = 'none';
                        });

                        // 3. Tampilkan pesan "Tidak ada notifikasi"
                        if (emptyState) {
                            emptyState.style.display = 'list-item'; // Pastikan ini 'list-item'
                        } else {
                            // Fallback jika emptyState (karena Bug 3) tidak ada
                            console.error('Elemen #notification-empty-state tidak ditemukan!');
                        }

                        // 4. Kirim request ke server
                        axios.post('{{ route('notifications.markAsRead') }}', {
                                _token: '{{ csrf_token() }}'
                            })
                            .catch(error => {
                                console.error('Gagal menandai notifikasi sebagai terbaca:', error);
                                // Rollback jika gagal
                                if (notificationBadge) notificationBadge.style.display = 'block';
                                if (markAsReadContainer) markAsReadContainer.style.display = 'list-item';
                                if (markAsReadDivider) markAsReadDivider.style.display = 'list-item';
                                allNotificationItems.forEach(item => {
                                    item.style.display = 'list-item';
                                });
                                if (emptyState) emptyState.style.display = 'none';
                            });
                    });
                }
            });
        </script>
    @endauth
</body>

</html>
