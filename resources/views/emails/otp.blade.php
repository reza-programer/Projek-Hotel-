<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP Anda</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #FDFAF5;
            color: #1A1A2E;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
        }
        .card {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(26, 26, 46, 0.05);
            border: 1px solid rgba(201, 168, 76, 0.2);
        }
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #9B2335;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .message {
            font-size: 16px;
            color: #4A4A5A;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .otp-box {
            background-color: #FDFAF5;
            border: 2px dashed #C9A84C;
            padding: 20px;
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #9B2335;
            border-radius: 4px;
            margin-bottom: 30px;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: #8E8E9F;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">Darma Mizuki</div>
            <div class="title">Verifikasi Email Anda</div>
            <div class="message">
                Terima kasih telah mendaftar di Darma Mizuki Club. Silakan gunakan kode OTP 6 digit berikut untuk menyelesaikan proses registrasi Anda. Kode ini berlaku selama 10 menit.
            </div>
            <div class="otp-box">
                {{ $otp }}
            </div>
            <div class="message" style="font-size: 14px;">
                Jika Anda tidak merasa melakukan pendaftaran ini, silakan abaikan email ini.
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Darma Mizuki Ryokan. Hak Cipta Dilindungi.
        </div>
    </div>
</body>
</html>
