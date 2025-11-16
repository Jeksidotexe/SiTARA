@extends('layouts.master')

@section('page', 'Edit Laporan Penguatan Ideologi Pancasila dan Karakter')
@section('title', 'Edit Laporan Penguatan Ideologi Pancasila dan Karakter')

@push('styles')
    <style>
        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .tox-tinymce {
            border: 1px solid #d2d6da;
            border-radius: 0.375rem;
        }

        .progress {
            height: 20px;
            font-size: 0.875rem;
        }

        .progress-bar {
            color: #ffffff;
            font-weight: 600;
            line-height: 20px;
        }

        input[type="file"].custom-file-input {
            display: none;
        }

        /* === CSS BARU DISALIN DARI CREATE.BLADE.PHP === */
        .file-list-container {
            margin-top: 15px;
            border: 1px solid #d2d6da;
            border-radius: 0.375rem;
            padding: 10px;
            background-color: #f8f9fa;
        }

        .file-list-item {
            display: flex;
            /* justify-content: space-between; <- Dihapus agar tombol rapat ke kanan */
            align-items: center;
            padding: 5px 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.9rem;
        }

        .file-list-item:last-child {
            border-bottom: none;
        }

        /* Container for thumbnail or icon */
        .file-preview-thumbnail {
            width: 50px;
            height: 50px;
            margin-right: 15px;
            flex-shrink: 0;
            background-color: #e9ecef;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Style for image thumbnail */
        .file-preview-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Style for icon (if not image) */
        .file-preview-thumbnail .file-icon {
            font-size: 1.5rem;
            color: #6c757d;
        }

        .file-list-item .file-info {
            flex-grow: 1;
            /* Membuat info file mengisi sisa ruang */
            margin-right: 10px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .file-list-item .file-size {
            color: #6c757d;
            font-size: 0.85rem;
            margin-left: 10px;
        }

        .file-list-item .btn {
            flex-shrink: 0;
            /* Mencegah tombol mengecil */
        }

        /* === AKHIR DARI CSS BARU === */
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Laporan Penguatan Ideologi Pancasila dan Karakter</h5>
                        <a href="{{ route('laporan_penguatan_ideologi.index') }}"
                            class="btn btn-secondary bg-gradient-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-4">II. LAPORAN PERMASALAHAN STRATEGIS</h5>
                    </div>

                    <div class="progress mb-4" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar progress-bar-striped bg-gradient-dark" id="progress-bar" style="width: 0%">
                        </div>
                    </div>

                    <form action="{{ route('laporan_penguatan_ideologi.update', $laporan->id_laporan) }}" method="POST"
                        id="laporan-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @php
                            $langkah = [
                                'deskripsi' => ['title' => false, 'field' => 'deskripsi', 'file' => false],
                                'a' => [
                                    'title' => 'A. Penyelenggaraan Pemerintah Daerah',
                                    'field' => 'narasi_a',
                                    'file' => 'file_a',
                                ],
                                'b' => [
                                    'title' => 'B. Pelaksanaan Program Pembangunan',
                                    'field' => 'narasi_b',
                                    'file' => 'file_b',
                                ],
                                'c' => ['title' => 'C. Pelayanan Publik', 'field' => 'narasi_c', 'file' => 'file_c'],
                                'd' => ['title' => 'D. Ideologi', 'field' => 'narasi_d', 'file' => 'file_d'],
                                'e' => ['title' => 'E. Politik', 'field' => 'narasi_e', 'file' => 'file_e'],
                                'f' => ['title' => 'F. Ekonomi', 'field' => 'narasi_f', 'file' => 'file_f'],
                                'g' => ['title' => 'G. Sosial Budaya', 'field' => 'narasi_g', 'file' => 'file_g'],
                                'h' => ['title' => 'H. Hankam', 'field' => 'narasi_h', 'file' => 'file_h'],
                                'penutup' => ['title' => 'III. Penutup', 'field' => 'penutup', 'file' => false],
                            ];
                        @endphp

                        @foreach ($langkah as $key => $step)
                            <div id="step-{{ $loop->iteration }}" class="form-step {{ $loop->first ? 'active' : '' }}">
                                @if ($step['title'])
                                    <h5 class="mb-3">{{ $step['title'] }}</h5>
                                @endif
                                <div class="mb-3">
                                    <textarea id="{{ $step['field'] }}" name="{{ $step['field'] }}" class="form-control tinymce-editor" rows="10">{{ old($step['field'], $laporan->{$step['field']}) }}</textarea>
                                    @if ($step['field'] == 'penutup')
                                        <div id="penutup-error" class="mt-2"
                                            style="color: #dc3545; font-size: 0.875em; display: none;">
                                            Penutup laporan wajib diisi.
                                        </div>
                                    @endif
                                </div>

                                @if ($step['file'])
                                    @php
                                        $fileKey = $step['file'];
                                        $existingFiles = $laporan->$fileKey ?? [];
                                    @endphp

                                    @if (!empty($existingFiles) && is_array($existingFiles))
                                        <div class="mb-3">
                                            <label class="form-label">File Saat Ini</label>
                                            <div class="file-list-container" style="display: block;">
                                                @foreach ($existingFiles as $filePath)
                                                    @php
                                                        $fullPath = asset($filePath);
                                                        $fileName = basename($filePath);
                                                        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                        $isImage = in_array($fileExt, [
                                                            'jpg',
                                                            'jpeg',
                                                            'png',
                                                            'gif',
                                                            'bmp',
                                                            'webp',
                                                        ]);
                                                        // PERUBAHAN: Menghapus 'isPdf'
                                                    @endphp

                                                    <div class="file-list-item" data-path="{{ $filePath }}">
                                                        <div class="file-preview-thumbnail">
                                                            @if ($isImage)
                                                                <img src="{{ $fullPath }}" alt="Preview">
                                                            @else
                                                                {{-- Ikon fallback untuk file lama (mungkin PDF) --}}
                                                                <i class="fa fa-file-alt file-icon"></i>
                                                            @endif
                                                        </div>

                                                        <span class="file-info">
                                                            {{ $fileName }}
                                                        </span>

                                                        <button type="button"
                                                            class="btn btn-dark bg-gradient-dark btn-sm me-1"
                                                            {{-- PERUBAHAN: Menghapus parameter 'isPdf' --}}
                                                            onclick="showPreview('{{ $fullPath }}', {{ $isImage ? 'true' : 'false' }})">
                                                            <i class="fa fa-eye"></i>
                                                        </button>

                                                        <button type="button" class="btn btn-danger btn-sm"
                                                            onclick="markForDeletion(this, '{{ $fileKey }}', '{{ $filePath }}')">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif


                                    <div class="mb-3">
                                        {{-- PERUBAHAN LABEL --}}
                                        <label class="form-label">Upload File Baru</label>
                                        <div>
                                            <input class="custom-file-input" type="file" id="{{ $fileKey }}"
                                                name="{{ $fileKey }}[]" multiple
                                                onchange="addFilesToDataTransfer('{{ $fileKey }}', 'file-list-{{ $key }}')"
                                                {{-- PERUBAHAN: Mengubah 'accept' --}} accept="image/*">

                                            <label for="{{ $fileKey }}" class="btn btn-sm bg-gradient-dark">
                                                <i class="fa fa-upload"></i> Pilih File
                                            </label>
                                        </div>

                                        <div class="file-list-container" id="file-list-{{ $key }}"
                                            style="display: none;">
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            {{-- PERUBAHAN: Mengubah teks bantuan --}}
                                            *Hanya menerima file gambar (JPG, JPEG, PNG). Maks. 2MB per
                                            file.
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <div id="deleted-files-container"></div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-sm btn-light bg-gradient-light" id="prev-btn"
                                style="display: none;">
                                <i class="fa fa-arrow-left"></i> Sebelumnya
                            </button>
                            <button type="button" class="btn btn-sm btn-dark bg-gradient-dark" id="next-btn">
                                Selanjutnya <i class="fa fa-arrow-right"></i>
                            </button>
                            <button type="submit" class="btn btn-dark bg-gradient-dark btn-sm" id="submit-btn"
                                style="display: none;">
                                <i class="fa fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Pratinjau File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img src="" id="previewImage" class="img-fluid" alt="Preview"
                        style="display: none; max-height: 75vh;">
                    <iframe src="" id="previewFrame"
                        style="width: 100%; height: 75vh; border: none; display: none;"></iframe>
                    <div id="previewFallback" style="display: none;">
                        <i class="fa fa-file-alt" style="font-size: 80px; color: #6c757d;"></i>
                        <p class="mt-3">Pratinjau tidak tersedia untuk tipe file ini.</p>
                        <a href="" id="previewDownloadLink" class="btn btn-dark" target="_blank">
                            <i class="fa fa-download"></i> Download File
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('master/assets/js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

    <script>
        let previewModal = null;
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('previewModal')) {
                previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            }
        });

        // PERUBAHAN: Menghapus parameter 'isPdf'
        function showPreview(filePath, isImage) {
            if (!previewModal) return;

            const imgEl = document.getElementById('previewImage');
            const frameEl = document.getElementById('previewFrame');
            const fallbackEl = document.getElementById('previewFallback');
            const downloadLink = document.getElementById('previewDownloadLink');

            imgEl.style.display = 'none';
            imgEl.src = '';
            frameEl.style.display = 'none';
            frameEl.src = '';
            fallbackEl.style.display = 'none';

            if (isImage) {
                imgEl.src = filePath;
                imgEl.style.display = 'block';
            } else {
                // Ini untuk file lama yg mungkin bukan gambar (cth: PDF lama)
                downloadLink.href = filePath;
                fallbackEl.style.display = 'block';
            }

            previewModal.show();
        }

        const deletedFilesContainer = document.getElementById('deleted-files-container');

        function markForDeletion(button, fileKey, filePath) {
            const item = button.closest('.file-list-item');
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `deleted_files[${fileKey}][]`;
            hiddenInput.value = filePath;
            deletedFilesContainer.appendChild(hiddenInput);
            item.style.opacity = '0.5';
            item.style.textDecoration = 'line-through';
            button.innerHTML = '<i class="fa fa-undo"></i>';
            button.classList.remove('btn-danger');
            button.classList.add('btn-warning');
            button.onclick = function() {
                unmarkForDeletion(button, hiddenInput);
            };
        }

        function unmarkForDeletion(button, hiddenInput) {
            const item = button.closest('.file-list-item');
            deletedFilesContainer.removeChild(hiddenInput);
            item.style.opacity = '1';
            item.style.textDecoration = 'none';
            button.innerHTML = '<i class="fa fa-trash"></i>';
            button.classList.remove('btn-warning');
            button.classList.add('btn-danger');
            const filePath = item.getAttribute('data-path');
            const fileKey = hiddenInput.name.match(/\[(.*?)\]/)[1];
            button.onclick = function() {
                markForDeletion(this, fileKey, filePath);
            };
        }

        let fileUploads = {};

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        function renderFileList(inputId, containerId) {
            const container = document.getElementById(containerId);
            const input = document.getElementById(inputId);
            const files = fileUploads[inputId] ? fileUploads[inputId].files : new FileList();

            container.innerHTML = '';

            if (!files || files.length === 0) {
                container.style.display = 'none';
                input.files = new DataTransfer().files;
                return;
            }

            container.style.display = 'block';

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileSize = formatBytes(file.size);

                const fileItem = document.createElement('div');
                fileItem.className = 'file-list-item';

                const thumbnailContainer = document.createElement('div');
                thumbnailContainer.className = 'file-preview-thumbnail';

                const fileInfo = document.createElement('span');
                fileInfo.className = 'file-info';
                fileInfo.textContent = file.name;
                const fileSizeSpan = document.createElement('span');
                fileSizeSpan.className = 'file-size';
                fileSizeSpan.textContent = ` (${fileSize})`;
                fileInfo.appendChild(fileSizeSpan);

                const previewBtn = document.createElement('button');
                previewBtn.type = 'button';
                previewBtn.className = 'btn btn-dark bg-gradient-dark btn-sm me-1';
                previewBtn.innerHTML = '<i class="fa fa-eye"></i>';
                previewBtn.disabled = true;

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-danger btn-sm';
                removeBtn.innerHTML = '<i class="fa fa-trash"></i>';
                removeBtn.onclick = function() {
                    removeFile(inputId, containerId, i);
                };

                const isImage = file.type.startsWith('image/');
                // PERUBAHAN: Menghapus 'isPdf'

                // PERUBAHAN: Hanya menyisakan logika 'isImage'
                if (isImage) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        thumbnailContainer.appendChild(img);

                        previewBtn.disabled = false;
                        // PERUBAHAN: Menghapus parameter 'isPdf'
                        previewBtn.onclick = () => showPreview(e.target.result, true);
                    };
                    reader.readAsDataURL(file);
                }
                // PERUBAHAN: Menghapus blok 'else if (isPdf)' dan 'else'

                fileItem.appendChild(thumbnailContainer);
                fileItem.appendChild(fileInfo);
                fileItem.appendChild(previewBtn);
                fileItem.appendChild(removeBtn);

                container.appendChild(fileItem);
            }
            input.files = files;
        }

        // --- FUNGSI UTAMA DENGAN VALIDASI TOAST ---
        function addFilesToDataTransfer(inputId, containerId) {
            const input = document.getElementById(inputId);
            const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2MB in bytes

            if (!fileUploads[inputId]) {
                fileUploads[inputId] = new DataTransfer();
            }
            const newFiles = input.files ? Array.from(input.files) : [];
            input.value = ''; // Kosongkan input file asli

            let rejectedFilesType = []; // Array untuk file dengan tipe salah
            let rejectedFilesSize = []; // Array untuk file yang ukurannya terlalu besar

            if (newFiles.length > 0) {
                for (const file of newFiles) {
                    const isImage = file.type.startsWith('image/');

                    // 1. Validasi Tipe
                    if (!isImage) {
                        rejectedFilesType.push(file.name);
                        continue; // Lanjut ke file berikutnya
                    }

                    // 2. Validasi Ukuran (hanya jika lolos validasi tipe)
                    if (file.size > MAX_FILE_SIZE) {
                        rejectedFilesSize.push(`${file.name} (${formatBytes(file.size)})`);
                        continue; // Lanjut ke file berikutnya
                    }

                    // 3. Jika lolos semua, tambahkan ke daftar
                    fileUploads[inputId].items.add(file);
                }
            }

            // --- PERUBAHAN: MENGGUNAKAN TOAST (TANPA LIST) ---
            if (rejectedFilesType.length > 0) {
                // Gabungkan nama file dengan <br>
                let message = 'File berikut ditolak (hanya gambar):<br>' + rejectedFilesType.join('<br>');

                if (typeof showMaterialToast === 'function') {
                    showMaterialToast(message, 'danger', 'Tipe File Ditolak');
                } else {
                    // Fallback jika fungsi toast tidak ditemukan
                    alert('File berikut ditolak karena tipe filenya tidak valid (hanya gambar):\n\n- ' +
                        rejectedFilesType.join('\n- '));
                }
            }

            if (rejectedFilesSize.length > 0) {
                // Gabungkan nama file dengan <br>
                let message = 'File berikut ditolak (melebihi 2MB):<br>' + rejectedFilesSize.join('<br>');

                if (typeof showMaterialToast === 'function') {
                    showMaterialToast(message, 'danger', 'Ukuran File Ditolak');
                } else {
                    alert('File berikut ditolak karena ukurannya melebihi 2MB:\n\n- ' + rejectedFilesSize.join(
                        '\n- '));
                }
            }
            // --- AKHIR PERUBAHAN ---

            renderFileList(inputId, containerId);
        }


        function removeFile(inputId, containerId, index) {
            if (!fileUploads[inputId]) return;
            fileUploads[inputId].items.remove(index);
            renderFileList(inputId, containerId);
        }

        tinymce.init({
            selector: '.tinymce-editor',
            license_key: 'gpl',
            promotion: false,
            plugins: 'autoresize link lists image table code wordcount',
            toolbar: 'undo redo | blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist checklist outdent indent | removeformat | a11ycheck code table help',
            menubar: true,
            statusbar: true,
            min_height: 500,
            autoresize_bottom_margin: 20
        });

        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 1;
            const totalSteps = document.querySelectorAll('.form-step').length;
            const progressBar = document.getElementById('progress-bar');
            const nextBtn = document.getElementById('next-btn');
            const prevBtn = document.getElementById('prev-btn');
            const submitBtn = document.getElementById('submit-btn');

            function updateButtons() {
                prevBtn.style.display = (currentStep === 1) ? 'none' : 'inline-block';
                if (currentStep === totalSteps) {
                    nextBtn.style.display = 'none';
                    submitBtn.style.display = 'inline-block';
                } else {
                    nextBtn.style.display = 'inline-block';
                    submitBtn.style.display = 'none';
                }
            }

            function updateProgressBar() {
                const percentage = Math.round((currentStep / totalSteps) * 100);
                progressBar.style.width = percentage + '%';
                progressBar.setAttribute('aria-valuenow', percentage);
                progressBar.textContent = percentage + '%';
            }

            function showStep(stepNumber) {
                document.querySelectorAll('.form-step').forEach((step, index) => {
                    step.classList.toggle('active', (index + 1) === stepNumber);
                });
                window.scrollTo(0, 0);
                updateButtons();
                updateProgressBar();
            }

            nextBtn.addEventListener('click', () => {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            });

            prevBtn.addEventListener('click', () => {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });

            showStep(currentStep);

            const form = document.getElementById('laporan-form');
            const submitButton = document.getElementById('submit-btn');
            const penutupError = document.getElementById('penutup-error');

            form.addEventListener('submit', function(event) {
                tinymce.triggerSave();

                const penutupEditor = tinymce.get('penutup');
                let penutupContent = '';
                let penutupContainer = null;

                if (penutupEditor) {
                    penutupContent = penutupEditor.getContent({
                        format: 'text'
                    }).trim();

                    penutupContainer = penutupEditor.getContentAreaContainer();
                }

                if (penutupError) penutupError.style.display = 'none';
                if (penutupContainer) {
                    penutupContainer.style.border = '';
                    penutupContainer.style.borderRadius = '';
                }

                if (penutupContent === '') {
                    event.preventDefault();

                    if (penutupError) penutupError.style.display = 'block';

                    if (penutupContainer) {
                        penutupContainer.style.border = '2px solid #f44335';
                        penutupContainer.style.borderRadius = '0.375rem';
                    }

                    penutupEditor.once('focus', () => {
                        if (penutupContainer) {
                            penutupContainer.style.border = '';
                            penutupContainer.style.borderRadius = '';
                        }
                        if (penutupError) penutupError.style.display = 'none';
                    });

                } else {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Menyimpan...';
                }
            });
        });
    </script>
@endpush
