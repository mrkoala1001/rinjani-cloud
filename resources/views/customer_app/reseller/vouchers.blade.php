@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6">
    <!-- Generate Section -->
    <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-slate-100" x-data="{ showForm: false }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Generate Voucher</h3>
            <button @click="showForm = !showForm" class="text-indigo-600 font-black text-[10px] uppercase tracking-widest pl-4">
                <span x-show="!showForm">Buat Baru <i class="fas fa-plus ml-1"></i></span>
                <span x-show="showForm">Tutup <i class="fas fa-times ml-1"></i></span>
            </button>
        </div>

        <div x-show="showForm" x-collapse x-cloak>
            <form action="{{ route('customer_app.reseller.generate') }}" method="POST" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Pilih Paket</label>
                    <select name="profile" class="w-full bg-slate-50 border-none rounded-2xl py-4 flex items-center px-4 font-bold text-slate-700 text-sm focus:ring-2 focus:ring-indigo-500 transition-all appearance-none" required>
                        <option value="">-- Pilih Profil --</option>
                        @foreach($profiles as $prof)
                            <option value="{{ $prof['name'] }}">{{ $prof['name'] }} (Rp {{ number_format($prof['price']) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Jumlah (Qty)</label>
                    <input type="number" name="qty" value="1" min="1" max="100" class="w-full bg-slate-50 border-none rounded-2xl py-4 flex items-center px-4 font-bold text-slate-700 text-sm focus:ring-2 focus:ring-indigo-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1 ml-1">Time Limit (Opsional)</label>
                    <input type="text" name="timelimit" placeholder="Contoh: 1h, 30m, 1d" class="w-full bg-slate-50 border-none rounded-2xl py-4 flex items-center px-4 font-bold text-slate-700 text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>
                <button type="submit" class="w-full bg-slate-900 text-white font-black py-4 rounded-2xl shadow-lg active:scale-95 transition-all text-sm tracking-widest uppercase">
                    GENERATE SEKARANG
                </button>
            </form>
        </div>
    </div>

    <!-- Redirection Link to Distribution -->
    <div class="px-2">
        <a href="{{ route('customer_app.reseller.distribution') }}" class="w-full bg-indigo-50 text-indigo-600 font-black py-4 rounded-3xl flex items-center justify-center gap-2 hover:bg-indigo-100 transition-all text-xs uppercase tracking-widest">
            <i class="fas fa-list-ul"></i>
            Lihat Distribusi Voucher (History)
        </a>
    </div>

    <!-- Voucher List Snapshot -->
    <div class="bg-white rounded-[2rem] overflow-hidden shadow-sm border border-slate-100">
        <div class="p-6 border-b border-slate-50">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Voucher Terbaru</h3>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($vouchers as $v)
                <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-50 text-indigo-500 rounded-xl flex items-center justify-center font-black text-xs">
                            {{ substr($v->profile, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-slate-800">{{ $v->username }}</h4>
                            <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest">{{ $v->profile }} • {{ \Carbon\Carbon::parse($v->created_at)->format('d M H:i') }}</p>
                        </div>
                    </div>
                    @if($v->first_login_at)
                        <span class="px-2 py-1 bg-green-50 text-green-600 text-[8px] font-bold rounded-full uppercase">Terpakai</span>
                    @else
                        <span class="px-2 py-1 bg-blue-50 text-blue-600 text-[8px] font-bold rounded-full uppercase">Aktif</span>
                    @endif
                </div>
            @empty
                <div class="p-10 text-center text-slate-400">
                    <i class="fas fa-ticket-alt text-2xl mb-2 opacity-20"></i>
                    <p class="text-[10px] font-bold uppercase tracking-widest">Belum ada voucher</p>
                </div>
            @endforelse
        </div>
        @if($vouchers->hasPages())
            <div class="p-4 bg-slate-50">
                {{ $vouchers->links() }}
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
