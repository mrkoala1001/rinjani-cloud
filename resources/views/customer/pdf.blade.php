<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #334155; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #1e293b; font-size: 18pt; }
        .header p { margin: 5px 0; color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f8fafc; color: #475569; font-weight: bold; text-transform: uppercase; font-size: 8pt; border: 1px solid #e2e8f0; padding: 10px; }
        td { border: 1px solid #e2e8f0; padding: 10px; vertical-align: top; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .status-active { color: #10b981; font-weight: bold; }
        .status-inactive { color: #ef4444; font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 8pt; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data Pelanggan</h1>
        <p>{{ $title }} - Dicetak pada: {{ date('d M Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th>Nama Pelanggan</th>
                <th>Info / Kontak</th>
                <th>Rincian Biaya</th>
                <th>Alamat / IP</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $index => $customer)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $customer->name }}</strong><br>
                    <small style="color: #6366f1">{{ $customer->type }}</small>
                </td>
                <td>
                    WA: {{ $customer->whatsapp ?? '-' }}<br>
                    Status: <span class="{{ $customer->is_active ? 'status-active' : 'status-inactive' }}">
                        {{ $customer->is_active ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                </td>
                <td class="text-right">
                    Rp {{ number_format($customer->bill_amount, 0, ',', '.') }}<br>
                    <small>Tempo: {{ $customer->payment_date ?? '-' }}</small>
                </td>
                <td>
                    {{ $customer->location ?? '-' }}<br>
                    <small>IP: {{ $customer->device_ip ?? '-' }}</small>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Hotpot Antigravity
    </div>
</body>
</html>
