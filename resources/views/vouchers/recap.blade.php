@extends('layouts.app')

@section('title', 'Rekap Voucher')
@section('header_title', 'Rekap Voucher')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Daily Recap -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Rekap Harian (30 Hari Terakhir)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3 text-center">Jumlah (Qty)</th>
                        <th class="px-6 py-3 text-right">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recapData as $r)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-mono">{{ $r->sale_date }}</td>
                        <td class="px-6 py-4 text-center font-bold">{{ $r->qty }}</td>
                        <td class="px-6 py-4 text-right font-bold text-green-600">
                            Rp {{ number_format($r->total_income, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">Data kosong.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mikrotik Reports (Scripts) -->
    <div class="space-y-6">
        @foreach($mikrotikReports as $year => $scripts)
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-4 border-b border-gray-200 bg-blue-50">
                <h2 class="text-lg font-semibold text-blue-800">Laporan Tahunan {{ $year }} <span class="text-xs font-normal text-gray-500">(via Mikrotik Script)</span></h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3">Bulan</th>
                            <th class="px-4 py-3">Source (Total)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scripts as $s)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-900">
                                {{ $s['name'] ?? '-' }}
                                <div class="text-xs text-gray-400 font-mono">{{ $s['last-started'] ?? '' }}</div>
                            </td>
                            <td class="px-4 py-2 font-mono text-blue-600 font-bold">
                                {{ $s['source'] ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-gray-400 italic">Tidak ada data script untuk tahun {{ $year }}.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
