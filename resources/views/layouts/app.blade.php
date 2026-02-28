<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOTPOT - @yield('title', 'Dashboard')</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <meta name="theme-color" content="#4f46e5">
    <link rel="manifest" href="/manifest-owner.json">
    
    <!-- Apple Mobile Web App Support -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="HotPot Manager">
    <link rel="apple-touch-icon" href="/img/icon-512.png">

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(reg => console.log('SW Registered', reg))
                    .catch(err => console.log('SW Error', err));
            });
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: #1e293b;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 5px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-100 selection:text-blue-600">

    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
        
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
             class="fixed inset-0 z-30 transition-opacity bg-slate-900 bg-opacity-50 md:hidden"></div>

        <!-- Sidebar -->
        <aside class="bg-slate-900 text-white w-64 space-y-6 py-7 px-2 absolute inset-y-0 left-0 transform md:relative md:translate-x-0 transition duration-200 ease-in-out z-40 flex flex-col h-full sidebar-scroll overflow-y-auto border-r border-slate-800 shadow-xl"
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
            
            <!-- Logo -->
            <div class="text-white flex items-center space-x-3 px-4 mb-2">
                 <div class="p-2 bg-gradient-to-br from-red-500 to-orange-600 rounded-lg shadow-lg">
                    <i class="fas fa-fire text-white text-xl"></i>
                 </div>
                 <span class="text-xl font-bold tracking-wide">HOT POT</span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 space-y-2 mt-6">
                
                @if(auth()->check() && auth()->user()->role === 'isp')
                    <div class="px-4 mb-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center border-b border-slate-800 pb-2">
                        Superduper Admin
                    </div>

                    <!-- ISP Menu -->
                    <a href="{{ route('hotsupport.dashboard') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('hotsupport.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-tachometer-alt mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> 
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="{{ route('hotsupport.report.form') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('hotsupport.report.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-paper-plane mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Report to Mr. Koala</span>
                    </a>
                    
                    <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group text-green-400 hover:text-white">
                        <i class="fab fa-whatsapp mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Curhat Ke Developer</span>
                    </a>

                    <!-- Mitra Reseller Menu -->
                    <div x-data="{ open: {{ request()->routeIs('hotsupport.mitra-reseller.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('hotsupport.mitra-reseller.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                            <div class="flex items-center">
                                <i class="fas fa-users-cog mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Mitra Reseller</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition.origin.top.duration.200ms class="space-y-1 pl-11 pr-2">
                            <a href="{{ route('hotsupport.mitra-reseller.balance') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('hotsupport.mitra-reseller.balance') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Manage Saldo
                            </a>
                            <a href="{{ route('hotsupport.mitra-reseller.balance.history') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('hotsupport.mitra-reseller.balance.history') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Riwayat Saldo
                            </a>
                            <a href="{{ route('hotsupport.mitra-reseller.profiles') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('hotsupport.mitra-reseller.profiles') || request()->routeIs('hotsupport.mitra-reseller.profiles.*') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Manage Profile
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('profile') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('profile') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-user-circle mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">My Profile</span>
                    </a>

                @elseif(auth()->check() && in_array(auth()->user()->role, ['owner', 'mitra', 'mitra-reseller']))
                    <!-- Owner / Mitra Menu -->
                    <a href="{{ route('dashboard') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-tachometer-alt mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>



                    <a href="{{ route('report.form') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('report.form') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-paper-plane mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Report to Mr. Koala</span>
                    </a>

                    <!-- Voucher Section -->
                    <div x-data="{ open: {{ request()->routeIs('voucher.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('voucher.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                            <div class="flex items-center">
                                <i class="fas fa-ticket-alt mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Vouchers</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition.origin.top.duration.200ms class="space-y-1 pl-11 pr-2">
                            <a href="{{ route('voucher.generate') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('voucher.generate') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Generate Voucher
                            </a>
                            @if(auth()->user()->role !== 'mitra-reseller')
                            <a href="{{ route('voucher.list') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('voucher.list') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Daftar Voucher
                            </a>
                            @endif
                            <a href="{{ route('voucher.distribution') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('voucher.distribution') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Distribusi
                            </a>
                            @if(auth()->user()->role !== 'mitra-reseller')
                            <a href="{{ route('voucher.profiles') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('voucher.profiles') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Profil & Sync
                            </a>
                            @endif
                            <a href="{{ route('voucher.online') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('voucher.online') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Online Users
                            </a>
                            <a href="{{ route('voucher.sold') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('voucher.sold') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Terjual
                            </a>
                            <a href="{{ route('voucher.templates') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('voucher.templates') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Templates
                            </a>
                            <a href="{{ route('owner.reseller.balance') }}" class="block py-2 px-3 rounded-md text-sm font-bold border-t border-slate-700/50 mt-1 pt-2 transition duration-200 hover:bg-slate-700 {{ request()->routeIs('owner.reseller.balance') ? 'bg-orange-600 text-white shadow-md' : 'text-orange-400 hover:text-white' }}">
                                <i class="fas fa-wallet mr-1"></i> {{ auth()->user()->role === 'mitra-reseller' ? 'Isi Saldo' : 'Manage Saldo' }}
                            </a>
                            @if(auth()->user()->role === 'mitra-reseller')
                            <a href="{{ route('owner.reseller.history') }}" class="block py-2 px-3 rounded-md text-sm font-bold transition duration-200 hover:bg-slate-700 {{ request()->routeIs('owner.reseller.history') ? 'bg-orange-600 text-white shadow-md' : 'text-orange-400 hover:text-white' }}">
                                <i class="fas fa-file-invoice-dollar mr-1"></i> Riwayat Saldo
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- PPPoE Section -->
                    <div x-data="{ open: {{ request()->routeIs('pppoe.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('pppoe.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                            <div class="flex items-center">
                                <i class="fas fa-network-wired mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">PPPoE</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition.origin.top.duration.200ms class="space-y-1 pl-11 pr-2">
                            <a href="{{ route('pppoe.active') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('pppoe.active') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Active
                            </a>
                            <a href="{{ route('pppoe.profiles') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('pppoe.profiles') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Profiles
                            </a>
                            <a href="{{ route('pppoe.secrets') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('pppoe.secrets') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Secrets
                            </a>
                        </div>
                    </div>

                    <!-- Pelanggan Section -->
                    <div x-data="{ open: {{ request()->routeIs('customer.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('customer.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                            <div class="flex items-center">
                                <i class="fas fa-users mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Pelanggan</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition.origin.top.duration.200ms class="space-y-1 pl-11 pr-2">
                            <a href="{{ route('customer.list') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->fullUrlIs(route('customer.list')) ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Semua Pelanggan
                            </a>
                            <a href="{{ route('customer.list', ['type' => 'MEMBER']) }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->fullUrlIs(route('customer.list', ['type' => 'MEMBER'])) ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Hotspot (Member)
                            </a>
                            <a href="{{ route('customer.list', ['type' => 'PERUMAHAN']) }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->fullUrlIs(route('customer.list', ['type' => 'PERUMAHAN'])) ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                PPPoE (Perumahan)
                            </a>
                            <a href="{{ route('customer.list', ['type' => 'RESELLER']) }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->fullUrlIs(route('customer.list', ['type' => 'RESELLER'])) ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Reseller
                            </a>
                        </div>
                    </div>

                    <!-- Billing & Sales Menu -->
                    <div x-data="{ open: {{ request()->routeIs('billing.*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('billing.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                            <div class="flex items-center">
                                <i class="fas fa-money-bill-wave mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Billing & Sales</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition.origin.top.duration.200ms class="space-y-1 pl-11 pr-2">
                            <a href="{{ route('billing.monitor') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('billing.monitor') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Monitor
                            </a>
                            <a href="{{ route('billing.income') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('billing.income') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Income
                            </a>
                            <a href="{{ route('billing.expense') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('billing.expense') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Expense
                            </a>
                            <a href="{{ route('billing.debt') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('billing.debt') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Debt / Hutang
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('settings') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('settings') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-cog mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Settings</span>
                    </a>
                    <a href="{{ route('report.index') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('report.index') || request()->routeIs('report.show') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-history mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">My Reports</span>
                    </a>
                    <a href="{{ route('profile') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('profile') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-user-circle mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">My Profile</span>
                    </a>
                @elseif(auth()->check() && auth()->user()->role === 'builder')
                     <div class="px-4 mb-4 text-[10px] font-bold text-blue-400 tracking-widest text-center border-b border-slate-800 pb-2">
                        KOALA BUILDER
                    </div>
                    <a href="{{ route('builder.dashboard') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('builder.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-home mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>
                    <a href="{{ route('builder.reports') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('builder.reports*') ? 'bg-slate-800 text-white' : 'text-yellow-500 hover:text-white' }}">
                        <i class="fas fa-inbox mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Reports Inbox</span>
                        @php $unread = \App\Models\Report::unread()->count(); @endphp
                        @if($unread > 0)
                            <span class="bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ml-auto">
                                {{ $unread }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('report.form') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('report.form') ? 'bg-blue-600 text-white shadow-md' : 'text-blue-400 hover:text-white' }}">
                        <i class="fas fa-paper-plane mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Report to Mr. Koala</span>
                    </a>
                    <a href="{{ route('builder.user.create') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('builder.user.create') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-user-plus mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Create Account</span>
                    </a>
                    <a href="{{ route('koala.index') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group text-slate-400 hover:text-white">
                        <i class="fas fa-book mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Documentation</span>
                    </a>
                    <a href="{{ route('profile') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('profile') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-user-circle mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">My Profile</span>
                    </a>
@elseif(auth()->check() && auth()->user()->role === 'reseller')
                    <!-- Reseller Menu -->
                    <a href="{{ route('reseller.dashboard') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('reseller.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-tachometer-alt mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="{{ route('reseller.vouchers') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('reseller.vouchers') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-shopping-cart mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Voucher Terjual</span>
                    </a>

                    <a href="{{ route('reseller.generate') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('reseller.generate') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-magic mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Generate Voucher</span>
                    </a>

                    <a href="{{ route('reseller.distribution') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('reseller.distribution') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-history mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Distribusi</span>
                    </a>

                    <a href="{{ route('reseller.balance') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('reseller.balance') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-wallet mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Tambah Saldo</span>
                    </a>
                    
                    <a href="{{ route('reseller.balance.history') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('reseller.balance.history') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-file-invoice-dollar mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Riwayat Saldo</span>
                    </a>
                    
                    <a href="{{ route('report.form') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('report.form') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-paper-plane mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Report to Mr. Koala</span>
                    </a>
                    <a href="{{ route('profile') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('profile') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-user-circle mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">My Profile</span>
                    </a>
                @endif

                
                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" class="mt-8 pt-4 border-t border-slate-800">
                    @csrf
                    <button type="submit" class="w-full flex items-center py-2.5 px-4 rounded-lg transition duration-200 text-red-500 hover:bg-red-500/10 hover:text-red-400 group">
                        <i class="fas fa-sign-out-alt mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>

            </nav>
            
             <!-- Footer in Sidebar -->
            <div class="px-4 py-2 border-t border-gray-800 text-xs text-gray-500 text-center">
                &copy; {{ date('Y') }} HOT POT Manager
            </div>

        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="flex justify-between items-center py-3 px-6 shadow-sm bg-white border-b border-slate-200 z-40 sticky top-0">
                
                @if(session('impersonated_by'))
                <div class="absolute top-0 left-0 w-full bg-red-600 text-white text-center text-sm py-1 font-bold z-50">
                    You are impersonating {{ auth()->user()->name }}. 
                    <a href="{{ route('impersonate.leave') }}" class="underline ml-2 bg-white text-red-600 px-2 rounded hover:bg-red-50">Stop Impersonating</a>
                </div>
                @endif

                <div class="flex items-center {{ session('impersonated_by') ? 'mt-6' : '' }}">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-blue-600 focus:outline-none md:hidden p-2 -ml-2 rounded-md hover:bg-slate-100 transition">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-bold text-slate-800 ml-3 md:ml-0 font-sans tracking-tight">@yield('header_title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-4 {{ session('impersonated_by') ? 'mt-6' : '' }}">
                    
                    <!-- Notification Bell -->
                    @php
                        $broadcasts = collect();
                        $unreadTickets = collect();
                        $recentTopups = collect();
                        if(auth()->check()) {
                            if(auth()->user()->role !== 'builder') {
                                $readBroadcastIds = \Illuminate\Support\Facades\DB::table('broadcast_reads')
                                    ->where('user_id', auth()->id())
                                    ->pluck('broadcast_id');

                                $broadcasts = \App\Models\Broadcast::where('is_active', true)
                                    ->whereNotIn('id', $readBroadcastIds)
                                    ->latest()
                                    ->get();
                                    
                                $unreadTickets = \App\Models\Report::where('sender_id', auth()->id())
                                    ->where('sender_type', get_class(auth()->user()))
                                    ->where('user_unread', true)
                                    ->latest('updated_at')
                                    ->get();
                            }
                            
                            if(auth()->user()->role === 'mitra-reseller' || auth()->user()->role === 'reseller') {
                                $recentTopups = \App\Models\BalanceHistory::where('customer_id', auth()->id())
                                    ->where('type', 'IN')
                                    ->latest()
                                    ->take(3)
                                    ->get();
                            }
                        }
                    @endphp
                    
                    <div x-data="{ 
                            open: false, 
                            hasUnread: {{ ($broadcasts->count() > 0 || $unreadTickets->count() > 0 || $recentTopups->count() > 0) ? 'true' : 'false' }} 
                        }" 
                        class="relative">
                        <button @click="open = !open; hasUnread = false" class="bg-gray-100 p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition relative focus:outline-none">
                            <i class="fas fa-bell"></i>
                            
                            <span x-show="hasUnread" x-transition.scale class="absolute top-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white bg-red-500 transform translate-x-1/4 -translate-y-1/4"></span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div x-show="open" @click.away="open = false" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-slate-100 z-50 overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Notifications</h3>
                                <span class="bg-blue-100 text-blue-600 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $broadcasts->count() + $unreadTickets->count() + $recentTopups->count() }} Active</span>
                            </div>
                            <div class="max-h-80 overflow-y-auto p-2 space-y-2">
                                
                                @forelse($unreadTickets as $ticket)
                                    @php
                                        // Determine route based on role
                                        $ticketRoute = '#';
                                        if (auth()->user()->role === 'isp') {
                                            $ticketRoute = route('hotsupport.tickets.show', $ticket->id);
                                        } elseif (auth()->user()->role === 'owner') {
                                            $ticketRoute = route('report.show', $ticket->id);
                                        } elseif (auth()->user()->role === 'builder') {
                                            $ticketRoute = route('builder.reports.show', $ticket->id);
                                        }
                                    @endphp
                                    <a href="{{ $ticketRoute }}" class="block p-3 rounded-lg border border-purple-100 bg-purple-50 group hover:bg-purple-100 transition">
                                         <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 mt-0.5">
                                                <i class="fas fa-ticket-alt text-purple-600"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-bold text-slate-800 group-hover:text-purple-700 transition-colors">
                                                    Update on {{ $ticket->ticket_id }}
                                                </h4>
                                                <p class="text-[11px] text-slate-600 mt-0.5 leading-snug">
                                                    Status: {{ ucfirst($ticket->status) }}. Click to view details.
                                                </p>
                                                <p class="text-[9px] text-slate-400 mt-1.5 text-right">{{ $ticket->updated_at->diffForHumans() }}</p>
                                            </div>
                                            <div class="absolute right-3 top-3 h-1.5 w-1.5 rounded-full bg-blue-500"></div>
                                         </div>
                                    </a>
                                @empty
                                    <!-- No Tickets, just continue to broadcasts -->
                                @endforelse

                                @forelse($broadcasts as $notification)
                                    <div class="p-3 rounded-lg border flex items-start gap-3 {{ 
                                        $notification->type == 'info' ? 'bg-blue-50 border-blue-100' : 
                                        ($notification->type == 'warning' ? 'bg-yellow-50 border-yellow-100' : 
                                        ($notification->type == 'danger' ? 'bg-red-50 border-red-100' : 
                                        'bg-green-50 border-green-100')) 
                                    }}">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <i class="fas {{ 
                                                $notification->type == 'info' ? 'fa-info-circle text-blue-500' : 
                                                ($notification->type == 'warning' ? 'fa-exclamation-triangle text-yellow-500' : 
                                                ($notification->type == 'danger' ? 'fa-exclamation-circle text-red-500' : 
                                                'fa-check-circle text-green-500')) 
                                            }}"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-xs font-bold text-slate-800">{{ $notification->title }}</h4>
                                            <p class="text-[11px] text-slate-600 mt-0.5 leading-snug">{{ $notification->message }}</p>
                                            <div class="flex justify-between items-center mt-1.5">
                                                <span class="text-[9px] text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                                                <form action="{{ route('broadcast.read', $notification->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="text-[9px] text-blue-600 hover:text-blue-800 font-bold underline">Mark as Read</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse

                                @forelse($recentTopups as $topup)
                                    <div class="p-3 rounded-lg border flex items-start gap-3 bg-green-50 border-green-100 group hover:bg-green-100 transition">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <i class="fas fa-wallet text-green-500"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="text-xs font-bold text-slate-800">Saldo Masuk</h4>
                                            <p class="text-[11px] text-slate-600 mt-0.5 leading-snug">Rp {{ number_format($topup->amount, 0, ',', '.') }} - {{ $topup->description ?? 'Topup Saldo oleh ISP' }}</p>
                                            <div class="flex justify-between items-center mt-1.5">
                                                <span class="text-[9px] text-slate-400 font-bold text-green-600">{{ $topup->created_at->diffForHumans() }}</span>
                                                <span class="text-[9px] text-slate-400">Ref: {{ $topup->reference_id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse

                                @if($unreadTickets->isEmpty() && $broadcasts->isEmpty() && $recentTopups->isEmpty())
                                    <div class="p-8 text-center text-slate-400">
                                        <i class="fas fa-bell-slash text-2xl mb-2 opacity-50"></i>
                                        <p class="text-xs">No active notifications</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                
                     <a href="{{ route('profile') }}" class="flex items-center gap-3 pl-4 border-l border-slate-200 hover:bg-slate-50 transition p-2 rounded-lg cursor-pointer">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-bold text-slate-700 leading-tight">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] font-bold uppercase tracking-wide {{ auth()->user()->role === 'isp' ? 'text-blue-500' : (auth()->user()->role === 'builder' ? 'text-indigo-600' : (auth()->user()->role === 'reseller' ? 'text-green-500' : 'text-slate-500')) }}">
                                {{ auth()->user()->role === 'isp' ? 'SUPERDUPER ADMIN' : (auth()->user()->role === 'builder' ? 'BUILDER' : (auth()->user()->role === 'reseller' ? 'RESELLER' : 'OWNER')) }}
                            </div>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md ring-2 ring-white hover:scale-105 transition-transform">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                     </a>
                </div>
            </header>

            <!-- Content Body -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 md:p-8 scroll-smooth">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 5000)"
                         class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-r shadow-sm flex justify-between items-center" role="alert">
                        <div>
                            <p class="font-bold">Success</p>
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-blue-400 hover:text-blue-600"><i class="fas fa-times"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms 
                         class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm flex justify-between items-center" role="alert">
                        <div>
                            <p class="font-bold">Error</p>
                            <p class="text-sm">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                    </div>
                @endif

                @if(isset($errors) && $errors->any())
                    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms 
                         class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm flex justify-between items-start" role="alert">
                        <div>
                            <p class="font-bold">Please fix the following errors:</p>
                            <ul class="list-disc list-inside text-sm mt-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button @click="show = false" class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                    </div>
                @endif
                
                <div class="fade-in">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    <style>
        .fade-in { animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
    <!-- PWA Install Popup -->
    <div id="pwa-install-overlay" class="fixed inset-0 z-[100] bg-slate-900/80 backdrop-blur-md flex items-center justify-center p-6 hidden">
        <div class="bg-white rounded-[2.5rem] w-full max-w-sm p-8 text-center shadow-2xl animate-in zoom-in duration-300">
            <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl mx-auto flex items-center justify-center shadow-xl mb-6">
                <i class="fas fa-fire text-white text-3xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 mb-2">Instal HOT POT App</h3>
            <p class="text-sm text-slate-500 font-medium leading-relaxed mb-8">Gunakan versi aplikasi untuk pengalaman yang lebih cepat, ringan, dan nyaman.</p>
            
            <div class="space-y-3">
                <button id="btn-pwa-install" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl shadow-lg shadow-indigo-200 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fas fa-download"></i>
                    INSTAL SEKARANG
                </button>
                <button id="btn-pwa-close" class="w-full py-4 bg-slate-50 hover:bg-slate-100 text-slate-400 font-black rounded-2xl transition-all active:scale-95">
                    NANTI SAJA
                </button>
            </div>

            <!-- iOS Instructions (Hidden by default) -->
            <div id="ios-instructions" class="mt-6 p-4 bg-blue-50 rounded-2xl text-[10px] font-bold text-blue-600 leading-relaxed hidden">
                <p>Klik tombol <i class="fas fa-share-square"></i> (Share) di Safari, lalu pilih <span class="uppercase">"Add to Home Screen"</span> untuk menginstal.</p>
            </div>
        </div>
    </div>

    <script>
        let deferredPrompt;
        const overlay = document.getElementById('pwa-install-overlay');
        const iosInstructions = document.getElementById('ios-instructions');
        const installBtn = document.getElementById('btn-pwa-install');
        const closeBtn = document.getElementById('btn-pwa-close');

        // Check if already in standalone mode
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

        if (!isStandalone) {
            window.addEventListener('load', () => {
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Freeze scrolling

                if (isIOS) {
                    installBtn.classList.add('hidden');
                    iosInstructions.classList.remove('hidden');
                }
            });
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
        });

        installBtn.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    overlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
                deferredPrompt = null;
            } else {
                alert('Gunakan Chrome untuk versi terbaik atau cek pengaturan browser Anda.');
            }
        });

        closeBtn.addEventListener('click', () => {
            overlay.classList.add('hidden');
            document.body.style.overflow = ''; // Unfreeze scrolling
        });
    </script>
</body>
</html>
