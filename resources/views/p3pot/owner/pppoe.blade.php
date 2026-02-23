@extends('p3pot.layouts.app')

@section('title', 'PPPoE List')
@section('header_title', 'PPPoE List (MikroTik)')

@section('content')
<div class="glass-card rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-slate-800 flex justify-between items-center bg-slate-900/50">
        <div>
            <h3 class="font-bold text-white">Active PPPoE Sessions</h3>
            <p class="text-xs text-slate-500">Real-time data from your MikroTik router</p>
        </div>
        <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-lg shadow-indigo-600/20 transition">
            <i class="fas fa-sync-alt mr-2"></i> Refresh
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-900/50 text-slate-500 text-xs uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Address</th>
                    <th class="px-6 py-4">Uptime</th>
                    <th class="px-6 py-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($active as $session)
                    <tr class="hover:bg-slate-800/50 transition">
                        <td class="px-6 py-4 text-white font-medium">{{ $session['user'] ?? '???' }}</td>
                        <td class="px-6 py-4 text-slate-400">{{ $session['address'] ?? '-' }}</td>
                        <td class="px-6 py-4"><span class="bg-indigo-500/10 text-indigo-400 px-2 py-1 rounded-md text-xs font-bold">{{ $session['uptime'] ?? '-' }}</span></td>
                        <td class="px-6 py-4">
                             <button class="text-red-400 hover:text-red-300 transition"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 italic">
                            No active PPPoE sessions found or router disconnected.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
