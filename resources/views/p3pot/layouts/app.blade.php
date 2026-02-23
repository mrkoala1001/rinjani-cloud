<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>P3POT - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                 <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg shadow-lg">
                    <i class="fas fa-satellite-dish text-white text-xl"></i>
                 </div>
                 <span class="text-xl font-bold tracking-wide">P3POT</span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 space-y-2 mt-6">
                
                @if(Auth::guard('p3pot')->check() && Auth::guard('p3pot')->user()->role === 'admin')
                    <!-- Owner Menu -->
                    <a href="{{ route('p3pot.owner.dashboard') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('p3pot.owner.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-tachometer-alt mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Dashboard</span>
                    </a>

                    <a href="#" @click.prevent="alert('Report to Mr. Koala functionality coming soon')" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group text-slate-400 hover:text-white">
                        <i class="fas fa-paper-plane mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Report to Mr. Koala</span>
                    </a>

                    <!-- PPPoE Section -->
                    <div x-data="{ open: {{ request()->routeIs('p3pot.owner.pppoe*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('p3pot.owner.pppoe*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                            <div class="flex items-center">
                                <i class="fas fa-network-wired mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">PPPoE</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition.origin.top.duration.200ms class="space-y-1 pl-11 pr-2">
                            <a href="{{ route('p3pot.owner.pppoe') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('p3pot.owner.pppoe') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Active Sessions
                            </a>
                            <a href="#" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 text-slate-400 hover:text-white">
                                Profiles
                            </a>
                            <a href="#" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 text-slate-400 hover:text-white">
                                Secrets
                            </a>
                        </div>
                    </div>

                    <!-- Pelanggan Section -->
                    <div x-data="{ open: {{ request()->routeIs('p3pot.owner.customers*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('p3pot.owner.customers*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                            <div class="flex items-center">
                                <i class="fas fa-users mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Pelanggan</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition.origin.top.duration.200ms class="space-y-1 pl-11 pr-2">
                            <a href="{{ route('p3pot.owner.customers') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('p3pot.owner.customers') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Semua Pelanggan
                            </a>
                        </div>
                    </div>

                    <!-- Billing & Sales Section -->
                    <div x-data="{ open: {{ request()->routeIs('p3pot.owner.billing*') ? 'true' : 'false' }} }" class="space-y-1">
                        <button @click="open = !open" class="w-full flex items-center justify-between py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('p3pot.owner.billing*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                            <div class="flex items-center">
                                <i class="fas fa-money-bill-wave mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">Billing & Sales</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition.origin.top.duration.200ms class="space-y-1 pl-11 pr-2">
                            <a href="{{ route('p3pot.owner.billing') }}" class="block py-2 px-3 rounded-md text-sm transition duration-200 hover:bg-slate-700 {{ request()->routeIs('p3pot.owner.billing') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                                Monitor
                            </a>
                        </div>
                    </div>

                    <!-- Payment Gateway -->
                    <a href="{{ route('p3pot.owner.payment_gateway') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('p3pot.owner.payment_gateway') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-wallet mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Payment Gateway</span>
                    </a>

                    <a href="{{ route('p3pot.owner.settings') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('p3pot.owner.settings') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-cog mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Settings</span>
                    </a>

                    <a href="{{ route('p3pot.owner.reports') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('p3pot.owner.reports') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-history mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">My Reports</span>
                    </a>

                @elseif(Auth::guard('p3pot')->check() && Auth::guard('p3pot')->user()->role === 'customer')
                    <!-- Customer Menu -->
                    <a href="{{ route('p3pot.customer.dashboard') }}" class="flex items-center py-2.5 px-4 rounded-lg transition duration-200 hover:bg-slate-800 group {{ request()->routeIs('p3pot.customer.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:text-white' }}">
                        <i class="fas fa-home mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">My Services</span>
                    </a>
                @endif

                <!-- Logout -->
                <form action="{{ route('p3pot.logout') }}" method="POST" class="mt-8 pt-4 border-t border-slate-800">
                    @csrf
                    <button type="submit" class="w-full flex items-center py-2.5 px-4 rounded-lg transition duration-200 text-red-500 hover:bg-red-500/10 hover:text-red-400 group">
                        <i class="fas fa-sign-out-alt mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>

            </nav>
            
             <!-- Footer in Sidebar -->
            <div class="px-4 py-2 border-t border-gray-800 text-xs text-gray-500 text-center">
                &copy; {{ date('Y') }} P3POT Manager
            </div>

        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="flex justify-between items-center py-3 px-6 shadow-sm bg-white border-b border-slate-200 z-40 sticky top-0">
                <div class="flex items-center">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-blue-600 focus:outline-none md:hidden p-2 -ml-2 rounded-md hover:bg-slate-100 transition">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-bold text-slate-800 ml-3 md:ml-0 font-sans tracking-tight">@yield('header_title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center gap-4">
                     <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-bold text-slate-700 leading-tight">{{ Auth::guard('p3pot')->user()->fullname }}</div>
                            <div class="text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                {{ Auth::guard('p3pot')->user()->role === 'admin' ? 'OWNER' : 'CUSTOMER' }}
                            </div>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md ring-2 ring-white">
                            {{ substr(Auth::guard('p3pot')->user()->fullname, 0, 1) }}
                        </div>
                     </div>
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
</body>
</html>
