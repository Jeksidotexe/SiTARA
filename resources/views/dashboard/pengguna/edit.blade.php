@extends('layouts.master')
@section('page', 'Edit Akun Pengguna')
@section('title', 'Edit Akun Pengguna')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Pengguna</h5>
                        <a href="{{ route('pengguna.index') }}" class="btn btn-sm btn-secondary bg-gradient-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('pengguna.update', $pengguna->id_users) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        value="{{ old('nama', $pengguna->nama) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="{{ old('username', $pengguna->username) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="{{ old('email', $pengguna->email) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="no_telepon" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="no_telepon" name="no_telepon"
                                        value="{{ old('no_telepon', $pengguna->no_telepon) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_wilayah" class="form-label">Wilayah</label>
                                    <select class="form-select" id="id_wilayah" name="id_wilayah" required>
                                        <option value="" disabled>Pilih Wilayah</option>
                                        @foreach ($wilayah as $item)
                                            <option value="{{ $item->id_wilayah }}"
                                                {{ old('id_wilayah', $pengguna->id_wilayah) == $item->id_wilayah ? 'selected' : '' }}>
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
                                        <option value="" disabled>Pilih Role</option>
                                        <option value="Admin"
                                            {{ old('role', $pengguna->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="Operator"
                                            {{ old('role', $pengguna->role) == 'operator' ? 'selected' : '' }}>Operator
                                        </option>
                                        <option value="Pimpinan"
                                            {{ old('role', $pengguna->role) == 'pimpinan' ? 'selected' : '' }}>Pimpinan
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3" required>{{ old('alamat', $pengguna->alamat) }}</textarea>

                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password Baru (Opsional)</label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" style="padding-right: 2.5rem;">
                                        <span id="togglePassword"
                                            style="position: absolute; top: 50%; right: 0.75rem; transform: translateY(-50%); cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        *Kosongkan jika tidak ingin mengubah password.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                    <div class="position-relative">
                                        <input type="password"
                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                            id="password_confirmation" name="password_confirmation"
                                            style="padding-right: 2.5rem;">
                                        <span id="togglePasswordConfirmation"
                                            style="position: absolute; top: 50%; right: 0.75rem; transform: translateY(-50%); cursor: pointer;">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="custom-file-upload-header-container border border-bottom-0 " id="foto_container_edit">
                            <div class="custom-file-upload-header">
                                <div class="text-content">
                                    <h6>Foto Profil</h6>
                                    <p>
                                        Silahkan upload foto baru untuk mengubah foto profil.
                                    </p>
                                </div>
                                <button type="button" class="btn btn-sm btn-dark bg-gradient-dark" id="uploadBtn_edit"
                                    onclick="document.getElementById('foto').click()">
                                    <i class="fa fa-upload"></i> Upload
                                </button>
                                <input type="file" id="foto" name="foto"
                                    accept="image/jpeg,image/png,image/jpg,image/gif" style="display: none;"
                                    onchange="handleFileChangeForEdit(this, 'fileInfoText_edit', 'foto-preview-wrapper', 'foto-preview')" />
                            </div>
                        </div>

                        <div class="custom-file-upload-footer-container border border-top-0 ">
                            <p class="custom-file-upload-info" id="fileInfoText_edit">
                                Maks. 2 MB | Format: JPG, PNG, GIF | *Kosongkan jika tidak ingin mengubah foto.*
                            </p>
                            @php
                                $isImage = false;
                                $initialSrc = '#';
                                if ($pengguna->foto) {
                                    $fullPath = storage_path('app/public/' . $pengguna->foto);
                                    if (file_exists($fullPath)) {
                                        try {
                                            $mime = mime_content_type($fullPath);
                                        } catch (\Exception $e) {
                                            $mime = null;
                                        }
                                        if ($mime && strpos($mime, 'image/') === 0) {
                                            $isImage = true;
                                            $initialSrc = Storage::url($pengguna->foto);
                                        }
                                    }
                                }
                            @endphp
                            <div id="foto-preview-wrapper" class="mt-3 {{ $isImage ? 'has-image-preview' : '' }}"
                                style="{{ $isImage ? 'display: block;' : 'display: none;' }}"
                                data-initial-src="{{ $initialSrc }}">
                                <label class="form-label">Preview Foto:</label><br>
                                <img id="foto-preview" src="{{ $initialSrc }}" alt="Foto Preview"
                                    class="img-thumbnail" style="max-height: 150px;">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-dark bg-gradient-dark"><li class="fa fa-save"></li> Simpan Perubahan</button>
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
