@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-8 pb-10">
    <!-- Real-time Status Floating Bar -->
    <div class="flex items-center justify-between bg-white/80 backdrop-blur-md px-6 py-4 rounded-[2rem] border border-white shadow-xl shadow-indigo-100/50 -mt-12 relative z-20">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-12 h-12 {{ $status == 'Online' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-rose-500/10 text-rose-600' }} rounded-2xl flex items-center justify-center text-xl transition-colors duration-500">
                    <i class="fas fa-signal"></i>
                </div>
                @if($status == 'Online')
                <span class="absolute -top-1 -right-1 flex h-4 w-4">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500 border-2 border-white"></span>
                </span>
                @endif
            </div>
            <div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Status Koneksi</p>
                <h4 class="text-sm font-black text-slate-800 tracking-tight">{{ $status == 'Online' ? 'Internet Aktif' : 'Terputus' }}</h4>
            </div>
        </div>
        <div class="text-right">
             <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Uptime</p>
             <h4 class="text-xs font-mono font-black text-indigo-600">{{ $mkInfo['uptime'] ?? '--:--:--' }}</h4>
        </div>
    </div>

    <!-- Main Stats Grid -->
    <div class="grid grid-cols-1 gap-4">
        <!-- Speed Card -->
        <div class="group bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-tachometer-alt text-4xl text-indigo-600"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Paket Internet</p>
            <h3 class="text-xl font-black text-slate-800 leading-tight">{{ $customer->device_name ?: '30 Mbps' }}</h3>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                <span class="text-[9px] font-bold text-indigo-500 uppercase tracking-wider">Layanan Aktif</span>
            </div>
        </div>

        <!-- IP Address Card -->
        <div class="group bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-500 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                <i class="fas fa-network-wired text-4xl text-purple-600"></i>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Alamat IP Publik</p>
            <h3 class="text-lg font-black text-slate-800 font-mono tracking-tighter truncate leading-tight">{{ $mkInfo['address'] ?? ($customer->device_ip ?: '0.0.0.0') }}</h3>
            <div class="mt-3 flex items-center gap-2 text-purple-500">
                <i class="fas fa-shield-alt text-[9px]"></i>
                <span class="text-[9px] font-bold uppercase tracking-wider">Dynamic Session</span>
            </div>
        </div>
    </div>

    <!-- Billing Info (Glass Card) -->
    <div class="relative bg-indigo-900 rounded-[3rem] p-8 text-white shadow-2xl overflow-hidden group">
        <!-- Background Decorations -->
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-indigo-600/50 to-purple-800/50"></div>
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-300 mb-2">Pihutang Layanan</p>
                <h3 class="text-4xl font-black tracking-tight">Rp {{ number_format($customer->bill_amount, 0, ',', '.') }}</h3>
                <div class="mt-4 flex items-center gap-3">
                    <div class="bg-white/15 px-4 py-2 rounded-2xl flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-[10px] text-indigo-200"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest text-white/90">
                           Due: {{ $customer->payment_date ? \Carbon\Carbon::parse($customer->payment_date)->format('d M Y') : '5 Tiap Bulan' }}
                        </span>
                    </div>
                </div>
            </div>
            <button class="bg-white text-indigo-900 font-black px-8 py-4 rounded-2xl shadow-xl hover:bg-slate-50 active:scale-95 transition-all text-sm tracking-[0.1em] uppercase">
                BAYAR SEKARANG
            </button>
        </div>
    </div>

    <!-- Features Section Grid -->
    <div class="grid grid-cols-1 gap-8">
        <!-- Laporan Gangguan -->
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-headset text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-800 tracking-tight">Pusat Bantuan</h4>
                        <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">Tiket Aktif: {{ count($reports) }}</p>
                    </div>
                </div>
                <a href="{{ route('customer_app.tickets.create') }}" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-indigo-600 transition-colors">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
            
            <div class="space-y-4">
                @forelse($reports->take(3) as $r)
                <div class="flex items-center justify-between p-4 bg-slate-50/50 rounded-2xl hover:bg-slate-50 transition-colors group">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-indigo-600 mb-0.5 group-hover:translate-x-1 transition-transform">{{ $r->ticket_id }}</span>
                        <span class="text-[11px] font-bold text-slate-500">{{ $r->created_at->diffForHumans() }}</span>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $r->status_color }}">
                        {{ $r->status }}
                    </span>
                </div>
                @empty
                <div class="text-center py-6 opacity-30">
                    <i class="fas fa-clipboard-check text-2xl mb-2"></i>
                    <p class="text-[10px] font-black uppercase tracking-widest">Tidak ada laporan</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- News/Announcements -->
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm relative overflow-hidden">
            <h4 class="text-sm font-black text-slate-800 tracking-tight mb-8">Promo & Pengumuman</h4>
            
            <div class="flex gap-4 overflow-x-auto pb-4 sidebar-scroll snap-x">
                @for($i=1; $i<=3; $i++)
                <div class="snap-start flex-shrink-0 w-48 h-32 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-[2rem] p-5 border border-indigo-100/50 flex flex-col justify-end group cursor-pointer transition-all hover:shadow-lg">
                    <div class="w-8 h-8 bg-white rounded-xl shadow-sm flex items-center justify-center text-indigo-500 mb-auto transition-transform group-hover:scale-110">
                        <i class="fas fa-gift text-sm"></i>
                    </div>
                    <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-1">PROMO LIMIT BERKAH</p>
                    <p class="text-[11px] font-bold text-slate-600 leading-tight">Diskon 20% khusus pembayaran awal bulan.</p>
                </div>
                @endfor
            </div>
        </div>
    </div>
</div>

<style>
    .sidebar-scroll::-webkit-scrollbar { width: 4px; height: 4px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: rgba(0,0,0,0.02); border-radius: 10px; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(79, 70, 229, 0.2); border-radius: 10px; }
    .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(79, 70, 229, 0.4); }
</style>
@endsection
