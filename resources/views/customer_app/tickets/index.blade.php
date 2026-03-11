@extends('layouts.customer_app')

@section('dashboard_content')
<div class="space-y-6 pb-10">
    <!-- Header Page -->
    <div class="flex items-center justify-between -mt-10 mb-8 relative z-20 px-2">
        <div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Tiket Laporan</h2>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Status gangguan & bantuan</p>
        </div>
        <a href="{{ route('customer_app.tickets.create') }}" class="w-12 h-12 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 active:scale-95 transition-all">
            <i class="fas fa-plus"></i>
        </a>
    </div>

    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-2xl flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-500">
        <i class="fas fa-check-circle text-emerald-500"></i>
        <p class="text-[11px] font-bold text-emerald-600">{{ session('success') }}</p>
    </div>
    @endif

    <div class="space-y-4">
        @forelse($tickets as $ticket)
        <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex items-start justify-between mb-4">
                <div class="space-y-1">
                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">{{ $ticket->ticket_id }}</span>
                    <h4 class="text-sm font-black text-slate-800">{{ $ticket->subject }}</h4>
                </div>
                <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $ticket->status_color }}">
                    {{ $ticket->status }}
                </span>
            </div>
            
            <p class="text-xs text-slate-500 font-medium leading-relaxed mb-4 line-clamp-2">{{ $ticket->message }}</p>
            
            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                <span class="text-[10px] font-bold text-slate-400"><i class="far fa-clock mr-1"></i> {{ $ticket->created_at->format('d M Y, H:i') }}</span>
                @if($ticket->admin_reply)
                <div class="flex items-center gap-1.5 text-emerald-600">
                    <i class="fas fa-reply text-[10px]"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Dibalas Admin</span>
                </div>
                @endif
            </div>

            @if($ticket->admin_reply)
            <div class="mt-4 p-4 bg-slate-50 rounded-2xl border-l-4 border-indigo-500">
                <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-1">Respon Admin:</p>
                <p class="text-[11px] text-slate-600 font-medium italic">"{{ $ticket->admin_reply }}"</p>
            </div>
            @endif
        </div>
        @empty
        <div class="py-20 flex flex-col items-center justify-center bg-white rounded-[3rem] border border-dashed border-slate-200 opacity-50">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-headset text-2xl text-slate-300"></i>
            </div>
            <h5 class="text-xs font-black text-slate-400 uppercase tracking-widest">Belum ada tiket</h5>
            <p class="text-[10px] font-medium text-slate-400 mt-1">Ada kendala? Buat laporan sekarang.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $tickets->links() }}
    </div>
</div>
@endsection
