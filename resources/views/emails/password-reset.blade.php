<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reset Password - Sistem Informasi Kampus</title>
    <style>
        :root {
            --color-primary: #1B4C89;
            --color-primary-dark: #153C6C;
        }

        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #1B4C89 0%, #153C6C 100%);
            font-family: Arial, sans-serif;
            min-height: 100vh;
        }

        .wrapper {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 32px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #1B4C89 0%, #153C6C 100%);
            color: white;
            padding: 24px;
            text-align: center;
        }

        .logo {
            max-width: 120px;
            margin-bottom: 16px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .content {
            padding: 32px;
            color: #4b5563;
            line-height: 1.6;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #1B4C89 0%, #153C6C 100%);
            color: white !important;
            text-decoration: none;
            padding: 14px 36px;
            border-radius: 50px;
            margin: 24px 0;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 4px 6px -1px rgba(27, 76, 137, 0.2);
        }

        .alert {
            background-color: #f8fafc;
            border-left: 4px solid #1B4C89;
            border-radius: 8px;
            padding: 16px;
            margin: 24px 0;
            font-size: 14px;
            color: #64748B;
        }

        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 24px 0;
        }

        p {
            font-size: 14px;
            color: #4b5563;
            margin: 16px 0;
        }

        .welcome-text {
            font-size: 16px;
            font-weight: 500;
            color: #1B4C89;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #64748B;
            padding: 0 20px;
            margin-top: 32px;
        }

        .url-text {
            color: #1B4C89;
            word-break: break-all;
            font-size: 12px;
            background-color: #f8fafc;
            padding: 12px;
            border-radius: 6px;
            margin-top: 12px;
            display: block;
        }

        .copyright {
            margin-top: 24px;
            color: #64748B;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                <img src="https://lppm.stikpartoraja.ac.id/public/custom/assetsFoto/logo.png" alt="Logo Kampus"
                    class="logo">
                <h1>Reset Password</h1>
            </div>
            <div class="content">
                <p class="welcome-text">Halo {{ $name }},</p>

                <p>Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda di Sistem
                    Informasi Kampus.</p>

                <div style="text-align: center;">
                    <a href="{{ $url }}" class="button">Reset Password</a>
                </div>

                <div class="alert">
                    <strong>Catatan:</strong> Link reset password ini akan kadaluarsa dalam {{ $count }} menit.
                </div>

                <p>Jika Anda tidak merasa melakukan permintaan reset password, abaikan email ini.</p>

                <div class="divider"></div>

                <div class="footer">
                    Jika Anda mengalami masalah saat mengklik tombol "Reset Password",
                    salin dan tempel URL di bawah ini ke browser Anda:
                    <span class="url-text">{{ $url }}</span>
                </div>

                <div class="copyright">
                    © {{ date('Y') }} Sistem Informasi Kampus. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
