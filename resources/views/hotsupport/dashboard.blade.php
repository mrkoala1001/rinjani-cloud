@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')

@section('content')
<div class="space-y-8">
    
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-slate-900 rounded-3xl p-8 md:p-12 shadow-2xl">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-slate-800 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-48 h-48 bg-blue-900 rounded-full blur-3xl opacity-30"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-wider mb-4">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    Super Admin Console
                </div>
                <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight mb-2">
                    Selamat Datang, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">{{ auth()->user()->name }}</span>
                </h2>
                <p class="text-slate-400 max-w-xl text-sm md:text-base">
                    Pantau kinerja seluruh mitra (RT/RW Net) dan status router secara realtime dari satu pusat kendali.
                </p>
            </div>
            
            <div class="flex flex-wrap gap-3">
                 <a href="{{ route('hotsupport.report.form') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-white text-slate-900 hover:bg-blue-50 font-bold rounded-xl shadow-lg transition-all active:scale-95 group">
                    <i class="fas fa-paper-plane text-blue-600 group-hover:-translate-y-1 group-hover:translate-x-1 transition-transform"></i>
                    <span>Tulis Laporan</span>
                </a>
                <a href="{{ route('hotsupport.owner.create') }}" class="inline-flex items-center gap-2 px-5 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 transition-all active:scale-95 group">
                    <i class="fas fa-plus group-hover:rotate-90 transition-transform"></i>
                    <span>Tambah Mitra</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Voucher Created -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl group-hover:bg-indigo-500 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-magic text-xl"></i>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg">All Time</span>
            </div>
            <div class="text-slate-500 text-xs font-bold uppercase tracking-wide mb-1">Total Vocher Terbuat</div>
            <div class="text-3xl font-black text-slate-800 tracking-tight">
                Rp {{ number_format($totalVoucherCreated, 0, ',', '.') }}
            </div>
            <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between text-xs text-slate-400">
                <span>Seluruh Mitra</span>
                <i class="fas fa-ticket-alt text-indigo-500"></i>
            </div>
        </div>

        <!-- Total Voucher Sold -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-shopping-cart text-xl"></i>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-500 bg-blue-50 px-2 py-1 rounded-lg">Realtime</span>
            </div>
            <div class="text-slate-500 text-xs font-bold uppercase tracking-wide mb-1">Total Vocher Terjual</div>
            <div class="text-3xl font-black text-slate-800 tracking-tight">
                Rp {{ number_format($totalVoucherSold, 0, ',', '.') }}
            </div>
            <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between text-xs text-slate-400">
                 <span>Aktif / Terjual</span>
                 <i class="fas fa-check-circle text-blue-500"></i>
            </div>
        </div>

        <!-- Total Income -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl group-hover:bg-emerald-500 group-hover:text-white transition-colors duration-300">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg">Manual Entry</span>
            </div>
            <div class="text-slate-500 text-xs font-bold uppercase tracking-wide mb-1">Total Pemasukan</div>
            <div class="text-3xl font-black text-slate-800 tracking-tight">
                Rp {{ number_format($totalManualIncome, 0, ',', '.') }}
            </div>
            <div class="mt-4 pt-4 border-t border-slate-50 flex items-center justify-between text-xs text-slate-400">
                 <span>Tabel Pemasukan</span>
                 <i class="fas fa-cash-register text-emerald-500"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Mitra Card -->
        <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 to-violet-700 rounded-3xl p-8 shadow-xl text-white group">
            <div class="absolute right-0 top-0 -mr-6 -mt-6 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all duration-500"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-white/10 backdrop-blur-md rounded-2xl">
                        <i class="fas fa-building text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-indigo-800 text-xs font-bold uppercase tracking-wider">Total Mitra</p>
                        <h3 class="text-xl text-indigo-800 font-bold">RT/RW Net Aktif</h3>
                    </div>
                </div>
                
                <div class="flex items-baseline gap-2">
                    <span class="text-6xl text-indigo-800 tracking-tighter">{{ $totalOwners }}</span>
                    <span class="text-indigo-800 font-bold">Perusahaan</span>
                </div>
                
                <div class="mt-6 pt-6 border-t border-white/10 flex justify-between items-center">
                    <span class="text-sm text-indigo-800">Terdaftar di sistem</span>
                    <a href="{{ route('hotsupport.owner.create') }}" class="px-4 py-2 bg-white/10 hover:bg-white text-white hover:text-indigo-600 rounded-xl text-xs font-bold transition-all flex items-center gap-2 backdrop-blur-sm">
                        <span>Tambah Baru</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Router Card -->
        <div class="relative overflow-hidden bg-gradient-to-br from-cyan-500 to-blue-600 rounded-3xl p-8 shadow-xl text-white group">
            <div class="absolute right-0 bottom-0 -mr-6 -mb-6 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all duration-500"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-3 bg-white/10 backdrop-blur-md rounded-2xl">
                        <i class="fas fa-server text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-cyan-800 text-xs font-bold uppercase tracking-wider">Infrastruktur</p>
                        <h3 class="text-xl text-cyan-800                font-bold">Total Router</h3>
                    </div>
                </div>
                
                <div class="flex items-baseline gap-2">
                    <span class="text-6xl text-cyan-800 font-black tracking-tighter">{{ $totalRouters }}</span>
                    <span class="text-cyan-800 font-bold">Connected</span>
                </div>
                
                <div class="mt-6 pt-6 border-t border-white/10 flex justify-between items-center">
                    <span class="text-sm text-cyan-800">Status Online</span>
                    <div class="flex -space-x-2">
                         @for($i=0; $i<min(3, $totalRouters); $i++)
                            <div class="w-8 h-8 rounded-full bg-slate-800 border-2 border-cyan-500 flex items-center justify-center text-[10px]">
                                <i class="fas fa-check text-green-400"></i>
                            </div>
                         @endfor
                         @if($totalRouters > 3)
                            <div class="w-8 h-8 rounded-full bg-slate-800 border-2 border-cyan-500 flex items-center justify-center text-[10px] font-bold">
                                +{{ $totalRouters - 3 }}
                            </div>
                         @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Partner List - Responsive (Table Desktop / Detail Cards Mobile) -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h3 class="font-black text-xl text-slate-800 flex items-center gap-2">
                    <i class="fas fa-list-ul text-blue-500"></i> Daftar Mitra
                </h3>
                <p class="text-slate-400 text-sm mt-1">Kelola akses dan data pelanggan mitra.</p>
            </div>
            
            <div class="relative">
                <input type="text" placeholder="Cari mitra..." class="pl-10 pr-4 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent w-full md:w-64">
                <i class="fas fa-search absolute left-3 top-3 text-slate-400 text-sm"></i>
            </div>
        </div>

        <!-- Desktop Table (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr class="text-left font-bold text-slate-500 uppercase text-[10px] tracking-widest">
                        <th class="px-6 py-4">Mitra / ID</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4">Lokasi</th>
                        <th class="px-6 py-4 text-right">Voucher Terbuat</th>
                        <th class="px-6 py-4 text-right">Voucher Terjual</th>
                        <th class="px-6 py-4 text-right">Total Pemasukan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($owners as $owner)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold ring-2 ring-white shadow-md">
                                    {{ substr($owner->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors">{{ $owner->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono bg-slate-100 px-1.5 py-0.5 rounded inline-block mt-0.5">ID: {{ $owner->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-slate-600">{{ $owner->email }}</div>
                            @if($owner->whatsapp)
                                <div class="text-[10px] text-green-600 flex items-center gap-1 mt-1">
                                    <i class="fab fa-whatsapp"></i> {{ $owner->whatsapp }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                             <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-lg uppercase tracking-wide">
                                <i class="fas fa-map-marker-alt text-slate-400"></i>
                                {{ Str::limit($owner->location ?? 'Global', 20) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                             <div class="text-[11px] font-bold text-slate-600 bg-slate-50 px-2 py-1 rounded-lg inline-block">Rp {{ number_format($owner->total_vouchers_created ?? 0, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                             <div class="text-[11px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg inline-block">Rp {{ number_format($owner->total_vouchers_sold ?? 0, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                             <div class="text-sm font-black text-emerald-600">Rp {{ number_format($owner->total_income_manual ?? 0, 0, ',', '.') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('hotsupport.impersonate', $owner->id) }}" class="p-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl transition-all active:scale-95 group/btn" title="Login as Mitra">
                                    <i class="fas fa-fingerprint group-hover/btn:scale-110 transition-transform"></i>
                                </a>
                                <a href="{{ route('hotsupport.owner.show', $owner->id) }}" class="p-2 bg-slate-100 text-slate-600 hover:bg-slate-800 hover:text-white rounded-xl transition-all active:scale-95 group/btn" title="Detail">
                                    <i class="fas fa-search group-hover/btn:scale-110 transition-transform"></i>
                                </a>
                                <a href="{{ route('hotsupport.owner.edit', $owner->id) }}" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-500 hover:text-white rounded-xl transition-all active:scale-95 group/btn" title="Edit">
                                    <i class="fas fa-edit group-hover/btn:rotate-12 transition-transform"></i>
                                </a>
                                <form action="{{ route('hotsupport.owner.destroy', $owner->id) }}" method="POST" onsubmit="return confirm('🚨 PERINGATAN! Anda akan menghapus Mitra ini beserta SELURUH datanya secara permanen. Tindakan ini tidak dapat dibatalkan.\n\nApakah Anda yakin?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl transition-all active:scale-95 group/btn" title="Hapus Mitra">
                                        <i class="fas fa-trash-alt group-hover/btn:scale-110 transition-transform"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-20 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-300">
                                    <i class="fas fa-folder-open text-2xl"></i>
                                </div>
                                <p class="font-bold text-slate-500">Belum ada mitra terdaftar.</p>
                                <p class="text-xs mt-1">Selahkan tambahkan mitra untuk mulai memantau revenue.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Stacked Cards (Visible on Mobile) -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($owners as $owner)
            <div class="p-4 space-y-4 hover:bg-slate-50 transition-colors">
                <!-- Header: Name & ID -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                         <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold ring-2 ring-white shadow-sm">
                            {{ substr($owner->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-800">{{ $owner->name }}</div>
                            <div class="text-xs text-slate-500">{{ $owner->email }}</div>
                        </div>
                    </div>
                    <span class="text-[10px] font-mono bg-slate-100 px-2 py-1 rounded text-slate-500">#{{ $owner->id }}</span>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-[10px] text-slate-400 uppercase font-bold mb-1">Lokasi</div>
                        <div class="font-medium text-slate-700 flex items-center gap-1">
                             <i class="fas fa-map-marker-alt text-slate-400 text-xs"></i>
                             {{ $owner->location ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] text-slate-400 uppercase font-bold mb-1">Pemasukan Manual</div>
                        <div class="font-black text-emerald-600">Rp {{ number_format($owner->total_income_manual ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>

                <!-- Action Buttons (Full Width) -->
                <div class="grid grid-cols-3 gap-2 pt-2">
                    <a href="{{ route('hotsupport.impersonate', $owner->id) }}" class="flex flex-col items-center justify-center gap-1 p-2 bg-blue-50 text-blue-600 rounded-xl text-xs font-bold active:scale-95 transition-transform">
                        <i class="fas fa-fingerprint text-lg"></i>
                        <span>Login</span>
                    </a>
                    <a href="{{ route('hotsupport.owner.show', $owner->id) }}" class="flex flex-col items-center justify-center gap-1 p-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold active:scale-95 transition-transform">
                        <i class="fas fa-search text-lg"></i>
                        <span>Detail</span>
                    </a>
                    <a href="{{ route('hotsupport.owner.edit', $owner->id) }}" class="flex flex-col items-center justify-center gap-1 p-2 bg-yellow-50 text-yellow-600 rounded-xl text-xs font-bold active:scale-95 transition-transform">
                        <i class="fas fa-edit text-lg"></i>
                        <span>Edit</span>
                    </a>
                    <form action="{{ route('hotsupport.owner.destroy', $owner->id) }}" method="POST" onsubmit="return confirm('🚨 Hapus Mitra dan seluruh data?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex flex-col items-center justify-center gap-1 p-2 bg-red-50 text-red-600 rounded-xl text-xs font-bold active:scale-95 transition-transform">
                            <i class="fas fa-trash-alt text-lg"></i>
                            <span>Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="py-12 text-center text-slate-400">
                <i class="fas fa-folder-open text-4xl mb-3 opacity-30"></i>
                <p>Belum ada mitra terdaftar.</p>
            </div>
            @endforelse
        </div>
        
    </div>
</div>
@endsection
