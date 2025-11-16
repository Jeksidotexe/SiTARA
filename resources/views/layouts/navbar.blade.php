<nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Dashboard</a>
                </li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">@yield('page')</li>
            </ol>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <ul class="navbar-nav ms-md-auto pe-md-3 d-flex align-items-center  justify-content-end">
                @auth
                    @if (Auth::user()->role == 'operator')
                        {{-- Tombol Kop Surat --}}
                        <li class="nav-item pe-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="btn-pengaturan-kop"
                                data-bs-toggle="modal" data-bs-target="#modalPengaturanKop" title="Update Kop Surat">
                                <i class="material-symbols-rounded me-1">badge</i>Kop Surat
                            </a>
                        </li>
                        {{-- Tombol Tanda Tangan --}}
                        <li class="nav-item pe-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="btn-pengaturan-ttd"
                                data-bs-toggle="modal" data-bs-target="#modalPengaturanTtd" title="Update Tanda Tangan">
                                <i class="material-symbols-rounded me-1">edit_note</i>Tanda Tangan
                            </a>
                        </li>
                    @endif
                @endauth
                <li class="nav-item dropdown pe-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0 position-relative" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="material-symbols-rounded">notifications</i>

                        {{-- BADGE UNTUK JUMLAH BELUM DIBACA --}}
                        @if (isset($unreadCount) && $unreadCount > 0)
                            <span id="notification-badge"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-gradient-dark border border-white small py-1 px-2"
                                style="font-size: 0.6rem;">
                                {{ $unreadCount }}
                            </span>
                        @else
                            {{-- Sediakan placeholder kosong agar JS bisa menambahkannya --}}
                            <span id="notification-badge"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-gradient-dark border border-white small py-1 px-2"
                                style="font-size: 0.6rem; display: none;">
                            </span>
                        @endif
                    </a>

                    <ul id="notification-dropdown-menu" class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4"
                        aria-labelledby="dropdownMenuButton">

                        <li class="dropdown-header d-flex justify-content-between align-items-center mb-1">
                            <h6 class="text-sm font-weight-bold mb-0">Notifikasi</h6>
                        </li>

                        <li id="mark-as-read-container"
                            style="display: {{ isset($unreadCount) && $unreadCount > 0 ? 'list-item' : 'none' }};">
                            <a id="mark-all-as-read-btn" class="dropdown-item text-center text-dark-blue py-2"
                                href="#" style="font-size: 0.75rem !important;">
                                <i class="material-symbols-rounded me-1"
                                    style="font-size: 1rem; vertical-align: middle;">done_all</i>
                                Tandai semua telah dibaca
                            </a>
                        </li>

                        <li class="dropdown-divider my-1" id="mark-as-read-divider"
                            style="display: {{ isset($unreadCount) && $unreadCount > 0 ? 'list-item' : 'none' }};">
                        </li>

                        {{-- [PERIKSA BLOK INI] --}}
                        @if (isset($notifications) && $notifications->count() > 0)
                            @foreach ($notifications as $notification)
                                @php
                                    $baseUrl = $notification->data['url'] ?? route('dashboard');
                                    // Cek apakah URL sudah memiliki query string '?'
                                    $separator = Str::contains($baseUrl, '?') ? '&' : '?';
                                    $finalUrl = $baseUrl . $separator . 'mark_as_read=' . $notification->id;
                                @endphp
                                <li class="mb-2 notification-item" data-id="{{ $notification->id }}">
                                    <a class="dropdown-item border-radius-md {{ $notification->read() ? '' : 'bg-gray-100' }}"
                                        href="{{ $finalUrl }}"> {{-- <-- Gunakan URL final --}}
                                        <div class="d-flex py-1">
                                            <div class="my-auto">
                                                {{-- Ikon berdasarkan status --}}
                                                @if (Str::contains($notification->data['message'], 'disetujui'))
                                                    <div
                                                        class="text-center me-2 d-flex align-items-center justify-content-center">
                                                        <i
                                                            class="material-symbols-rounded avatar avatar-sm text-success bg-gradient-light me-3 py-2">check_circle</i>
                                                    </div>
                                                @elseif(Str::contains($notification->data['message'], 'revisi'))
                                                    <div
                                                        class="text-center me-2 d-flex align-items-center justify-content-center">
                                                        <i
                                                            class="material-symbols-rounded avatar avatar-sm text-warning bg-gradient-light me-3 py-2">edit_note</i>
                                                    </div>
                                                @else
                                                    <div
                                                        class="text-center me-2 d-flex align-items-center justify-content-center">
                                                        <i
                                                            class="material-symbols-rounded avatar avatar-sm text-info bg-gradient-light me-3 py-2">campaign</i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="text-sm font-weight-normal mb-1">
                                                    {{ $notification->data['message'] ?? 'Notifikasi baru.' }}
                                                </h6>
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="fa fa-clock me-1"></i>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        @endif

                        {{-- 2. Elemen 'empty-state' yang selalu ada tapi disembunyikan --}}
                        <li id="notification-empty-state" class="text-center text-muted text-xs px-3 py-2"
                            style="display: {{ isset($notifications) && $notifications->count() > 0 ? 'none' : 'list-item' }};">
                            Tidak ada notifikasi terbaru.
                        </li>
                        {{-- [AKHIR BLOK] --}}
                    </ul>
                </li>
                @auth
                    <li class="nav-item ps-3 pe-3 d-flex align-items-center">

                        <a href="#" class="nav-link text-body font-weight-bold p-0">
                            @if (Auth::user()->foto)
                                <img src="{{ asset('storage/' . Auth::user()->foto) }}" alt="{{ Auth::user()->nama }}"
                                    class="avatar avatar-sm">
                            @else
                                <i class="material-symbols-rounded avatar avatar-sm">account_circle</i>
                            @endif
                            <span class="d-none d-sm-inline-block ms-1">{{ Auth::user()->nama }}</span>
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
