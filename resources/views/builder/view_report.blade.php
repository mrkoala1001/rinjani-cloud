@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('builder.reports') }}" class="inline-flex items-center text-gray-400 hover:text-white transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Kembali ke Inbox
        </a>
        <div class="flex gap-2">
            <form action="{{ route('builder.reports.read', $report->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg text-sm transition text-gray-200">
                    Tandai Selesai / Baca
                </button>
            </form>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl shadow-2xl border border-gray-700 overflow-hidden max-w-4xl mx-auto">
        <!-- Header Email Style -->
        <div class="p-6 bg-gray-900/50 border-b border-gray-700">
            <h1 class="text-2xl font-bold text-white mb-4">{{ $report->subject }}</h1>
            
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white text-xl">
                        {{ strtoupper(substr($report->sender_name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-white font-medium">
                            {{ $report->sender->fullname ?? ($report->sender->name ?? $report->sender_name) }} 
                            <span class="text-gray-500 font-normal">&lt;{{ $report->sender->email ?? ($report->sender->username ?? '-') }}&gt;</span>
                        </div>
                        <div class="text-xs flex items-center gap-2 mt-1">
                             <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $report->sender_type === 'App\Models\P3potUser' ? 'bg-rose-900/40 text-rose-400 border border-rose-800' : 'bg-indigo-900/40 text-indigo-400 border border-indigo-800' }}">
                                {{ $report->sender_type === 'App\Models\P3potUser' ? 'P3POT' : 'HOTSPOT' }}
                             </span>
                             <span class="px-1.5 py-0.5 rounded bg-blue-900/40 text-blue-400 border border-blue-800 uppercase text-[10px]">{{ $report->sender_role }}</span>
                             <span class="text-gray-500">Dikirim kepada Builder (Mr. Koala)</span>
                        </div>
                    </div>
                </div>
                <div class="text-right text-sm text-gray-500">
                    <div>{{ $report->created_at->format('d M Y') }}</div>
                    <div>{{ $report->created_at->format('H:i') }} ({{ $report->created_at->diffForHumans() }})</div>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="p-8 text-gray-300 leading-relaxed min-h-[200px] whitespace-pre-wrap font-serif text-lg">
            {{ $report->message }}
        </div>

        @if($report->admin_reply)
        <div class="px-8 py-6 bg-blue-900/20 border-t border-blue-900/50">
            <h3 class="text-blue-400 font-bold mb-2 flex items-center gap-2">
                <i class="fas fa-reply"></i> Balasan Anda:
            </h3>
            <div class="text-blue-200 whitespace-pre-wrap">{{ $report->admin_reply }}</div>
            <p class="text-xs text-blue-500 mt-2">Dibalas pada: {{ $report->reply_at ? $report->reply_at->format('d M Y H:i') : '-' }}</p>
        </div>
        @endif

        <!-- Footer / Action Area -->
        <div class="p-6 bg-gray-900/50 border-t border-gray-700">
            <form action="{{ route('builder.reports.reply', $report->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="admin_reply" class="block text-gray-400 text-sm font-bold mb-2">Balas Tiket:</label>
                    <textarea name="admin_reply" id="admin_reply" rows="5" class="w-full bg-gray-800 border border-gray-700 text-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-3" placeholder="Tulis balasan untuk user...">{{ $report->admin_reply }}</textarea>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <label class="text-gray-400 text-sm font-bold">Status:</label>
                        <select name="status" class="bg-gray-800 border border-gray-700 text-gray-300 rounded p-2 text-sm">
                            <option value="pending" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processed" {{ $report->status == 'processed' ? 'selected' : '' }}>Diff (Proses)</option>
                            <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Selesai (Resolved)</option>
                            <option value="closed" {{ $report->status == 'closed' ? 'selected' : '' }}>Tutup (Closed)</option>
                        </select>
                    </div>

                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow transition flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i> Kirim Balasan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
<style>
    body { background-color: #0f172a; }
</style>
