@extends('layouts.app')

@section('title', 'Settings')
@section('header_title', 'Pengaturan Sistem')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative whitespace-pre-line" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative whitespace-pre-line" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Tabs -->
    <div x-data="{ activeTab: 'mikrotik' }" class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'mikrotik'" 
                        :class="activeTab === 'mikrotik' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <i class="fas fa-network-wired mr-2"></i> Konfigurasi MikroTik
                </button>
                <button @click="activeTab = 'application'" 
                        :class="activeTab === 'application' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <i class="fas fa-cog mr-2"></i> Pengaturan Aplikasi
                </button>
            </nav>
        </div>

        <!-- MikroTik Configuration Tab -->
        <div x-show="activeTab === 'mikrotik'" class="mt-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-server mr-2 text-blue-600"></i> Konfigurasi Koneksi MikroTik Router
                </h3>
                <p class="text-sm text-gray-600 mb-6">Atur koneksi ke MikroTik Router melalui API. Pastikan API service sudah aktif di router Anda.</p>

                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="host">
                                <i class="fas fa-globe mr-1"></i> MikroTik IP / Host
                            </label>
                            <input type="text" name="host" id="host" required
                                   value="{{ $config->host ?? '' }}"
                                   {{ auth()->user()->role === 'owner' ? 'readonly disabled' : '' }}
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="192.168.88.1">
                            <p class="text-xs text-gray-500 mt-1">IP Address atau hostname router MikroTik</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="port">
                                <i class="fas fa-plug mr-1"></i> API Port
                            </label>
                            <input type="number" name="port" id="port"
                                   value="{{ $config->port ?? '8728' }}"
                                   {{ auth()->user()->role === 'owner' ? 'readonly disabled' : '' }}
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="8728">
                            <p class="text-xs text-gray-500 mt-1">Default: 8728 (API), 8729 (API-SSL)</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="user">
                                <i class="fas fa-user mr-1"></i> Username
                            </label>
                            <input type="text" name="user" id="user" required
                                   value="{{ $config->user ?? '' }}"
                                   {{ auth()->user()->role === 'owner' ? 'readonly disabled' : '' }}
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="admin">
                            <p class="text-xs text-gray-500 mt-1">Username dengan akses API</p>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="pass">
                                <i class="fas fa-lock mr-1"></i> Password
                            </label>
                            <input type="password" name="pass" id="pass" autocomplete="current-password"
                                   value="{{ $config->pass ?? '' }}"
                                   {{ auth()->user()->role === 'owner' ? 'readonly disabled' : '' }}
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="********">
                            <p class="text-xs text-gray-500 mt-1">Password untuk user tersebut</p>
                        </div>

                        <div class="md:col-span-2 bg-blue-50 border border-blue-200 rounded-lg p-4 mt-2">
                             <div class="flex items-center">
                                <div class="flex items-center h-5">
                                    <input id="use_radius" name="use_radius" type="checkbox" value="1" {{ ($config->use_radius ?? false) ? 'checked' : '' }}
                                           {{ auth()->user()->role === 'owner' ? 'disabled' : '' }}
                                           class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300 rounded cursor-pointer">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="use_radius" class="font-bold text-blue-800 cursor-pointer flex items-center">
                                        <i class="fas fa-broadcast-tower mr-2"></i> Aktifkan Mode RADIUS (Optimasi MikroTik)
                                    </label>
                                    <p class="text-blue-600 text-xs mt-1">
                                        Rekomendasi jika user sudah di atas 500. MikroTik akan mengambil data dari server secara realtime tanpa menyimpan user di router. 
                                        <strong>Pastikan server ini sudah terinstall FreeRADIUS.</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(auth()->user()->role !== 'owner')
                    <div class="flex items-center justify-between mt-8 pt-6 border-t">
                        <button type="submit" formaction="{{ route('settings.test') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition flex items-center">
                            <i class="fas fa-plug mr-2"></i> Test Koneksi
                        </button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:shadow-outline transition flex items-center">
                            <i class="fas fa-save mr-2"></i> Simpan Konfigurasi
                        </button>
                    </div>
                    @else
                    <div class="mt-8 pt-6 border-t text-red-500 font-bold">
                        <i class="fas fa-info-circle mr-1"></i> Konfigurasi koneksi MikroTik Anda dikelola oleh ISP.
                    </div>
                    @endif
                </form>

                <!-- Connection Tips -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded p-4">
                    <h4 class="font-bold text-blue-800 mb-2"><i class="fas fa-info-circle mr-2"></i> Tips Koneksi</h4>
                    <ul class="text-sm text-blue-700 space-y-1 list-disc list-inside">
                        <li>Pastikan API service aktif di MikroTik: <code class="bg-blue-100 px-1 rounded">/ip service print</code></li>
                        <li>Cek firewall tidak memblokir port API (8728)</li>
                        <li>Gunakan user dengan group <code class="bg-blue-100 px-1 rounded">full</code> atau <code class="bg-blue-100 px-1 rounded">api</code></li>
                        <li>Test koneksi terlebih dahulu sebelum menyimpan</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Application Settings Tab -->
        <div x-show="activeTab === 'application'" class="mt-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-sliders-h mr-2 text-blue-600"></i> Pengaturan Aplikasi
                </h3>
                
                <div class="space-y-6">
                    <!-- Application Info -->
                    <div class="bg-gray-50 rounded p-4">
                        <h4 class="font-bold text-gray-700 mb-3">Informasi Aplikasi</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Nama Aplikasi:</span>
                                <span class="font-bold text-gray-800 ml-2">HOTPOT BY DEPOOTCOM</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Versi:</span>
                                <span class="font-bold text-gray-800 ml-2">1.0.0</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Laravel Version:</span>
                                <span class="font-bold text-gray-800 ml-2">{{ app()->version() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">PHP Version:</span>
                                <span class="font-bold text-gray-800 ml-2">{{ PHP_VERSION }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="bg-gray-50 rounded p-4">
                        <h4 class="font-bold text-gray-700 mb-3">Status Sistem</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Database Connection:</span>
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">
                                    <i class="fas fa-check-circle mr-1"></i> Connected
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Storage:</span>
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">
                                    <i class="fas fa-check-circle mr-1"></i> Writable
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Cache:</span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">
                                    <i class="fas fa-info-circle mr-1"></i> File Driver
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-gray-50 rounded p-4">
                        <h4 class="font-bold text-gray-700 mb-3">Quick Actions</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <button onclick="alert('Cache cleared! (Feature coming soon)')" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded transition text-sm">
                                <i class="fas fa-broom mr-2"></i> Clear Cache
                            </button>
                            <button onclick="alert('Logs viewed! (Feature coming soon)')" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition text-sm">
                                <i class="fas fa-file-alt mr-2"></i> View Logs
                            </button>
                        </div>
                    </div>

                    <!-- Danger Zone -->
                    @if(auth()->user()->role !== 'owner')
                    <div class="bg-red-50 rounded p-4 border border-red-100">
                        <h4 class="font-bold text-red-700 mb-1 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Zona Berbahaya
                        </h4>
                        <p class="text-xs text-red-600 mb-4 font-medium">Hapus seluruh konfigurasi MikroTik dan putuskan hubungan sistem dengan router Anda.</p>
                        
                        <div class="flex flex-col sm:flex-row gap-4">
                            <form action="{{ route('settings.disconnect') }}" method="POST" onsubmit="return confirm('⚠️ PERINGATAN! Tindakan ini akan menghapus seluruh data koneksi MikroTik Anda dari sistem. Aplikasi tidak akan bisa berfungsi sampai Anda memasukkan konfigurasi baru.\n\nApakah Anda yakin ingin memutuskan jaringan?')">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-6 rounded transition text-sm shadow-sm">
                                    <i class="fas fa-unlink mr-2"></i> Putuskan Jaringan
                                </button>
                            </form>

                            <form action="{{ route('settings.wipe') }}" method="POST" onsubmit="return confirm('🚨 PERINGATAN KRITIKAL! Tindakan ini akan MENGHAPUS SELURUH data billing, riwayat transaksi, daftar pelanggan, dan konfigurasi MikroTik Anda secara PERMANEN.\n\nData yang sudah dihapus TIDAK DAPAT DIKEMBALIKAN.\n\nApakah Anda benar-benar yakin ingin menghapus SEMUA data Anda?')">
                                @csrf
                                <button type="submit" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded transition text-sm shadow-sm">
                                    <i class="fas fa-trash-alt mr-2"></i> Bersihkan Seluruh Data
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
