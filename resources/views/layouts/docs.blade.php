<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documentation - HOT POT</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
    </style>
</head>
<body class="font-sans text-slate-800 antialiased bg-slate-50 selection:bg-brand-500 selection:text-white">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 glass-nav border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex-shrink-0 flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-brand-500 to-rose-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-rose-500/30">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div>
                        <span class="font-bold text-xl tracking-tight text-slate-900">HOT POT</span>
                        <span class="text-[10px] block font-semibold text-slate-500 uppercase tracking-wider leading-none">Docs</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ url('/') }}" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition">Home</a>
                    <a href="{{ route('koala.index') }}" class="text-sm font-bold text-brand-600 transition">Documentation</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ 
                            auth()->user()->role === 'builder' ? route('builder.dashboard') : 
                            (auth()->user()->role === 'isp' ? route('hotsupport.dashboard') : route('dashboard')) 
                        }}" class="text-sm font-semibold text-slate-700 hover:text-brand-600 px-4 py-2 transition">
                            Dashboard
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-700 px-4 py-2 transition">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-brand-600 px-4 py-2 transition">
                            Log in
                        </a>
                    @endauth
                    
                    <a href="https://wa.me/62859175387852" target="_blank" class="hidden sm:flex text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 px-5 py-2.5 rounded-full shadow-lg shadow-rose-500/30 transition transform hover:-translate-y-0.5 items-center gap-2">
                        <span>Konsultasi</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="pt-28 pb-20 min-h-screen">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center text-sm text-slate-400">
            <p>&copy; {{ date('Y') }} HOT POT by DEPOOTCOM. All rights reserved.</p>
            <div class="flex gap-4 mt-4 md:mt-0">
                <a href="#" class="hover:text-slate-600"><i class="fab fa-facebook"></i></a>
                <a href="#" class="hover:text-slate-600"><i class="fab fa-twitter"></i></a>
                <a href="#" class="hover:text-slate-600"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>

</body>
</html>
