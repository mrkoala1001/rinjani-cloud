@extends('layouts.app')

@section('title', 'Manage Profile - ' . $user->name)
@section('header_title', 'Manage Profile: ' . $user->name)

@section('content')
<div x-data="{ 
    editMode: false, 
    importModalOpen: false,
    formUrl: '{{ route('hotsupport.mitra-reseller.profiles.store', $user->id) }}',
    defaultUrl: '{{ route('hotsupport.mitra-reseller.profiles.store', $user->id) }}',
    updateUrl: '{{ route('hotsupport.mitra-reseller.profiles.update', $user->id) }}',
    p_name: '', p_shared: '1', p_rate: '', p_val: '', p_price: '', p_sell: '', p_id: '',
    editProfile(prof) {
        this.editMode = true;
        this.formUrl = this.updateUrl;
        this.p_id = prof['.id'];
        this.p_name = prof['name'];
        this.p_shared = prof['shared-users'] || '1';
        this.p_rate = prof['rate-limit'] || '';
        if (prof['local_metadata']) {
            this.p_val = prof['local_metadata']['validity'] || '';
            this.p_price = prof['local_metadata']['price'] || '';
            this.p_sell = prof['local_metadata']['selling_price'] || '';
        } else {
            this.p_val = prof['session-timeout'] || ''; 
            this.p_price = ''; this.p_sell = '';
        }
        window.scrollTo({top: 0, behavior: 'smooth'});
    },
    cancelEdit() {
        this.editMode = false;
        this.formUrl = this.defaultUrl;
        this.p_id = ''; this.p_name = ''; this.p_shared = '1'; this.p_rate = ''; this.p_val = ''; this.p_price = ''; this.p_sell = '';
    }
}">
    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('hotsupport.mitra-reseller.profiles') }}" class="text-blue-500 hover:underline font-bold"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Mitra</a>
        <div class="flex gap-2">
            <a href="{{ route('hotsupport.mitra-reseller.profiles.template') }}" class="px-4 py-2 bg-gray-500 hover:bg-slate-700 text-white text-xs font-black rounded-lg transition-all flex items-center gap-2">
                <i class="fas fa-file-csv"></i> DOWNLOAD TEMPLATE
            </a>
            <button @click="importModalOpen = true" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-black rounded-lg transition-all flex items-center gap-2">
                <i class="fas fa-file-import"></i> IMPORT CSV
            </button>
        </div>
    </div>

    <div class="relative">
        <!-- Import Modal -->
        <div x-show="importModalOpen" x-cloak class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-md overflow-hidden" @click.away="importModalOpen = false">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                    <h3 class="font-black text-slate-800 uppercase tracking-tight">Import Profiles via CSV</h3>
                    <button @click="importModalOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('hotsupport.mitra-reseller.profiles.import', $user->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100">
                        <p class="text-[10px] font-bold text-indigo-700 leading-relaxed uppercase tracking-widest">
                            Format Kolom CSV: <br>
                            <span class="text-slate-500">name, shared_users, rate_limit, validity, price, sell_price</span>
                        </p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Pilih File CSV</label>
                        <input type="file" name="file" accept=".csv" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 outline-none" required>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="w-full py-4 bg-purple-600 hover:bg-purple-700 text-white font-black rounded-xl shadow-lg shadow-purple-600/30 transition-all active:scale-95 flex items-center justify-center gap-2">
                            <i class="fas fa-upload"></i> MULAI IMPORT SEKARANG
                        </button>
                    </div>
                </form>
            </div>
        </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sticky top-8 transition-colors" :class="editMode ? 'bg-indigo-50/50 border-indigo-200' : ''">
                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                    <i class="fas" :class="editMode ? 'fa-edit text-indigo-500' : 'fa-plus-circle text-purple-500'"></i> 
                    <span x-text="editMode ? 'Update Profile' : 'Buat Profile Baru'"></span>
                </h3>
                
                <form :action="formUrl" method="POST">
                    @csrf
                    <input type="hidden" name="mk_id" x-model="p_id">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Profile</label>
                            <input type="text" name="name" x-model="p_name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-purple-500/20 outline-none" required>
                        </div>
                         <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Shared Users</label>
                            <input type="number" name="shared_users" x-model="p_shared" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-purple-500/20 outline-none" required>
                        </div>
                         <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Rate Limit (rx/tx)</label>
                            <input type="text" name="rate_limit" x-model="p_rate" placeholder="1M/1M" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-purple-500/20 outline-none">
                        </div>
                         <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Validity</label>
                            <input type="text" name="validity" x-model="p_val" placeholder="1d" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-purple-500/20 outline-none">
                        </div>
                         <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Harga (Modal)</label>
                            <input type="number" name="price" x-model="p_price" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-purple-500/20 outline-none">
                        </div>
                         <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Harga Jual</label>
                            <input type="number" name="sell_price" x-model="p_sell" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-purple-500/20 outline-none">
                        </div>
                        
                        <div class="flex flex-col gap-2 mt-2">
                            <button type="submit" class="w-full py-4 text-white font-black rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2"
                                    :class="editMode ? 'bg-indigo-600 hover:bg-indigo-600 shadow-indigo-600/30' : 'bg-purple-600 hover:bg-purple-600 shadow-purple-500/30'">
                                <i class="fas fa-save"></i> <span x-text="editMode ? 'SIMPAN PERUBAHAN' : 'SIMPAN PROFILE'"></span>
                            </button>
                            <button type="button" x-show="editMode" @click="cancelEdit()" class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-500 font-black rounded-xl transition-all">
                                BATAL EDIT
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400">Nama</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400">Shared</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400">Rate Limit</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400">Validity</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400">Harga</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($profiles as $prof)
                            <tr class="border-t border-slate-100 hover:bg-slate-50 transition" :class="p_id === '{{ $prof['.id'] }}' ? 'bg-indigo-50' : ''">
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $prof['name'] }}</td>
                                <td class="px-4 py-3 text-slate-600 text-sm font-bold">{{ $prof['shared-users'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-600 text-sm font-bold">{{ $prof['rate-limit'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-600 text-sm font-bold">{{ $prof['local_metadata']['validity'] ?? $prof['session-timeout'] ?? '1d' }}</td>
                                <td class="px-4 py-3 text-slate-600 text-sm font-bold text-green-600">
                                    Rp {{ number_format($prof['local_metadata']['selling_price'] ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button type="button" @click="editProfile({{ json_encode($prof) }})" class="text-xs font-black text-indigo-500 hover:underline">Edit / Lihat</button>
                                        <a href="{{ route('hotsupport.mitra-reseller.profiles.delete', ['id' => $user->id, 'mk_id' => $prof['.id'], 'name' => $prof['name']]) }}" class="text-xs font-black text-red-500 hover:underline" onclick="return confirm('Apakah Anda yakin ingin menghapus profile ini?')">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @if(empty($profiles))
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400 font-bold">Mitra belum memiliki profile hotspot.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
