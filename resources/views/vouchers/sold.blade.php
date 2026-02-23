@extends('layouts.app')

@section('title', 'Voucher Terjual')
@section('header_title', 'History Voucher Terjual')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{
    search: '',
}">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6">
        <form action="{{ route('voucher.sold') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-bold text-slate-500 mb-1">DARI TANGGAL</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-secondary focus:border-secondary">
            </div>
            <div class="flex-1 w-full">
                <label class="block text-xs font-bold text-slate-500 mb-1">SAMPAI TANGGAL</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm focus:ring-secondary focus:border-secondary">
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit" class="bg-secondary text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-opacity-90 transition shadow-sm">
                    <i class="fas fa-filter mr-2"></i> Tampilkan
                </button>
                @if($startDate && $endDate)
                <a href="{{ route('voucher.sold.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="bg-green-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-green-700 transition shadow-sm">
                    <i class="fas fa-file-excel mr-2"></i> Recap Excel
                </a>
                @endif
                @if($startDate || $endDate)
                <a href="{{ route('voucher.sold') }}" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-slate-200 transition">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-start mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">History Voucher Terjual</h2>
            
            <div class="text-sm text-gray-500 mt-4 flex flex-wrap gap-4">
                <!-- Statistik Hari Ini (Live from Router) -->
                <div class="bg-green-50 px-3 py-2 rounded-lg border border-green-200 shadow-sm min-w-[120px]">
                    <span class="block text-[10px] uppercase text-green-800 font-bold mb-1">Hari Ini (Live)</span>
                    <span class="text-lg font-black text-green-700">Rp {{ number_format($salesToday ?? 0, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-green-600 block">({{ $countToday ?? 0 }} voucher)</span>
                </div>
                
                <!-- Statistik Bulan Ini (Live from Router) -->
                <div class="bg-blue-50 px-3 py-2 rounded-lg border border-blue-200 shadow-sm min-w-[120px]">
                    <span class="block text-[10px] uppercase text-blue-800 font-bold mb-1">Bulan Ini (Live)</span>
                    <span class="text-lg font-black text-blue-700">Rp {{ number_format($salesMonth ?? 0, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-blue-600 block">({{ $countMonth ?? 0 }} voucher)</span>
                </div>

                @if($startDate && $endDate)
                <!-- Total Periode (Filter Result) -->
                <div class="bg-amber-50 px-3 py-2 rounded-lg border border-amber-200 shadow-sm min-w-[120px]">
                    <span class="block text-[10px] uppercase text-amber-800 font-bold mb-1">Periode Terpilih</span>
                    <span class="text-lg font-black text-amber-700">Rp {{ number_format($filteredTotal ?? 0, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-amber-600 block">({{ count($vouchers) }} data tampil)</span>
                </div>
                @endif
                
                <!-- Total DB -->
                <div class="bg-slate-50 px-3 py-2 rounded-lg border border-slate-200 shadow-sm min-w-[120px]">
                    <span class="block text-[10px] uppercase text-slate-800 font-bold mb-1">Total (Database)</span>
                    <span class="text-lg font-black text-slate-700">Rp {{ number_format($grandTotal ?? 0, 0, ',', '.') }}</span>
                    <span class="text-[10px] text-slate-500 block">All Time</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-secondary">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal text-xs">
                <thead>
                    <tr class="bg-gray-50 uppercase text-gray-600 border-b">
                        <th class="px-4 py-3 text-left font-bold">Waktu</th>
                        <th class="px-4 py-3 text-left font-bold">Nama User</th>
                        <th class="px-4 py-3 text-left font-bold">Profile</th>
                        <th class="px-4 py-3 text-left font-bold">Server</th>
                        <th class="px-4 py-3 text-left font-bold">Harga</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($vouchers as $v)
                    <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                        <td class="px-4 py-3 text-gray-500 font-mono">
                            {{ \Carbon\Carbon::parse($v->first_login_at)->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 font-bold text-gray-800 font-mono">{{ $v->voucher_code }}</td>
                        <td class="px-4 py-3 font-bold text-secondary italic">
                            {{ $v->profile }} 
                            <span class="text-[10px] text-gray-400 font-normal">({{ $v->reseller_name ?? 'Admin' }})</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-mono">{{ $v->server ?? 'all' }}</td>
                        <td class="px-4 py-3 font-bold text-green-600 font-mono">
                            Rp {{ number_format($v->current_meta_price ?? $v->price, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-20 bg-white text-center text-gray-400 italic font-bold">
                            Tidak ada riwayat penjualan ditemukan (Last 100).
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
