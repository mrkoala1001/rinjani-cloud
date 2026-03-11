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
    <link rel="manifest" href="/manifest-reseller.json">
    
    <!-- Apple Mobile Web App Support -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="HotPot Reseller">
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
    <!-- Bottom Navigation -->
    <nav class="fixed bottom-0 left-0 right-0 mx-auto w-full max-w-lg mb-4 px-4 z-50">
        <div class="bg-white/80 backdrop-blur-lg border border-slate-100 flex items-stretch py-3 px-2 rounded-[2rem] shadow-2xl shadow-indigo-200/50">
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
            <a href="{{ route('customer_app.tickets') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-1 {{ request()->routeIs('customer_app.tickets*') ? 'text-indigo-600' : 'text-slate-400' }}">
                <div class="h-6 flex items-center justify-center">
                    <i class="fas fa-headset text-lg"></i>
                </div>
                <span class="text-[7px] font-black uppercase tracking-tighter leading-none">Tiket</span>
            </a>
            <a href="{{ route('customer_app.reseller.profile') }}" class="flex-1 flex flex-col items-center justify-center gap-1 py-1 {{ request()->routeIs('customer_app.reseller.profile') ? 'text-indigo-600' : 'text-slate-400' }}">
                <div class="h-6 flex items-center justify-center">
                    <i class="fas fa-user-circle text-lg"></i>
                </div>
                <span class="text-[7px] font-black uppercase tracking-tighter leading-none">Profil</span>
            </a>
        </div>
    </nav>

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

            <!-- manual Instructions -->
            <div id="ios-instructions" class="mt-6 p-4 bg-blue-50 rounded-2xl text-[10px] font-bold text-blue-600 leading-relaxed hidden">
                <p class="mb-2 uppercase tracking-widest text-[9px] opacity-70">Instruksi iPhone:</p>
                <p>Klik tombol <i class="fas fa-share-square"></i> (Share) di Safari, lalu pilih <span class="uppercase">"Add to Home Screen"</span> untuk menginstal.</p>
            </div>

            <div id="android-instructions" class="mt-6 p-4 bg-orange-50 rounded-2xl text-[10px] font-bold text-orange-600 leading-relaxed hidden">
                <p class="mb-2 uppercase tracking-widest text-[9px] opacity-70">Instruksi Android:</p>
                <p>Klik titik tiga <i class="fas fa-ellipsis-v"></i> di pojok kanan atas browser, lalu pilih <span class="uppercase">"Instal Aplikasi"</span> atau <span class="uppercase">"Tambahkan ke Layar Utama"</span>.</p>
            </div>
        </div>
    </div>

    <script>
        let deferredPrompt;
        const overlay = document.getElementById('pwa-install-overlay');
        const iosInstructions = document.getElementById('ios-instructions');
        const androidInstructions = document.getElementById('android-instructions');
        const installBtn = document.getElementById('btn-pwa-install');
        const closeBtn = document.getElementById('btn-pwa-close');

        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

        // Check if already in standalone mode
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

        if (!isStandalone) {
            window.addEventListener('load', () => {
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; 

                if (isIOS) {
                    installBtn.classList.add('hidden');
                    iosInstructions.classList.remove('hidden');
                }
            });
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            // Jika prompt muncul, pastikan tombol instal terlihat & instruksi android sembunyi
            if (!isIOS) {
                installBtn.classList.remove('hidden');
                androidInstructions.classList.add('hidden');
            }
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
                // Jika otomatis gagal (misal bukan di Chrome/In-app browser), tunjukkan cara manual
                installBtn.classList.add('hidden');
                androidInstructions.classList.remove('hidden');
            }
        });

        closeBtn.addEventListener('click', () => {
            overlay.classList.add('hidden');
            document.body.style.overflow = ''; 
        });
    </script>
</body>
</html>
