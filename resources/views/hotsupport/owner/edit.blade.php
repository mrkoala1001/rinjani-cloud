@extends('layouts.app')

@section('title', 'Edit Mitra')
@section('header_title', 'Edit Data Mitra')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-bold text-gray-800">Edit: {{ $owner->name }}</h3>
    </div>
    <form action="{{ route('hotsupport.owner.update', $owner->id) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="role">
                    Role
                </label>
                <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="role" name="role" required>
                    <option value="owner" {{ $owner->role == 'owner' ? 'selected' : '' }}>Owner</option>
                    <option value="mitra" {{ $owner->role == 'mitra' ? 'selected' : '' }}>Mitra</option>
                    <option value="mitra-reseller" {{ $owner->role == 'mitra-reseller' ? 'selected' : '' }}>Mitra-Reseller</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    Nama Perusahaan / Mitra
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" type="text" name="name" value="{{ $owner->name }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                    Username
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" name="username" value="{{ $owner->username }}" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password Baru
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" type="text" name="password" placeholder="Biarkan kosong jika tidak ubah">
                <p class="text-xs text-gray-500 mt-1">Isi hanya jika ingin mengganti password.</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="location">
                    Lokasi
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="location" type="text" name="location" value="{{ $owner->location }}">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="whatsapp">
                    WhatsApp
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="whatsapp" type="text" name="whatsapp" value="{{ $owner->whatsapp }}">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="dns">
                    DNS / Link Login
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="dns" type="text" name="dns" value="{{ $owner->dns }}">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="winbox">
                    Winbox
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="winbox" type="text" name="winbox" value="{{ $owner->winbox }}">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="ip_api">
                    IP API
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="ip_api" type="text" name="ip_api" value="{{ $owner->ip_api }}">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">
                Catatan
            </label>
            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="notes" name="notes" rows="3">{{ $owner->notes }}</textarea>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('hotsupport.dashboard') }}" class="text-gray-500 hover:text-gray-700 font-bold text-sm">
                Batal
            </a>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Update Mitra
            </button>
        </div>
    </form>
</div>
@endsection
