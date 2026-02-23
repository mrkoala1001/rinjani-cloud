@extends('layouts.app')

@section('title', 'Voucher Profiles')
@section('header_title', 'User Profiles')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ 
    addModalOpen: false, 
    editModalOpen: false, 
    viewModalOpen: false,
    currentProfile: {},
    editData: {}
}">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-800">User Profiles (Paket Voucher)</h2>
        <div class="flex space-x-2">
            <a href="{{ route('voucher.profiles') }}" class="bg-teal-500 hover:bg-teal-600 text-white font-bold py-2 px-4 rounded shadow transition text-sm">
                <i class="fas fa-sync mr-2"></i>Sync Profiles
            </a>
            <button @click="addModalOpen = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition text-sm">
                <i class="fas fa-plus mr-2"></i>Tambah Paket
            </button>
        </div>
    </div>
    
    <div class="bg-white shadow-md rounded-lg overflow-hidden border-t-4 border-purple-600">
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Name</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Shared Users</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Rate Limit</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Validity</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Price</th>
                        <th class="px-4 py-3 text-left font-bold text-gray-700 uppercase">Selling Price</th>
                        <th class="px-4 py-3 text-center font-bold text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($profiles as $prof)
                    @php
                        $meta = $prof['local_metadata'] ?? null;
                        $validity = $meta ? $meta->validity : '-';
                        $price = $meta ? $meta->price : 0;
                        $sell_price = $meta ? $meta->selling_price : 0;
                        $profileData = json_encode([
                            'id' => $prof['.id'],
                            'name' => $prof['name'],
                            'shared_users' => $prof['shared-users'] ?? '1',
                            'rate_limit' => $prof['rate-limit'] ?? '',
                            'validity' => $validity,
                            'price' => $price,
                            'sell_price' => $sell_price
                        ]);
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-bold text-gray-800">{{ $prof['name'] }}</td>
                        <td class="px-4 py-3 font-mono text-gray-600">{{ $prof['shared-users'] ?? '1' }}</td>
                        <td class="px-4 py-3 font-mono text-gray-600">{{ $prof['rate-limit'] ?? '-' }}</td>
                        <td class="px-4 py-3 text-orange-600 font-bold">{{ $validity }}</td>
                        <td class="px-4 py-3 text-green-600 font-bold">Rp {{ number_format($price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-blue-600 font-bold">Rp {{ number_format($sell_price, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <!-- View Button -->
                                <button @click="currentProfile = {{ $profileData }}; viewModalOpen = true" 
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs font-bold transition inline-flex items-center"
                                        title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                <!-- Edit Button -->
                                <button @click="editData = {{ $profileData }}; editModalOpen = true" 
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded text-xs font-bold transition inline-flex items-center"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <!-- Delete Button -->
                                <a href="{{ route('voucher.deleteProfile', ['id' => $prof['.id'], 'name' => $prof['name']]) }}" 
                                   onclick="return confirm('Hapus profil {{ $prof['name'] }}?')"
                                   class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs font-bold transition inline-flex items-center"
                                   title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400 italic">Tidak ada profil ditemukan via API.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Profile Modal -->
    <div x-show="addModalOpen" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative p-5 border w-full max-w-2xl shadow-2xl rounded-lg bg-white" @click.away="addModalOpen = false">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-800">Tambah Profil Baru</h3>
                <button @click="addModalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Help Notice -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-3 mb-4">
                <div class="flex">
                    <i class="fas fa-info-circle text-blue-500 mr-2 mt-1"></i>
                    <div class="text-xs text-blue-700">
                        <strong>Petunjuk Pengisian:</strong>
                        <ul class="list-disc ml-4 mt-1 space-y-1">
                            <li><strong>Nama Profil:</strong> Nama paket voucher (contoh: 1Hari-2GB, 12Jam-Unlimited)</li>
                            <li><strong>Shared Users:</strong> Jumlah device yang bisa login bersamaan (biasanya 1)</li>
                            <li><strong>Rate Limit:</strong> Kecepatan upload/download (contoh: 2M/2M = 2Mbps, 512k/512k = 512Kbps)</li>
                            <li><strong>Validity:</strong> Masa aktif (contoh: 1d = 1 hari, 12h = 12 jam, 30m = 30 menit)</li>
                            <li><strong>Price:</strong> Harga modal/beli dari provider</li>
                            <li><strong>Selling:</strong> Harga jual ke customer (harus lebih tinggi dari price)</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('voucher.storeProfile') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">
                            Nama Profil <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required 
                               placeholder="contoh: 1Hari-2GB"
                               class="w-full border rounded px-3 py-2 text-sm focus:ring-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">
                            Shared Users <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="shared_users" value="1" 
                               class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                        <p class="text-xs text-gray-500 mt-1">Default: 1 device</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Rate Limit (upload/download)</label>
                    <input type="text" name="rate_limit" 
                           placeholder="contoh: 2M/2M atau 512k/512k" 
                           class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">Format: upload/download (M = Mbps, k = Kbps)</p>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Validity</label>
                        <input type="text" name="validity" 
                               placeholder="contoh: 1d" 
                               class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                        <p class="text-xs text-gray-500 mt-1">d=hari, h=jam, m=menit</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Price (Modal)</label>
                        <input type="number" name="price" 
                               placeholder="5000" 
                               class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                        <p class="text-xs text-gray-500 mt-1">Harga beli</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Selling (Jual)</label>
                        <input type="number" name="sell_price" 
                               placeholder="6000" 
                               class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                        <p class="text-xs text-gray-500 mt-1">Harga jual</p>
                    </div>
                </div>

                <div class="flex justify-end pt-4 border-t mt-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded transition shadow-md">
                        <i class="fas fa-save mr-2"></i>Simpan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Profile Modal -->
    <div x-show="viewModalOpen" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative p-5 border w-full max-w-md shadow-2xl rounded-lg bg-white" @click.away="viewModalOpen = false">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-800">Detail Profil</h3>
                <button @click="viewModalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-sm font-bold text-gray-600">Nama Profil:</span>
                    <span class="text-sm text-gray-800" x-text="currentProfile.name"></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-sm font-bold text-gray-600">Shared Users:</span>
                    <span class="text-sm text-gray-800" x-text="currentProfile.shared_users"></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-sm font-bold text-gray-600">Rate Limit:</span>
                    <span class="text-sm text-gray-800 font-mono" x-text="currentProfile.rate_limit || '-'"></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-sm font-bold text-gray-600">Validity:</span>
                    <span class="text-sm text-orange-600 font-bold" x-text="currentProfile.validity"></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-sm font-bold text-gray-600">Price (Modal):</span>
                    <span class="text-sm text-green-600 font-bold" x-text="'Rp ' + parseInt(currentProfile.price || 0).toLocaleString('id-ID')"></span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-sm font-bold text-gray-600">Selling Price:</span>
                    <span class="text-sm text-blue-600 font-bold" x-text="'Rp ' + parseInt(currentProfile.sell_price || 0).toLocaleString('id-ID')"></span>
                </div>
            </div>
            
            <div class="flex justify-end pt-4 border-t mt-4">
                <button @click="viewModalOpen = false" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div x-show="editModalOpen" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative p-5 border w-full max-w-2xl shadow-2xl rounded-lg bg-white" @click.away="editModalOpen = false">
            <div class="flex items-center justify-between border-b pb-3 mb-4">
                <h3 class="text-xl font-bold text-gray-800">Edit Profil</h3>
                <button @click="editModalOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Help Notice -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2 mt-1"></i>
                    <div class="text-xs text-yellow-700">
                        <strong>Perhatian:</strong> Perubahan akan mempengaruhi voucher yang akan di-generate selanjutnya.
                    </div>
                </div>
            </div>
            
            <form action="{{ route('voucher.updateProfile') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="id" x-model="editData.id">
                <input type="hidden" name="name" x-model="editData.name">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Profil</label>
                        <input type="text" x-model="editData.name" disabled
                               class="w-full border rounded px-3 py-2 text-sm bg-gray-100 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Nama tidak bisa diubah</p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Shared Users</label>
                        <input type="number" name="shared_users" x-model="editData.shared_users"
                               class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Rate Limit</label>
                    <input type="text" name="rate_limit" x-model="editData.rate_limit"
                           placeholder="contoh: 2M/2M"
                           class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Validity</label>
                        <input type="text" name="validity" x-model="editData.validity"
                               placeholder="1d"
                               class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Price</label>
                        <input type="number" name="price" x-model="editData.price"
                               class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Selling</label>
                        <input type="number" name="sell_price" x-model="editData.sell_price"
                               class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t mt-4">
                    <button type="button" @click="editModalOpen = false" 
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded transition shadow-md">
                        <i class="fas fa-save mr-2"></i>Update Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
