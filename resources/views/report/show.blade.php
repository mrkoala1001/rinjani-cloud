@extends('layouts.app')

@section('title', 'Ticket Details - Koala Hotpot')
@section('header_title', 'Ticket #' . $report->id)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('report.index') }}" class="text-slate-500 hover:text-slate-800 font-bold text-sm flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
        <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
            {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
            {{ $report->status === 'processed' ? 'bg-blue-100 text-blue-700' : '' }}
            {{ $report->status === 'resolved' ? 'bg-green-100 text-green-700' : '' }}
            {{ $report->status === 'closed' ? 'bg-slate-100 text-slate-600' : '' }}">
            Status: {{ $report->status }}
        </span>
    </div>

    <div class="bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden mb-6">
        <div class="p-6 bg-slate-50 border-b border-slate-100">
            <h1 class="text-xl font-bold text-slate-800 mb-2">{{ $report->subject }}</h1>
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <i class="fas fa-clock"></i> Sent on {{ $report->created_at->format('d M Y, H:i') }}
            </div>
        </div>
        <div class="p-8 text-slate-700 leading-relaxed whitespace-pre-line">
            {{ $report->message }}
        </div>
    </div>

    @if($report->admin_reply)
    <div class="bg-blue-50 rounded-2xl shadow-md border border-blue-100 overflow-hidden relative">
        <div class="absolute top-0 left-0 w-1.5 h-full bg-blue-500"></div>
        <div class="p-6 border-b border-blue-100/50 flex items-center gap-3">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-blue-600 shadow-sm">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h3 class="font-bold text-blue-900">Response from Mr. Koala</h3>
                <p class="text-xs text-blue-600/80">{{ \Carbon\Carbon::parse($report->reply_at)->diffForHumans() }}</p>
            </div>
        </div>
        <div class="p-8 text-blue-900 leading-relaxed whitespace-pre-line">
            {{ $report->admin_reply }}
        </div>
    </div>
    @else
    <div class="text-center p-8 bg-slate-50 rounded-xl border border-slate-200 border-dashed text-slate-400 text-sm">
        <i class="fas fa-hourglass-half mb-2 text-xl"></i><br>
        Waiting for response from admin...
    </div>
    @endif
</div>
@endsection
