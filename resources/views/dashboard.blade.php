@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')

@section('content')
@php
    $statsData = [
        'totalIncome' => $totalIncome,
        'voucherRealtime' => $voucherRealtime,
        'totalVoucherSold' => $totalVoucherSold,
        'billingManual' => $billingManual,
        'hotspotActive' => $hotspotActiveCount,
        'totalVoucher' => $totalVoucherCount,

        'cpuLoad' => $routerResources['cpu-load'] ?? 0,
        'freeMemory' => isset($routerResources['free-memory']) ? round($routerResources['free-memory'] / 1024 / 1024, 2) : 0,
        'uptime' => $routerResources['uptime'] ?? '-',
        'boardName' => $routerResources['board-name'] ?? '-',
        'version' => $routerResources['version'] ?? '-',
        'time' => $routerTime['time'] ?? '-',
        'date' => $routerTime['date'] ?? '-',
        'timeZone' => $routerTime['time-zone-name'] ?? '-',
        'gmtOffset' => $routerTime['gmt-offset'] ?? '-'
    ];
@endphp

<div x-data="dashboardStats({{ json_encode($statsData) }})">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Pemasukan & Status Router</h2>
        </div>
        <a href="{{ route('report.form') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-900 hover:bg-black text-white font-bold rounded-xl shadow-lg transition-all active:scale-95 group">
            <i class="fas fa-paper-plane text-blue-400 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"></i> 
            <span>Report to Mr. Koala</span>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Card 1: Voucher Realtime -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-xl bg-blue-50 text-blue-500 mr-4 group-hover:scale-110 transition">
                    <i class="fas fa-ticket-alt fa-lg"></i>
                </div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">TOTAL HARGA VOCHER YANG SUDAH DIBUAT</p>
            </div>
            <p class="text-2xl font-black text-slate-800" x-text="formatRupiah(stats.voucherRealtime)"></p>
        </div>

        <!-- Card 2: Total Voucher Terjual -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-xl bg-green-50 text-green-500 mr-4 group-hover:scale-110 transition">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                </div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">TOTAL TERJUAL (BULAN INI)</p>
            </div>
            <p class="text-2xl font-black text-slate-800" x-text="formatRupiah(stats.totalVoucherSold)"></p>
        </div>

        <!-- Card 3: Billing Manual -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-xl bg-purple-50 text-purple-500 mr-4 group-hover:scale-110 transition">
                    <i class="fas fa-cash-register fa-lg"></i>
                </div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Total Pemasukan</p>
            </div>
            <p class="text-2xl font-black text-slate-800" x-text="formatRupiah(stats.billingManual)"></p>
        </div>

    
        <!-- Card 3: Total User Aktif -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-xl bg-emerald-50 text-emerald-500 mr-4 group-hover:scale-110 transition">
                    <i class="fas fa-globe fa-lg"></i>
                </div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Total User Aktif</p>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-2xl font-black text-slate-800" x-text="stats.hotspotActive"></p>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Online</span>
            </div>
        </div>

        <!-- Card 4: Total Jumlah User -->
        <div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-200 hover:shadow-md transition group">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-xl bg-rose-50 text-rose-500 mr-4 group-hover:scale-110 transition">
                    <i class="fas fa-users fa-lg"></i>
                </div>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Total Voucher</p>
            </div>
            <div class="flex items-baseline gap-2">
                <p class="text-2xl font-black text-slate-800" x-text="stats.totalVoucher"></p>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-tighter">Generated</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 mb-8">
        <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center">
            <i class="fas fa-server mr-3 text-blue-500"></i> Router Status & Information
        </h3>
        
        <!-- Connection Status -->
        <div class="mb-8 p-4 bg-slate-50 rounded-xl border border-slate-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full {{ $routerStatus == 'Connected' ? 'bg-green-500 animate-pulse shadow-[0_0_10px_rgba(34,197,94,0.5)]' : 'bg-red-500' }}"></div>
                    <span class="text-sm font-bold text-slate-700">MikroTik Connection: </span>
                    <span class="text-sm font-black uppercase tracking-wider {{ $routerStatus == 'Connected' ? 'text-green-600' : 'text-red-600' }}">{{ $routerStatus }}</span>
                </div>
                @if($routerStatus == 'Disconnected')
                    <a href="{{ route('settings') }}" class="text-xs font-black text-blue-600 hover:text-blue-800 uppercase tracking-widest flex items-center gap-1 group">
                        Configure Now <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform font-normal"></i>
                    </a>
                @endif
            </div>
        </div>
        
        @if($routerStatus == 'Connected')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Router Resources -->
                @if($routerResources)
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-slate-50 text-8xl transition-transform group-hover:scale-110">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <h4 class="font-black text-slate-400 mb-5 text-[10px] uppercase tracking-widest border-b border-slate-50 pb-2">Resource Hardware</h4>
                    <div class="space-y-4 relative z-10">
                        <div class="flex justify-between items-center bg-slate-50/50 p-2 rounded-lg">
                            <span class="text-sm font-bold text-slate-500">Board Name</span>
                            <span class="text-sm font-black text-slate-800" x-text="stats.boardName"></span>
                        </div>
                        <div class="flex justify-between items-center p-2">
                            <span class="text-sm font-bold text-slate-500">CPU Load</span>
                            <div class="flex items-center gap-3">
                                <div class="w-24 bg-slate-100 h-1.5 rounded-full overflow-hidden hidden sm:block">
                                    <div class="h-full transition-all duration-500" :class="stats.cpuLoad > 80 ? 'bg-red-500' : 'bg-green-500'" :style="'width: ' + stats.cpuLoad + '%'"></div>
                                </div>
                                <span class="text-sm font-black" :class="stats.cpuLoad > 80 ? 'text-red-600' : 'text-green-600'">
                                    <span x-text="stats.cpuLoad"></span>%
                                </span>
                            </div>
                        </div>
                        <div class="flex justify-between items-center bg-slate-50/50 p-2 rounded-lg">
                            <span class="text-sm font-bold text-slate-500">Free Memory</span>
                            <span class="text-sm font-black text-slate-800"><span x-text="stats.freeMemory"></span> MB</span>
                        </div>
                        <div class="flex justify-between items-center p-2">
                            <span class="text-sm font-bold text-slate-500">Uptime</span>
                            <span class="text-sm font-black text-blue-600" x-text="stats.uptime"></span>
                        </div>
                        <div class="flex justify-between items-center bg-slate-50/50 p-2 rounded-lg">
                            <span class="text-sm font-bold text-slate-500">RouterOS</span>
                            <span class="text-sm font-black text-slate-800" x-text="stats.version"></span>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Time Server -->
                @if($routerTime)
                <div class="bg-slate-900 rounded-2xl p-6 shadow-xl text-white relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-white/5 text-8xl transition-transform group-hover:scale-110">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h4 class="font-black text-blue-400 mb-5 text-[10px] uppercase tracking-widest border-b border-white/10 pb-2">Router Time Info</h4>
                    <div class="space-y-4 relative z-10">
                        <div class="flex justify-between items-center bg-white/5 p-2 rounded-lg">
                            <span class="text-sm font-bold text-slate-400">Date</span>
                            <span class="text-sm font-black" x-text="stats.date"></span>
                        </div>
                        <div class="flex flex-col items-center justify-center py-4">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Local Time</span>
                            <span class="text-4xl font-black text-blue-400 tracking-tighter" x-text="stats.time"></span>
                        </div>
                        <div class="flex justify-between items-center bg-white/5 p-2 rounded-lg">
                            <span class="text-sm font-bold text-slate-400">Timezone</span>
                            <span class="text-sm font-black truncate max-w-[150px]" x-text="stats.timeZone"></span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        @else
            <div class="bg-rose-50 border border-rose-100 rounded-2xl p-6 flex flex-col items-center text-center gap-3">
                <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mb-2">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <h4 class="font-black text-rose-800 tracking-tight">Router Disconnected</h4>
                <p class="text-sm text-rose-600 max-w-md">
                    Unable to fetch router details. Please check your config in settings and ensure your MikroTik is reachable.
                </p>
                <a href="{{ route('settings') }}" class="mt-2 bg-rose-600 text-white px-6 py-2 rounded-xl font-bold text-xs hover:bg-rose-700 transition shadow-lg">
                    Update Settings
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardStats', (initialStats) => ({
            stats: initialStats,
            formatRupiah(number) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number).replace('Rp', 'Rp ');
            },
            init() {
                // Start local clock ticker
                setInterval(() => {
                    this.updateTime()
                }, 1000);

                if (typeof Echo !== 'undefined') {
                    Echo.channel('dashboard-stats.{{ auth()->id() }}')
                        .listen('DashboardStatsUpdated', (e) => {
                            this.stats.hotspotActive = e.stats.hotspot_count;
                            this.stats.totalVoucher = e.stats.totalVoucher;
                            this.stats.cpuLoad = e.stats.cpu_load;
                            this.stats.uptime = e.stats.uptime;
                            this.stats.freeMemory = (e.stats.free_memory / 1024 / 1024).toFixed(2);
                            this.stats.boardName = e.stats.board_name;
                            this.stats.version = e.stats.version;
                            
                            if (e.stats.time) {
                                this.stats.time = e.stats.time.time; // "HH:MM:SS"
                                this.stats.date = e.stats.time.date;
                                this.stats.timeZone = e.stats.time['time-zone-name'];
                                this.stats.gmtOffset = e.stats.time['gmt-offset'];
                            }
                            
                            if (e.stats.totalIncome) this.stats.totalIncome = e.stats.totalIncome;
                            if (e.stats.voucherRealtime) this.stats.voucherRealtime = e.stats.voucherRealtime;
                            if (e.stats.totalVoucherSold) this.stats.totalVoucherSold = e.stats.totalVoucherSold;
                            if (e.stats.monthlyIncome) this.stats.monthlyIncome = e.stats.monthlyIncome;
                        });
                }
            },
            updateTime() {
                if (!this.stats.time || this.stats.time === '-') return;
                
                // Parse current time string "HH:MM:SS"
                let parts = this.stats.time.split(':');
                if (parts.length !== 3) return;
                
                let h = parseInt(parts[0]);
                let m = parseInt(parts[1]);
                let s = parseInt(parts[2]);
                
                s++;
                if (s >= 60) { s = 0; m++; }
                if (m >= 60) { m = 0; h++; }
                if (h >= 24) { h = 0; }
                
                // Update stats.time with formatted string
                this.stats.time = 
                    (h < 10 ? '0' + h : h) + ':' + 
                    (m < 10 ? '0' + m : m) + ':' + 
                    (s < 10 ? '0' + s : s);
            }
        }));
    });
</script>
@endsection
