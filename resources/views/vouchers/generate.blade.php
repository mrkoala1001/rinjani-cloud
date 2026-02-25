@extends('layouts.app')

@section('title', 'Generate Voucher')
@section('header_title', 'Generate Voucher')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-center">
        <!-- Generator Form -->
        <div class="w-full">
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border-t-4 border-blue-600">
                <div class="bg-gray-50 px-4 py-3 border-b flex items-center justify-between">
                    <h3 class="font-bold text-gray-700"><i class="fas fa-magic mr-2"></i>Voucher Generator</h3>
                </div>
                <form action="{{ route('voucher.store') }}" method="POST" class="p-4 space-y-4">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <div class="mt-2">
                                @foreach ($errors->all() as $error)
                                    <div class="text-sm whitespace-pre-line">{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif



                    <!-- Quantity -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Quantity</label>
                        <input type="number" name="qty" required min="1" max="1000" value="{{ old('qty', 10) }}" 
                               class="w-full border rounded px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    
                    <!-- Server -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Server</label>
                        <select name="server" class="w-full border rounded px-3 py-2 text-sm focus:outline-none bg-white">
                            <option value="all">all</option>
                            @foreach($serverProfiles as $srv)
                                <option value="{{ $srv['name'] ?? '' }}">{{ $srv['name'] ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- User Mode -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">User Mode</label>
                        <select name="user_mode" class="w-full border rounded px-3 py-2 text-sm focus:outline-none bg-white">
                            <option value="up" {{ old('user_mode') == 'up' ? 'selected' : '' }}>Username = Password</option>
                            <option value="u+p" {{ old('user_mode') == 'u+p' ? 'selected' : '' }}>Username & Password</option>
                        </select>
                    </div>

                    <!-- Name Length & Prefix -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Name Length</label>
                            <input type="number" name="user_length" value="{{ old('user_length', 6) }}" min="3" max="12"
                                   class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Prefix</label>
                            <input type="text" name="prefix" value="{{ old('prefix') }}" placeholder="VC-" 
                                   class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                        </div>
                    </div>

                    <!-- Character Set -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Character Set</label>
                        <select name="char_set" class="w-full border rounded px-3 py-2 text-sm focus:outline-none bg-white">
                            <option value="mixed">Mixed (a,b,1,2)</option>
                            <option value="uppercase">Uppercase (A,B,1,2)</option>
                            <option value="lowercase">Lowercase (a,b,1,2)</option>
                            <option value="numbers">Numbers Only (1,2)</option>
                        </select>
                    </div>

                    <!-- Profile -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Profile</label>
                        <select name="profile" required class="w-full border rounded px-3 py-2 text-sm focus:outline-none font-bold text-yellow-600 bg-white">
                            @foreach ($profiles as $prof)
                                <option value="{{ $prof['name'] }}" {{ old('profile') == $prof['name'] ? 'selected' : '' }}>{{ $prof['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Time Limit & Data Limit -->
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Time Limit</label>
                            <input type="text" name="timelimit" value="{{ old('timelimit') }}" placeholder="e.g. 1h" 
                                   class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Data Limit</label>
                            <input type="text" name="datalimit" value="{{ old('datalimit') }}" placeholder="e.g. 500M" 
                                   class="w-full border rounded px-3 py-2 text-sm focus:outline-none">
                        </div>
                    </div>

                     <!-- Reseller -->
                     <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Seller / Reseller</label>
                        <select name="reseller_id" class="w-full border rounded px-3 py-2 text-sm focus:outline-none bg-white">
                            <option value="0">Default (Admin)</option>
                            @foreach ($resellers as $res)
                                <option value="{{ $res->id }}" {{ old('reseller_id') == $res->id ? 'selected' : '' }}>{{ $res->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Template -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Template</label>
                        <select name="template_id" class="w-full border rounded px-3 py-2 text-sm focus:outline-none font-bold text-purple-600 bg-white">
                            <option value="0">-- Default View --</option>
                            @foreach ($templates as $tpl)
                                <option value="{{ $tpl->id }}" {{ old('template_id') == $tpl->id ? 'selected' : '' }}>{{ $tpl->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" id="btnSubmit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded transition shadow-md mt-4 relative">
                        <span id="btnText"><i class="fas fa-play mr-2"></i>Generate</span>
                        <span id="btnLoading" class="hidden">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Sedang Memproses...
                        </span>
                    </button>
                </form>
            </div>
            
            <!-- Loading Indicator Overlay -->
            <div id="loadingOverlay" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded-lg shadow-xl text-center max-w-sm w-full mx-4">
                    <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-600 mx-auto mb-4"></div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Memproses Voucher</h3>
                    <p class="text-gray-600">Mohon tunggu sebentar, sistem sedang berkomunikasi dengan MikroTik...</p>
                    <p class="text-xs text-gray-400 mt-4 italic">Jangan tutup halaman ini sampai selesai.</p>
                </div>
            </div>
            
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function() {
        // Show loading indicator
        document.getElementById('loadingOverlay').classList.remove('hidden');
        
        // Disable button and show spinner
        const btn = document.getElementById('btnSubmit');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');
        
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        btnText.classList.add('hidden');
        btnLoading.classList.remove('hidden');
    });
</script>
@endsection

