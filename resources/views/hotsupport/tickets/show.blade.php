@extends('layouts.app')

@section('title', 'Ticket Details')
@section('header_title', 'Ticket Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('hotsupport.tickets.index') }}" class="text-slate-500 hover:text-blue-600 flex items-center gap-2 transition">
                <i class="fas fa-arrow-left"></i> Back to Tickets
            </a>
            <span class="px-3 py-1 rounded-full text-sm font-bold uppercase {{ $ticket->status_color }}">
                {{ ucfirst($ticket->status) }}
            </span>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
            <div class="p-6 border-b border-slate-100 bg-slate-50">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 mb-2">{{ $ticket->subject }}</h1>
                        <div class="flex items-center gap-4 text-sm text-slate-500">
                            <span class="font-mono bg-slate-200 px-2 rounded">{{ $ticket->ticket_id }}</span>
                            <span><i class="far fa-clock"></i> {{ $ticket->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-8 text-slate-700 leading-relaxed text-lg">
                {!! nl2br(e($ticket->message)) !!}
            </div>
        </div>

        @if($ticket->admin_reply)
        <div class="bg-blue-50 rounded-2xl shadow-sm border border-blue-100 overflow-hidden relative">
            <div class="absolute top-0 right-0 p-4 opacity-10">
                <i class="fas fa-reply text-8xl text-blue-500"></i>
            </div>
            <div class="p-6 border-b border-blue-100 bg-blue-100/50 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h3 class="font-bold text-blue-900">Mr. Koala (Admin)</h3>
                    <p class="text-xs text-blue-600">Replied {{ $ticket->reply_at ? $ticket->reply_at->diffForHumans() : '' }}</p>
                </div>
            </div>
            <div class="p-8 text-blue-900 leading-relaxed text-lg relative z-10">
                {!! nl2br(e($ticket->admin_reply)) !!}
            </div>
        </div>
        @else
        <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-300">
            <i class="fas fa-hourglass-half text-4xl text-slate-300 mb-4 animate-pulse"></i>
            <p class="text-slate-500 font-medium">Waiting for admin reply...</p>
            <p class="text-slate-400 text-sm">We usually respond within 24 hours.</p>
        </div>
        @endif
    </div>
</div>
@endsection
