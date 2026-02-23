@extends('layouts.app')

@section('title', 'Builder Dashboard - Mikhmon')
@section('header_title', 'KOALA BUILDER')

@section('content')
<!-- Server Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-md border border-slate-200 hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Server Load</h3>
            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                <i class="fas fa-microchip"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-slate-800">{{ $serverLoad }}</div>
    </div>
    
    <div class="bg-white rounded-2xl p-6 shadow-md border border-slate-200 hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Uptime</h3>
            <div class="p-2 bg-green-50 text-green-600 rounded-lg">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-slate-800">{{ $uptime }}</div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-md border border-slate-200 hover:shadow-lg transition">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Disk Usage</h3>
            <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                <i class="fas fa-hdd"></i>
            </div>
        </div>
        <div class="flex items-end gap-3">
            <div class="text-3xl font-bold text-slate-800">{{ $diskUsage }}%</div>
            <div class="flex-1 mb-2">
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full shadow-sm" style="width: {{ $diskUsage }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Database Overview -->
<div class="mb-6 flex items-center gap-2">
    <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
    <h2 class="text-lg font-bold text-slate-800">Database Overview</h2>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-2xl shadow-md border-b-4 border-yellow-500 hover:-translate-y-1 transition transform">
        <div class="text-xs font-bold text-slate-500 uppercase mb-1">Total Users</div>
        <div class="text-3xl font-bold text-slate-800">{{ $usersCount }}</div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-md border-b-4 border-blue-500 hover:-translate-y-1 transition transform">
        <div class="text-xs font-bold text-slate-500 uppercase mb-1">ISP Admins</div>
        <div class="text-3xl font-bold text-slate-800">{{ $ispCount }}</div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-md border-b-4 border-green-500 hover:-translate-y-1 transition transform">
        <div class="text-xs font-bold text-slate-500 uppercase mb-1">Owners (Mitra)</div>
        <div class="text-3xl font-bold text-slate-800">{{ $ownerCount }}</div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-md border-b-4 border-rose-500 hover:-translate-y-1 transition transform">
        <div class="text-xs font-bold text-slate-500 uppercase mb-1">Total Vouchers</div>
        <div class="text-3xl font-bold text-slate-800">{{ $billingCount }}</div>
    </div>
</div>

<!-- Bottom Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-md">
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fas fa-toolbox text-blue-500"></i> Quick Tools
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <a href="{{ route('builder.user.create') }}" class="flex flex-col p-5 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-blue-50 hover:border-blue-200 transition group">
                <div class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-blue-600 mb-3 group-hover:scale-110 transition">
                    <i class="fas fa-user-plus text-lg"></i>
                </div>
                <span class="font-bold text-slate-800">Tambah Akun Baru</span>
                <span class="text-xs text-slate-500 mt-1 line-clamp-1">Register new ISP or Builder</span>
            </a>

            <a href="{{ route('builder.reports') }}" class="flex flex-col p-5 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-yellow-50 hover:border-yellow-200 transition group relative">
                <div class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-yellow-600 mb-3 group-hover:scale-110 transition">
                    <i class="fas fa-inbox text-lg"></i>
                </div>
                @php $unread = \App\Models\Report::unread()->count(); @endphp
                @if($unread > 0)
                    <span class="absolute top-4 right-4 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">
                        {{ $unread }} NEW
                    </span>
                @endif
                <span class="font-bold text-slate-800">Reports Inbox</span>
                <span class="text-xs text-slate-500 mt-1 line-clamp-1">Customer feedback & logs</span>
            </a>

            <a href="{{ route('report.form') }}" class="flex flex-col p-5 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-rose-50 hover:border-rose-200 transition group">
                <div class="w-10 h-10 bg-white shadow-sm rounded-xl flex items-center justify-center text-rose-600 mb-3 group-hover:scale-110 transition">
                    <i class="fas fa-flag text-lg"></i>
                </div>
                <span class="font-bold text-slate-800">Report to Master</span>
                <span class="text-xs text-slate-500 mt-1 line-clamp-1">Send note to Mr. Koala</span>
            </a>
        </div>
    </div>

    <!-- Broadcast Management (Moved up slightly or kept in sidebar) -->
    <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-md">
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fas fa-bullhorn text-orange-500"></i> Broadcast Messages
        </h3>
        
        <!-- Create Form -->
        <form action="{{ route('builder.broadcast.store') }}" method="POST" class="mb-8">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Judul Pesan</label>
                    <input type="text" name="title" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. Maintenance Notice" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori (Type)</label>
                    <select name="type" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                        <option value="info">Info (Biru)</option>
                        <option value="warning">Warning (Kuning)</option>
                        <option value="danger">Danger / Maintenance (Merah)</option>
                        <option value="success">Success (Hijau)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Isi Pesan</label>
                    <textarea name="message" rows="2" class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="Pesan untuk semua user..." required></textarea>
                </div>
                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 rounded-xl transition text-sm">
                    Kirim Broadcast
                </button>
            </div>
        </form>
    </div>

    <!-- Content Management -->
    <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-md">
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
            <i class="fas fa-edit text-purple-500"></i> Content Management
        </h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('builder.templates.index') }}" class="flex flex-col items-center justify-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition text-center group">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-purple-600 mb-2 shadow-sm group-hover:scale-110 transition">
                    <i class="fas fa-file-code"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">Templates</span>
            </a>
            <a href="{{ route('builder.documentation.index') }}" class="flex flex-col items-center justify-center p-4 bg-teal-50 rounded-xl hover:bg-teal-100 transition text-center group">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-teal-600 mb-2 shadow-sm group-hover:scale-110 transition">
                    <i class="fas fa-book"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">Documentation</span>
            </a>
            <a href="{{ route('builder.comments.index') }}" class="flex flex-col items-center justify-center p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition text-center group">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-orange-600 mb-2 shadow-sm group-hover:scale-110 transition">
                    <i class="fas fa-comments"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">Comments</span>
            </a>
            <a href="{{ route('builder.faqs.index') }}" class="flex flex-col items-center justify-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition text-center group">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-blue-600 mb-2 shadow-sm group-hover:scale-110 transition">
                    <i class="fas fa-question-circle"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">FAQs</span>
            </a>
        </div>
    </div>
</div>

<!-- AIO DB Section (Full Width or Span 2/3) -->
<div class="mt-8 bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-slate-50/50">
        <div>
            <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-database text-indigo-500 text-2xl"></i> All-In-One Database (AIO DB)
            </h3>
            <p class="text-sm text-slate-500 mt-1">Management pusat untuk seluruh akun (Hotspot & P3POT)</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" id="aioSearch" class="pl-10 pr-4 py-2.5 bg-white border-slate-200 rounded-xl text-sm focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-80 shadow-sm" placeholder="Cari nama, email, atau lokasi...">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 uppercase text-[10px] font-bold text-slate-500 tracking-wider">
                    <th class="px-6 py-4 border-b border-slate-100">User Info</th>
                    <th class="px-6 py-4 border-b border-slate-100">Origin</th>
                    <th class="px-6 py-4 border-b border-slate-100">Role</th>
                    <th class="px-6 py-4 border-b border-slate-100">Status</th>
                    <th class="px-6 py-4 border-b border-slate-100 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="aioTableBody">
                @foreach($managedUsers as $user)
                @php 
                    $origin = $user->origin ?? 'hotpot';
                    $isP3pot = ($origin === 'p3pot');
                @endphp
                <tr class="hover:bg-slate-50/80 transition group border-b border-slate-50 {{ $isP3pot ? 'hover:bg-rose-50/50 border-rose-50/30' : '' }}">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-xs
                                @if($isP3pot)
                                    bg-rose-100 text-rose-600
                                @else
                                    {{ $user->role === 'isp' ? 'bg-indigo-100 text-indigo-600' : ($user->role === 'owner' ? 'bg-emerald-100 text-emerald-600' : 'bg-purple-100 text-purple-600') }}
                                @endif">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-sm">{{ $user->name }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $user->username ?: $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($origin === 'hotpot')
                            <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded">HOTSPOT</span>
                        @elseif($origin === 'p3pot')
                            <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded">P3POT</span>
                        @elseif($origin === 'blog')
                            <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded">BLOG</span>
                        @elseif($origin === 'semua')
                            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded">SEMUA (AIO)</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider
                            @if($isP3pot)
                                bg-rose-50 text-rose-700
                            @else
                                {{ $user->role === 'isp' ? 'bg-indigo-50 text-indigo-700' : ($user->role === 'owner' ? 'bg-emerald-50 text-emerald-700' : 'bg-purple-50 text-purple-700') }}
                            @endif">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->is_active)
                            <span class="flex items-center gap-1.5 text-[10px] font-bold text-emerald-600 uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Active
                            </span>
                        @else
                            <span class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                Suspended
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-1 transition-opacity">
                            <a href="{{ route('builder.user.edit', $user->id) }}" class="p-2 bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white rounded-lg transition" title="Edit User">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form action="{{ route('builder.user.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg transition" title="Delete User">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($managedUsers->isEmpty())
    <div class="p-12 text-center">
        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
            <i class="fas fa-users-slash text-2xl"></i>
        </div>
        <h4 class="font-bold text-slate-800">No managed users found</h4>
        <p class="text-xs text-slate-500 mt-1">Start by creating an ISP account.</p>
    </div>
    @endif
</div>

<script>
document.getElementById('aioSearch').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#aioTableBody tr');
            rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
    <!-- Active Broadcasts History -->
    <div class="bg-white rounded-2xl p-8 border border-slate-200 shadow-md lg:col-span-1">
        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
            <i class="fas fa-history text-slate-400"></i> Recent Broadcasts
        </h4>
        <div class="space-y-3">
            @forelse($broadcasts as $broadcast)
                <div class="p-4 rounded-xl border border-slate-50 bg-slate-50/50 flex justify-between items-start group">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase 
                                {{ $broadcast->type == 'info' ? 'bg-blue-100 text-blue-600' : '' }}
                                {{ $broadcast->type == 'warning' ? 'bg-yellow-100 text-yellow-600' : '' }}
                                {{ $broadcast->type == 'danger' ? 'bg-red-100 text-red-600' : '' }}
                                {{ $broadcast->type == 'success' ? 'bg-green-100 text-green-600' : '' }}">
                                {{ $broadcast->type }}
                            </span>
                            <span class="text-[10px] text-slate-400">{{ $broadcast->created_at->diffForHumans() }}</span>
                        </div>
                        <h5 class="font-bold text-slate-800 text-xs">{{ $broadcast->title }}</h5>
                    </div>
                    <a href="{{ route('builder.broadcast.delete', $broadcast->id) }}" class="p-1.5 text-slate-300 hover:text-red-500 transition opacity-0 group-hover:opacity-100" onclick="return confirm('Hapus pesan ini?')">
                        <i class="fas fa-trash-alt text-[10px]"></i>
                    </a>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 text-xs italic">No active broadcasts.</div>
            @endforelse
        </div>
    </div>
</div>

    <!-- System Info -->
    <div class="bg-slate-900 rounded-2xl p-8 shadow-xl text-white relative overflow-hidden h-full lg:col-span-2">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <i class="fas fa-server text-9xl"></i>
        </div>
        <h3 class="text-lg font-bold mb-6 flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-400"></i> System Identity
        </h3>
        <div class="space-y-4 relative z-10">
            <div class="flex items-center justify-between py-2 border-b border-white/10">
                <span class="text-slate-400 text-sm">Hostname</span>
                <span class="font-mono text-sm">{{ gethostname() }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-white/10">
                <span class="text-slate-400 text-sm">PHP Version</span>
                <span class="font-mono text-sm">{{ phpversion() }}</span>
            </div>
            <div class="flex items-center justify-between py-2 border-b border-white/10">
                <span class="text-slate-400 text-sm">Laravel Engine</span>
                <span class="font-mono text-sm">v{{ app()->version() }}</span>
            </div>
            <div class="flex items-center justify-between py-2">
                <span class="text-slate-400 text-sm">Environment</span>
                <span class="px-2 py-0.5 bg-yellow-500/20 text-yellow-500 rounded text-xs font-bold uppercase">{{ app()->environment() }}</span>
            </div>
        </div>
        
        <div class="mt-10 p-5 bg-white/5 rounded-xl border border-white/10 flex items-center justify-between group cursor-pointer hover:bg-white/10 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center text-blue-400">
                    <i class="fas fa-book"></i>
                </div>
                <div>
                    <div class="font-bold text-sm">Internal Docs</div>
                    <div class="text-[10px] text-slate-500 uppercase tracking-wider">Koala Bertanya</div>
                </div>
            </div>
            <a href="{{ route('koala.index') }}" class="text-slate-500 group-hover:text-white transition">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
@endsection