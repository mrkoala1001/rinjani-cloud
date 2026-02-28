@extends('layouts.app')

@section('title', 'Template Manager')
@section('header_title', 'Template Manager')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ addModalOpen: false }">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Template Manager</h2>
        <button @click="addModalOpen = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition text-sm">
            <i class="fas fa-plus mr-2"></i>Tambah Template
        </button>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($templates as $tpl)
        <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden hover:shadow-lg transition relative group">
            <div class="bg-gray-50 px-4 py-2 border-b flex justify-between items-center">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">
                    @if(isset($tpl->is_system) && $tpl->is_system)
                        <span class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">
                            <i class="fas fa-shield-alt mr-1"></i>System
                        </span>
                    @else
                        <span class="text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-100">
                            <i class="fas fa-user-edit mr-1"></i>User Template
                        </span>
                    @endif
                </span>
                @if(!(isset($tpl->is_system) && $tpl->is_system))
                <a href="{{ route('voucher.deleteTemplate', $tpl->id) }}" class="text-red-400 hover:text-red-600 transition p-1 hover:bg-red-50 rounded" onclick="return confirm('Hapus template ini?')">
                    <i class="fas fa-trash-alt text-[10px]"></i>
                </a>
                @endif
            </div>
            <div class="p-4 text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-file-code text-blue-500 text-2xl"></i>
                </div>
                <h3 class="font-bold text-gray-700 truncate">{{ $tpl->name }}</h3>
                <p class="text-[10px] text-gray-400 mt-1">Ready for printing</p>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white p-20 text-center border-2 border-dashed border-gray-300 rounded-lg">
            <i class="fas fa-clone fa-3x text-gray-200 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-400">Belum ada template.</h3>
            <p class="text-gray-400 text-sm mt-2">Silahkan tambah template baru untuk mencetak voucher.</p>
        </div>
        @endforelse
    </div>

    <!-- Add Template Modal -->
    <div x-show="addModalOpen" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative p-5 border w-11/12 max-w-4xl shadow-2xl rounded-lg bg-white my-10" @click.away="addModalOpen = false">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-800">Tambah Template Baru</h3>
                <button @click="addModalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('voucher.storeTemplate') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Template</label>
                    <input type="text" name="name" required placeholder="e.g. Thermal 58mm" class="w-full border rounded px-3 py-2 text-sm focus:ring-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Template Code (HTML + CSS)</label>
                    <p class="text-[10px] text-gray-500 mb-2">Supported placeholders: @{{username}}, @{{password}}, @{{price}}, @{{validity}}, @{{profile}}, @{{qrcode}}, @{{reseller}}, @{{hotspotname}}, @{{login_link}}, @{{wa_number}}</p>
                    <textarea name="html_content" rows="15" class="w-full border rounded px-3 py-2 text-xs font-mono bg-gray-900 text-green-400 focus:outline-none" 
placeholder="<style>
.card { border: 1px solid black; padding: 5px; width: 200px; text-align: center; }
</style>
<div class='card'>
    <b>@{{username}}</b><br>
    <small>@{{password}}</small><br>
    @{{price}} - @{{validity}}
</div>"
></textarea>
                </div>
                <div class="flex justify-end pt-4 border-t">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded transition shadow-md">
                        <i class="fas fa-save mr-2"></i>Simpan Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
