@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6">
    <div class="flex items-center justify-between px-2">
        <div>
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Detail Batch</h3>
            <p class="text-[10px] font-bold text-slate-900">{{ $batchId }}</p>
        </div>
        <button onclick="window.print()" class="bg-slate-900 text-white p-3 rounded-2xl shadow-lg active:scale-95 transition-all">
            <i class="fas fa-print"></i>
        </button>
    </div>

    <!-- Voucher Cards Grid -->
    <div class="grid grid-cols-1 gap-4 print:p-0">
        @foreach($vouchers as $v)
            <div class="bg-white rounded-[1.5rem] border-2 border-dashed border-slate-200 p-6 flex items-center justify-between overflow-hidden relative group print:border-slate-400 print:shadow-none print:break-inside-avoid">
                <!-- Decoration -->
                <div class="absolute -right-4 -bottom-4 text-indigo-50/50 text-6xl transform rotate-12 group-hover:scale-110 transition-transform print:hidden">
                    <i class="fas fa-ticket-alt"></i>
                </div>

                <div class="relative z-10 w-full">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-50 pb-3 print:pb-2">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-fire text-indigo-500 text-xs"></i>
                            <span class="text-[9px] font-black text-slate-900 uppercase tracking-widest">HOT POT WIFI</span>
                        </div>
                        <span class="text-[9px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full uppercase tracking-tighter print:border print:border-indigo-600">{{ $v->profile }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between gap-4">
                        <div class="space-y-1">
                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest leading-none">Kode Voucher</p>
                            <h4 class="text-2xl font-black text-slate-800 tracking-tighter">{{ $v->username }}</h4>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest leading-none mb-1">Durasi</p>
                            <p class="text-xs font-black text-slate-700">{{ $v->timelimit ?: 'Unlimited' }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between print:mt-2">
                        <p class="text-[8px] text-slate-400 font-bold uppercase leading-none">Harga: <span class="text-slate-800">Rp {{ number_format($v->selling_price ?: $v->price) }}</span></p>
                        <p class="text-[8px] text-slate-500 font-medium">Batas Login: {{ $v->validity ?: '-' }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="pb-10 print:hidden text-center">
        <a href="{{ route('customer_app.reseller.distribution') }}" class="inline-flex items-center gap-2 text-slate-400 font-bold text-xs hover:text-indigo-600 transition-all">
            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
        </a>
    </div>
</div>

<style>
@media print {
    body { background: white !important; }
    header, nav, footer, .print-hidden, a { display: none !important; }
    main { padding: 0 !important; margin: 0 !important; }
    .grid { display: block !important; }
    .bg-white { border: 1px solid #ccc !important; margin-bottom: 10px !important; }
}
</style>
@endsection
