<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - HOT POT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="theme-color" content="#4f46e5">
    <link rel="manifest" href="/manifest.json">
    
    <!-- Apple Mobile Web App Support -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="HotPot App">
    <link rel="apple-touch-icon" href="https://cdn-icons-png.flaticon.com/512/916/916771.png">

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(reg => console.log('SW Registered', reg))
                    .catch(err => console.log('SW Error', err));
            });
        }
    </script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased pb-20">
    
    <!-- App Header -->
    <header class="bg-white border-b border-slate-100 sticky top-0 z-30 px-6 py-5 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-fire text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-sm font-black text-slate-900 leading-none">HOT POT</h1>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Self Service App</p>
            </div>
        </div>
        <form action="{{ route('customer_app.logout') }}" method="POST">
            @csrf
            <button type="submit" class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </form>
    </header>

    <main class="p-6 max-w-lg mx-auto">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-[2rem] p-8 text-white shadow-xl shadow-indigo-200 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-80 mb-2">Selamat Datang Kembali</p>
                <h2 class="text-2xl font-black mb-1">{{ $customer->name }}</h2>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[10px] font-bold uppercase tracking-wider mt-2">
                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                    {{ $customer->type }} CLIENT
                </div>
            </div>
        </div>

        @yield('dashboard_content')

    </main>

    <!-- Bottom Navigation -->
    <div class="fixed bottom-0 left-0 right-0 p-4 z-40 pointer-events-none">
        <nav class="max-w-lg mx-auto bg-white/80 backdrop-blur-lg border border-slate-100 flex items-stretch py-3 px-2 rounded-[2rem] shadow-2xl shadow-indigo-200/50 pointer-events-auto">
            <a href="{{ route('customer_app.dashboard') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-1 {{ request()->routeIs('customer_app.dashboard') ? 'text-indigo-600' : 'text-slate-400' }}">
                <div class="h-6 flex items-center justify-center">
                    <i class="fas fa-th-large text-lg"></i>
                </div>
                <span class="text-[7px] font-black uppercase tracking-tighter leading-none">Beranda</span>
            </a>
            <a href="{{ route('customer_app.reseller.transactions') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-1 {{ request()->routeIs('customer_app.reseller.transactions') ? 'text-indigo-600' : 'text-slate-400' }}">
                <div class="h-6 flex items-center justify-center">
                    <i class="fas fa-receipt text-lg"></i>
                </div>
                <span class="text-[7px] font-black uppercase tracking-tighter leading-none">Riwayat</span>
            </a>
            <a href="{{ route('customer_app.reseller.balance_logs') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-1 {{ request()->routeIs('customer_app.reseller.balance_logs') ? 'text-indigo-600' : 'text-slate-400' }}">
                <div class="h-6 flex items-center justify-center">
                    <i class="fas fa-wallet text-lg"></i>
                </div>
                <span class="text-[7px] font-black uppercase tracking-tighter leading-none">Saldo</span>
            </a>
            <a href="{{ route('customer_app.reseller.profile') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-1 {{ request()->routeIs('customer_app.reseller.profile') ? 'text-indigo-600' : 'text-slate-400' }}">
                <div class="h-6 flex items-center justify-center">
                    <i class="fas fa-user-circle text-lg"></i>
                </div>
                <span class="text-[7px] font-black uppercase tracking-tighter leading-none">Profil</span>
            </a>
        </nav>
    </div>
</body>
</html>
