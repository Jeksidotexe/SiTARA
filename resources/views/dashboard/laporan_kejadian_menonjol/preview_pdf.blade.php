<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan {{ $filters['tipe_laporan'] }}
        @if (isset($filters['bulan']))
            - {{ $filters['bulan'] }} {{ $filters['tahun'] }}
        @else
            - {{ $filters['tanggal'] }}
        @endif
    </title>

    <style>
        @page {
            margin-top: 3cm;
            margin-bottom: 1.27cm;
            margin-left: 3cm;
            margin-right: 2cm;
        }

        body {
            margin: 0;
            font-family: 'Times New Roman', Times, serif;
            font-size: 16px;
            line-height: 1.5;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .report-page {
            width: 100%;
            margin: 0 auto;
            page-break-after: always;
        }

        .report-page:last-child {
            page-break-after: avoid;
        }

        .page-break {
            page-break-after: always;
        }

        .kop-surat {
            width: 100%;
            margin-bottom: 0;
        }

        .kop-border {
            border-top: 4px solid #000;
            border-bottom: 1px solid #000;
            height: 2px;
            margin-top: 5px;
            margin-bottom: 15px;
        }

        .kop-surat img {
            width: 100%;
        }

        .report-title-simple {
            text-align: center;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 16px;
            text-decoration: underline;
        }

        .report-content {
            margin-top: 10px;
            text-align: justify;
        }

        .report-content h4 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            margin-top: 15px;
            padding-left: 0;
        }

        .narasi-umum {
            margin-bottom: 15px;
            word-wrap: break-word;
        }

        .narasi-umum p {
            margin-top: 0;
        }

        .report-section {
            margin-bottom: 10px;
        }

        .report-section h5 {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            padding-left: 20px;
        }

        .report-section .narasi {
            word-wrap: break-word;
            padding-left: 40px;
        }

        .report-section .narasi p {
            margin-top: 0;
            margin-bottom: 0;
        }

        .report-section .lampiran {
            margin-top: 0;
            font-size: 12px;
            padding-left: 40px;
        }

        .image-gallery {
            margin-top: 0;
            page-break-inside: avoid;
            padding-left: 0;
        }

        .image-gallery img {
            width: 200px;
            height: 150px;
            object-fit: contain;
            margin: 5px;
            border: 1px solid #ccc;
            vertical-align: top;
        }

        .nihil {
            font-style: italic;
            color: #555;
            font-size: 16px;
            padding-left: 40px;
        }

        .signature {
            margin-top: 40px;
            width: 40%;
            margin-left: 60%;
            text-align: center;
            line-height: 1.5;
            page-break-inside: avoid;
            font-size: 12px;
        }

        .signature img {
            width: 100%;
            max-width: 500px;
        }

        .no-data {
            text-align: center;
            font-size: 16px;
            font-style: italic;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    @forelse ($reports as $report)
        <div class="report-page">

            @if ($wilayah->kop_surat && Storage::disk('public')->exists($wilayah->kop_surat))
                <div class="kop-surat">
                    <img src="{{ public_path('storage/' . $wilayah->kop_surat) }}" alt="Kop Surat">
                </div>
            @else
                <div class="kop-surat" style="text-align: center; padding: 20px; border: 1px dashed red;">
                    (Kop Surat belum di-upload untuk wilayah {{ $wilayah->nama_wilayah }})
                </div>
            @endif

            <div class="kop-border"></div>

            <div class="report-content">
                @if (!empty($report->deskripsi))
                    <div class="narasi-umum">
                        {!! $report->deskripsi !!}
                    </div>
                @endif

                <h4>II. LAPORAN PERMASALAHAN STRATEGIS:</h4>

                @foreach ($sectionKeys as $key)
                    @php
                        $narasi = $report->{'narasi_' . $key};
                        $files = $report->{'file_' . $key};
                    @endphp

                    <div class="report-section">
                        <h5>{{ $sectionTitles[$key] }}</h5>

                        @if (empty($narasi) && (empty($files) || count($files) == 0))
                            <p class="nihil">(NIHIL)</p>
                        @else
                            @if (!empty($narasi))
                                <div class="narasi">
                                    {!! $narasi !!}
                                </div>
                            @endif

                            @if (!empty($files) && count($files) > 0)
                                <div class="lampiran">
                                    @php
                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                                        $images = [];

                                        foreach ($files as $file) {
                                            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            if (in_array($extension, $imageExtensions)) {
                                                $images[] = $file;
                                            }
                                        }
                                    @endphp
                                    @if (count($images) > 0)
                                        <div class="image-gallery">
                                            @foreach ($images as $img)
                                                @if (Storage::disk('public')->exists(str_replace('storage/', '', $img)))
                                                    <img src="{{ public_path($img) }}">
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach

                @if (!empty($report->penutup))
                    <h4>III. PENUTUP</h4>

                    <div class="narasi-umum" style="padding-left: 30px;">
                        {!! $report->penutup !!}
                    </div>
                @endif

            </div>

            <div class="signature">
                @if ($wilayah->tanda_tangan && Storage::disk('public')->exists($wilayah->tanda_tangan))
                    <img src="{{ public_path('storage/' . $wilayah->tanda_tangan) }}" alt="Tanda Tangan">
                @else
                    <div style="text-align: center; padding: 20px; border: 1px dashed red; height: 100px;">
                        (Tanda Tangan belum di-upload)
                    </div>
                @endif
            </div>

        </div>

    @empty
        <div class="report-page">
            @if ($wilayah->kop_surat && Storage::disk('public')->exists($wilayah->kop_surat))
                <div class="kop-surat">
                    <img src="{{ public_path('storage/' . $wilayah->kop_surat) }}" alt="Kop Surat">
                </div>
            @endif

            <div class="kop-border"></div>

            <div class="no-data">
                <p>(Tidak ada laporan {{ $filters['tipe_laporan'] }} yang disetujui untuk wilayah
                    {{ $wilayah->nama_wilayah }}
                    @if (isset($filters['bulan']))
                        pada periode {{ $filters['bulan'] }} {{ $filters['tahun'] }}
                    @else
                        pada tanggal {{ $filters['tanggal'] }}
                    @endif
                    .)
                </p>
            </div>

            @if ($wilayah->tanda_tangan && Storage::disk('public')->exists($wilayah->tanda_tangan))
                <div class="signature">
                    <img src="{{ public_path('storage/' . $wilayah->tanda_tangan) }}" alt="Tanda Tangan">
                </div>
            @endif
        </div>
    @endforelse

</body>

</html>
