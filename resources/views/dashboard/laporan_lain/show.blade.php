@extends('layouts.master')
@section('page', 'Detail Laporan Lain-Lain')
@section('title', 'Detail Laporan Lain-Lain')

@push('styles')
    <style>
        .section-card {
            background: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .section-card-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .section-card-header h6 {
            font-size: 0.875rem;
            font-weight: 700;
            color: #344767;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            margin: 0;
        }

        .section-card-body {
            padding: 1.5rem;
        }

        .content-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f2f5;
        }

        .content-header .icon-wrapper {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(195deg, #42424a 0%, #191919 100%);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.12);
        }

        .content-header .icon-wrapper i {
            color: #fff;
            font-size: 1rem;
        }

        .content-header h4 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #344767;
            margin: 0;
        }

        .content-header h5 {
            font-size: 1.125rem;
            font-weight: 700;
            color: #344767;
            margin: 0;
        }

        .subsection-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.5rem;
            background: linear-gradient(195deg, #42424a 0%, #191919 100%);
            color: #fff;
            font-weight: 700;
            font-size: 0.875rem;
            margin-right: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.12);
        }

        .report-content-wrapper {
            background: linear-gradient(195deg, #FAFBFC 0%, #F8F9FA 100%);
            border-radius: 0.75rem;
            padding: 1.5rem;
            color: #344767;
            line-height: 1.8;
        }

        .report-content-wrapper h1,
        .report-content-wrapper h2,
        .report-content-wrapper h3 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            color: #344767;
        }

        .report-content-wrapper p {
            margin-bottom: 1rem;
        }

        .report-content-wrapper ul,
        .report-content-wrapper ol {
            padding-left: 2rem;
            margin-bottom: 1rem;
        }

        .file-list-wrapper {
            background: #f8f9fa;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-top: 1rem;
        }

        .file-item-material {
            background: #ffffff;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .file-item-material:last-child {
            margin-bottom: 0;
        }

        .file-thumbnail-material {
            width: 4rem;
            height: 4rem;
            border-radius: 0.75rem;
            background: linear-gradient(195deg, #EEEEEE 0%, #E0E0E0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .file-thumbnail-material img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .file-thumbnail-material i {
            font-size: 1.5rem;
            color: #67748e;
        }

        .file-info-material {
            flex-grow: 1;
            min-width: 0;
        }

        .file-name-material {
            font-weight: 600;
            color: #344767;
            font-size: 0.875rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .file-size-material {
            font-size: 0.75rem;
            color: #67748e;
            margin-top: 0.25rem;
        }

        .empty-state-material {
            text-align: center;
            padding: 3rem 1rem;
            color: #67748e;
        }

        .empty-state-material i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state-material p {
            font-size: 0.875rem;
            margin: 0;
        }

        .section-divider-material {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.1), transparent);
            margin: 2rem 0;
        }

        .info-card-custom {
            background: linear-gradient(195deg, #FFFFFF 0%, #F8F9FA 100%);
            border-radius: 0.75rem;
            padding: 1rem;
            height: 100%;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .info-card-icon-wrapper {
            width: 3rem;
            height: 3rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.12);
            flex-shrink: 0;
        }

        .info-card-icon-wrapper i {
            font-size: 1.25rem;
            color: #fff;
        }

        .info-card-text-content {
            flex-grow: 1;
            min-width: 0;
        }

        .info-card-label {
            font-size: 0.8rem;
            color: #67748e;
            font-weight: 600;
            text-transform: capitalize;
            line-height: 1.2;
            display: block;
            margin-bottom: 0;
        }

        .info-card-value {
            font-size: 1rem;
            font-weight: 700;
            color: #344767;
            line-height: 1.4;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .info-card-value .btn {
            margin-top: 0.25rem;
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .file-item-material {
                flex-direction: column;
                text-align: center;
            }

            .content-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div id="laporan-content-wrapper">
                    <div class="card mb-5">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div>
                                    <h5 class="font-weight-bolder mb-0">Detail Laporan Lain-Lain</h5>
                                </div>
                                @php
                                    $from = request('from');
                                    $userRole = Auth::user()->role;
                                    $backUrl = route('laporan_lain.index');

                                    if ($from == 'pending') {
                                        $backUrl = route('verifikasi.pending');
                                    } elseif ($from == 'history') {
                                        $backUrl = route('verifikasi.history');
                                    } elseif ($from == 'dashboard') {
                                        $backUrl = route('dashboard');
                                    } elseif ($from == 'laporan-bulanan') {
                                        $backUrl = route('laporan-bulanan.index');
                                    } elseif ($from == 'notification') {
                                        $backUrl =
                                            $userRole == 'pimpinan' ? route('verifikasi.pending') : route('dashboard');
                                    }
                                @endphp
                                <a href="{{ $backUrl }}" class="btn btn-sm btn-secondary bg-gradient-secondary mb-0">
                                    <i class="fas fa-arrow-left me-1"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">
                                <i class="fas fa-info-circle me-2"></i>Informasi Laporan
                            </h6>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                            <div class="info-card-custom">
                                <div class="info-card-icon-wrapper bg-gradient-dark">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="info-card-text-content">
                                    <div class="info-card-label">Operator</div>
                                    <div class="info-card-value">{{ $laporan->operator->nama ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                            <div class="info-card-custom">
                                <div class="info-card-icon-wrapper bg-gradient-dark">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="info-card-text-content">
                                    <div class="info-card-label">Tanggal Laporan</div>
                                    <div class="info-card-value">
                                        {{ $laporan->tanggal_laporan ? $laporan->tanggal_laporan->isoFormat('D MMMM YYYY') : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                            <div class="info-card-custom">
                                <div
                                    class="info-card-icon-wrapper
                                @if ($laporan->isApproved()) bg-gradient-success
                                @elseif ($laporan->needsRevision()) bg-gradient-warning
                                @else bg-gradient-info @endif">
                                    <i
                                        class="fas
                                @if ($laporan->isApproved()) fa-check-circle
                                @elseif ($laporan->needsRevision()) fa-exclamation-triangle
                                @else fa-hourglass-half @endif">
                                    </i>
                                </div>
                                <div class="info-card-text-content">
                                    <div class="info-card-label">Status Verifikasi</div>
                                    <div>
                                        @if ($laporan->isApproved())
                                            <span class="badge badge-sm bg-gradient-success"><i
                                                    class="fas fa-circle-check"></i>
                                                Disetujui</span>
                                        @elseif ($laporan->needsRevision())
                                            <span class="badge badge-sm bg-gradient-warning"><i
                                                    class="fas fa-exclamation-triangle"></i> Revisi</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-info"><i
                                                    class="fas fa-hourglass-half"></i>
                                                Menunggu Verifikasi</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (
                        (Auth::user()->role == 'pimpinan' && $laporan->isPending()) ||
                            (Auth::user()->role == 'operator' && $laporan->needsRevision()))

                        <div class="row mt-4 align-items-start">
                            <div class="col-12">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">
                                    <i class="fas fa-tasks me-2"></i>Tindakan
                                </h6>
                            </div>

                            @if (Auth::user()->role == 'pimpinan' && $laporan->isPending())
                                <div class="col-xl-6 col-md-6 mb-3">
                                    <div class="info-card-custom">
                                        <div class="info-card-icon-wrapper bg-gradient-success">
                                            <i class="fas fa-check-circle"></i>
                                        </div>
                                        <div class="info-card-text-content">
                                            <div class="info-card-label">Setujui Laporan</div>
                                            <p class="text-sm mb-2" style="white-space: normal;">
                                                Menyetujui laporan ini dan menandai sebagai terverifikasi
                                            </p>
                                            <div class="info-card-value">
                                                @php
                                                    $wilayah = $laporan->operator->wilayah ?? null;
                                                    $statusSaatIni = $wilayah->status_wilayah ?? 'Tidak Diketahui';
                                                @endphp
                                                <button type="button"
                                                    class="btn btn-sm btn-success bg-gradient-success w-100 mb-0"
                                                    id="btn-approve-sweetalert"
                                                    data-wilayah-nama="{{ $wilayah->nama_wilayah ?? 'Wilayah Tidak Ditemukan' }}"
                                                    data-wilayah-status="{{ $statusSaatIni }}">
                                                    <i class="fas fa-check-circle me-2"></i> Setujui
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-md-6 mb-3">
                                    <div class="info-card-custom">
                                        <div class="info-card-icon-wrapper bg-gradient-warning">
                                            <i class="fas fa-edit"></i>
                                        </div>
                                        <div class="info-card-text-content">
                                            <div class="info-card-label">Minta Revisi</div>
                                            <p class="text-sm mb-2" style="white-space: normal;">
                                                Mengembalikan laporan untuk diperbaiki oleh operator
                                            </p>
                                            <div class="info-card-value">
                                                <button type="button"
                                                    class="btn btn-sm btn-warning bg-gradient-warning w-100 mb-0"
                                                    data-bs-toggle="modal" data-bs-target="#revisionModal">
                                                    <i class="fas fa-edit me-2"></i> Minta Revisi
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (Auth::user()->role == 'operator' && $laporan->needsRevision())
                                <div class="col-xl-12 col-md-12 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body p-3"
                                            style="background: linear-gradient(195deg, #FFF3E0 0%, #FFE0B2 100%); border-left: 4px solid #fb8c00; border-radius:0.75rem;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="font-weight-bolder mb-0 text-warning">
                                                    <i class="fas fa-sticky-note me-2"></i>
                                                    Catatan Revisi
                                                </h6>
                                            </div>
                                            <p class="mb-0 text-sm text-dark">
                                                {!! nl2br(e($laporan->catatan)) !!}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    @endif

                    @if (Auth::user()->role == 'pimpinan' && $laporan->isPending())
                        <form id="approve-form"
                            action="{{ route('verifikasi.approve', ['reportType' => 'laporan-lain', 'id' => $laporan->id_laporan]) }}"
                            method="POST" style="display: none;">
                            @csrf
                            <input type="hidden" name="catatan" value="">
                            <input type="hidden" name="status_wilayah_baru" id="input-status-wilayah-baru" value="">
                        </form>

                        <div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form id="revision-form"
                                        action="{{ route('verifikasi.requestRevision', ['reportType' => 'laporan-lain', 'id' => $laporan->id_laporan]) }}"
                                        method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="revisionModalLabel">Form Permintaan Revisi</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="catatan" class="form-label">
                                                    Catatan Revisi <span class="text-danger">*</span>
                                                </label>
                                                <textarea class="form-control py-2 px-3" id="catatan" name="catatan" rows="6" required
                                                    placeholder="Jelaskan secara detail bagian mana yang perlu diperbaiki...">{{ old('catatan') }}</textarea>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            <div class="alert alert-light text-dark text-sm mt-3">
                                                <i class="fas fa-info-circle me-2 text-dark"></i>
                                                Operator akan melihat catatan ini di dashboard mereka.
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-secondary bg-gradient-secondary"
                                                data-bs-dismiss="modal">
                                                <i class="fas fa-times me-1"></i> Batal
                                            </button>
                                            <button type="submit" class="btn btn-sm btn-dark bg-gradient-dark">
                                                <i class="fas fa-paper-plane me-1"></i> Kirim Revisi
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($laporan->pimpinan && !(Auth::user()->role == 'operator' && $laporan->needsRevision()))
                        <div class="row mt-4">
                            <div class="col-12 mb-3">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-0">
                                    <i class="fas fa-clipboard-check me-2"></i>Informasi Verifikasi
                                </h6>
                            </div>

                            @if ($laporan->isApproved())
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-4">
                                    <div class="info-card-custom">
                                        <div class="info-card-icon-wrapper bg-gradient-dark">
                                            <i class="fas fa-user-check"></i>
                                        </div>
                                        <div class="info-card-text-content">
                                            <div class="info-card-label">Diverifikasi Oleh</div>
                                            <div class="info-card-value">{{ $laporan->pimpinan->nama ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-4">
                                    <div class="info-card-custom">
                                        <div class="info-card-icon-wrapper bg-gradient-info">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="info-card-text-content">
                                            <div class="info-card-label">Tanggal Verifikasi</div>
                                            <div class="info-card-value">
                                                {{ $laporan->verified_at?->translatedFormat('d F Y,') }} pukul
                                                {{ $laporan->verified_at?->translatedFormat('H.i') }} WIB
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($laporan->catatan)
                                <div class="col-12 mb-4">
                                    <div class="card">
                                        @if ($laporan->needsRevision())
                                            @if (Auth::user()->role != 'operator')
                                                <div class="card-body p-3"
                                                    style="background: linear-gradient(195deg, #FFF3E0 0%, #FFE0B2 100%); border-left: 4px solid #fb8c00; border-radius:0.75rem;">
                                                    <h6 class="font-weight-bolder mb-2 text-warning">
                                                        <i class="fas fa-sticky-note me-2"></i>
                                                        Catatan Revisi
                                                    </h6>
                                                    <p class="mb-0 text-sm text-dark">{!! nl2br(e($laporan->catatan)) !!}</p>
                                                </div>
                                            @endif
                                        @elseif ($laporan->isApproved())
                                            <div class="card-body p-3"
                                                style="background: linear-gradient(195deg, #E8F5E9 0%, #C8E6C9 100%); border-left: 4px solid #66BB6A;">
                                                <h6 class="font-weight-bolder mb-2 text-success">
                                                    <i class="fas fa-sticky-note me-2"></i>
                                                    Catatan Persetujuan (Oleh:
                                                    {{ $laporan->pimpinan->nama ?? 'Pimpinan' }})
                                                </h6>
                                                <p class="mb-0 text-sm text-dark">{!! nl2br(e($laporan->catatan)) !!}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="content-header mb-0">
                                            <h4><i class="fas fa-file-pdf me-2 text-dark"></i>Pratinjau Laporan</h4>
                                        </div>
                                        @if (Auth::user()->role == 'operator' && $laporan->needsRevision())
                                            <a href="{{ route('laporan_lain.edit', ['laporan_lain' => $laporan->id_laporan]) }}"
                                                class="btn btn-sm btn-dark bg-gradient-dark">
                                                <i class="fas fa-edit me-2"></i> Perbaiki Laporan Ini
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body p-3 bg-dark">
                                    <div id="pdf-preview-container"
                                        style="width: 100%; height: 800px; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                                        <div id="pdf-loading"
                                            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
                                            <div class="spinner-border text-light mb-2" role="status"></div>
                                            <p>Memuat Dokumen...</p>
                                        </div>
                                        <div id="pdf-error"
                                            style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: #ff6b6b;">
                                            <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                                            <h5>Gagal Memuat Dokumen</h5>
                                            <p class="text-sm text-light opacity-8">Terjadi kesalahan saat mengambil data
                                                PDF.</p>
                                            <button onclick="loadPdf()" class="btn btn-sm btn-outline-white mt-2">
                                                <i class="fas fa-sync-alt me-1"></i> Coba Lagi
                                            </button>
                                        </div>
                                        <iframe id="pdf-iframe" width="100%" height="100%"
                                            style="border: none; opacity: 0; transition: opacity 0.3s ease;"
                                            allowfullscreen>
                                            Browser Anda tidak mendukung preview PDF.
                                        </iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let currentPdfUrl = null;
        let isManuallyUpdating = false;

        function initializeLaporanScripts() {
            console.log('[Init] Memasang ulang event listener...');

            const approveForm = document.getElementById('approve-form');
            const revisionForm = document.getElementById('revision-form');
            const btnApprove = document.getElementById('btn-approve-sweetalert');
            const revisionModalElement = document.getElementById('revisionModal');
            const catatanRevisiTextarea = document.getElementById('catatan');

            if (btnApprove && approveForm) {
                btnApprove.addEventListener('click', function(event) {
                    event.preventDefault();
                    const wilayahNama = event.currentTarget.dataset.wilayahNama || 'Wilayah';
                    const wilayahStatus = event.currentTarget.dataset.wilayahStatus || 'Tidak Diketahui';

                    let badgeClass = 'badge bg-secondary';
                    if (wilayahStatus === 'Aman') badgeClass = 'badge bg-gradient-success';
                    else if (wilayahStatus === 'Siaga') badgeClass = 'badge bg-gradient-warning';
                    else if (wilayahStatus === 'Bahaya') badgeClass = 'badge bg-gradient-danger';

                    Swal.fire({
                        title: 'Konfirmasi & Status Wilayah',
                        html: `
                        <p class="mb-2">Anda akan menyetujui laporan ini. Silakan tentukan status baru untuk wilayah Anda:</p>
                        <strong class="mb-2 d-block text-dark">${wilayahNama}</strong>
                        <p class="mb-3 text-sm">Status saat ini : <span class="${badgeClass} ms-1">${wilayahStatus}</span></p>
                        <div class="form-group text-start">
                            <label for="swal-select-status" class="form-label font-weight-bold">Konfirmasi Status Wilayah:</label>
                                <select id="swal-select-status" class="form-select form-control py-2 px-3" required>
                                    <option value="" disabled>-- Pilih Status --</option>
                                    <option value="Aman" ${wilayahStatus === 'Aman' ? 'selected' : ''}>Aman</option>
                                    <option value="Siaga" ${wilayahStatus === 'Siaga' ? 'selected' : ''}>Siaga</option>
                                    <option value="Bahaya" ${wilayahStatus === 'Bahaya' ? 'selected' : ''}>Bahaya</option>
                                </select>
                        </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-check-circle me-1"></i> Ya, Setujui',
                        cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
                        confirmButtonColor: '#262626',
                        cancelButtonColor: '#67748e',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-sm btn-dark bg-gradient-dark ms-2',
                            cancelButton: 'btn btn-sm btn-secondary bg-gradient-secondary me-2'
                        },
                        buttonsStyling: false,
                        preConfirm: () => {
                            const selectedStatus = document.getElementById('swal-select-status').value;
                            if (!selectedStatus) {
                                Swal.showValidationMessage('Anda harus memilih status wilayah.');
                                return false;
                            }
                            return selectedStatus;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const newStatus = result.value;

                            isManuallyUpdating = true;

                            Swal.fire({
                                title: 'Memproses Persetujuan',
                                html: 'Mohon tunggu, sistem sedang memverifikasi laporan...',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const formData = new FormData(approveForm);
                            formData.append('status_wilayah_baru', newStatus);

                            fetch(approveForm.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.close();

                                    if (data.success) {
                                        showMaterialToast(data.message || 'Laporan berhasil disetujui.',
                                            'success');

                                        if (typeof decrementSidebarBadge === 'function') {
                                            decrementSidebarBadge();
                                        }

                                        setTimeout(() => {
                                            updatePageContent();
                                            setTimeout(() => {
                                                isManuallyUpdating = false;
                                            }, 3000);
                                        }, 500);
                                    } else {
                                        isManuallyUpdating = false;
                                        showMaterialToast(data.message || 'Terjadi kesalahan.',
                                            'danger');
                                    }
                                })
                                .catch(error => {
                                    Swal.close();
                                    isManuallyUpdating = false;
                                    console.error('Error:', error);
                                    showMaterialToast('Terjadi kesalahan jaringan.', 'danger');
                                });
                        }
                    });
                });
            }

            if (revisionForm && catatanRevisiTextarea) {
                revisionForm.addEventListener('submit', function(event) {
                    event.preventDefault();

                    if (!catatanRevisiTextarea.value.trim()) {
                        catatanRevisiTextarea.classList.add('is-invalid');
                        let errorMsg = catatanRevisiTextarea.parentNode.querySelector('.invalid-feedback');
                        if (!errorMsg) {
                            errorMsg = document.createElement('div');
                            errorMsg.className = 'invalid-feedback d-block';
                            catatanRevisiTextarea.parentNode.appendChild(errorMsg);
                        }
                        errorMsg.textContent = 'Catatan revisi wajib diisi.';
                        return;
                    }

                    Swal.fire({
                        title: 'Konfirmasi Revisi',
                        html: "Anda yakin ingin meminta <strong>REVISI</strong>?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Ya, Kirim',
                        cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
                        confirmButtonColor: '#262626',
                        cancelButtonColor: '#67748e',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-sm btn-dark bg-gradient-dark ms-1',
                            cancelButton: 'btn btn-sm btn-secondary bg-gradient-secondary me-1'
                        },
                        buttonsStyling: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            isManuallyUpdating = true;

                            Swal.fire({
                                title: 'Mengirim Revisi',
                                html: 'Mohon tunggu...',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const formData = new FormData(revisionForm);

                            fetch(revisionForm.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.close();

                                    if (data.success) {
                                        const modal = bootstrap.Modal.getInstance(revisionModalElement);
                                        if (modal) modal.hide();

                                        showMaterialToast(data.message || 'Permintaan revisi terkirim.',
                                            'success');

                                        if (typeof decrementSidebarBadge === 'function') {
                                            decrementSidebarBadge();
                                        }

                                        setTimeout(() => {
                                            updatePageContent();
                                            setTimeout(() => {
                                                isManuallyUpdating = false;
                                            }, 3000);
                                        }, 500);
                                    } else {
                                        isManuallyUpdating = false;
                                        showMaterialToast(data.message || 'Terjadi kesalahan.',
                                            'danger');
                                    }
                                })
                                .catch(error => {
                                    Swal.close();
                                    isManuallyUpdating = false;
                                    console.error('Error:', error);
                                    showMaterialToast('Terjadi kesalahan jaringan.', 'danger');
                                });
                        }
                    });
                });

                if (revisionModalElement) {
                    revisionModalElement.addEventListener('hidden.bs.modal', function() {
                        catatanRevisiTextarea.classList.remove('is-invalid');
                        catatanRevisiTextarea.value = '';
                        const err = catatanRevisiTextarea.parentNode.querySelector('.invalid-feedback');
                        if (err) err.remove();
                    });
                }
            }
        }

        function updatePageContent() {
            const contentWrapperId = 'laporan-content-wrapper';

            fetch(window.location.href, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const newDoc = parser.parseFromString(html, 'text/html');
                    const newContent = newDoc.getElementById(contentWrapperId);
                    const oldContent = document.getElementById(contentWrapperId);

                    if (newContent && oldContent) {
                        oldContent.innerHTML = newContent.innerHTML;
                        console.log('[Update] Konten berhasil diperbarui.');

                        initializeLaporanScripts();

                        loadPdf();
                    }
                })
                .catch(error => console.error('[Update] Gagal fetch konten:', error));
        }

        function loadPdf() {
            const container = document.getElementById('pdf-preview-container');
            const loading = document.getElementById('pdf-loading');
            const errorDiv = document.getElementById('pdf-error');

            if (!container) return;

            loading.style.display = 'block';
            errorDiv.style.display = 'none';

            const oldIframe = document.getElementById('pdf-iframe');
            if (oldIframe) oldIframe.remove();

            if (currentPdfUrl) {
                URL.revokeObjectURL(currentPdfUrl);
                currentPdfUrl = null;
            }

            const timestamp = new Date().getTime();
            const baseUrl = "{{ route('laporan_lain.previewPdf', $laporan->id_laporan) }}";
            const url = `${baseUrl}?t=${timestamp}`;

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Gagal memuat PDF');
                    return response.blob();
                })
                .then(blob => {
                    currentPdfUrl = URL.createObjectURL(blob);

                    const newIframe = document.createElement('iframe');
                    newIframe.id = 'pdf-iframe';
                    newIframe.width = '100%';
                    newIframe.height = '100%';
                    newIframe.style.border = 'none';
                    newIframe.style.opacity = '0';
                    newIframe.style.transition = 'opacity 0.3s ease';
                    newIframe.setAttribute('allowfullscreen', '');
                    newIframe.src = currentPdfUrl + "#toolbar=1&navpanes=1&scrollbar=1&view=100";

                    container.appendChild(newIframe);

                    newIframe.onload = function() {
                        loading.style.display = 'none';
                        newIframe.style.opacity = '1';
                    };

                    setTimeout(() => {
                        if (loading.style.display !== 'none') {
                            console.warn('[PDF] Fallback: Force show iframe.');
                            loading.style.display = 'none';
                            newIframe.style.opacity = '1';
                        }
                    }, 2000);
                })
                .catch(error => {
                    console.error('Error fetching PDF:', error);
                    loading.style.display = 'none';
                    errorDiv.style.display = 'block';
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializeLaporanScripts();
            loadPdf();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.Echo !== 'undefined') {
                const thisLaporanId = {{ $laporan->id_laporan }};

                window.Echo.channel('laporan-updates')
                    .listen('.LaporanUpdated', (e) => {
                        if (e.laporanId === thisLaporanId) {

                            if (isManuallyUpdating) {
                                console.log('[Reverb] Event diabaikan (Update manual sedang berjalan).');
                                return;
                            }

                            if (e.newStatus === 'deleted') {
                                Swal.fire({
                                    title: 'Laporan Dihapus',
                                    text: 'Laporan yang sedang Anda lihat telah dihapus oleh operator.',
                                    icon: 'warning',
                                    showConfirmButton: false,
                                    timer: 2500,
                                    timerProgressBar: true
                                }).then(() => {
                                    window.location.href = "{{ route('dashboard') }}";
                                });
                                return;
                            }
                            console.log('[Reverb] Update diterima, merefresh konten...');
                            updatePageContent();
                        }
                    });
            }
        });
    </script>
@endpush
