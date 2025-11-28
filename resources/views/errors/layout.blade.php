<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Mengambil judul dari exception, dengan fallback --}}
    <title>@yield('title', 'Terjadi Kesalahan') | SiTARA</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

    <style>
        body {
            /* Menggunakan font yang sama dengan app Anda */
            font-family: 'Inter', sans-serif;
            /* Latar belakang abu-abu seperti layout dashboard Anda */
            background-color: #f8f9fa;
        }

        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .error-card {
            max-width: 600px;
            width: 100%;
            border: none;
            border-radius: 0.75rem;
            /* Radius yang lebih modern */
        }

        .error-icon {
            font-size: 4rem;
            /* Menggunakan warna utama 'dark-blue' dari app Anda */
            color: #344767;
        }

        .error-code {
            font-size: 5rem;
            font-weight: 700;
            color: #344767;
            line-height: 1;
        }

        /* Menggunakan style tombol yang sama dengan app Anda */
        .btn-gradient-dark {
            background-image: linear-gradient(195deg, #42424a 0%, #191919 100%);
            color: #fff;
        }

        .btn-gradient-dark:hover {
            color: #fff;
            opacity: 0.9;
        }
    </style>
</head>

<body class="antialiased">
    <div class="error-container">
        <div class="card shadow-lg error-card">
            <div class="card-body p-4 p-md-5 text-center">

                {{-- Ikon default jika tidak ada gambar --}}
                <span class="material-symbols-rounded error-icon mb-3 d-block">
                    @yield('icon', 'error')
                </span>

                <h1 class="error-code">
                    {{--
                      Secara cerdas mengambil kode dari section atau dari exception
                      (Contoh: $exception->getStatusCode())
                    --}}
                    @yield('code', $exception->getStatusCode())
                </h1>

                <h2 class="h4 fw-bold text-dark mb-2">
                    @yield('title')
                </h2>

                <p class="text-muted mb-4">
                    @yield('message', $exception->getMessage())
                </p>

                <a href="{{ url('/dashboard') }}" class="btn btn-xl btn-dark btn-gradient-dark px-4 shadow-sm">
                    <span class="material-symbols-rounded align-middle me-1" style="font-size: 1.2rem;">home</span>
                    Kembali ke Halaman Utama
                </a>

                <div class="mt-5 text-muted text-xs">
                    &copy; 2025 SiTARA
                </div>

            </div>
        </div>
    </div>
</body>

</html>
