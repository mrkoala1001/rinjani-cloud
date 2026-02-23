<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'HOT POT - Powered by DEPOOTCOM')</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #f43f5e, #e11d48, #be123c);
            background-size: 200% 200%;
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            background-position: 100% 0;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(225,29,72,0.5);
        }
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            left: 30px;
            background-color: #25d366;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.5);
            transition: all 0.3s ease;
            z-index: 1000;
            cursor: pointer;
        }
        .whatsapp-float:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white overflow-x-hidden bg-slate-50">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 glass-nav border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="{{ route('landing') }}" class="flex-shrink-0 flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-brand-500 to-rose-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-rose-500/30">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div>
                        <span class="font-bold text-xl tracking-tight text-slate-900">HOT POT</span>
                        <span class="text-[10px] block font-semibold text-slate-500 uppercase tracking-wider leading-none">by DEPOOTCOM</span>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('landing') }}#about" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition relative group">About</a>
                    <a href="{{ route('landing') }}#features" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition relative group">Why Us?</a>
                    <a href="{{ route('documentation.index') }}" class="text-sm font-medium {{ request()->routeIs('documentation.*') ? 'text-brand-600 font-bold' : 'text-slate-600' }} hover:text-brand-600 transition relative group">Dokumentasi</a>
                    <a href="{{ route('landing') }}#faq" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition relative group">FAQ</a>
                    <a href="{{ route('landing') }}#join" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition relative group">Join Now</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-white btn-gradient px-5 py-2.5 rounded-full shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                            <span>Dashboard</span>
                            <i class="fas fa-tachometer-alt text-xs"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-brand-600 px-4 py-2 transition relative group">Log in</a>
                        <a href="https://wa.me/62859175387852" target="_blank" class="text-sm font-semibold text-white btn-gradient px-5 py-2.5 rounded-full shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                            <span>Daftar Sekarang</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                   <a href="{{ route('login') }}" class="text-sm font-semibold text-brand-600 mr-4">Log in</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center text-sm text-slate-400">
                <p>&copy; {{ date('Y') }} HOT POT by DEPOOTCOM. All rights reserved.</p>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <a href="#" class="hover:text-slate-600 transition transform hover:scale-110"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="hover:text-slate-600 transition transform hover:scale-110"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-slate-600 transition transform hover:scale-110"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp -->
    <a href="https://wa.me/62859175387852" target="_blank" class="whatsapp-float" title="Live Chat WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            duration: 1000,
        });
    </script>
</body>
</html>
