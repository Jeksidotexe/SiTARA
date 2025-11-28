@extends('layouts.master')
@section('title', 'Dashboard')
@section('page', 'Home')

@push('styles')
    <style>
        .custom-leaflet-popup .leaflet-popup-content-wrapper {
            background: rgba(40, 40, 40, 0.9);
            color: #f8f9fa;
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .custom-leaflet-popup .leaflet-popup-content {
            padding: 0;
            font-family: "Inter", sans-serif;
            line-height: 1.5;
            margin: 0;
            min-width: 200px;
        }

        .custom-leaflet-popup .leaflet-popup-tip {
            background: rgba(40, 40, 40, 0.9);
        }

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
        }

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

        .marker-aman {
            background-color: #00e676;
        }

        .marker-aman::before {
            box-shadow: 0 0 12px 4px #00e676;
        }

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

        .leaflet-div-icon {
            background: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
        }

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
        }

        #wilayah-datatable_wrapper .dataTables-length,
        #wilayah-datatable_wrapper .dataTables-filter {
            padding: 0;
        }

        #wilayah-datatable_wrapper .dataTables-filter input {
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

        .table-responsive {
            overflow-x: hidden;
        }

        #map {
            z-index: 1;
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4 align-items-center">
        <div class="col-lg-6 col-md-7 col-12 mb-3 mb-md-0">
            <h3 class="font-weight-bolder text-uppercase mb-1">Admin {{ Auth::user()->wilayah->nama_wilayah }}</h3>
            <p class="text-muted text-sm mb-0">Selamat datang, {{ Auth::user()->nama }}. Ringkasan data sistem.</p>
        </div>

        <div class="col-lg-6 col-md-5 col-12 text-md-end text-start">
            <div class="d-inline-block px-3 py-2 border-radius-lg">
                <p class="text-sm text-muted mb-0 font-weight-bold text-end" id="realtime-date">
                    Memuat tanggal...
                </p>
                <h4 class="font-weight-bolder mb-0 d-flex align-items-center justify-content-md-end text-dark">
                    <i class="material-symbols-rounded me-2 text-gradient text-dark fs-4">schedule</i>
                    <span id="realtime-clock" style="min-width: 110px;">00:00:00</span>
                    <span class="text-xs font-weight-normal ms-1 text-muted">WIB</span>
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 d-flex flex-column">
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
                <p class="mb-0 pt-1 text-sm text-secondary">Semua kategori laporan.</p>
            </div>

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

            <div class="card card-body border-radius-lg shadow-sm mb-lg-0 mb-4">
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

        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="mb-0 font-weight-bold d-flex align-items-center">
                        <i class="material-symbols-rounded text-dark me-2 fs-5">bar_chart</i>
                        Laporan Disetujui per Bulan (Tahun {{ now()->year }})
                    </h6>
                    <p class="text-sm text-muted mt-1 mb-0">
                        Grafik ini menunjukkan total semua laporan setiap bulan.
                    </p>
                </div>
                <div class="card-body p-3 d-flex flex-column">
                    <div class="chart flex-grow-1">
                        <canvas id="chart-laporan-global" class="chart-canvas"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-4">
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card shadow-sm">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 font-weight-bold d-flex align-items-center">
                            <i class="material-symbols-rounded text-dark me-2 fs-5">public</i>
                            Peta Monitoring Situasi Daerah Kabupaten/Kota
                        </h6>
                        <span class="badge badge-sm bg-gradient-light text-dark" id="map-status-badge">
                            <i class="fas fa-sync-alt fa-spin me-1"></i> Memuat data...
                        </span>
                    </div>
                    <p class="text-sm text-muted mt-1 mb-0">
                        Real-time monitoring situasi daerah
                    </p>
                </div>
                <div class="card-body p-3">
                    <div id="map" style="height: 400px; border-radius: .5rem; background-color: #262626;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 font-weight-bold d-flex align-items-center">
                                <i class="material-symbols-rounded text-dark me-2 fs-5">format_list_bulleted</i>
                                Daftar Wilayah
                            </h6>
                            <p class="text-sm text-muted mt-1 mb-0">
                                Semua wilayah beserta statusnya.
                            </p>
                        </div>
                        <div class="ms-3" style="width: 150px;">
                            <input type="text" id="custom-wilayah-search" class="form-control form-control-sm"
                                placeholder="Cari wilayah...">
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div id="wilayah-loading" class="text-center py-5">
                        <div class="spinner-border text-info" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-sm text-muted mt-2 mb-0 font-weight-bold">Sedang memuat data wilayah...</p>
                    </div>

                    <div class="table-responsive d-none" id="wilayah-table-container">
                        <table id="wilayah-datatable" class="table table-hover align-middle w-100">
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let map;
            let markersLayer;
            let myLocationMarker = null;
            const mapStatusBadge = document.getElementById('map-status-badge');

            let markerReferences = {};

            const defaultCenter = [-0.3, 110];
            const defaultZoom = 7;

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

            document.getElementById('custom-wilayah-search').addEventListener('keyup', function() {
                wilayahTable.search(this.value).draw();
            });

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

            try {
                map.addControl(new L.Control.Fullscreen({
                    position: 'topleft'
                }));
            } catch (e) {
                console.warn("Plugin Fullscreen (Mapbox) gagal dimuat. Error:", e);
            }

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
                    })
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

            function getBadgeClass(status) {
                if (status === 'Aman') return 'bg-gradient-success';
                if (status === 'Siaga') return 'bg-gradient-warning';
                if (status === 'Bahaya') return 'bg-gradient-danger';
                return 'bg-gradient-secondary';
            }

            let dataGagalDimuat = false;

            function loadMapData() {
                if (dataGagalDimuat) return;

                // (Optional) Reset tampilan loading jika dipanggil ulang
                document.getElementById('wilayah-loading').classList.remove('d-none');
                document.getElementById('wilayah-table-container').classList.add('d-none');

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
                        document.getElementById('wilayah-loading').classList.add('d-none');
                        document.getElementById('wilayah-table-container').classList.remove('d-none');

                        if (data.error) throw new Error(data.error);

                        markersLayer.clearLayers();
                        markerReferences = {};

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
                            if (wilayah.lat && wilayah.lng) {
                                const lat = parseFloat(wilayah.lat);
                                const lng = parseFloat(wilayah.lng);

                                if (!isNaN(lat) && !isNaN(lng)) {
                                    const icon = getWilayahIcon(wilayah.status_wilayah);
                                    const marker = L.marker([lat, lng], {
                                        icon: icon
                                    });

                                    const popupHtml = `
                                        <div class="popup-title">${wilayah.nama_wilayah}</div>
                                        <hr class="light horizontal my-0">
                                        <div class="popup-status">
                                            <span>Status:</span>
                                            <span class="badge badge-sm ${getBadgeClass(wilayah.status_wilayah)}">${wilayah.status_wilayah}</span>
                                        </div>`;

                                    marker.bindPopup(popupHtml, {
                                        className: 'custom-leaflet-popup'
                                    });

                                    marker.on('click', function(e) {
                                        map.flyTo(e.latlng, 15, {
                                            animate: true,
                                            duration: 1
                                        });
                                    });

                                    markersLayer.addLayer(marker);
                                    bounds.push([lat, lng]);

                                    markerReferences[wilayah.nama_wilayah] = marker;
                                }
                            }
                        });

                        if (wilayahTable) {
                            wilayahTable.clear().rows.add(data).draw();
                            wilayahTable.columns.adjust();

                            if (wilayahTable.responsive) {
                                wilayahTable.responsive.recalc();
                            }
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

                        document.getElementById('wilayah-loading').classList.add('d-none');
                        document.getElementById('wilayah-table-container').classList.remove('d-none');

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

            loadMapData();

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

            $('#wilayah-datatable tbody').on('click', 'tr', function(e) {
                const rowData = wilayahTable.row(this).data();

                if (rowData && rowData.lat && rowData.lng) {
                    const lat = parseFloat(rowData.lat);
                    const lng = parseFloat(rowData.lng);

                    if (!isNaN(lat) && !isNaN(lng)) {
                        map.flyTo([lat, lng], 15, {
                            animate: true,
                            duration: 1
                        });

                        const marker = markerReferences[rowData.nama_wilayah];
                        if (marker) {
                            setTimeout(() => {
                                marker.openPopup();
                            }, 1000);
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

            const ctxGlobal = document.getElementById('chart-laporan-global').getContext('2d');
            const labelsBulanGlobal = @json($labelsBulanGlobal);
            const dataJumlahGlobal = @json($dataJumlahGlobal);
            const currentMonthIndex = @json($currentMonthIndex);

            const defaultColor = 'rgba(58, 65, 111, 0.6)';
            const highlightColor = 'rgba(70, 78, 130, 0.9)';

            const backgroundColors = dataJumlahGlobal.map((_, index) =>
                index === currentMonthIndex ? highlightColor : defaultColor
            );

            new Chart(ctxGlobal, {
                type: 'bar',
                data: {
                    labels: labelsBulanGlobal,
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: dataJumlahGlobal,
                        backgroundColor: backgroundColors,
                        borderWidth: 0,
                        borderRadius: 4,
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
                            backgroundColor: 'rgb(38, 38, 38, 0.9)',
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
                                    if (context.dataIndex === currentMonthIndex) {
                                        label += ' (Saat Ini)';
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
                                color: function(context) {
                                    return context.index === currentMonthIndex ? '#464E82' : '#6c757d';
                                },
                                font: {
                                    weight: function(context) {
                                        return context.index === currentMonthIndex ? 'bold' : 'normal';
                                    },
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cek apakah moment.js tersedia (sudah ada di master.blade.php)
            if (typeof moment !== 'undefined') {
                // Set locale ke Indonesia
                moment.locale('id');

                function updateTime() {
                    const now = moment();

                    // Update Jam (Format: 14:30:59)
                    const timeString = now.format('HH:mm:ss');
                    const clockElement = document.getElementById('realtime-clock');
                    if (clockElement) clockElement.innerText = timeString;

                    // Update Tanggal (Format: Senin, 25 November 2024)
                    const dateString = now.format('dddd, D MMMM YYYY');
                    const dateElement = document.getElementById('realtime-date');
                    if (dateElement) dateElement.innerText = dateString;
                }

                // Jalankan fungsi update setiap 1 detik
                setInterval(updateTime, 1000);

                // Jalankan segera saat load agar tidak ada delay tampilan
                updateTime();
            } else {
                console.error('Moment.js tidak ditemukan. Pastikan sudah di-load di master layout.');
            }
        });
    </script>
@endpush
