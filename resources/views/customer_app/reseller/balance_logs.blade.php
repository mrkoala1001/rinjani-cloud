@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6">
    <div class="flex items-center justify-between px-2">
        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Riwayat Saldo</h3>
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-bold text-slate-400">Total Saldo:</span>
            <span class="text-xs font-black text-orange-600">Rp {{ number_format($customer->balance, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm border border-slate-100">
        <div class="divide-y divide-slate-50">
            @forelse($logs as $log)
                <div class="p-5 flex items-center justify-between hover:bg-slate-50 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $log->type == 'IN' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                            <i class="fas {{ $log->type == 'IN' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-800 leading-tight">{{ $log->description }}</p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-1">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}
                            </p>
                            @if($log->reference_id)
                                <p class="text-[8px] text-slate-300 font-medium mt-0.5">Ref: {{ $log->reference_id }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black {{ $log->type == 'IN' ? 'text-green-600' : 'text-red-500' }}">
                            {{ $log->type == 'IN' ? '+' : '-' }} {{ number_format($log->amount, 0, ',', '.') }}
                        </p>
                        <p class="text-[9px] text-slate-400 font-bold">
                            Saldo: {{ number_format($log->after_balance, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-slate-300">
                    <i class="fas fa-wallet text-3xl mb-3 opacity-20"></i>
                    <p class="text-[10px] font-black uppercase tracking-widest">Belum ada riwayat saldo</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($logs->hasPages())
    <div class="px-2">
        {{ $logs->links() }}
    </div>
    @endif

    <div class="pb-10">
        <a href="{{ route('customer_app.dashboard') }}" class="flex items-center justify-center gap-2 text-slate-400 font-bold text-xs hover:text-indigo-600 transition-all">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
