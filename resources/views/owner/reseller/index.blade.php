@extends('layouts.app')

@section('title', 'Manajemen Reseller')
@section('header_title', 'Daftar Reseller')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Manajemen Reseller</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest leading-none mt-1">Kelola akun reseller yang Anda buat</p>
        </div>
    </div>
    <a href="{{ route('owner.reseller.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition-all active:scale-95 group">
        <i class="fas fa-plus"></i> 
        <span>Tambah Reseller</span>
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Reseller</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Username</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Lokasi</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($resellers as $reseller)
                <tr class="hover:bg-slate-50/30 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center font-black text-sm">
                                {{ substr($reseller->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-black text-slate-800">{{ $reseller->name }}</p>
                                <p class="text-[10px] text-slate-400 font-bold">Dibuat: {{ $reseller->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <code class="px-2 py-1 bg-slate-100 rounded text-xs font-bold text-slate-600">{{ $reseller->username }}</code>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-slate-600">{{ $reseller->location ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $reseller->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $reseller->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $reseller->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('owner.reseller.balance') }}" class="p-2 text-slate-400 hover:text-orange-500 transition-colors" title="Tambah Saldo">
                                <i class="fas fa-wallet"></i>
                            </a>
                            <a href="{{ route('owner.reseller.edit', $reseller->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('owner.reseller.destroy', $reseller->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus reseller ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition-colors" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-slate-50 text-slate-200 rounded-full flex items-center justify-center text-2xl">
                                <i class="fas fa-users-slash"></i>
                            </div>
                            <p class="font-black text-slate-400 uppercase tracking-widest text-xs">Belum ada reseller</p>
                            <a href="{{ route('owner.reseller.create') }}" class="text-blue-600 font-bold text-sm hover:underline">Tambah Sekarang</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
