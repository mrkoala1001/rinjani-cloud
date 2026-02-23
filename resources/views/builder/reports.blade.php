@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-white flex items-center gap-3">
            <span class="text-yellow-500">📥</span> Kotak Masuk Laporan (Reports)
        </h1>
        <div class="flex gap-2">
            <span class="px-3 py-1 bg-blue-900/40 text-blue-400 rounded-full text-sm border border-blue-800">
                Total: {{ $reports->total() }}
            </span>
        </div>
    </div>

    <div class="bg-gray-800 rounded-xl shadow-2xl border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-900/50 border-b border-gray-700">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Pengirim</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Subjek</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($reports as $report)
                        <tr class="hover:bg-gray-700/50 transition-colors cursor-pointer group" onclick="window.location='{{ route('builder.reports.show', $report->id) }}'">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($report->status === 'unread')
                                    <span class="flex h-3 w-3 rounded-full bg-blue-500"></span>
                                @else
                                    <span class="flex h-3 w-3 rounded-full bg-gray-600"></span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-200">
                                    {{ $report->sender->fullname ?? ($report->sender->name ?? $report->sender_name) }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $report->sender->email ?? ($report->sender->username ?? '-') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-200 line-clamp-1 {{ $report->status === 'unread' ? 'font-bold text-white' : '' }}">
                                    {{ $report->subject }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase text-center {{ $report->sender_type === 'App\Models\P3potUser' ? 'bg-rose-900/40 text-rose-400 border border-rose-800' : 'bg-indigo-900/40 text-indigo-400 border border-indigo-800' }}">
                                        {{ $report->sender_type === 'App\Models\P3potUser' ? 'P3POT' : 'HOTSPOT' }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-medium uppercase text-center bg-gray-900/40 text-gray-400 border border-gray-800">
                                        {{ $report->sender_role }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                {{ $report->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm" onclick="event.stopPropagation()">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <!-- Working -->
                                    <form action="{{ route('builder.reports.status', $report->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="status" value="processed">
                                        <button type="submit" title="Mark as Working" class="text-blue-500 hover:text-blue-300 transition-colors transform hover:scale-110">
                                            <i class="fas fa-tools"></i>
                                        </button>
                                    </form>

                                    <!-- Finish -->
                                    <form action="{{ route('builder.reports.status', $report->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="status" value="resolved">
                                        <button type="submit" title="Mark as Finished" class="text-green-500 hover:text-green-300 transition-colors transform hover:scale-110">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>

                                    <!-- Read -->
                                    <form action="{{ route('builder.reports.read', $report->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit" title="Mark as Read" class="text-gray-400 hover:text-white transition-colors transform hover:scale-110">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </form>

                                    <!-- Detail -->
                                    <a href="{{ route('builder.reports.show', $report->id) }}" title="View Details" class="text-gray-400 hover:text-white transition-colors transform hover:scale-110">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('builder.reports.delete', $report->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this report?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Delete" class="text-red-500 hover:text-red-400 transition-colors transform hover:scale-110">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p>Belum ada laporan masuk.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reports->hasPages())
            <div class="px-6 py-4 bg-gray-900/50 border-t border-gray-700">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
