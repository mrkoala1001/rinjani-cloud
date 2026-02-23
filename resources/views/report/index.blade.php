@extends('layouts.app')

@section('title', 'My Reports - Koala Hotpot')
@section('header_title', 'My Reports & Tickets')

@section('content')
<div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
        <div>
            <h3 class="text-lg font-bold text-slate-800">Support Tickets</h3>
            <p class="text-sm text-slate-500">History of your reports sent to Mr. Koala</p>
        </div>
        <a href="{{ route('report.form') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition shadow-sm">
            <i class="fas fa-plus mr-1"></i> New Report
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 uppercase text-[10px] font-bold text-slate-500 tracking-wider">
                    <th class="px-6 py-4 border-b border-slate-100">Ticket ID</th>
                    <th class="px-6 py-4 border-b border-slate-100">Subject</th>
                    <th class="px-6 py-4 border-b border-slate-100">Status</th>
                    <th class="px-6 py-4 border-b border-slate-100">Last Update</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                <tr class="hover:bg-slate-50 transition border-b border-slate-50">
                    <td class="px-6 py-4 font-mono text-xs text-slate-600">#{{ $report->id }}</td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800 text-sm">{{ $report->subject }}</div>
                        <div class="text-xs text-slate-500 line-clamp-1">{{ Str::limit($report->message, 50) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider
                            {{ $report->status === 'pending' ? 'bg-yellow-50 text-yellow-700' : '' }}
                            {{ $report->status === 'processed' ? 'bg-blue-50 text-blue-700' : '' }}
                            {{ $report->status === 'resolved' ? 'bg-green-50 text-green-700' : '' }}
                            {{ $report->status === 'closed' ? 'bg-slate-100 text-slate-600' : '' }}">
                            {{ $report->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        {{ $report->updated_at->diffForHumans() }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('report.show', $report->id) }}" class="text-blue-600 hover:text-blue-800 font-bold text-xs">
                            View Details <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400 text-sm">
                        <i class="fas fa-inbox text-4xl mb-3 block opacity-20"></i>
                        No reports found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($reports->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $reports->links() }}
    </div>
    @endif
</div>
@endsection
