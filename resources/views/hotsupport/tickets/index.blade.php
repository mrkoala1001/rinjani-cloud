@extends('layouts.app')

@section('title', 'My Tickets')
@section('header_title', 'My Support Tickets')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Support Tickets</h2>
            <p class="text-slate-500 text-sm">Track your reports and inquiries.</p>
        </div>
        <a href="{{ route('hotsupport.report.form') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-md transition flex items-center gap-2">
            <i class="fas fa-plus"></i> New Ticket
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr class="text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Ticket ID</th>
                    <th class="px-6 py-4">Subject</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Last Update</th>
                    <th class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <span class="font-mono text-sm font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded">{{ $ticket->ticket_id ?? '#ID-PENDING' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-slate-800">{{ Str::limit($ticket->subject, 50) }}</div>
                        <div class="text-xs text-slate-500">{{ Str::limit($ticket->message, 50) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase {{ $ticket->status_color }}">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">
                        {{ $ticket->updated_at->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('hotsupport.tickets.show', $ticket->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-sm">
                            View Details
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                        <i class="fas fa-ticket-alt text-4xl mb-3 opacity-30"></i>
                        <p>No tickets found. Submit a new report to get started.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
