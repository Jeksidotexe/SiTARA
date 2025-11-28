<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand px-4 py-3 m-0" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/Logo.png') }}" class="navbar-brand-img" width="auto" height="26"
                alt="main_logo">
            <span class="ms-1 text-sm font-weight-bold">SITARA</span>
        </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-auto " id="sidenav-collapse-main">
        <ul class="navbar-nav">

            {{-- === MENU UMUM === --}}
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                    href="{{ route('dashboard') }}">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-symbols-rounded opacity-5">dashboard</i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            {{-- === MENU KHUSUS PIMPINAN === --}}
            @if (Auth::user()->role == 'pimpinan')
                @inject('pendingCountService', 'App\Services\VerificationService')
                @php
                    $pimpinanPendingCount = $pendingCountService->getPendingCount();
                @endphp

                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Verifikasi
                        Laporan</h6>
                </li>
                <li class="nav-item {{ request()->routeIs('verifikasi.pending*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('verifikasi.pending*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                        href="{{ route('verifikasi.pending') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-symbols-rounded opacity-5">pending_actions</i>
                        </div>
                        <span class="nav-link-text ms-1">Menunggu Verifikasi</span>
                        <span id="sidebar-pending-badge" class="badge bg-gradient-info ms-1 me-5"
                            style="{{ $pimpinanPendingCount > 0 ? '' : 'display: none;' }}">
                            <span id="sidebar-pending-count">{{ $pimpinanPendingCount }}</span>
                        </span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('verifikasi.history*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('verifikasi.history*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                        href="{{ route('verifikasi.history') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-symbols-rounded opacity-5">history</i>
                        </div>
                        <span class="nav-link-text ms-1">Riwayat Verifikasi</span>
                    </a>
                </li>
            @endif

            {{-- === MENU KHUSUS OPERATOR & ADMIN === --}}
            @if (in_array(Auth::user()->role, ['operator']))
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Manajemen Laporan</h6>
                </li>
                <li class="nav-item {{ request()->routeIs('laporan_situasi_daerah.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('laporan_situasi_daerah.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                        href="{{ route('laporan_situasi_daerah.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-symbols-rounded opacity-5">assignment</i>
                        </div>
                        <span class="nav-link-text ms-1">Situasi Daerah</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('laporan_pilkada_serentak.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('laporan_pilkada_serentak.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                        href="{{ route('laporan_pilkada_serentak.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-symbols-rounded opacity-5">assignment</i>
                        </div>
                        <span class="nav-link-text ms-1">Pilkada Serentak</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('laporan_kejadian_menonjol.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('laporan_kejadian_menonjol.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                        href="{{ route('laporan_kejadian_menonjol.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-symbols-rounded opacity-5">assignment</i>
                        </div>
                        <span class="nav-link-text ms-1">Kejadian Menonjol</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('laporan_pelanggaran_kampanye.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('laporan_pelanggaran_kampanye.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                        href="{{ route('laporan_pelanggaran_kampanye.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-symbols-rounded opacity-5">assignment</i>
                        </div>
                        <span class="nav-link-text ms-1">Pelanggaran Kampanye</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('laporan_penguatan_ideologi.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('laporan_penguatan_ideologi.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                        href="{{ route('laporan_penguatan_ideologi.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-symbols-rounded opacity-5">assignment</i>
                        </div>
                        <span class="nav-link-text ms-1">Penguatan Ideologi</span>
                    </a>
                </li>
            @endif


            {{-- === MENU KHUSUS ADMIN === --}}
            @if (Auth::user()->role == 'admin')
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Data Master</h6>
                </li>
                <li class="nav-item {{ request()->routeIs('laporan-bulanan.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('laporan-bulanan.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                        href="{{ route('laporan-bulanan.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-symbols-rounded opacity-5">calendar_month</i>
                        </div>
                        <span class="nav-link-text ms-1 text-wrap">Laporan Bulanan Semua Wilayah</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('rekapitulasi.laporan') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('rekapitulasi.laporan') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                        href="{{ route('rekapitulasi.laporan') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-symbols-rounded opacity-5">summarize</i>
                        </div>
                        <span class="nav-link-text ms-1 text-wrap">Rekapitulasi Laporan
                            Bulanan & Puskomin</span>
                    </a>
                </li>
                <li class="nav-item {{ request()->routeIs('wilayah.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('wilayah.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                        href="{{ route('wilayah.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-symbols-rounded opacity-5">map</i>
                        </div>
                        <span class="nav-link-text ms-1">Daftar Wilayah</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Akun</h6>
                </li>
                <li class="nav-item {{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->routeIs('pengguna.*') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                        href="{{ route('pengguna.index') }}">
                        <div class="text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-symbols-rounded opacity-5">group</i>
                        </div>
                        <span class="nav-link-text ms-1">Akun Pengguna</span>
                    </a>
                </li>
            @endif

            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Pengaturan</h6>
            </li>
            <li class="nav-item {{ request()->routeIs('profil.edit') ? 'active' : '' }}">
                <a class="nav-link {{ request()->routeIs('profil.edit') ? 'active bg-gradient-dark text-white' : 'text-dark' }}"
                    href="{{ route('profil.edit') }}">
                    <div class="text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-symbols-rounded opacity-5">person</i>
                    </div>
                    <span class="nav-link-text ms-1">Edit Profil</span>
                </a>
            </li>

        </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 mb-3 px-3">
        <a class="btn bg-gradient-dark w-100 d-flex align-items-center justify-content-center" href="#"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="material-symbols-rounded opacity-10 me-2">logout</i>
            <span class="text-sm">Keluar</span>
        </a>
    </div>
    <form action="{{ route('logout') }}" method="post" id="logout-form" style="display: none;">
        @csrf
    </form>
</aside>
