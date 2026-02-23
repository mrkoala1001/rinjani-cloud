@extends('layouts.app')

@section('title', 'Voucher Terjual')
@section('header_title', 'Voucher Terjual')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Voucher Terjual</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest leading-none mt-1">Riwayat penjualan voucher Anda</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Username / Kode</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Harga</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($vouchers as $v)
                <tr class="hover:bg-slate-50/30 transition-colors group">
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-600 text-sm">{{ $v->created_at->format('d M Y') }}</p>
                        <p class="text-[10px] text-slate-400 font-bold">{{ $v->created_at->format('H:i:s') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <code class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-xs font-black">{{ $v->username }}</code>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-black text-slate-800 text-sm">Rp {{ number_format($v->price, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Terjual
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center text-2xl">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <p class="font-black text-slate-400 uppercase tracking-widest text-xs">Belum ada data penjualan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($vouchers->hasPages())
    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
        {{ $vouchers->links() }}
    </div>
    @endif
</div>
@endsection
