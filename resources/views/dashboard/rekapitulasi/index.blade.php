@extends('layouts.master')
@section('title', 'Cetak Laporan')
@section('page', 'Rekapitulasi Laporan')

@section('content')
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Cetak Laporan</h5>
                    <p class="text-sm mb-0">Pilih kriteria untuk menggabungkan laporan yang disetujui ke dalam satu file PDF.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('rekapitulasi.cetak') }}" method="GET" target="_blank" id="form-rekapitulasi">
                        @if (Auth::user()->role == 'admin')
                            <div class="mb-3">
                                <label for="id_wilayah" class="form-label">Wilayah</label>
                                <select class="form-select" id="id_wilayah" name="id_wilayah" required>
                                    <option value="" selected disabled>-- Pilih Wilayah --</option>
                                    @foreach ($wilayahs as $wilayah)
                                        <option value="{{ $wilayah->id_wilayah }}">{{ $wilayah->nama_wilayah }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="tipe_laporan" class="form-label">Tipe Laporan</label>
                            <select class="form-select" id="tipe_laporan" name="tipe_laporan" required>
                                <option value="" selected disabled>-- Pilih Tipe Laporan --</option>
                                @foreach ($reportTitles as $key => $title)
                                    <option value="{{ $key }}">{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <p class="text-xs text-muted mt-4 mb-2">Pilih filter bulanan ATAU filter harian. <br/>Jika filter harian diisi, filter bulanan akan diabaikan.</p>
                        <div class="row" id="grup-bulanan">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bulan" class="form-label">Filter Bulanan</label>
                                    <select class="form-select" id="bulan" name="bulan">
                                        <option value="">-- Pilih Bulan --</option>
                                        @for ($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $m == now()->month ? 'selected' : '' }}>
                                                {{ Carbon\Carbon::create(null, $m, 1)->isoFormat('MMMM') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tahun" class="form-label">Tahun</label>
                                    <select class="form-select" id="tahun" name="tahun">
                                        <option value="">-- Pilih Tahun --</option>
                                        @foreach ($availableYears as $year)
                                            <option value="{{ $year }}" {{ $year == now()->year ? 'selected' : '' }}>
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="grup-tanggal">
                            <label for="tanggal" class="form-label">Filter Harian (Tanggal Spesifik)</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="">
                        </div>

                        <div class="mt-2">
                            <button type="submit" class="btn btn-dark bg-gradient-dark-blue w-100">
                                <i class="material-symbols-rounded me-1">picture_as_pdf</i>
                                Cetak PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#id_wilayah, #tipe_laporan, #bulan, #tahun').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#form-rekapitulasi')
            });
            $('#tanggal').on('change', function() {
                if ($(this).val()) {
                    $('#bulan').val(null).trigger('change');
                    $('#tahun').val(null).trigger('change');
                }
            });
            $('#bulan, #tahun').on('change', function() {
                if ($('#bulan').val() || $('#tahun').val()) {
                    $('#tanggal').val('');
                }
            });
        });
    </script>
@endpush
