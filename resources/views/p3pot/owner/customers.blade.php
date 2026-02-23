@extends('p3pot.layouts.app')

@section('title', 'Customers')
@section('header_title', 'Semua Pelanggan (P3POT)')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center">
    <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
        <i class="fas fa-users text-3xl"></i>
    </div>
    <h3 class="text-xl font-bold text-slate-800 mb-2">Manajemen Pelanggan</h3>
    <p class="text-slate-500 mb-6">Fitur untuk mengelola semua pelanggan PPPoE Anda akan tersedia di sini.</p>
    <div class="flex justify-center gap-4">
        <div class="p-4 border border-slate-100 rounded-xl bg-slate-50 min-w-[150px]">
            <p class="text-[10px] font-bold text-slate-400 uppercase">Total Pelanggan</p>
            <p class="text-2xl font-black text-slate-800">0</p>
        </div>
    </div>
</div>
@endsection
