@extends('layouts.master')
@section('title', 'Dashboard')
@section('page', 'Home')

{{-- [MODIFIKASI] Menambahkan style untuk DataTables, tombol "Reset View", dan POPUP BARU --}}
@push('styles')
    {{-- [BARU] CSS DataTables Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css">

    <style>
        /* [BARU] CSS untuk Popup Peta Kustom yang Modern */
        .custom-leaflet-popup .leaflet-popup-content-wrapper {
            background: rgba(40, 40, 40, 0.9);
            /* Latar belakang gelap semi-transparan */
            color: #f8f9fa;
            /* Teks terang */
            border: none;
            border-radius: 0.5rem;
            /* Sesuai card style */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(5px);
            /* Efek blur (jika didukung) */
            -webkit-backdrop-filter: blur(5px);
        }

        .custom-leaflet-popup .leaflet-popup-content {
            padding: 0;
            /* Hapus padding default */
            font-family: "Inter", sans-serif;
            /* Sesuaikan font */
            line-height: 1.5;
            margin: 0;
            min-width: 200px;
            /* Atur lebar minimum */
        }

        .custom-leaflet-popup .leaflet-popup-tip {
            background: rgba(40, 40, 40, 0.9);
            /* Cocokkan dengan wrapper */
        }

        /* [BARU] CSS untuk konten di dalam popup */
        .popup-title {
            font-size: 1rem;
            font-weight: 600;
            color: #ffffff;
            margin: 0;
            padding: 0.75rem 1rem;
        }

        .popup-status {
            font-size: 0.875rem;
            color: #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
        }

        .popup-status .badge {
            margin-left: 10px;
            /* Jarak antara "Status:" dan badge */
        }

        /* Akhir CSS Popup Kustom */

        /* [BARU] CSS untuk marker "Neon Pulse" */
        .neon-marker {
            position: relative;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.5);
        }

        .neon-marker::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 14px;
            height: 14px;
            border-radius: 50%;
            box-shadow: 0 0 10px 3px #fff;
            opacity: 0.8;
        }

        .neon-marker::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: transparent;
        }

        /* AMAN (Hijau) */
        .marker-aman {
            background-color: #00e676;
        }

        .marker-aman::before {
            box-shadow: 0 0 12px 4px #00e676;
        }

        /* SIAGA (Kuning/Oranye) */
        .marker-siaga {
            background-color: #ffeb3b;
        }

        .marker-siaga::before {
            box-shadow: 0 0 12px 4px #ffeb3b;
        }

        .marker-siaga::after {
            animation: pulse-ring 1.5s ease-out infinite;
            border: 2px solid #ffeb3b;
        }

        /* BAHAYA (Merah) */
        .marker-bahaya {
            background-color: #f44336;
        }

        .marker-bahaya::before {
            box-shadow: 0 0 12px 4px #f44336;
        }

        .marker-bahaya::after {
            animation: pulse-ring 1.5s ease-out infinite;
            border: 2px solid #f44336;
        }

        @keyframes pulse-ring {
            0% {
                transform: translate(-50%, -50%) scale(0.5);
                opacity: 0.8;
            }

            80% {
                transform: translate(-50%, -50%) scale(2);
                opacity: 0;
            }

            100% {
                opacity: 0;
            }
        }

        /* [FIX] Menghilangkan border persegi pada marker */
        .leaflet-div-icon {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
        }

        /* CSS untuk tombol "Locate Me" kustom */
        .leaflet-control-locate a {
            background-color: #fff;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border-radius: 4px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.65);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .leaflet-control-locate a .material-symbols-rounded {
            font-size: 20px;
            color: #333;
        }

        .leaflet-bar .leaflet-control-locate {
            border-radius: 4px;
        }

        /* [BARU] CSS untuk tombol "Reset View" kustom */
        .leaflet-control-reset-view a {
            background-color: #fff;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border-radius: 4px;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.65);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .leaflet-control-reset-view a .material-symbols-rounded {
            font-size: 20px;
            color: #333;
        }

        .leaflet-bar .leaflet-control-reset-view {
            border-radius: 4px;
            margin-top: 5px;
            /* Memberi jarak dari tombol di atasnya */
        }


        /* [MODIFIKASI] CSS untuk DataTables agar rapi */
        #wilayah-datatable_wrapper .dataTables-length,
        #wilayah-datatable_wrapper .dataTables-filter {
            padding: 0;
        }

        #wilayah-datatable_wrapper .dataTables-filter input {
            /* Input search dari Material Dashboard */
            display: block;
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1.4rem;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #d2d6da;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0.5rem;
            transition: box-shadow 0.15s ease, border-color 0.15s ease;
        }

        #wilayah-datatable_wrapper .dataTables-filter input:focus {
            border-color: #344767;
            outline: 0;
            box-shadow: 0 0 0 2px rgba(52, 71, 103, 0.25);
        }

        #wilayah-datatable_wrapper .dataTables-paginate .pagination {
            justify-content: end;
        }

        /* Memastikan tabel tidak keluar dari card body */
        .table-responsive {
            overflow-x: hidden;
        }

        #map {
            z-index: 1;
        }
    </style>
@endpush

@section('content')
    {{-- Baris Header --}}
    <div class="row mb-4">
        <div class="col-lg-12">
            <h3 class="font-weight-bolder text-uppercase mb-1">Admin {{ Auth::user()->wilayah->nama_wilayah }}</h3>
            <p class="text-muted text-sm">Selamat datang, {{ Auth::user()->nama }}. Ringkasan data sistem.</p>
        </div>
    </div>

    {{-- [MODIFIKASI] Baris Kartu Statistik dan Chart --}}
    <div class="row">
        {{-- Kolom Kiri: 3 Kartu Statistik Vertikal --}}
        <div class="col-lg-4 d-flex flex-column">
            {{-- Card: Total Laporan --}}
            <div class="card card-body border-radius-lg shadow-sm mb-4">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-muted">Total Laporan Masuk</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $totalLaporan ?? 0 }}
                                <span class="text-sm font-weight-light text-muted ms-1">Laporan</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded text-lg opacity-10">assignment</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-2">
                <p class="mb-0 pt-1 text-sm text-secondary">Semua tipe laporan.</p>
            </div>

            {{-- Card: Total Pengguna --}}
            <div class="card card-body border-radius-lg shadow-sm mb-4">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-muted">Total Pengguna Terdaftar</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $totalUsers ?? 0 }}
                                <span class="text-sm font-weight-light text-muted ms-1">Pengguna</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded text-lg opacity-10">group</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-2">
                <a class="text-sm text-secondary font-weight-normal mb-0 pt-1" href="{{ route('pengguna.index') }}">
                    Kelola Akun Pengguna <i class="fas fa-arrow-circle-right ms-1"></i>
                </a>
            </div>

            {{-- Card: Total Wilayah --}}
            <div class="card card-body border-radius-lg shadow-sm mb-lg-0 mb-4"> {{-- Hapus margin bawah di tampilan lg --}}
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="numbers">
                            <p class="text-sm mb-0 text-muted">Total Wilayah Terdata</p>
                            <h5 class="font-weight-bolder mb-0">
                                {{ $totalWilayah ?? 0 }}
                                <span class="text-sm font-weight-light text-muted ms-1">Wilayah</span>
                            </h5>
                        </div>
                    </div>
                    <div class="col-4 text-end">
                        <div class="icon icon-md icon-shape bg-gradient-dark shadow text-center border-radius-lg">
                            <i class="material-symbols-rounded text-lg opacity-10">map</i>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-2">
                <a class="text-sm text-secondary font-weight-normal mb-0 pt-1" href="{{ route('wilayah.index') }}">
                    Kelola Data Wilayah <i class="fas fa-arrow-circle-right ms-1"></i>
                </a>
            </div>
        </div>
        {{-- Akhir Kolom Kiri --}}

        {{-- Kolom Kanan: 1 Kartu Chart --}}
        <div class="col-lg-8">
            <div class="card shadow-sm h-100"> {{-- Tambah h-100 agar tinggi kartu sama --}}
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="mb-0 font-weight-bold d-flex align-items-center">
                        <i class="material-symbols-rounded text-dark me-2 fs-5">bar_chart</i>
                        Laporan Disetujui per Bulan (Tahun {{ now()->year }})
                    </h6>
                    <p class="text-sm text-muted mt-1 mb-0">
                        Grafik ini menunjukkan total semua laporan setiap bulan.
                    </p>
                </div>
                {{-- Tambah d-flex dan flex-column agar chart bisa tumbuh --}}
                <div class="card-body p-3 d-flex flex-column">
                    <div class="chart flex-grow-1"> {{-- Tambah flex-grow-1 agar chart mengisi sisa ruang --}}
                        {{-- Hapus height="300" agar chart responsif terhadap tinggi card --}}
                        <canvas id="chart-laporan-global" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>
        {{-- Akhir Kolom Kanan --}}

    </div>
    {{-- Akhir Baris Modifikasi --}}


    {{-- Baris Peta Monitoring dan Daftar Wilayah --}}
    <div class="row mt-4">
        {{-- Kolom Peta (8 Kolom) --}}
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card shadow-sm">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold d-flex align-items-center">
                            <i class="material-symbols-rounded text-dark me-2 fs-5">public</i>
                            Peta Monitoring Situasi Wilayah
                        </h6>
                        <span class="badge badge-sm bg-gradient-light text-dark" id="map-status-badge">
                            <i class="fas fa-sync-alt fa-spin me-1"></i> Memuat data...
                        </span>
                    </div>
                    <p class="text-sm text-muted mt-1 mb-0">
                        Monitoring status wilayah secara real-time.
                    </p>
                </div>
                <div class="card-body p-3">
                    <div id="map" style="height: 400px; border-radius: .5rem; background-color: #262626;"></div>
                </div>
            </div>
        </div>

        {{-- [MODIFIKASI] Kolom Daftar Status Wilayah (4 Kolom) --}}
        <div class="col-lg-5">
            <div class="card shadow-sm h-100"> {{-- [UBAH] Tambah h-100 --}}
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            {{-- Judul H6 --}}
                            <h6 class="mb-0 font-weight-bold d-flex align-items-center">
                                <i class="material-symbols-rounded text-dark me-2 fs-5">format_list_bulleted</i>
                                Daftar Wilayah
                            </h6>
                            <p class="text-sm text-muted mt-1 mb-0">
                                Semua wilayah beserta statusnya.
                            </p>
                        </div>
                        {{-- [BARU] Input pencarian kustom --}}
                        <div class="ms-3" style="width: 150px;">
                            {{-- Kita gunakan class 'form-control-sm' agar ukurannya pas --}}
                            <input type="text" id="custom-wilayah-search" class="form-control form-control-sm"
                                placeholder="Cari wilayah...">
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    {{-- [UBAH] Ganti div list dengan struktur table --}}
                    <div class="table-responsive">
                        <table id="wilayah-datatable" class="table table-hover align-middle w-100">
                            {{-- Header tabel akan di-generate oleh JS,
                                 tapi bisa juga ditambahkan di sini untuk kejelasan --}}
                            <thead>
                                <tr>
                                    <th>Wilayah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data di-render oleh JavaScript/DataTables --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Script untuk Grafik dan Peta --}}
@push('scripts')
    {{-- [BARU] Tambahkan JS untuk DataTables (jika belum ada di master) --}}
    {{-- Pastikan jQuery sudah dimuat sebelum ini --}}
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // =========================================================
            // [MODIFIKASI] KODE UNTUK PETA DAN DAFTAR WILAYAH (DATATABLES)
            // =========================================================
            let map;
            let markersLayer;
            let myLocationMarker = null;
            const mapStatusBadge = document.getElementById('map-status-badge');

            // [BARU] Variabel untuk menyimpan referensi ke marker di peta
            let markerReferences = {};

            const defaultCenter = [-0.5, 111.4];
            const defaultZoom = 7;

            // [BARU] 1. Inisialisasi DataTable (dulu, sebelum map)
            let wilayahTable = new DataTable('#wilayah-datatable', {
                columns: [{
                        data: 'nama_wilayah',
                        title: 'Wilayah'
                    },
                    {
                        data: 'status_wilayah',
                        title: 'Status',
                        render: function(data, type, row) {
                            const badgeClass = getBadgeClass(data);
                            return `<span class="badge badge-sm ${badgeClass}">${data}</span>`;
                        }
                    }
                ],
                data: [],
                paging: false,
                info: false,
                scrollY: '350px',
                scrollCollapse: true,
                searching: true,
                lengthChange: false,
                autoWidth: false,
                responsive: true,
                language: {
                    emptyTable: "Belum ada data wilayah.",
                    zeroRecords: "Wilayah tidak ditemukan."
                },
                dom: 't<"d-none"r>'
            });

            // Hubungkan input kustom kita ke API search DataTables
            document.getElementById('custom-wilayah-search').addEventListener('keyup', function() {
                wilayahTable.search(this.value).draw();
            });

            // 2. Inisialisasi Peta
            try {
                map = L.map('map').setView(defaultCenter, defaultZoom);
                L.tileLayer(
                    'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                        maxZoom: 19,
                        attribution: false
                    }).addTo(map);
                markersLayer = L.layerGroup().addTo(map);
            } catch (e) {
                console.error("Gagal inisialisasi Leaflet:", e);
                document.getElementById('map').innerHTML =
                    '<div class="alert alert-danger text-white text-center">Gagal memuat peta. Periksa koneksi internet atau plugin Leaflet.</div>';
                return;
            }

            // 3. Tambahkan kontrol fullscreen
            try {
                map.addControl(new L.Control.Fullscreen({
                    position: 'topleft'
                }));
            } catch (e) {
                console.warn("Plugin Fullscreen (Mapbox) gagal dimuat. Error:", e);
            }

            // 4. Buat Tombol "Lacak Lokasi Saya" Kustom
            L.Control.LocateMe = L.Control.extend({
                onAdd: function(map) {
                    var container = L.DomUtil.create('div',
                        'leaflet-bar leaflet-control leaflet-control-locate');
                    container.innerHTML =
                        `<a href="#" title="Lacak Lokasi Saya" role="button" aria-label="Lacak Lokasi Saya">
                            <span class="material-symbols-rounded">my_location</span>
                           </a>`;
                    L.DomEvent.on(container, 'click', L.DomEvent.stopPropagation)
                        .on(container, 'click', L.DomEvent.preventDefault)
                        .on(container, 'click', function() {
                            map.locate({
                                setView: true,
                                maxZoom: 15
                            });
                        });
                    return container;
                },
                onRemove: function(map) {}
            });
            new L.Control.LocateMe({
                position: 'topleft'
            }).addTo(map);

            // 5. Buat Tombol "Reset View" Kustom
            L.Control.ResetView = L.Control.extend({
                onAdd: function(map) {
                    var container = L.DomUtil.create('div',
                        'leaflet-bar leaflet-control leaflet-control-reset-view');
                    container.innerHTML =
                        `<a href="#" title="Kembali ke Tampilan Awal" role="button" aria-label="Kembali ke Tampilan Awal">
                            <span class="material-symbols-rounded">home</span>
                           </a>`;
                    L.DomEvent.on(container, 'click', L.DomEvent.stopPropagation)
                        .on(container, 'click', L.DomEvent.preventDefault)
                        .on(container, 'click', function() {
                            if (myLocationMarker) {
                                map.removeLayer(myLocationMarker);
                                myLocationMarker = null;
                            }
                            map.setView(defaultCenter, defaultZoom);
                        });
                    return container;
                },
                onRemove: function(map) {}
            });
            new L.Control.ResetView({
                position: 'topleft'
            }).addTo(map);

            // 6. Handle hasil pelacakan lokasi
            map.on('locationfound', function(e) {
                if (myLocationMarker) {
                    map.removeLayer(myLocationMarker);
                }

                const popupHtml =
                    `<div class="popup-title text-center" style="margin: 0; padding: 0.75rem 1rem; border: none;">Lokasi Anda</div>`;
                myLocationMarker = L.circleMarker(e.latlng, {
                        radius: 8,
                        color: '#0d6efd',
                        fillColor: '#0d6efd',
                        fillOpacity: 0.8,
                        weight: 2
                    }).addTo(map)
                    .bindPopup(popupHtml, {
                        className: 'custom-leaflet-popup'
                    }) // Terapkan class
                    .openPopup();

                if (typeof showMaterialToast === 'function') {
                    showMaterialToast('Lokasi Anda ditemukan.', 'success');
                }
            });

            map.on('locationerror', function(e) {
                if (typeof showMaterialToast === 'function') {
                    showMaterialToast(`Gagal melacak lokasi: ${e.message}`, 'danger');
                }
            });

            // 7. Fungsi untuk membuat Ikon "Neon Pulse" (Tidak berubah)
            function getWilayahIcon(status) {
                let statusClass = 'marker-default';
                if (status === 'Aman') statusClass = 'marker-aman';
                else if (status === 'Siaga') statusClass = 'marker-siaga';
                else if (status === 'Bahaya') statusClass = 'marker-bahaya';

                return L.divIcon({
                    className: 'leaflet-div-icon',
                    html: `<div class="neon-marker ${statusClass}"></div>`,
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });
            }

            // 8. Helper untuk mendapatkan kelas badge Bootstrap (Tidak berubah)
            function getBadgeClass(status) {
                if (status === 'Aman') return 'bg-gradient-success';
                if (status === 'Siaga') return 'bg-gradient-warning';
                if (status === 'Bahaya') return 'bg-gradient-danger';
                return 'bg-gradient-secondary';
            }

            // 9. [MODIFIKASI] Fungsi untuk Memuat/Me-refresh Peta dan DATATABLE
            let dataGagalDimuat = false;

            function loadMapData() {
                if (dataGagalDimuat) return;

                if (mapStatusBadge) {
                    mapStatusBadge.innerHTML =
                        '<i class="fas fa-sync-alt fa-spin me-1"></i> Memperbarui data...';
                    mapStatusBadge.className = 'badge badge-sm bg-gradient-light text-dark';
                }

                fetch('{{ route('dashboard.mapData') }}')
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) throw new Error(data.error);

                        markersLayer.clearLayers();
                        markerReferences = {}; // Reset referensi

                        if (data.length === 0) {
                            if (mapStatusBadge) {
                                mapStatusBadge.innerHTML =
                                    '<i class="fas fa-info-circle me-1"></i> Belum ada data';
                                mapStatusBadge.className = 'badge badge-sm bg-gradient-secondary';
                            }
                            if (wilayahTable) {
                                wilayahTable.clear().draw();
                            }
                            return;
                        }

                        let bounds = [];

                        data.forEach(wilayah => {
                            // Bagian Peta
                            if (wilayah.lat && wilayah.lng) {
                                const lat = parseFloat(wilayah.lat);
                                const lng = parseFloat(wilayah.lng);

                                if (!isNaN(lat) && !isNaN(lng)) {
                                    const icon = getWilayahIcon(wilayah.status_wilayah);
                                    const marker = L.marker([lat, lng], {
                                        icon: icon
                                    });

                                    // Buat HTML kustom untuk popup
                                    const popupHtml = `
                                        <div class="popup-title">${wilayah.nama_wilayah}</div>
                                        <hr class="light horizontal my-0">
                                        <div class="popup-status">
                                            <span>Status:</span>
                                            <span class="badge badge-sm ${getBadgeClass(wilayah.status_wilayah)}">${wilayah.status_wilayah}</span>
                                        </div>`;

                                    // Terapkan HTML dan class kustom
                                    marker.bindPopup(popupHtml, {
                                        className: 'custom-leaflet-popup'
                                    });

                                    // --- [INI TAMBAHAN BARUNYA] ---
                                    // Tambahkan event listener 'click' ke marker
                                    marker.on('click', function(e) {
                                        // 'e.latlng' berisi koordinat marker yang diklik
                                        map.flyTo(e.latlng, 15, { // Zoom 15 (level kota)
                                            animate: true,
                                            duration: 1 // 1 detik
                                        });
                                        // Popup akan terbuka secara otomatis karena sudah di-bind.
                                    });
                                    // --- [AKHIR TAMBAHAN BARU] ---

                                    markersLayer.addLayer(marker);
                                    bounds.push([lat, lng]);

                                    // Simpan referensi marker menggunakan nama wilayah sebagai key
                                    markerReferences[wilayah.nama_wilayah] = marker;
                                }
                            }
                        });

                        // Isi DataTable
                        if (wilayahTable) {
                            wilayahTable.clear().rows.add(data).draw();
                        }

                        if (bounds.length === 0) {
                            map.setView(defaultCenter, defaultZoom);
                        }

                        if (mapStatusBadge) {
                            mapStatusBadge.innerHTML =
                                '<i class="fas fa-check-circle me-1"></i> Real-time';
                            mapStatusBadge.className = 'badge badge-sm bg-gradient-success';
                        }
                    })
                    .catch(error => {
                        console.error('Error memuat data peta/wilayah:', error.message);
                        dataGagalDimuat = true;
                        if (mapStatusBadge) {
                            mapStatusBadge.innerHTML =
                                '<i class="fas fa-exclamation-triangle me-1"></i> Gagal Memuat Data';
                            mapStatusBadge.className = 'badge badge-sm bg-gradient-danger';
                        }
                        if (wilayahTable) {
                            wilayahTable.clear().draw();
                        }
                        if (typeof showMaterialToast === 'function') {
                            showMaterialToast(`Gagal memuat data peta: ${error.message}`, 'danger');
                        }
                    });
            }

            // 10. Muat data pertama kali
            loadMapData();

            // 11. [REAL-TIME FIX] Dengarkan Event 'WilayahUpdated'
            try {
                window.Echo.channel('wilayah-updates')
                    .listen('.WilayahUpdated', (e) => {
                        console.log('[Reverb] Menerima event WilayahUpdated:', e);

                        let message = '';
                        let notifType = 'info';
                        let title = 'Informasi Wilayah';

                        if (e && e.wilayah && e.action) {
                            if (e.action === 'created') {
                                title = 'Wilayah Baru Ditambahkan';
                                message = `Wilayah ${e.wilayah.nama_wilayah} telah ditambahkan ke sistem.`;
                                notifType = 'success';
                            } else if (e.action === 'updated') {
                                title = 'Update Status Wilayah';
                                message =
                                    `Status Wilayah <b>${e.wilayah.nama_wilayah}</b> telah diperbarui menjadi: <b>${e.wilayah.status_wilayah || 'Belum Diatur'}</b>`;
                                if (e.wilayah.status_wilayah === 'Aman') {
                                    notifType = 'success';
                                } else if (e.wilayah.status_wilayah === 'Siaga') {
                                    notifType = 'warning';
                                } else if (e.wilayah.status_wilayah === 'Bahaya') {
                                    notifType = 'danger';
                                } else {
                                    notifType = 'info';
                                }
                            } else if (e.action === 'deleted') {
                                title = 'Wilayah Dihapus';
                                message = `Wilayah ${e.wilayah.nama_wilayah} telah dihapus dari sistem.`;
                                notifType = 'danger';
                            }
                        } else {
                            message = 'Data wilayah diperbarui. Me-refresh peta dan daftar.';
                            notifType = 'info';
                        }

                        if (typeof showMaterialToast === 'function' && message) {
                            showMaterialToast(message, notifType, title);
                        } else if (message) {
                            alert(message.replace(/<b>/g, '').replace(/<\/b>/g, ''));
                        } else {
                            console.warn('[Reverb] Event diterima tetapi tidak ada data/aksi yang jelas:', e);
                        }
                        loadMapData();
                    });
            } catch (e) {
                console.warn("Laravel Echo tidak terkonfigurasi. Update real-time tidak akan berfungsi.", e);
                if (mapStatusBadge) {
                    mapStatusBadge.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Mode Offline';
                    mapStatusBadge.className = 'badge badge-sm bg-gradient-warning';
                }
            }

            // =========================================================
            // [BLOK BARU] MENANGANI KLIK PADA BARIS DATATABLES
            // =========================================================
            $('#wilayah-datatable tbody').on('click', 'tr', function(e) {
                // Dapatkan data untuk baris yang diklik
                const rowData = wilayahTable.row(this).data();

                if (rowData && rowData.lat && rowData.lng) {
                    const lat = parseFloat(rowData.lat);
                    const lng = parseFloat(rowData.lng);

                    if (!isNaN(lat) && !isNaN(lng)) {
                        // 1. Pindahkan peta ke lokasi
                        map.flyTo([lat, lng], 15, { // Zoom 15 adalah level kota
                            animate: true,
                            duration: 1 // Durasi 1 detik
                        });

                        // 2. Buka popup marker yang sesuai
                        const marker = markerReferences[rowData.nama_wilayah];
                        if (marker) {
                            // Tambahkan timeout kecil agar popup terbuka SETELAH map selesai terbang
                            setTimeout(() => {
                                marker.openPopup();
                            }, 1000); // 1000ms = 1 detik (sesuai durasi flyTo)
                        }
                    } else {
                        if (typeof showMaterialToast === 'function') {
                            showMaterialToast(
                                `Wilayah '${rowData.nama_wilayah}' tidak memiliki data lokasi (lat/lng).`,
                                'warning');
                        }
                    }
                }
            });


            // =========================================================
            // KODE ANDA YANG SUDAH ADA UNTUK GRAFIK (TIDAK BERUBAH)
            // =========================================================
            const ctxGlobal = document.getElementById('chart-laporan-global').getContext('2d');
            const labelsBulanGlobal = @json($labelsBulanGlobal);
            const dataJumlahGlobal = @json($dataJumlahGlobal);
            const primaryColor = 'rgba(58, 65, 111, 0.8)';
            const primaryColorBorder = 'rgba(58, 65, 111, 1)';

            new Chart(ctxGlobal, {
                type: 'bar',
                data: {
                    labels: labelsBulanGlobal,
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: dataJumlahGlobal,
                        backgroundColor: primaryColor,
                        borderColor: primaryColorBorder,
                        borderWidth: 1,
                        borderRadius: 5,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#344767',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y + ' Laporan';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                drawBorder: false,
                                display: true,
                                drawOnChartArea: true,
                                drawTicks: false,
                                borderDash: [5, 5],
                                color: 'rgba(0, 0, 0, .1)'
                            },
                            ticks: {
                                display: true,
                                padding: 10,
                                color: '#6c757d',
                                font: {
                                    size: 11,
                                    family: "Inter",
                                    style: 'normal',
                                    lineHeight: 2
                                },
                                callback: function(value) {
                                    if (value % 1 === 0) {
                                        return value;
                                    }
                                }
                            }
                        },
                        x: {
                            grid: {
                                drawBorder: false,
                                display: false,
                                drawOnChartArea: false,
                                drawTicks: false
                            },
                            ticks: {
                                display: true,
                                color: '#6c757d',
                                padding: 10,
                                font: {
                                    size: 11,
                                    family: "Inter",
                                    style: 'normal',
                                    lineHeight: 2
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
