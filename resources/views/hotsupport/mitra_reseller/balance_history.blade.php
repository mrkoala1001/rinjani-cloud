@extends('layouts.app')

@section('title', 'Riwayat Saldo Mitra Reseller')
@section('header_title', 'Riwayat Saldo Mitra Reseller')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-1.5 h-8 bg-blue-500 rounded-full"></div>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Riwayat Saldo</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest leading-none mt-1">Log penambahan dan pengurangan saldo mitra</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-8">
    <form action="{{ route('hotsupport.mitra-reseller.balance.history') }}" method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Cari berdasarkan Mitra</label>
            <select name="reseller_id" class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 outline-none w-64">
                <option value="">-- Semua Mitra --</option>
                @foreach($resellers as $reseller)
                    <option value="{{ $reseller->id }}" {{ request('reseller_id') == $reseller->id ? 'selected' : '' }}>{{ $reseller->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="py-2 px-6 bg-blue-500 hover:bg-blue-600 text-white font-black rounded-xl shadow-lg transition-all active:scale-95">
            <i class="fas fa-search"></i> FILTER
        </button>
        @if(request('reseller_id'))
            <a href="{{ route('hotsupport.mitra-reseller.balance.history') }}" class="py-2 px-6 bg-slate-100 hover:bg-slate-200 text-slate-500 font-black rounded-xl transition-all">
                RESET
            </a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Mitra / Reseller</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipe</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Nominal</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Saldo Sesudah</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Deskripsi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($history as $item)
                <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-4 text-xs font-bold text-slate-500">{{ $item->created_at->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4 font-black text-slate-800">{{ isset($resellerMap[$item->customer_id]) ? $resellerMap[$item->customer_id]->name : 'Unknown' }}</td>
                    <td class="px-6 py-4">
                        @if($item->type == 'IN')
                            <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded">MASUK</span>
                        @else
                            <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-1 rounded">KELUAR</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right font-black {{ $item->type == 'IN' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $item->type == 'IN' ? '+' : '-' }} Rp {{ number_format($item->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-right font-black text-slate-800">
                        Rp {{ number_format($item->after_balance, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">{{ $item->description }}</td>
                </tr>
                @endforeach
                @if($history->isEmpty())
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-400 font-bold">Belum ada riwayat saldo.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @if($history->hasPages())
        <div class="p-6 border-t border-slate-50">{{ $history->links() }}</div>
    @endif
</div>
@endsection
