@extends('layouts.master')
@section('page', 'Tambah Akun Pengguna')
@section('title', 'Tambah Akun Pengguna')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tambah Pengguna</h5>
                        <a href="{{ route('pengguna.index') }}" class="btn btn-sm btn-secondary bg-gradient-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('pengguna.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        value="{{ old('nama') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="{{ old('username') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_telepon" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                                        value="{{ old('no_telepon') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_wilayah" class="form-label">Wilayah</label>
                                    <select class="form-select" id="id_wilayah" name="id_wilayah" required>
                                        <option value="" disabled selected>Pilih Wilayah</option>
                                        @foreach ($wilayah as $item)
                                            <option value="{{ $item->id_wilayah }}"
                                                {{ old('id_wilayah') == $item->id_wilayah ? 'selected' : '' }}>
                                                {{ $item->nama_wilayah }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="" disabled selected>Pilih Role</option>
                                        <option value="Admin" {{ old('role') == 'Admin' ? 'selected' : '' }}>Admin
                                        </option>
                                        <option value="Operator" {{ old('role') == 'Operator' ? 'selected' : '' }}>Operator
                                        </option>
                                        <option value="Pimpinan" {{ old('role') == 'Pimpinan' ? 'selected' : '' }}>Pimpinan
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ old('alamat') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control" id="password" name="password"
                                            value="{{ old('password') }}" required style="padding-right: 2.5rem;">
                                        <span id="togglePassword"
                                            style="position: absolute; top: 50%; right: 0.75rem; transform: translateY(-50%); cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required style="padding-right: 2.5rem;">
                                        <span id="togglePasswordConfirmation"
                                            style="position: absolute; top: 50%; right: 0.75rem; transform: translateY(-50%); cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="custom-file-upload-header-container border border-bottom-0"
                            id="foto_container_create">
                            <div class="custom-file-upload-header">
                                <div class="text-content">
                                    <h6>Foto Profil</h6>
                                    <p>
                                        Silahkan upload foto dengan mengklik tombol Upload.
                                    </p>
                                </div>
                                <button type="button" class="btn btn-sm btn-dark bg-gradient-dark"
                                    id="uploadBtn_create" onclick="document.getElementById('foto').click()">
                                    <i class="fa fa-upload"></i> Upload
                                </button>
                                <input type="file" id="foto" name="foto"
                                    accept="image/jpeg,image/png,image/jpg,image/gif" style="display: none;"
                                    onchange="displayFileDetails(this, 'fileInfoText_create', 'uploadedFileDetails_create', 'uploadBtn_create', 'foto-preview-wrapper')" />
                            </div>
                        </div>

                        <div class="custom-file-upload-footer-container border border-top-0">
                            <p class="custom-file-upload-info" id="fileInfoText_create">
                                Maks. 2 MB | Format: JPG, PNG, GIF
                            </p>

                            <div class="uploaded-file-details" id="uploadedFileDetails_create">
                                <img src="{{ asset('images/default-image-icon.png') }}" alt="preview"
                                    class="file-preview-icon" id="filePreviewIcon_create">
                                <div class="file-details-info">
                                    <p><strong>Nama File:</strong> <span id="fileName_create"></span></p>
                                    <p><strong>Tipe:</strong> <span id="fileType_create"></span></p>
                                    <p><strong>Ukuran:</strong> <span id="fileSize_create"></span></p>
                                </div>
                                <div class="file-action-buttons">
                                    <button type="button" class="btn-action btn-delete" title="Hapus"
                                        onclick="deleteSelectedFile('foto', 'fileInfoText_create', 'uploadedFileDetails_create', 'uploadBtn_create', 'foto-preview-wrapper')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div id="foto-preview-wrapper" class="mt-3">
                                <label class="form-label">Preview Foto:</label><br>
                                <img id="foto-preview" src="#" alt="Foto Preview" class="img-thumbnail"
                                    style="max-height: 150px;">
                            </div>

                        </div>

                        <button type="submit" class="btn btn-sm btn-dark bg-gradient-dark">
                            <li class="fa fa-save"></li> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#role').select2({
                theme: "bootstrap-5"
            });
            $('#id_wilayah').select2({
                theme: "bootstrap-5"
            });
        });
    </script>
@endpush
