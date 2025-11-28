@extends('layouts.master')
@section('page', 'Daftar Wilayah')
@section('sub-page', 'Edit Wilayah')
@section('title', 'Edit Wilayah')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Wilayah</h5>
                        <a href="{{ route('wilayah.index') }}" class="btn btn-sm btn-secondary bg-gradient-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('wilayah.update', $wilayah->id_wilayah) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama_wilayah" class="form-label">Nama Wilayah</label>
                                    <input type="text" class="form-control @error('nama_wilayah') is-invalid @enderror"
                                        id="nama_wilayah" name="nama_wilayah"
                                        value="{{ old('nama_wilayah', $wilayah->nama_wilayah) }}" required>
                                    @error('nama_wilayah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status_wilayah" class="form-label">Status Wilayah</label>
                                    <select class="form-select @error('status_wilayah') is-invalid @enderror"
                                        id="status_wilayah" name="status_wilayah" required>
                                        <option value="" disabled>Pilih Status</option>
                                        <option value="Aman"
                                            {{ old('status_wilayah', $wilayah->status_wilayah) == 'Aman' ? 'selected' : '' }}>
                                            Aman</option>
                                        <option value="Siaga"
                                            {{ old('status_wilayah', $wilayah->status_wilayah) == 'Siaga' ? 'selected' : '' }}>
                                            Siaga</option>
                                        <option value="Bahaya"
                                            {{ old('status_wilayah', $wilayah->status_wilayah) == 'Bahaya' ? 'selected' : '' }}>
                                            Bahaya</option>
                                    </select>
                                    @error('status_wilayah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" step="any" class="form-control" id="latitude" name="latitude"
                                        value="{{ old('latitude', $wilayah->latitude) }}" placeholder="Contoh: -0.02356">
                                    <small class="text-muted">Gunakan titik (.) untuk desimal.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" step="any" class="form-control" id="longitude"
                                        name="longitude" value="{{ old('longitude', $wilayah->longitude) }}"
                                        placeholder="Contoh: 109.33314">
                                    <small class="text-muted">Gunakan titik (.) untuk desimal.</small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-dark bg-gradient-dark"><i class="fa fa-save"></i> Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#status_wilayah').select2({
                theme: "bootstrap-5",
            });
        });
    </script>
@endpush
