@extends('layouts.app')

@section('title', 'Add Router')
@section('header_title', 'Add Router for ' . $owner->name)

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-bold text-gray-800">Router Configuration</h3>
    </div>
    <form action="{{ route('hotsupport.router.store', $owner->id) }}" method="POST" class="p-6">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="host">
                Host / IP Address
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="host" type="text" name="host" placeholder="192.168.1.1" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="user">
                Username
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="user" type="text" name="user" placeholder="admin" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="pass">
                Password
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="pass" type="password" name="pass" required autocomplete="password">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="port">
                Port (API)
            </label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="port" type="number" name="port" value="8728" required>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('hotsupport.owner.show', $owner->id) }}" class="text-gray-500 hover:text-gray-700 font-bold text-sm">
                Cancel
            </a>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Save Router
            </button>
        </div>
    </form>
</div>
@endsection
