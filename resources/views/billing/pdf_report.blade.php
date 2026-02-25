<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan Hotpot</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2d3748;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1a202c;
            margin: 0 0 5px 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 0;
            color: #718096;
            font-size: 14px;
        }
        
        .summary-box {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 15px;
            text-align: center;
            border: 1px solid #e2e8f0;
            background-color: #f7fafc;
        }
        .summary-title {
            font-size: 11px;
            color: #718096;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .summary-value {
            font-size: 20px;
            font-weight: bold;
        }
        .val-income { color: #38a169; }
        .val-expense { color: #e53e3e; }
        .val-profit { color: #3182ce; }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f7fafc;
            color: #4a5568;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
        }
        table.data-table tr:nth-child(even) { background-color: #fcfcfc; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        
        .section-title {
            color: #2d3748;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 5px;
            margin-top: 40px;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 70px;
            border-top: 1px solid #000;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>LAPORAN KEUANGAN HOTSPOT</h1>
        <p>Sistem Billing & Financial Management</p>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}</strong></p>
    </div>

    <table class="summary-box">
        <tr>
            <td style="width: 33%">
                <span class="summary-title">Total Pemasukan (Omset)</span>
                <span class="summary-value val-income">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
            </td>
            <td style="width: 33%">
                <span class="summary-title">Total Pengeluaran (Beban)</span>
                <span class="summary-value val-expense">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
            </td>
            <td style="width: 33%">
                <span class="summary-title">Keuntungan Bersih (Laba)</span>
                <span class="summary-value val-profit">Rp {{ number_format($profit, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    <!-- INCOMES -->
    <h2 class="section-title">1. Rincian Pemasukan</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Tanggal</th>
                <th width="12%">Kategori</th>
                <th width="25%">Pelanggan</th>
                <th width="25%">Keterangan</th>
                <th width="20%" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incomes as $key => $income)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($income->date)->format('d/m/Y') }}</td>
                <td>{{ $income->category }}</td>
                <td>{{ $income->customer_name ?? '-' }}</td>
                <td>{{ $income->description }}</td>
                <td class="text-right">{{ number_format($income->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada transaksi pemasukan pada periode ini.</td>
            </tr>
            @endforelse
            <tr>
                <td colspan="5" class="text-right" style="font-weight: bold;">TOTAL PEMASUKAN</td>
                <td class="text-right val-income" style="font-weight: bold;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- EXPENSES -->
    <h2 class="section-title">2. Rincian Pengeluaran Operasional</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Tanggal</th>
                <th width="15%">Kategori</th>
                <th width="47%">Keterangan</th>
                <th width="20%" class="text-right">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $key => $expense)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($expense->date)->format('d/m/Y') }}</td>
                <td>{{ $expense->category }}</td>
                <td>
                    {{ $expense->description }}
                    @if($expense->debt)
                        <br><small style="color:#718096">Hutang: {{ $expense->debt->description }}</small>
                    @endif
                </td>
                <td class="text-right">{{ number_format($expense->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Tidak ada transaksi pengeluaran pada periode ini.</td>
            </tr>
            @endforelse
            <tr>
                <td colspan="4" class="text-right" style="font-weight: bold;">TOTAL PENGELUARAN</td>
                <td class="text-right val-expense" style="font-weight: bold;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
    
    <div class="page-break"></div>
    
    <div class="header" style="margin-top: 30px;">
        <h2>LAMPIRAN: STATUS HUTANG (DEBT)</h2>
        <p>Rekap status keseluruhan catatan hutang pada sistem.</p>
    </div>

    <!-- DEBTS -->
    <table class="summary-box" style="margin-bottom: 15px;">
        <tr>
            <td style="width: 50%">
                <span class="summary-title">Total Hutang Berjalan</span>
                <span class="summary-value" style="color:#d69e2e">Rp {{ number_format($totalDebt, 0, ',', '.') }}</span>
            </td>
            <td style="width: 50%">
                <span class="summary-title">Sudah Dibayar (Dari Tabel Pengeluaran)</span>
                <span class="summary-value val-income">Rp {{ number_format($totalPaidDebt, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="50%">Deskripsi Hutang</th>
                <th width="30%" class="text-right">Sisa Hutang (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($debts as $key => $debt)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($debt->date)->format('d/m/Y') }}</td>
                <td>{{ $debt->description }}</td>
                <td class="text-right font-bold">{{ number_format($debt->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">Sistem bersih dari hutang aktif.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p style="font-size: 10px; color: #a0aec0; text-align: left; float: left; margin-top: 30px;">
            Dicetak secara otomatis dari Sistem Hotpot Billing pada {{ now()->format('d/m/Y H:i') }}
        </p>
        <div class="signature-box">
            <p style="margin-bottom: 5px;">Mengetahui,</p>
            <p><strong>Administrator</strong></p>
            <div class="signature-line"></div>
        </div>
    </div>

</body>
</html>
