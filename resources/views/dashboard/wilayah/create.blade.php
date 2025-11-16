@extends('layouts.master')

@section('page')
    Data Wilayah
@endsection

@section('sub-page')
    Tambah Wilayah
@endsection

@section('title')
    Tambah Wilayah
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Form Tambah Wilayah</h5>
                        <a href="{{ route('wilayah.index') }}" class="btn btn-secondary bg-gradient-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('wilayah.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            {{-- Nama Wilayah --}}
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="nama_wilayah" class="form-label">Nama Wilayah</label>
                                    <input type="text" class="form-control" id="nama_wilayah" name="nama_wilayah"
                                        value="{{ old('nama_wilayah') }}" required>
                                </div>
                            </div>
                        </div>

                        {{-- [BARU] Input Latitude dan Longitude --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" step="any" class="form-control" id="latitude" name="latitude"
                                        value="{{ old('latitude') }}" placeholder="Contoh: -0.02356">
                                    <small class="text-muted">Gunakan titik (.) untuk desimal.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" step="any"
                                        class="form-control" id="longitude"
                                        name="longitude" value="{{ old('longitude') }}" placeholder="Contoh: 109.33314">
                                    <small class="text-muted">Gunakan titik (.) untuk desimal.</small>
                                </div>
                            </div>
                        </div>
                        {{-- [AKHIR BARU] --}}

                        <button type="submit" class="btn btn-dark"><i class="fa fa-save"></i> Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
