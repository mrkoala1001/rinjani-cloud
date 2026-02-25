@extends('layouts.app')

@section('title', 'Manage Saldo Mitra Reseller')
@section('header_title', 'Manage Saldo Mitra Reseller')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-1.5 h-8 bg-blue-500 rounded-full"></div>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Manage Saldo Mitra Reseller</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest leading-none mt-1">Tambah saldo untuk mitra-reseller</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-8">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                <i class="fas fa-plus-circle text-blue-500"></i> Form Top-Up
            </h3>
            
            <form action="{{ route('hotsupport.mitra-reseller.addBalance') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih Mitra-Reseller</label>
                        <select name="reseller_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none" required>
                            <option value="">-- Pilih Mitra --</option>
                            @foreach($resellers as $reseller)
                                <option value="{{ $reseller->id }}">{{ $reseller->name }} (Rp {{ number_format($reseller->balance ?? 0, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jumlah Saldo (IDR)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-slate-400">Rp</span>
                            <input type="number" name="amount" min="100" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-12 pr-4 py-3 font-black text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none" placeholder="0" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full py-4 bg-blue-500 hover:bg-blue-600 text-white font-black rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-check"></i> PROSES TOP-UP
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Mitra-Reseller</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Saldo Saat Ini</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($resellers as $reseller)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-4 font-black text-slate-800">{{ $reseller->name }}</td>
                            <td class="px-6 py-4 text-right font-black text-slate-800">Rp {{ number_format($reseller->balance ?? 0, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="document.querySelector('select[name=reseller_id]').value = '{{ $reseller->id }}';" class="text-xs font-black text-blue-500 hover:underline">Top-Up</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($resellers->hasPages())
                <div class="p-6 border-t border-slate-50">{{ $resellers->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
