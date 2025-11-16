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

        /* KOP SURAT */
        .kop-surat {
            width: 100%;
            margin-bottom: 0;
        }

        /* [UPDATED] BORDER KUSTOM (SESUAI GAMBAR) */
        .kop-border {
            border-top: 4px solid #000;
            /* Garis tebal */
            border-bottom: 1px solid #000;
            /* Garis tipis */
            height: 2px;
            /* Jarak antara 2 garis */
            margin-top: 5px;
            /* Jarak dari gambar kop */
            margin-bottom: 15px;
            /* Jarak ke konten di bawahnya */
        }

        .kop-surat img {
            width: 100%;
        }

        /* Judul Per Tanggal Laporan */
        .report-title-simple {
            text-align: center;
            margin-bottom: 15px;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 16px;
            text-decoration: underline;
        }

        /* ISI LAPORAN */
        .report-content {
            margin-top: 10px;
            text-align: justify;
        }

        /* Heading "II." dan "III." */
        .report-content h4 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
            margin-top: 15px;
            padding-left: 0;
        }

        /* Narasi umum (Deskripsi & Penutup) */
        .narasi-umum {
            margin-bottom: 15px;
            word-wrap: break-word;
        }

        .narasi-umum p {
            margin-top: 0;
        }

        /* Blok Seksi A, B, C */
        .report-section {
            margin-bottom: 10px;
            /* page-break-inside: avoid; */
        }

        /* Heading Seksi "A.", "B." */
        .report-section h5 {
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 5px;
            padding-left: 20px;
            /* Indent 1 level */
        }

        /* Konten di dalam seksi */
        .report-section .narasi {
            word-wrap: break-word;
            padding-left: 40px;
            /* Indent 2 level */
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

        /* Teks (NIHIL) */
        .nihil {
            font-style: italic;
            color: #555;
            font-size: 16px;
            padding-left: 40px;
        }

        /* TANDA TANGAN */
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

    {{-- Loop utama: Satu halaman per laporan --}}
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

            {{-- [UPDATED] Menggunakan 1 div untuk border --}}
            <div class="kop-border"></div>

            <div class="report-content">

                {{-- Tampilkan Deskripsi Umum (jika ada) -- TANPA INDENT --}}
                @if (!empty($report->deskripsi))
                    <div class="narasi-umum">
                        {!! $report->deskripsi !!}
                    </div>
                @endif

                {{-- HEADING STATIS II. --}}
                <h4>II. LAPORAN PERMASALAHAN STRATEGIS:</h4>

                {{-- Loop A-H untuk laporan ini --}}
                @foreach ($sectionKeys as $key)
                    @php
                        $narasi = $report->{'narasi_' . $key};
                        $files = $report->{'file_' . $key};
                    @endphp

                    <div class="report-section">
                        {{-- "A.", "B.", dst. (Indent 1 level) --}}
                        <h5>{{ $sectionTitles[$key] }}</h5>

                        @if (empty($narasi) && (empty($files) || count($files) == 0))
                            {{-- (NIHIL) (Indent 2 level) --}}
                            <p class="nihil">(NIHIL)</p>
                        @else
                            {{-- Narasi (Indent 2 level) --}}
                            @if (!empty($narasi))
                                <div class="narasi">
                                    {!! $narasi !!}
                                </div>
                            @endif

                            {{-- Lampiran (Indent 2 level) --}}
                            @if (!empty($files) && count($files) > 0)
                                <div class="lampiran">
                                    @php
                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                                        $images = [];

                                        // Logika disederhanakan: kita hanya peduli untuk mengumpulkan gambar
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
                {{-- Akhir Loop A-H --}}

                {{-- Penutup (jika ada) --}}
                @if (!empty($report->penutup))
                    {{-- HEADING STATIS III. --}}
                    <h4>III. PENUTUP</h4>

                    {{-- Konten Penutup (Indent 1 level) --}}
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

        </div> {{-- Akhir .report-page --}}

    @empty
        {{-- Jika tidak ada laporan sama sekali, tampilkan pesan --}}
        <div class="report-page">
            @if ($wilayah->kop_surat && Storage::disk('public')->exists($wilayah->kop_surat))
                <div class="kop-surat">
                    <img src="{{ public_path('storage/' . $wilayah->kop_surat) }}" alt="Kop Surat">
                </div>
            @endif

            {{-- [UPDATED] Border kustom di halaman kosong --}}
            <div class="kop-border"></div>

            <div class="no-data">
                {{-- [UPDATED] Pesan 'no-data' dinamis untuk harian atau bulanan --}}
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
