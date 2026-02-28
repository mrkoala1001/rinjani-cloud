@extends('layouts.app')

@section('title', 'Owner Detail - ' . $owner->name)
@section('header_title', 'Owner Detail')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <a href="{{ route('hotsupport.dashboard') }}" class="text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
    </a>
    <div class="space-x-2">
        <a href="{{ route('hotsupport.impersonate', $owner->id) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow">
            <i class="fas fa-sign-in-alt mr-2"></i> Login as {{ $owner->name }}
        </a>
    </div>
</div>

<!-- Profile & Income Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-gray-800 col-span-1">
        <div class="flex justify-between items-start mb-2">
            <h3 class="font-bold text-gray-800 text-lg">{{ $owner->name }}</h3>
            <a href="{{ route('hotsupport.owner.edit', $owner->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
        <p class="text-gray-600 mb-1"><i class="fas fa-user mr-2 w-5"></i> Username: <span class="font-mono bg-gray-100 px-1 rounded">{{ $owner->username }}</span></p>
        <p class="text-gray-600 mb-1"><i class="fas fa-envelope mr-2 w-5"></i> {{ $owner->email }}</p>
        <p class="text-gray-600 mb-1"><i class="fas fa-map-marker-alt mr-2 w-5"></i> {{ $owner->location ?? 'Belum ada lokasi' }}</p>
        <p class="text-gray-600 mb-1"><i class="fas fa-key mr-2 w-5"></i> Password: <span class="italic text-xs text-gray-400">Encrypted</span></p>
        <p class="text-gray-600 mb-1"><i class="fas fa-network-wired mr-2 w-5"></i> IP: {{ $owner->mikrotikConfigs->first()?->host ?? '-' }}</p>
        <p class="text-gray-600 mb-1"><i class="fas fa-link mr-2 w-5"></i> DNS: <a href="http://{{ $owner->dns }}" target="_blank" class="text-blue-600 hover:underline">{{ $owner->dns ?? '-' }}</a></p>
        <p class="text-gray-600 mb-1"><i class="fas fa-desktop mr-2 w-5"></i> Winbox: <span class="font-mono bg-gray-100 px-1 rounded">{{ $owner->winbox ?? '-' }}</span></p>
        <p class="text-gray-600 mb-1"><i class="fas fa-server mr-2 w-5"></i> IP API: <span class="font-mono bg-gray-100 px-1 rounded">{{ $owner->ip_api ?? '-' }}</span></p>
        <p class="text-gray-600 mt-2 text-xs italic"><i class="fas fa-sticky-note mr-2 w-5"></i> Notes: {{ $owner->notes ?? '-' }}</p>
    </div>

    <!-- Stats -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-600 col-span-2">
        <h4 class="font-bold text-gray-800 mb-4 border-b pb-2">Pemasukan Bulan Ini ({{ date('F Y') }})</h4>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 text-center">
            <div class="p-2 bg-gray-50 rounded">
                <div class="text-xs text-gray-500 uppercase">Total</div>
                <div class="font-bold text-lg text-green-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
            </div>
            <div class="p-2 bg-gray-50 rounded">
                <div class="text-xs text-gray-500 uppercase">Voucher</div>
                <div class="font-bold text-lg text-blue-600">Rp {{ number_format($incomeVoucher, 0, ',', '.') }}</div>
            </div>
            <div class="p-2 bg-gray-50 rounded">
                <div class="text-xs text-gray-500 uppercase">Member</div>
                <div class="font-bold text-lg text-purple-600">Rp {{ number_format($incomeMember, 0, ',', '.') }}</div>
            </div>
            <div class="p-2 bg-gray-50 rounded">
                <div class="text-xs text-gray-500 uppercase">PPPoE / Reseller</div>
                <div class="font-bold text-lg text-indigo-600">
                    Rp {{ number_format($incomePppoe + $incomeReseller, 0, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="mt-4 text-xs text-gray-400 text-right">
            *Data diambil dari menu Billing -> Sales -> Monitor (Income)
        </div>
    </div>
</div>

<!-- Routers -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-8">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Connected Routers</h3>
        <a href="{{ route('hotsupport.router.create', $owner->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded shadow">
            <i class="fas fa-plus mr-2"></i> Add Router
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-left font-bold bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
                    <th class="px-6 py-3">Host / IP</th>
                    <th class="px-6 py-3">User</th>
                    <th class="px-6 py-3">Port</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($owner->mikrotikConfigs as $router)
                <tr>
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $router->host }}</td>
                    <td class="px-6 py-4">{{ $router->user }}</td>
                    <td class="px-6 py-4">{{ $router->port }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Active
                        </span>
                    </td>
                    <td class="px-6 py-4 flex space-x-2">
                        <a href="{{ route('hotsupport.router.edit', $router->id) }}" class="text-yellow-600 hover:text-yellow-900 font-bold text-sm">Edit</a>
                        <form action="{{ route('hotsupport.router.destroy', $router->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 font-bold text-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        No routers configured.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
