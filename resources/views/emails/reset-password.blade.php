<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reset Password</title>

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
    </style>
</head>

<body style="margin: 0; padding: 0; background-color: #f5f5f5; font-family: 'Inter', Helvetica, Arial, sans-serif;">
    <table width="100%" border="0" cellspacing="0" cellpadding="0"
        style="background-color: #f5f5f5; padding: 20px 0;">
        <tr>
            <td align="center">
                <table class="container" width="600" border="0" cellspacing="0" cellpadding="0"
                    style="width: 100%; max-width: 600px; margin: 0 auto;">

                    <tr>
                        <td align="center" style="padding: 20px 0;">
                            <img src="https://i.ibb.co.com/Szy4sRP/Logo.png" alt="Logo" width="76"
                                style="display: block; width: 76px; height: auto;">
                                <strong>Pemerintah Provinsi Kalimantan Barat</strong>
                        </td>
                    </tr>

                    <tr>
                        <td align="center">
                            <table width="100%" border="0" cellspacing="0" cellpadding="0"
                                style="background-color: #ffffff;
                                       border-radius: 8px;
                                       border: 1px solid #d4d4d4;">

                                <tr>
                                    <td style="padding: 30px 30px 20px 30px; font-family: 'Inter', sans-serif;">

                                        <h1
                                            style="margin: 0 0 15px 0; font-size: 24px; font-weight: 600; color: #262626; line-height: 1.3;">
                                            Permintaan Reset Password
                                        </h1>

                                        <p
                                            style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6; color: #737373;">
                                            Halo, <strong>{{ $nama_user }}</strong>,
                                        </p>
                                        <p
                                            style="margin: 0 0 25px 0; font-size: 16px; line-height: 1.6; color: #737373;">
                                            Kami menerima permintaan untuk mereset password akun Anda. Jika Anda merasa
                                            tidak melakukan permintaan ini,
                                            silakan abaikan email ini.
                                        </p>

                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td align="center" style="padding: 10px 0 25px 0;">
                                                    <a href="{{ $url_reset }}" target="_blank"
                                                        style="display: inline-block;
                                                               padding: 12px 24px;
                                                               font-family: 'Inter', sans-serif;
                                                               font-size: 14px;
                                                               font-weight: 500;
                                                               color: #ffffff;
                                                               text-decoration: none;
                                                               background-color: #262626;
                                                               border-radius: 6px;
                                                               letter-spacing: 0.5px;">
                                                        RESET PASSWORD SAYA
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                        <p
                                            style="margin: 0 0 25px 0; font-size: 14px; line-height: 1.5; color: #737373;">
                                            Link reset password ini akan kedaluwarsa dalam
                                            <strong>{{ config('auth.passwords.users.expire', 60) }} menit</strong>.
                                        </p>

                                        <p style="margin: 0 0 10px 0; font-size: 14px; color: #737373;">
                                            Jika Anda kesulitan mengklik tombol, salin dan tempel URL di bawah ini
                                            ke browser web Anda:
                                        </p>
                                        <p
                                            style="margin: 0 0 20px 0; font-size: 12px; color: #1a73e8; word-break: break-all;">
                                            {{ $url_reset }}
                                        </p>

                                        <p style="margin: 0; font-size: 16px; line-height: 1.6; color: #737373;">
                                            Terima kasih.
                                        </p>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 30px 20px;">
                            <p
                                style="margin: 0 0 5px 0; font-family: 'Inter', sans-serif; font-size: 12px; color: #737373;">
                                &copy; 2025 Badan Kesatuan Bangsa dan Politik Provinsi Kalimantan Barat. All rights
                                reserved.
                            </p>
                            <p style="margin: 0; font-family: 'Inter', sans-serif; font-size: 12px; color: #737373;">
                                Email ini dibuat secara otomatis, mohon untuk tidak membalas.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
