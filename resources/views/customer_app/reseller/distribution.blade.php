@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6">
    <div class="flex items-center justify-between px-2">
        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Riwayat Distribusi</h3>
        <a href="{{ route('customer_app.reseller.vouchers') }}" class="text-indigo-600 font-bold text-[10px] uppercase tracking-widest">
            <i class="fas fa-plus mr-1"></i> Generate Lagi
        </a>
    </div>

    <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm border border-slate-100">
        <div class="divide-y divide-slate-50">
            @forelse($batches as $b)
                <div class="p-6 hover:bg-slate-50 transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">{{ \Carbon\Carbon::parse($b->created_at)->format('d/m/Y H:i') }}</p>
                            <h4 class="text-sm font-black text-slate-800 leading-tight">{{ $b->batch_id }}</h4>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[9px] font-black rounded-lg uppercase tracking-tighter">{{ $b->profile }}</span>
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[9px] font-black rounded-lg uppercase tracking-tighter">{{ $b->qty }} Vouchers</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('customer_app.reseller.batch.view', $b->batch_id) }}" class="flex items-center justify-center gap-2 bg-indigo-600 text-white font-black py-4 rounded-xl text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-100 active:scale-95 transition-all w-full">
                            <i class="fas fa-eye"></i> LIHAT / PRINT VOUCHER
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400">
                    <i class="fas fa-layer-group text-2xl mb-2 opacity-20"></i>
                    <p class="text-[10px] font-bold uppercase tracking-widest">Belum ada riwayat distribusi</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($batches->hasPages())
    <div class="px-2">
        {{ $batches->links() }}
    </div>
    @endif

    <div class="pb-10">
        <a href="{{ route('customer_app.dashboard') }}" class="flex items-center justify-center gap-2 text-slate-400 font-bold text-xs hover:text-indigo-600 transition-all">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
