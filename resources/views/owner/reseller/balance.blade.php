@extends('layouts.app')

@section('title', 'Tambah Saldo Reseller')
@section('header_title', 'Tambah Saldo Reseller')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-1.5 h-8 bg-orange-500 rounded-full"></div>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Tambah Saldo Reseller</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest leading-none mt-1">Isi saldo reseller untuk transaksi voucher</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-8 p-4 bg-green-50 border border-green-100 rounded-xl flex items-center gap-3 text-green-600 animate-bounce">
    <i class="fas fa-check-circle"></i>
    <p class="font-bold text-sm">{{ session('success') }}</p>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Tambah Saldo -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-8">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                <i class="fas fa-plus-circle text-orange-500"></i>
                Form Top-Up
            </h3>
            
            <form action="{{ route('owner.reseller.addBalance') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih Reseller</label>
                        <select name="reseller_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none" required>
                            <option value="">-- Pilih Reseller --</option>
                            @foreach($allResellers as $reseller)
                                <option value="{{ $reseller->id }}">{{ $reseller->name }} (Saldo: Rp {{ number_format($reseller->balance, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Jumlah Saldo (IDR)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-slate-400">Rp</span>
                            <input type="number" name="amount" min="100" class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-12 pr-4 py-3 font-black text-slate-700 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition-all outline-none" placeholder="0" required>
                        </div>
                        <p class="mt-2 text-[10px] text-slate-400 font-bold italic">Minimal top-up Rp 100</p>
                    </div>
                    
                    <button type="submit" class="w-full py-4 bg-orange-500 hover:bg-orange-600 text-white font-black rounded-xl shadow-lg shadow-orange-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i class="fas fa-check"></i>
                        PROSES TOP-UP
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Saldo Saat Ini -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Ringkasan Saldo Reseller</h3>
                <span class="px-3 py-1 bg-slate-100 rounded-full text-[10px] font-black text-slate-500 uppercase tracking-widest">{{ $resellers->total() }} Reseller</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Reseller</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Saldo Saat Ini</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($resellers as $reseller)
                        <tr class="hover:bg-slate-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center font-black text-xs">
                                        {{ substr($reseller->name, 0, 1) }}
                                    </div>
                                    <p class="font-black text-slate-800">{{ $reseller->name }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-black text-slate-800">Rp {{ number_format($reseller->balance, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button onclick="document.querySelector('select[name=reseller_id]').value = '{{ $reseller->id }}'; document.querySelector('input[name=amount]').focus();" class="text-xs font-black text-orange-500 uppercase tracking-widest hover:underline px-3 py-1 rounded-lg hover:bg-orange-50">
                                    Top-Up
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($resellers->hasPages())
            <div class="p-6 border-t border-slate-50">
                {{ $resellers->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
