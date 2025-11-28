@extends('layouts.master')
@section('page', 'Tambah Laporan Harian Pilkada Serentak')
@section('title', 'Tambah Laporan Harian Pilkada Serentak')

@push('styles')
    <style>
        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .tox-tinymce {
            border: 1px solid #dad2d2;
            border-radius: 0.375rem;
        }

        .progress {
            height: 20px;
            font-size: 0.875rem;
        }

        input[type="file"].custom-file-input {
            display: none;
        }

        .file-list-container {
            margin-top: 15px;
            border: 1px solid #d2d6da;
            border-radius: 0.375rem;
            padding: 10px;
            background-color: #f8f9fa;
        }

        .file-list-item {
            display: flex;
            align-items: center;
            padding: 5px 10px;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.9rem;
        }

        .file-list-item:last-child {
            border-bottom: none;
        }

        .file-list-item .file-info {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-right: 10px;
        }

        .file-list-item .file-size {
            color: #6c757d;
            font-size: 0.85rem;
            margin-left: 10px;
        }

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

        .file-preview-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .file-preview-thumbnail .file-icon {
            font-size: 1.5rem;
            color: #6c757d;
        }

        .file-list-item .file-info {
            flex-grow: 1;
            margin-right: 10px;
        }

        .file-list-item .btn {
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tambah Laporan Harian Pilkada Serentak</h5>
                        <a href="{{ route('laporan_pilkada_serentak.index') }}"
                            class="btn btn-sm btn-secondary bg-gradient-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-5">II. LAPORAN PERMASALAHAN STRATEGIS</h5>
                    </div>
                    <div class="progress mb-4" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar progress-bar-striped bg-gradient-dark" id="progress-bar" style="width: 0%">
                        </div>
                    </div>

                    <form action="{{ route('laporan_pilkada_serentak.store') }}" method="POST" id="laporan-form"
                        enctype="multipart/form-data">
                        @csrf

                        @php
                            $langkah = [
                                'deskripsi' => [
                                    'title' => false,
                                    'field' => 'deskripsi',
                                    'file' => false,
                                ],
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
                                    <textarea id="{{ $step['field'] }}" name="{{ $step['field'] }}" class="form-control tinymce-editor" rows="10"
                                        @if ($step['field'] == 'penutup')  @endif>
                                        @if ($step['field'] == 'deskripsi')
{!! old($step['field'], '<h2 style="text-align: center;">LAPORAN PUSKOMIN</h2>') !!}@else{!! old($step['field']) !!}
@endif
                                    </textarea>
                                    @if ($step['field'] == 'penutup')
                                        <div id="penutup-error" class="mt-2"
                                            style="color: #f44335; font-size: 0.875em; display: none;">
                                            Penutup laporan wajib diisi.
                                        </div>
                                    @endif
                                </div>

                                @if ($step['file'])
                                    <div class="mb-3">
                                        <label class="form-label">Silahkan Pilih dan Upload File</label>
                                        <div>
                                            <input class="custom-file-input" type="file" id="{{ $step['file'] }}"
                                                name="{{ $step['file'] }}[]" multiple
                                                onchange="addFilesToDataTransfer('{{ $step['file'] }}', 'file-list-{{ $key }}')"
                                                accept="image/*">

                                            <label for="{{ $step['file'] }}" class="btn btn-sm btn-dark bg-gradient-dark">
                                                <i class="fa fa-upload"></i> Pilih File
                                            </label>
                                        </div>

                                        <div class="file-list-container" id="file-list-{{ $key }}"
                                            style="display: none;">
                                        </div>
                                        <small class="text-muted mt-2 d-block">*Hanya menerima file gambar (JPG, JPEG, PNG).
                                            Maks. 2MB per
                                            file.</small>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-sm btn-light bg-gradient-light" id="prev-btn"
                                style="display: none;">
                                <i class="fa fa-arrow-left"></i> Sebelumnya
                            </button>
                            <button type="button" class="btn btn-sm btn-dark bg-gradient-dark" id="next-btn">
                                Selanjutnya <i class="fa fa-arrow-right"></i>
                            </button>
                            <button type="submit" class="btn btn-sm btn-dark bg-gradient-dark" id="submit-btn"
                                style="display: none;">
                                <i class="fa fa-save"></i> Simpan Laporan
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
                        <a href="" id="previewDownloadLink" class="btn btn-sm btn-dark bg-gradient-dark" target="_blank">
                            <i class="fa fa-download"></i> Download File
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let previewModal = null;
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('previewModal')) {
                previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
            }
        });

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
                downloadLink.href = filePath;
                fallbackEl.style.display = 'block';
            }

            previewModal.show();
        }

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        let fileUploads = {};

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
                previewBtn.className = 'btn btn-sm btn-dark bg-gradient-dark me-1';
                previewBtn.innerHTML = '<i class="fa fa-eye"></i>';
                previewBtn.disabled = true;

                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger bg-gradient-danger';
                removeBtn.innerHTML = '<i class="fa fa-trash"></i>';

                removeBtn.onclick = function() {
                    removeFile(inputId, containerId, i);
                };

                const isImage = file.type.startsWith('image/');

                if (isImage) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        thumbnailContainer.appendChild(img);

                        previewBtn.disabled = false;
                        previewBtn.onclick = () => showPreview(e.target.result, true);
                    };
                    reader.readAsDataURL(file);
                }

                fileItem.appendChild(thumbnailContainer);
                fileItem.appendChild(fileInfo);
                fileItem.appendChild(previewBtn);
                fileItem.appendChild(removeBtn);

                container.appendChild(fileItem);
            }

            input.files = files;
        }

        function addFilesToDataTransfer(inputId, containerId) {
            const input = document.getElementById(inputId);
            const MAX_FILE_SIZE = 2 * 1024 * 1024;

            if (!fileUploads[inputId]) {
                fileUploads[inputId] = new DataTransfer();
            }

            const newFiles = input.files ? Array.from(input.files) : [];
            input.value = '';

            let rejectedFilesType = [];
            let rejectedFilesSize = [];

            if (newFiles.length > 0) {
                for (const file of newFiles) {
                    const isImage = file.type.startsWith('image/');

                    if (!isImage) {
                        rejectedFilesType.push(file.name);
                        continue;
                    }

                    if (file.size > MAX_FILE_SIZE) {
                        rejectedFilesSize.push(`${file.name} (${formatBytes(file.size)})`);
                        continue;
                    }

                    fileUploads[inputId].items.add(file);
                }
            }

            if (rejectedFilesType.length > 0) {
                let message = 'File berikut ditolak (hanya gambar):<br>' + rejectedFilesType.join('<br>');

                if (typeof showMaterialToast === 'function') {
                    showMaterialToast(message, 'danger', 'Tipe File Ditolak');
                } else {
                    alert('File berikut ditolak karena tipe filenya tidak valid (hanya gambar):\n\n- ' +
                        rejectedFilesType.join('\n- '));
                }
            }

            if (rejectedFilesSize.length > 0) {
                let message = 'File berikut ditolak (melebihi 2MB):<br>' + rejectedFilesSize.join('<br>');

                if (typeof showMaterialToast === 'function') {
                    showMaterialToast(message, 'danger', 'Ukuran File Ditolak');
                } else {
                    alert('File berikut ditolak karena ukurannya melebihi 2MB:\n\n- ' + rejectedFilesSize.join(
                        '\n- '));
                }
            }

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
                if (penutupContainer) penutupContainer.style.border = '';

                if (penutupContent === '') {
                    event.preventDefault();

                    if (penutupError) penutupError.style.display = 'block';

                    if (penutupContainer) {
                        penutupContainer.style.border = '2px solid #f44335';
                        penutupContainer.style.borderRadius = '0.375rem';
                    }

                    penutupEditor.once('focus', () => {
                        if (penutupContainer) penutupContainer.style.border = '';
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
