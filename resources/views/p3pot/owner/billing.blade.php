@extends('p3pot.layouts.app')

@section('title', 'Billing')
@section('header_title', 'Billing & Sales (P3POT)')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center">
    <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-money-bill-wave text-3xl"></i>
    </div>
    <h3 class="text-xl font-bold text-slate-800 mb-2">Monitor Keuangan</h3>
    <p class="text-slate-500 mb-6">Laporan pemasukan dan pengeluaran dari layanan P3POT akan tampil di sini.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-lg mx-auto">
        <div class="p-4 border border-slate-100 rounded-xl bg-slate-50">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pemasukan Hari Ini</p>
            <p class="text-xl font-black text-slate-800">Rp 0</p>
        </div>
        <div class="p-4 border border-slate-100 rounded-xl bg-slate-50">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pemasukan Bulan Ini</p>
            <p class="text-xl font-black text-slate-800">Rp 0</p>
        </div>
    </div>
</div>
@endsection
