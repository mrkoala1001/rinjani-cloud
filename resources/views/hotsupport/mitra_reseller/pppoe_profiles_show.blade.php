@extends('layouts.app')

@section('title', 'Manage PPPoE Profile - ' . $user->name)
@section('header_title', 'Manage PPPoE Profile: ' . $user->name)

@section('content')
<div x-data="{ 
    editMode: false, 
    formUrl: '{{ route('hotsupport.mitra-reseller.pppoe-profiles.store', $user->id) }}',
    defaultUrl: '{{ route('hotsupport.mitra-reseller.pppoe-profiles.store', $user->id) }}',
    updateUrl: '{{ route('hotsupport.mitra-reseller.pppoe-profiles.update', $user->id) }}',
    p_name: '', p_local: '', p_remote: '', p_rate: '', p_dns: '', p_price: '', p_sell: '', p_id: '',
    editProfile(prof) {
        this.editMode = true;
        this.formUrl = this.updateUrl;
        this.p_id = prof['.id'];
        this.p_name = prof['name'];
        this.p_local = prof['local-address'] || '';
        this.p_remote = prof['remote-address'] || '';
        this.p_rate = prof['rate-limit'] || '';
        this.p_dns = prof['dns-server'] || '';
        if (prof['local_metadata']) {
            this.p_price = prof['local_metadata']['price'] || '';
            this.p_sell = prof['local_metadata']['selling_price'] || '';
        } else {
            this.p_price = ''; this.p_sell = '';
        }
        window.scrollTo({top: 0, behavior: 'smooth'});
    },
    cancelEdit() {
        this.editMode = false;
        this.formUrl = this.defaultUrl;
        this.p_id = ''; this.p_name = ''; this.p_local = ''; this.p_remote = ''; this.p_rate = ''; this.p_dns = ''; this.p_price = ''; this.p_sell = '';
    }
}">
    <div class="mb-4">
        <a href="{{ route('hotsupport.mitra-reseller.pppoe-profiles') }}" class="text-blue-500 hover:underline font-bold"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Mitra</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 sticky top-8 transition-colors" :class="editMode ? 'bg-blue-50/50 border-blue-200' : ''">
                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                    <i class="fas" :class="editMode ? 'fa-edit text-blue-500' : 'fa-plus-circle text-blue-500'"></i> 
                    <span x-text="editMode ? 'Update Profile' : 'Buat Profile PPPoE Baru'"></span>
                </h3>
                
                <form :action="formUrl" method="POST">
                    @csrf
                    <input type="hidden" name="mk_id" x-model="p_id">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Nama Profile</label>
                            <input type="text" name="name" x-model="p_name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 outline-none" required>
                        </div>
                         <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Local Address</label>
                                <input type="text" name="local_address" x-model="p_local" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Remote Address</label>
                                <input type="text" name="remote_address" x-model="p_remote" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 outline-none">
                            </div>
                        </div>
                         <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Rate Limit (rx/tx)</label>
                            <input type="text" name="rate_limit" x-model="p_rate" placeholder="1M/1M" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 outline-none">
                        </div>
                         <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Harga (Modal)</label>
                            <input type="number" name="price" x-model="p_price" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 outline-none">
                        </div>
                         <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Harga Jual</label>
                            <input type="number" name="sell_price" x-model="p_sell" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 outline-none">
                        </div>
                        
                        <div class="flex flex-col gap-2 mt-2">
                            <button type="submit" class="w-full py-4 text-white font-black rounded-xl shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2"
                                    :class="editMode ? 'bg-blue-700 hover:bg-blue-800 shadow-blue-600/30' : 'bg-blue-600 hover:bg-blue-700 shadow-blue-500/30'">
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
                                <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400">Rate Limit</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400">Harga</th>
                                <th class="px-4 py-4 text-[10px] font-black uppercase text-slate-400 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($profiles as $prof)
                            <tr class="border-t border-slate-100 hover:bg-slate-50 transition" :class="p_id === '{{ $prof['.id'] }}' ? 'bg-blue-50' : ''">
                                <td class="px-4 py-3 font-bold text-slate-800">{{ $prof['name'] }}</td>
                                <td class="px-4 py-3 text-slate-600 text-sm font-bold">{{ $prof['rate-limit'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-600 text-[10px] font-bold">
                                    <div class="flex flex-col">
                                        <span class="text-slate-400">Modal: Rp {{ number_format($prof['local_metadata']['price'] ?? 0, 0, ',', '.') }}</span>
                                        <span class="text-green-600">Jual: Rp {{ number_format($prof['local_metadata']['selling_price'] ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-3">
                                        <button type="button" @click="editProfile({{ json_encode($prof) }})" class="text-xs font-black text-blue-500 hover:underline">Edit / Lihat</button>
                                        <a href="{{ route('hotsupport.mitra-reseller.pppoe-profiles.delete', ['id' => $user->id, 'mk_id' => $prof['.id'], 'name' => $prof['name']]) }}" class="text-xs font-black text-red-500 hover:underline" onclick="return confirm('Apakah Anda yakin ingin menghapus profile ini?')">Hapus</a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            @if(empty($profiles))
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-bold">Mitra belum memiliki profile PPPoE.</td>
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
