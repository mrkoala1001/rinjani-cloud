@extends('layouts.app')

@section('title', 'Riwayat Topup Reseller')
@section('header_title', 'Riwayat Topup Reseller')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 overflow-hidden border border-slate-100">
        <div class="p-0">
            <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Daftar Permintaan Topup</h2>
                    <p class="text-xs text-slate-500 font-medium mt-1">Monitor semua transaksi topup reseller via Payment Gateway.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-8 py-5 text-left font-black text-slate-400 uppercase text-[10px] tracking-widest uppercase">Reseller</th>
                            <th class="px-8 py-5 text-left font-black text-slate-400 uppercase text-[10px] tracking-widest uppercase">Ref / Merchant Ref</th>
                            <th class="px-8 py-5 text-left font-black text-slate-400 uppercase text-[10px] tracking-widest uppercase">Metode</th>
                            <th class="px-8 py-5 text-right font-black text-slate-400 uppercase text-[10px] tracking-widest uppercase">Nominal</th>
                            <th class="px-8 py-5 text-center font-black text-slate-400 uppercase text-[10px] tracking-widest uppercase">Status</th>
                            <th class="px-8 py-5 text-center font-black text-slate-400 uppercase text-[10px] tracking-widest uppercase">Waktu</th>
                            <th class="px-8 py-5 text-center font-black text-slate-400 uppercase text-[10px] tracking-widest uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topups as $t)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="font-black text-slate-800 text-sm">{{ $t->user->name ?? 'Unknown' }}</div>
                                <div class="text-[10px] text-slate-400 font-bold">@ {{ $t->user->username ?? '-' }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-xs font-black text-slate-600">{{ $t->reference }}</div>
                                <div class="text-[10px] text-slate-400 font-medium">{{ $t->merchant_ref }}</div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-slate-600">{{ $t->payment_method }}</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="font-black text-slate-900 text-sm">Rp {{ number_format($t->total, 0, ',', '.') }}</div>
                                <div class="text-[10px] text-slate-400 font-medium">Beban: Rp {{ number_format($t->fee, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if($t->status === 'PAID')
                                    <span class="px-3 py-1.5 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-wider border border-green-100">LUNAS</span>
                                @elseif($t->status === 'UNPAID')
                                    <span class="px-3 py-1.5 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black uppercase tracking-wider border border-amber-100">PENDING</span>
                                @else
                                    <span class="px-3 py-1.5 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-wider border border-red-100">{{ $t->status }}</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-center text-xs text-slate-500 font-medium">
                                {{ $t->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if($t->status === 'UNPAID' && ($config->mode ?? '') === 'sandbox')
                                <form action="{{ route('hotsupport.payment-gateway.simulate', $t->id) }}" method="POST" onsubmit="return confirm('Simulasi bayar transaksi ini?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-slate-900 text-white text-[9px] font-black rounded-lg hover:bg-black transition-all uppercase tracking-tighter">
                                        Simulasi Bayar
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mx-auto mb-4">
                                    <i class="fas fa-history text-3xl"></i>
                                </div>
                                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Belum ada riwayat topup</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($topups->hasPages())
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                {{ $topups->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
