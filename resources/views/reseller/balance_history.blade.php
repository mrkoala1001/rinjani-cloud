@extends('layouts.app')

@section('title', 'Riwayat Saldo')
@section('header_title', 'Riwayat Topup & Penggunaan')

@section('content')
<div class="max-w-6xl mx-auto pb-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Riwayat Saldo</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">
                Catatan mutasi masuk dan keluar untuk akun Anda.
            </p>
        </div>
        <a href="{{ auth()->user()->role === 'mitra-reseller' ? route('owner.reseller.balance') : route('reseller.balance') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 font-bold rounded-xl shadow-sm transition-all active:scale-95 group">
            <i class="fas fa-wallet transition-transform"></i> Kembali ke Dompet
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 text-white/10 text-8xl transition-transform group-hover:scale-110">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-indigo-100 mb-1">Total Pemasukan Saldo</p>
                <p class="text-3xl font-black mb-1">Rp {{ number_format($history->where('type', 'IN')->sum('amount'), 0, ',', '.') }}</p>
                <div class="inline-flex text-[10px] font-bold bg-white/20 px-2 py-0.5 rounded-full border border-white/10 items-center gap-1">
                    <i class="fas fa-history"></i> {{ $history->where('type', 'IN')->count() }} Transaksi
                </div>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-6 -bottom-6 text-slate-50 text-8xl transition-transform group-hover:scale-110">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Total Pengeluaran</p>
                <p class="text-3xl font-black mb-1 text-slate-800">Rp {{ number_format($history->where('type', 'OUT')->sum('amount'), 0, ',', '.') }}</p>
                <div class="inline-flex text-[10px] font-bold bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full border border-slate-200 items-center gap-1">
                    <i class="fas fa-receipt"></i> {{ $history->where('type', 'OUT')->count() }} Transaksi
                </div>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="font-bold text-slate-800 flex items-center">
                <i class="fas fa-list text-indigo-500 mr-2"></i> Mutasi Rekening
            </h3>
        </div>

        @if($history->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-slate-100 text-[10px] uppercase tracking-widest text-slate-400">
                            <th class="p-4 font-bold whitespace-nowrap"><i class="far fa-calendar-alt mr-1"></i> Tanggal</th>
                            <th class="p-4 font-bold"><i class="fas fa-info-circle mr-1"></i> Deskripsi</th>
                            <th class="p-4 font-bold text-right"><i class="fas fa-money-bill-wave mr-1"></i> Mutasi</th>
                            <th class="p-4 font-bold text-right"><i class="fas fa-wallet mr-1"></i> Saldo Akhir</th>
                            @if(session('impersonated_by'))
                            <th class="p-4 font-bold text-center">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-100">
                        @foreach($history as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4 align-top">
                                <span class="font-bold text-slate-700 block">{{ $item->created_at->format('d M Y') }}</span>
                                <span class="text-xs text-slate-400 font-mono">{{ $item->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="p-4 max-w-xs align-top">
                                <div class="flex items-start gap-3">
                                    <div class="mt-1 flex-shrink-0">
                                        @if($item->type == 'IN')
                                            <div class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs">
                                                <i class="fas fa-plus"></i>
                                            </div>
                                        @else
                                            <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-xs">
                                                <i class="fas fa-arrow-up"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-700 block">{{ $item->description ?: 'Beli Voucher Biasa' }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono flex items-center gap-1 mt-0.5">
                                            <i class="fas fa-hashtag"></i> {{ $item->reference_id }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 font-black whitespace-nowrap text-right align-top">
                                @if($item->type === 'IN')
                                    <span class="text-green-500">+ Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-rose-500">- Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="p-4 font-black text-slate-700 whitespace-nowrap text-right align-top">
                                Rp {{ number_format($item->after_balance, 0, ',', '.') }}
                            </td>
                            @if(session('impersonated_by'))
                            <td class="p-4 text-center align-top">
                                <form action="{{ route('balance.history.delete', $item->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat mutasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-700 p-2 transition-colors">
                                        <i class="fas fa-trash-alt shadow-sm"></i>
                                    </button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($history->hasPages())
            <div class="p-5 border-t border-slate-100 bg-slate-50/50">
                {{ $history->links() }}
            </div>
            @endif
        @else
            <div class="p-12 text-center flex flex-col items-center justify-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-file-invoice-dollar text-3xl text-slate-300"></i>
                </div>
                <h3 class="text-lg font-black text-slate-700 mb-2">Belum Ada Transaksi</h3>
                <p class="text-slate-500 font-medium max-w-sm text-sm">Akun Anda belum memiliki riwayat pengisian maupun pemotongan saldo. Riwayat transaksi akan otomatis muncul di sini.</p>
                <a href="{{ auth()->user()->role === 'mitra-reseller' ? route('owner.reseller.balance') : route('reseller.balance') }}" class="mt-6 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-all active:scale-95 inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i> Isi Saldo Sekarang
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
