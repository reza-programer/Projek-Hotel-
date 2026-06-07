<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f3ed;
            color: #1a1a2e;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            border: 4px double #9B2335;
            padding: 30px;
            border-radius: 4px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1a1a2e;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .logo-kanji {
            font-size: 40px;
            font-weight: bold;
            color: #9B2335;
            margin-bottom: 10px;
        }
        .title-jp {
            font-size: 24px;
            letter-spacing: 4px;
            margin: 0;
        }
        .title-en {
            font-size: 12px;
            letter-spacing: 2px;
            color: #C9A84C;
            margin-top: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .guest-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 14px;
        }
        th {
            border-bottom: 2px solid #1a1a2e;
            text-align: left;
            padding: 10px 5px;
        }
        td {
            padding: 15px 5px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }
        .total-row {
            background-color: #fdfaf5;
            font-weight: bold;
            font-size: 16px;
        }
        .total-row td {
            border-top: 2px solid #1a1a2e;
            border-bottom: 2px solid #1a1a2e;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .stamp-container {
            text-align: center;
            margin-top: 20px;
        }
        .stamp {
            display: inline-block;
            border: 2px solid #9B2335;
            color: #9B2335;
            padding: 10px 15px;
            font-size: 20px;
            font-weight: bold;
            transform: rotate(-5deg);
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-kanji">雅</div>
            <div class="title-jp">領収書</div>
            <div class="title-en">RECEIPT / INVOICE</div>
        </div>

        <div class="info-row">
            <div>No. Nota: <strong>INV/{{ $reservation->booking_code }}</strong></div>
            <div>Tanggal: {{ now()->format('d M Y') }}</div>
        </div>

        <div class="guest-name">
            Tuan / Nyonya: <strong>{{ $reservation->user->name }}</strong>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Detail Kunjungan</th>
                    <th style="text-align:right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $reservation->room->name }}</strong><br>
                        <small>Check-in: {{ $reservation->check_in->format('d M Y') }} · Check-out: {{ $reservation->check_out->format('d M Y') }} ({{ $reservation->nights }} Malam)</small>
                    </td>
                    <td style="text-align:right;">Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Pajak Hotel (11%)</td>
                    <td style="text-align:right;">Rp {{ number_format($reservation->total_price * 0.11, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Biaya Layanan Omotenashi (5%)</td>
                    <td style="text-align:right;">Rp {{ number_format($reservation->total_price * 0.05, 0, ',', '.') }}</td>
                </tr>
                @php
                    $subtotal = $reservation->total_price;
                    $tax = $subtotal * 0.11;
                    $service = $subtotal * 0.05;
                    $totalGross = $subtotal + $tax + $service;
                @endphp
                <tr class="total-row">
                    <td>TOTAL TAGIHAN LUNAS</td>
                    <td style="text-align:right; color:#9B2335;">Rp {{ number_format($totalGross, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="stamp-container">
            <div class="stamp">領収</div>
            <div style="font-size: 10px; color: #4A8A5C; margin-top: 5px; font-weight: bold;">TERBAYAR / PAID</div>
        </div>

        <div class="footer">
            <strong>DARMA MIZUKI</strong><br>
            雅ホテル • Omotenashi Luxury<br>
            Jl. Sakura Indah No. 88, Jakarta Selatan<br>
            hello@darmamizuki.com
        </div>
    </div>
</body>
</html>
