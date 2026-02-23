@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                    <span class="text-4xl text-blue-600">🎫</span> 
                    <span>Buat Tiket Baru</span>
                </h1>
                <p class="text-gray-600 mt-2">Laporkan masalah teknis atau request fitur. Tim kami akan segera membalas.</p>
            </div>
            <a href="{{ auth()->user()->role === 'isp' ? route('hotsupport.tickets.index') : (auth()->user()->role === 'builder' ? route('builder.reports') : route('report.index')) }}" class="text-blue-600 font-bold hover:underline">
                Lihat Tiket Saya &rarr;
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform hover:scale-[1.01] transition-transform duration-300">
            <!-- Header: Compose Message Style -->
            <div class="bg-gray-50 px-8 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">New Ticket</div>
                <div class="flex gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-400"></span>
                    <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                    <span class="w-3 h-3 rounded-full bg-green-400"></span>
                </div>
            </div>

            <form action="{{ auth()->user()->role === 'isp' ? route('hotsupport.tickets.store') : route('report.send') }}" method="POST" class="p-0">
                <!-- Using hotsupport.tickets.store as alias for report.send, logic updated in Controller to redirect to ticket index -->
                @csrf
                
                <div class="px-8 py-5 border-b border-gray-50">
                    <div class="flex items-center">
                        <span class="text-gray-400 w-20 font-medium">Kepada:</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-bold flex items-center gap-2">
                             Mr. Koala (Support Team) 
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l5-5z" clip-rule="evenodd" />
                             </svg>
                        </span>
                    </div>
                </div>

                <div class="px-8 py-5 border-b border-gray-50">
                    <div class="flex items-center">
                        <label for="subject" class="text-gray-400 w-20 font-medium cursor-pointer">Subjek:</label>
                        <input type="text" name="subject" id="subject" required value="{{ old('subject') }}" placeholder="Judul masalah..."
                               class="flex-grow border-none focus:ring-0 text-gray-800 placeholder-gray-300 font-medium italic text-lg">
                    </div>
                    @error('subject') <p class="text-red-500 text-xs mt-1 ml-20">{{ $message }}</p> @enderror
                </div>

                <div class="px-8 py-6">
                    <textarea name="message" required rows="10" placeholder="Deskripsikan masalah Anda secara detail..."
                              class="w-full border-none focus:ring-0 text-gray-700 text-lg leading-relaxed resize-none font-sans min-h-[300px]">{{ old('message') }}</textarea>
                    @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Footer / Send Button -->
                <div class="px-8 py-6 bg-gray-50 flex items-center justify-between border-t border-gray-100">
                    <div class="flex items-center gap-4 text-gray-400">
                         <i class="fas fa-paperclip cursor-pointer hover:text-gray-600"></i>
                         <i class="fas fa-image cursor-pointer hover:text-gray-600"></i>
                    </div>
                    <button type="submit" class="inline-flex items-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl transition-all active:scale-95 gap-3">
                        Kirim Tiket
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="mt-8 text-center text-gray-400 text-sm flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Pesan Anda terenkripsi dan hanya dapat dibaca oleh tim Builder.
        </div>
    </div>
</div>
@endsection
<style>
    body { background-color: #f8fafc; }
    textarea { font-family: 'Georgia', 'Times New Roman', serif; }
</style>
