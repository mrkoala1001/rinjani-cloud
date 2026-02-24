@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6">
    <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm border border-slate-100">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">User Aktif Online</h3>
            <span class="bg-blue-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full">{{ count($activeUsers) }}</span>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($activeUsers as $user)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-green-50 text-green-500 rounded-xl flex items-center justify-center relative">
                            <i class="fas fa-wifi text-xs"></i>
                            <span class="absolute top-0 right-0 w-2 h-2 bg-green-500 rounded-full border-2 border-white animate-pulse"></span>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-slate-800">{{ $user['user'] }}</h4>
                            <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest">{{ $user['address'] }} • {{ $user['uptime'] }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-slate-400">
                    <i class="fas fa-users-slash text-2xl mb-2 opacity-20"></i>
                    <p class="text-[10px] font-bold uppercase tracking-widest">Tidak ada user online</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="pb-10">
        <a href="{{ route('customer_app.dashboard') }}" class="flex items-center justify-center gap-2 text-slate-400 font-bold text-xs hover:text-indigo-600 transition-all">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
