@extends('layouts.app')

@section('title', 'Manage Profile Mitra Reseller')
@section('header_title', 'Manage Profile Mitra Reseller')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-1.5 h-8 bg-purple-500 rounded-full"></div>
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Manage Voucher Profiles</h2>
            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest leading-none mt-1">Kelola profile hotspot untuk mitra-reseller</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-slate-50">
            <tr>
                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400">Nama Mitra</th>
                <th class="px-6 py-4 text-[10px] font-black uppercase text-slate-400 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resellers as $reseller)
            <tr class="border-t border-slate-100 hover:bg-slate-50 transition">
                <td class="px-6 py-4 font-black text-slate-800">{{ $reseller->name }}</td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('hotsupport.mitra-reseller.profiles.show', $reseller->id) }}" class="inline-block bg-purple-50 text-purple-600 font-bold px-4 py-2 rounded-lg hover:bg-purple-100">Lihat / Edit Profile</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
