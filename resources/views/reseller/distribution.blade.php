@extends('layouts.app')

@section('title', 'Riwayat Distribusi Voucher')
@section('header_title', 'Riwayat Distribusi Voucher')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-1.5 h-8 bg-blue-600 rounded-full"></div>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Voucher Saya</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest leading-none mt-1">Daftar batch voucher yang didistribusikan ke Anda</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Waktu Generate</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Profile / Paket</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Qty</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Total Nilai</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($batches as $batch)
                <tr class="hover:bg-slate-50/30 transition-colors group">
                    <td class="px-6 py-4">
                        <p class="font-bold text-slate-700 text-sm">{{ \Carbon\Carbon::parse($batch->generated_at)->format('d M Y') }}</p>
                        <p class="text-[10px] text-slate-400 font-bold">{{ \Carbon\Carbon::parse($batch->generated_at)->format('H:i') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-black uppercase tracking-wider">
                            {{ $batch->profile }}
                        </span>
                        <p class="text-[10px] text-slate-400 font-bold mt-1">{{ $batch->template_name ?? 'Template Default' }}</p>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-black text-slate-800 text-sm">{{ number_format($batch->qty) }} Pcs</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-black text-green-600 text-sm">Rp {{ number_format($batch->total_price, 0, ',', '.') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($batch->payment_status == 'paid')
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                            <i class="fas fa-check-circle"></i> Lunas
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                            <i class="fas fa-clock"></i> Belum Lunas
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('voucher.viewBatch', $batch->batch_id) }}" class="p-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm group-hover:scale-110">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('voucher.printBatch', $batch->batch_id) }}" target="_blank" class="p-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-orange-500 hover:text-white transition-all shadow-sm group-hover:scale-110">
                                <i class="fas fa-print"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-20 h-20 bg-slate-50 text-slate-200 rounded-3xl flex items-center justify-center text-4xl">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <div class="max-w-xs">
                                <h4 class="font-black text-slate-800 uppercase tracking-wider mb-1">Belum Ada Distribusi</h4>
                                <p class="text-xs text-slate-400 font-medium">Voucher yang Anda buat atau yang diberikan oleh owner akan muncul di sini.</p>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($batches->hasPages())
    <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-100">
        {{ $batches->links() }}
    </div>
    @endif
</div>
@endsection
