<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $transaction->voucher_code }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 58mm; /* Thermal paper width */
            margin: 0 auto;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 16px;
            margin: 0;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
        }
        .details {
            width: 100%;
            margin-bottom: 20px;
        }
        .details td {
            vertical-align: top;
            padding: 2px 0;
        }
        .label {
            font-weight: bold;
        }
        .total {
            text-align: right;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 10px 0;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
        .btn {
            display: block;
            background: #000;
            color: #fff;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            margin-bottom: 10px;
            font-family: sans-serif;
            border-radius: 5px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print">
        <a href="#" onclick="window.print()" class="btn">Print Struk</a>
        <a href="{{ route('billing.income') }}" class="btn" style="background: #ccc; color: #000;">Kembali</a>
    </div>

    <div class="header">
        <h1>HOTPOT</h1>
        <p>Internet Hotspot & Billing</p>
        <p>{{ date('d/m/Y H:i:s') }}</p>
    </div>

    <table class="details">
        <tr>
            <td class="label">No. Transaksi</td>
            <td>: {{ $transaction->voucher_code }}</td>
        </tr>
        <tr>
            <td class="label">Pelanggan</td>
            <td>: {{ $transaction->customer_name ?: 'Guest' }}</td>
        </tr>
        <tr>
            <td class="label">Kategori</td>
            <td>: {{ $transaction->category }}</td>
        </tr>
        <tr>
            <td class="label">Metode</td>
            <td>: {{ $transaction->payment_method }}</td>
        </tr>
        <tr>
            <td class="label">Tgl Bayar</td>
            <td>: {{ date('d/m/Y', strtotime($transaction->date_sold)) }}</td>
        </tr>
    </table>

    <div class="total">
        TOTAL: Rp {{ number_format($transaction->price, 0, ',', '.') }}
    </div>

    <div class="footer">
        <p>Terima Kasih</p>
        <p>Simpan struk ini sebagai bukti pembayaran yang sah.</p>
    </div>
</body>
</html>
