@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6">
    <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm border border-slate-100">
        <div class="p-6 border-b border-slate-50">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Riwayat Penjualan</h3>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($transactions as $t)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-xs">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-slate-800">{{ $t->username }}</h4>
                            <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest">{{ \Carbon\Carbon::parse($t->first_login_at)->format('d M H:i') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-slate-800">Rp {{ number_format($t->selling_price) }}</p>
                        <p class="text-[8px] text-green-600 font-black uppercase">{{ $t->profile }}</p>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400">
                    <i class="fas fa-history text-2xl mb-2 opacity-20"></i>
                    <p class="text-[10px] font-bold uppercase tracking-widest">Belum ada transaksi</p>
                </div>
            @endforelse
        </div>
        @if($transactions->hasPages())
            <div class="p-4 bg-slate-50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <div class="pb-10">
        <a href="{{ route('customer_app.dashboard') }}" class="flex items-center justify-center gap-2 text-slate-400 font-bold text-xs hover:text-indigo-600 transition-all">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
