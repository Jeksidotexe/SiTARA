@extends('layouts.master')
@section('page', 'Detail Laporan Penguatan Ideologi Pancasila dan Karakter')
@section('title', 'Detail Laporan Penguatan Ideologi Pancasila dan Karakter')


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
                                    <h5 class="font-weight-bolder mb-0">Detail Laporan Penguatan Ideologi</h5>
                                </div>
                                @php
                                    $from = request('from');
                                    $userRole = Auth::user()->role;
                                    $backUrl = route('dashboard');

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
                                @if ($laporan->isApproved()) bg-gradient-dark
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
                                                @endphp
                                                <button type="button" class="btn btn-sm btn-success bg-gradient-success w-100 mb-0"
                                                    id="btn-approve-sweetalert"
                                                    data-wilayah-nama="{{ $wilayah->nama_wilayah ?? 'Wilayah Tidak Ditemukan' }}">
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
                                                <button type="button" class="btn btn-sm btn-warning bg-gradient-warning w-100 mb-0"
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
                                                <a href="{{ route('laporan_penguatan_ideologi.edit', ['laporan_penguatan_ideologi' => $laporan->id_laporan]) }}"
                                                    class="btn btn-sm btn-dark bg-gradient-dark mb-0">
                                                    <i class="fas fa-edit me-1"></i> Perbaiki Laporan Ini
                                                </a>
                                            </div>
                                            <hr class="dark horizontal my-2">
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
                            action="{{ route('verifikasi.approve', ['reportType' => 'laporan-penguatan-ideologi', 'id' => $laporan->id_laporan]) }}"
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
                                        action="{{ route('verifikasi.requestRevision', ['reportType' => 'laporan-penguatan-ideologi', 'id' => $laporan->id_laporan]) }}"
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
                        <div class="row mt-5">
                            <div class="col-12 mb-3">
                                <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-0">
                                    <i class="fas fa-clipboard-check me-2"></i>Informasi Verifikasi
                                </h6>
                            </div>

                            @if ($laporan->isApproved())
                                <div class="col-lg-6 col-md-6 col-sm-6 mb-4">
                                    <div class="info-card-custom">
                                        <div class="info-card-icon-wrapper bg-gradient-success">
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
                                                {{ $laporan->verified_at?->isoFormat('D MMMM YYYY, HH:mm') ?? 'N/A' }}
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
                                                    style="background: linear-gradient(195deg, #FFF3E0 0%, #FFE0B2 100%); border-left: 4px solid #fb8c00;">
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
                                    <div class="content-header">
                                        <h4>Deskripsi</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="report-content-wrapper">
                                        {!! $laporan->deskripsi !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <div class="content-header">
                                        <h4>II. Laporan Permasalahan Strategis</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @foreach ($fileFields as $index => $fieldKey)
                                        @php
                                            $title = $fieldTitles[$fieldKey] ?? "Bagian $fieldKey";
                                            $narasiField = "narasi_$fieldKey";
                                            $fileField = "file_$fieldKey";
                                            $narasiContent = $laporan->$narasiField;
                                            $files = $laporan->$fileField ?? [];
                                        @endphp

                                        <div class="mb-4">
                                            <h5 class="font-weight-bolder mb-3">
                                                {{ $title }}
                                            </h5>

                                            @if (!empty(strip_tags($narasiContent)))
                                                <div class="report-content-wrapper mb-3">
                                                    {!! $narasiContent !!}
                                                </div>
                                            @else
                                                <div class="empty-state-material">
                                                    <i class="fas fa-file-alt"></i>
                                                    <p>Tidak ada narasi untuk bagian ini</p>
                                                </div>
                                            @endif

                                            @if (!empty($files) && is_array($files))
                                                <label class="form-label text-dark font-weight-bold">
                                                    <i class="fas fa-paperclip me-2"></i>File Lampiran
                                                </label>
                                                <div class="file-list-wrapper">
                                                    @foreach ($files as $filePath)
                                                        @php
                                                            $fullPath = asset($filePath);
                                                            $fileName = basename($filePath);
                                                            $fileExt = strtolower(
                                                                pathinfo($fileName, PATHINFO_EXTENSION),
                                                            );
                                                            $isImage = in_array($fileExt, [
                                                                'jpg',
                                                                'jpeg',
                                                                'png',
                                                            ]);
                                                        @endphp

                                                        <div class="file-item-material">
                                                            <div class="file-thumbnail-material">
                                                                @if ($isImage)
                                                                    <img src="{{ $fullPath }}" alt="Preview">
                                                                @else
                                                                    <i class="fas fa-file-alt"></i>
                                                                @endif
                                                            </div>
                                                            <div class="file-info-material">
                                                                <div class="file-name-material">{{ $fileName }}</div>
                                                            </div>
                                                            <button type="button"
                                                                class="btn btn-sm btn-dark bg-gradient-dark mb-0"
                                                                onclick="showPreview('{{ $fullPath }}', {{ $isImage ? 'true' : 'false' }})">
                                                                <i class="fas fa-eye me-1"></i> Lihat
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <label class="form-label text-dark font-weight-bold">
                                                    <i class="fas fa-paperclip me-2"></i>File Lampiran
                                                </label>
                                                <div class="empty-state-material">
                                                    <i class="fas fa-folder-open"></i>
                                                    <p>Tidak ada file lampiran</p>
                                                </div>
                                            @endif
                                        </div>

                                        @if (!$loop->last)
                                            <div class="section-divider-material"></div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header pb-0">
                                    <div class="content-header">
                                        <h4>III. Penutup</h4>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="report-content-wrapper">
                                        {!! $laporan->penutup !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Pratinjau File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <img src="" id="previewImage" class="img-fluid" alt="Preview"
                        style="display: none; max-height: 75vh; border-radius: 0.75rem;">
                    <iframe src="" id="previewFrame"
                        style="width: 100%; height: 75vh; border: none; display: none; border-radius: 0.75rem;"></iframe>
                    <div id="previewFallback" style="display: none;">
                        <i class="fas fa-file-alt" style="font-size: 80px; color: #67748e;"></i>
                        <p class="mt-3 text-secondary">Pratinjau tidak tersedia untuk tipe file ini.</p>
                        <a href="" id="previewDownloadLink" class="btn btn-sm btn-dark bg-gradient-dark" target="_blank">
                            <i class="fas fa-download me-2"></i> Download File
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let previewModal = null;
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('previewModal')) {
                previewModal = new bootstrap.Modal(document.getElementById('previewModal'));

                document.getElementById('previewModal').addEventListener('hidden.bs.modal', function() {
                    document.getElementById('previewFrame').src = 'about:blank';
                    document.getElementById('previewImage').src = '';
                });
            }
        });

        function showPreview(filePath, isImage) {
            if (!previewModal) return;

            const imgEl = document.getElementById('previewImage');
            const frameEl = document.getElementById('previewFrame');
            const fallbackEl = document.getElementById('previewFallback');
            const downloadLink = document.getElementById('previewDownloadLink');
            const modalLabel = document.getElementById('previewModalLabel');

            imgEl.style.display = 'none';
            imgEl.src = '';
            frameEl.style.display = 'none';
            frameEl.src = 'about:blank';
            fallbackEl.style.display = 'none';

            if (isImage) {
                imgEl.src = filePath;
                imgEl.style.display = 'block';
                modalLabel.innerText = 'Pratinjau Gambar';
            } else {
                downloadLink.href = filePath;
                fallbackEl.style.display = 'block';
                modalLabel.innerText = 'Download File';
            }

            previewModal.show();
        }
    </script>

    <script>
        function initializeLaporanScripts() {
            console.log('[Init] Memasang ulang event listener untuk tombol...');
            const approveForm = document.getElementById('approve-form');
            const revisionForm = document.getElementById('revision-form');

            const btnApprove = document.getElementById('btn-approve-sweetalert');
            const revisionModalElement = document.getElementById('revisionModal');
            const catatanRevisiTextarea = document.getElementById('catatan');

            if (btnApprove && approveForm) {
                btnApprove.addEventListener('click', function(event) {
                    const wilayahNama = event.currentTarget.dataset.wilayahNama || 'Wilayah';

                    Swal.fire({
                        title: 'Konfirmasi & Status Wilayah',
                        html: `
                            <p class="mb-2">Anda akan menyetujui laporan ini. Silakan tentukan status baru untuk wilayah Anda:</p>
                            <strong class="mb-3 d-block text-dark">${wilayahNama}</strong>
                            <div class="form-group text-start">
                                <label for="swal-select-status" class="form-label">Status Wilayah Baru</label>
                                <select id="swal-select-status" class="form-select form-control py-2 px-3" required>
                                    <option value="" selected disabled>Pilih status...</option>
                                    <option value="Aman">Aman</option>
                                    <option value="Siaga">Siaga</option>
                                    <option value="Bahaya">Bahaya</option>
                                </select>
                            </div>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-check-circle"></i> Ya, Setujui & Atur Status',
                        cancelButtonText: '<i class="fas fa-times"></i> Batal',
                        confirmButtonColor: '#66BB6A',
                        cancelButtonColor: '#67748e',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-sm btn-dark bg-gradient-dark ms-2',
                            cancelButton: 'btn btn-sm btn-secondary bg-gradient-secondary me-2'
                        },
                        buttonsStyling: false,
                        preConfirm: () => {
                            const selectedStatus = document.getElementById('swal-select-status')
                                .value;
                            if (!selectedStatus) {
                                Swal.showValidationMessage(
                                    'Anda harus memilih status wilayah.');
                                return false;
                            }
                            return selectedStatus;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const newStatus = result.value;
                            document.getElementById('input-status-wilayah-baru').value = newStatus;
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Mohon tunggu sebentar',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            approveForm.submit();
                        }
                    });
                });
            }

            if (revisionForm && catatanRevisiTextarea) {
                revisionForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    let isValid = true;
                    let errorMsgElement = catatanRevisiTextarea.parentNode.querySelector(
                        '.invalid-feedback');

                    if (!catatanRevisiTextarea.value.trim()) {
                        isValid = false;
                        catatanRevisiTextarea.classList.add('is-invalid');
                        if (!errorMsgElement) {
                            errorMsgElement = document.createElement('div');
                            errorMsgElement.className = 'invalid-feedback d-block';
                            catatanRevisiTextarea.parentNode.appendChild(errorMsgElement);
                        }
                        errorMsgElement.textContent = 'Catatan revisi tidak boleh kosong.';
                        catatanRevisiTextarea.focus();
                    } else {
                        catatanRevisiTextarea.classList.remove('is-invalid');
                        if (errorMsgElement) {
                            errorMsgElement.remove();
                        }
                    }

                    if (!isValid) {
                        return;
                    }

                    Swal.fire({
                        title: 'Konfirmasi Revisi',
                        html: "Anda yakin ingin mengirim permintaan <strong>REVISI</strong>?<br><small class='text-muted'>Operator akan menerima notifikasi untuk memperbaiki laporan.</small>",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-paper-plane me-1"></i> Ya, Kirim!',
                        cancelButtonText: '<i class="fas fa-times me-1"></i> Batal',
                        confirmButtonColor: '#FFA726',
                        cancelButtonColor: '#67748e',
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-sm btn-dark bg-gradient-dark ms-2',
                            cancelButton: 'btn btn-sm btn-secondary bg-gradient-secondary me-2'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Mengirim...',
                                text: 'Mohon tunggu sebentar',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            event.target.submit();
                        }
                    });
                });

                if (revisionModalElement) {
                    revisionModalElement.addEventListener('hidden.bs.modal', function() {
                        catatanRevisiTextarea.classList.remove('is-invalid');
                        let errorMsg = catatanRevisiTextarea.parentNode.querySelector(
                            '.invalid-feedback');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                        catatanRevisiTextarea.value = '';
                    });
                }
            }
        }
        document.addEventListener('DOMContentLoaded', initializeLaporanScripts);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.Echo !== 'undefined') {
                const thisLaporanId = {{ $laporan->id_laporan }};
                const contentWrapperId = 'laporan-content-wrapper';
                console.log(`[Reverb] Mendengarkan di channel 'laporan-updates' untuk ID: ${thisLaporanId}`);

                window.Echo.channel('laporan-updates')
                    .listen('.LaporanUpdated', (e) => {
                        console.log('[Reverb] Menerima event:', e);
                        if (e.laporanId === thisLaporanId) {
                            console.log(
                                `[Reverb] Event ini untuk laporan ${thisLaporanId}. Melakukan update...`);
                            if (e.newStatus === 'deleted') {
                                Swal.fire({
                                    title: 'Laporan Dihapus',
                                    text: 'Laporan yang sedang Anda lihat telah dihapus.',
                                    icon: 'warning',
                                    showConfirmButton: false,
                                    timer: 2500,
                                    timerProgressBar: true
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('laporan_penguatan_ideologi.index') }}";
                                });
                                return;
                            }

                            fetch(window.location.href)
                                .then(response => response.text())
                                .then(html => {
                                    const parser = new DOMParser();
                                    const newDoc = parser.parseFromString(html, 'text/html');

                                    const newContent = newDoc.getElementById(contentWrapperId);
                                    const oldContent = document.getElementById(contentWrapperId);

                                    if (newContent && oldContent) {
                                        oldContent.innerHTML = newContent.innerHTML;
                                        console.log('[Reverb] Konten berhasil diperbarui tanpa reload.');
                                        initializeLaporanScripts();
                                    } else {
                                        console.warn(
                                            '[Reverb] Gagal menemukan wrapper konten. Melakukan fallback ke reload.'
                                        );
                                        location.reload();
                                    }
                                })
                                .catch(error => {
                                    console.error('[Reverb] Gagal fetch update konten:', error);
                                    location.reload();
                                });

                        } else {
                            console.log(
                                `[Reverb] Event diabaikan (untuk ID: ${e.laporanId}, halaman ini: ${thisLaporanId}).`
                            );
                        }
                    });

            } else {
                console.error('Laravel Echo not initialized. Pastikan master layout memuatnya.');
            }
        });
    </script>
@endpush
