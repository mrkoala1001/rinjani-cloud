<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HOT POT - Powered by DEPOOTCOM</title>
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
        .hero-pattern {
            background-color: #ffffff;
            background-image: radial-gradient(#e11d48 0.5px, transparent 0.5px), radial-gradient(#e11d48 0.5px, #ffffff 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.1;
        }

        /* Network animation canvas */
        #network-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        /* Floating shapes */
        .floating-shape {
            position: absolute;
            background: linear-gradient(135deg, rgba(244,63,94,0.1) 0%, rgba(225,29,72,0.1) 100%);
            border-radius: 50%;
            filter: blur(40px);
            z-index: 0;
            animation: float 8s infinite;
        }

        /* Card hover effect */
        .hover-card {
            transition: all 0.3s ease;
        }
        .hover-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px -10px rgba(225,29,72,0.3);
        }

        /* Modern gradient button */
        .btn-gradient {
            background: linear-gradient(135deg, #f43f5e, #e11d48, #be123c);
            background-size: 200% 200%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-gradient:hover {
            background-position: 100% 0;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(225,29,72,0.5);
        }
        .btn-gradient::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 50%);
            transform: rotate(30deg);
            animation: shimmer 3s infinite;
        }

        /* Text gradient animation */
        .gradient-text {
            background: linear-gradient(135deg, #f43f5e, #e11d48, #fb7185);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 4s ease infinite;
        }

        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Ripple effect on canvas click */
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(225, 29, 72, 0.3);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
            z-index: 1;
        }
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }



        /* Top terminal specific */
        .top-terminal {
            max-width: 680px;
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 50px -10px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(34, 197, 94, 0.2);
            backdrop-filter: blur(10px);
        }

        /* Floating WhatsApp button */
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
            animation: pulse-green 2s infinite;
        }
        .whatsapp-float:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.7);
        }
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(37, 211, 102, 0); }
            100% { box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
        }

    </style>
</head>
<body class="font-sans text-slate-800 antialiased selection:bg-brand-500 selection:text-white overflow-x-hidden">

    <!-- Canvas for network animation -->
    <canvas id="network-canvas"></canvas>

    <!-- Floating shapes background -->
    <div class="floating-shape w-96 h-96 top-20 left-10"></div>
    <div class="floating-shape w-80 h-80 bottom-20 right-10 animation-delay-2000"></div>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 glass-nav border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2" data-aos="fade-right" data-aos-duration="1000">
                    <div class="w-10 h-10 bg-gradient-to-br from-brand-500 to-rose-600 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-rose-500/30 animate-pulse-slow">
                        <i class="fas fa-fire"></i>
                    </div>
                    <div>
                        <span class="font-bold text-xl tracking-tight text-slate-900">HOT POT</span>
                        <span class="text-[10px] block font-semibold text-slate-500 uppercase tracking-wider leading-none">by DEPOOTCOM</span>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#about" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition relative group">
                        About
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-brand-600 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="#features" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition relative group">
                        Why Us?
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-brand-600 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="{{ route('documentation.index') }}" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition relative group">
                        Dokumentasi
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-brand-600 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="#faq" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition relative group">
                        FAQ
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-brand-600 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="#join" class="text-sm font-medium text-slate-600 hover:text-brand-600 transition relative group">
                        Join Now
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-brand-600 transition-all group-hover:w-full"></span>
                    </a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center gap-3" data-aos="fade-left" data-aos-duration="1000">
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-700 hover:text-brand-600 px-4 py-2 transition relative group">
                        Log in
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-brand-600 transition-all group-hover:w-full"></span>
                    </a>
                    <a href="https://wa.me/62859175387852" target="_blank" class="text-sm font-semibold text-white btn-gradient px-5 py-2.5 rounded-full shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <span>Daftar Sekarang</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                   <a href="{{ route('login') }}" class="text-sm font-semibold text-brand-600 mr-4">Log in</a>
                   <a href="https://wa.me/62859175387852" class="p-2 text-slate-600 hover:text-brand-600 transition">
                       <i class="fas fa-user-plus text-xl"></i>
                   </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Top Terminal -->
    <div class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 hero-pattern z-0"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div data-aos="fade-down" data-aos-duration="1200" class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-100 text-brand-600 text-xs font-bold uppercase tracking-wide mb-6 animate-pulse">
                <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                Dari Kecil Menjadi Besar — Mulai dengan HOT POT
            </div>
            <h1 data-aos="zoom-in" data-aos-duration="1500" class="text-5xl md:text-7xl font-extrabold text-slate-900 tracking-tight mb-6 leading-tight">
                Mulai kelola jaringan <br />
                <span class="gradient-text">Tanpa ribet dan mahal</span>
            </h1>
            <p data-aos="fade-up" data-aos-delay="200" data-aos-duration="1200" class="mt-4 max-w-2xl mx-auto text-xl text-slate-600 mb-10">
                HOT POT dirancang untuk mereka yang baru membangun. Mudah digunakan, biaya terjangkau, dan siap menemani dari skala kecil menuju besar — didukung oleh DEPOOTCOM.
            </p>

            <!-- Top Terminal -->
            <div data-aos="fade-up" data-aos-delay="600" class="mb-16 md:mb-12 top-terminal mx-4 sm:mx-auto">
                <div class="bg-slate-900/90 rounded-xl shadow-2xl border border-green-500/20 overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-2 bg-slate-800/50 border-b border-green-500/20">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-yellow-500"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                            <span class="text-[10px] sm:text-xs text-slate-400 ml-2">mikrotik@hotpot:~$</span>
                        </div>
                        <i class="fas fa-terminal text-green-400 text-[10px] sm:text-xs"></i>
                    </div>
                    <div class="p-3 sm:p-4 font-mono text-[10px] sm:text-sm h-32 sm:h-40 overflow-hidden flex flex-col justify-end">
                        <div id="top-terminal-line-1" class="mb-1 text-green-400"></div>
                        <div id="top-terminal-line-2" class="mb-1 text-green-500"></div>
                        <div id="top-terminal-line-3" class="mb-1 text-green-600"></div>
                        <div id="top-terminal-line-4" class="mb-1 text-green-600"></div>
                        <div id="top-terminal-line-5" class="text-green-700 flex items-center">
                            <span class="mr-1">~$</span>
                            <span id="top-terminal-cursor" class="animate-pulse">_</span>
                        </div>
                    </div>
                </div>
            </div>

            <div data-aos="fade-up" data-aos-delay="800" class="flex flex-col sm:flex-row gap-4 justify-center mt-20">
                <a href="https://wa.me/62859175387852" target="_blank" class="inline-flex justify-center items-center px-8 py-4  text-base font-bold text-white btn-gradient rounded-full shadow-xl transition transform hover:-translate-y-1">
                    <i class="fab fa-whatsapp mr-2 text-lg"></i>
                    Hubungi Kami via WhatsApp
                </a>
                <a href="#about" class="inline-flex justify-center items-center px-8 py-4 text-base font-bold text-slate-700 bg-white border border-slate-200 rounded-full hover:bg-slate-50 hover:border-slate-300 transition shadow-sm hover:shadow-lg">
                    Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </div>

    <!-- About Section -->
    <section id="about" class="py-24 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div data-aos="fade-right" data-aos-duration="1200">
                    <h2 class="text-brand-600 font-bold uppercase tracking-wide text-sm mb-2">About Us</h2>
                    <h3 class="text-4xl font-extrabold text-slate-900 mb-6">Apa itu HOT POT?</h3>
                    <p class="text-lg text-slate-600 mb-6 leading-relaxed">
                        <strong class="text-brand-600">HOT POT</strong> adalah salah satu produk unggulan dari <strong class="text-slate-900">DEPOOTCOM</strong> yang dirancang khusus untuk mempermudah pengelolaan bisnis Hotspot dan ISP Anda.
                    </p>
                    <p class="text-lg text-slate-600 mb-8 leading-relaxed">
                        Kami juga menghadirkan <strong class="text-brand-600">HOT SUPPORT</strong>, layanan dukungan teknis prioritas yang siap membantu Anda mengatasi kendala jaringan kapan saja. Dengan HOT POT, fokus Anda hanya pada pengembangan bisnis, biarkan kami yang mengurus teknisnya.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 hover:shadow-lg transition hover:border-brand-200 hover-card">
                            <i class="fas fa-server text-3xl text-brand-500 mb-3 animate-pulse-slow"></i>
                            <h4 class="font-bold text-slate-900">Reliable Server</h4>
                            <p class="text-sm text-slate-500">Uptime tinggi untuk kenyamanan pelanggan Anda.</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 hover:shadow-lg transition hover:border-brand-200 hover-card">
                            <i class="fas fa-headset text-3xl text-brand-500 mb-3 animate-pulse-slow"></i>
                            <h4 class="font-bold text-slate-900">24/7 Support</h4>
                            <p class="text-sm text-slate-500">Tim teknis kami selalu siap membantu.</p>
                        </div>
                    </div>
                </div>
                <div class="relative" data-aos="fade-left" data-aos-duration="1200">
                    <div class="absolute inset-0 bg-gradient-to-tr from-brand-200 to-rose-100 rounded-3xl transform rotate-3 scale-105 opacity-50 blur-lg animate-network-pulse"></div>
                    <div class="relative bg-white p-8 rounded-3xl shadow-2xl border border-slate-100 hover-card">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 ">
                                <i class="fas fa-user-shield text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-lg text-slate-900">Builder Mr. Koala</h4>
                                <p class="text-sm text-slate-500">Lead Developer & System Architect</p>
                            </div>
                        </div>
                        <p class="text-slate-600 italic border-l-4 border-brand-500 pl-4 py-2 bg-slate-50 rounded-r-lg">
                            "DEPOOTCOM berfokus menciptakan solusi digital yang bisa diakses lebih luas. HOT POT adalah salah satu langkah kami dalam menyederhanakan manajemen jaringan tanpa membuatnya mahal atau rumit."
                        </p>
                        <div class="mt-6 flex items-center justify-between">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 rounded-full bg-slate-200 border-2 border-white animate-pulse"></div>
                                <div class="w-8 h-8 rounded-full bg-slate-300 border-2 border-white animate-pulse animation-delay-200"></div>
                                <div class="w-8 h-8 rounded-full bg-slate-400 border-2 border-white animate-pulse animation-delay-400"></div>
                                <div class="w-8 h-8 rounded-full bg-brand-500 border-2 border-white flex items-center justify-center text-white text-[10px] font-bold animate-pulse">+5k</div>
                            </div>
                            <span class="text-sm font-bold text-slate-500">Trusted by Partners</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-slate-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up" data-aos-duration="1000">
                <h2 class="text-brand-600 font-bold uppercase tracking-wide text-sm mb-2">Kenapa Memilih Kami?</h2>
                <h3 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-4">Mengapa Harus HOT POT?</h3>
                <p class="text-slate-600 text-lg">Platform all-in-one yang menghemat waktu dan biaya Anda dalam mengelola bisnis jaringan.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100 hover:shadow-xl transition hover-card" data-aos="flip-left" data-aos-delay="100" data-aos-duration="1000">
                    <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-brand-600 text-2xl mb-6 group-hover:scale-110 transition duration-300">
                        <i class="fas fa-magic"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Kemudahan Penggunaan</h4>
                    <p class="text-slate-600 leading-relaxed">
                        Antarmuka yang intuitif dan mudah dipahami, bahkan untuk pemula sekalipun. Tidak perlu coding.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100 hover:shadow-xl transition hover-card" data-aos="flip-left" data-aos-delay="300" data-aos-duration="1000">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-2xl mb-6 animate-pulse">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Sistem Tiket Terintegrasi</h4>
                    <p class="text-slate-600 leading-relaxed">
                        Lapor kendala dan dapatkan bantuan langsung dari dashboard Anda dengan sistem tiket yang responsif.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100 hover:shadow-xl transition hover-card" data-aos="flip-left" data-aos-delay="500" data-aos-duration="1000">
                    <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 text-2xl mb-6 animate-pulse-slow">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4 class="text-xl font-bold text-slate-900 mb-3">Analisis Bisnis</h4>
                    <p class="text-slate-600 leading-relaxed">
                        Pantau pertumbuhan bisnis Anda dengan laporan dan statistik yang akurat dan real-time.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24 bg-white relative">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up" data-aos-duration="1000">
                <h2 class="text-brand-600 font-bold uppercase tracking-wide text-sm mb-2">Pusat Bantuan</h2>
                <h3 class="text-3xl font-extrabold text-slate-900">Frequently Asked Questions</h3>
            </div>
            
            <div class="space-y-4">
                @foreach($faqs as $index => $faq)
                <div class="border border-slate-200 rounded-2xl overflow-hidden" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <button class="w-full flex items-center justify-between p-6 bg-slate-50 hover:bg-slate-100 transition text-left focus:outline-none" onclick="document.getElementById('faq-ans-{{ $loop->iteration }}').classList.toggle('hidden'); document.getElementById('faq-icon-{{ $loop->iteration }}').classList.toggle('rotate-180');">
                        <span class="font-bold text-slate-800">{{ $faq->question }}</span>
                        <i id="faq-icon-{{ $loop->iteration }}" class="fas fa-chevron-down text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div id="faq-ans-{{ $loop->iteration }}" class="hidden p-6 bg-white border-t border-slate-100 text-slate-600 leading-relaxed text-sm">
                        {{ $faq->answer }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Comments Section -->
    <section id="comments" class="py-24 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16">
                <!-- Comment Form -->
                <div data-aos="fade-right" data-aos-duration="1000">
                    <h3 class="text-2xl font-bold text-slate-900 mb-6">Apa kata mereka?</h3>
                    <p class="text-slate-600 mb-8">Bagikan pengalaman Anda menggunakan HOT POT. Masukan Anda sangat berarti bagi kami.</p>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm font-bold">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('comment.store') }}" method="POST" class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" class="w-full rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Email (Privasi Terjaga)</label>
                                <input type="email" name="email" class="w-full rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Komentar</label>
                                <textarea name="content" rows="4" class="w-full rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500" required></textarea>
                            </div>
                            <button type="submit" class="w-full btn-gradient text-white font-bold py-3 rounded-xl shadow-lg hover:shadow-xl transition transform hover:-translate-y-1">
                                Kirim Komentar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Latest Comments -->
                <div data-aos="fade-left" data-aos-duration="1000">
                    <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <i class="fas fa-comments text-brand-500"></i> Komentar Terbaru
                    </h3>
                    <div class="space-y-6 max-h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($comments as $comment)
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 relative">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-10 h-10 bg-gradient-to-br from-slate-100 to-slate-200 rounded-full flex items-center justify-center font-bold text-slate-500 text-sm">
                                    {{ substr($comment->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">{{ $comment->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $comment->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <p class="text-slate-600 text-sm leading-relaxed">{{ $comment->content }}</p>
                            <div class="absolute -left-2 top-8 w-4 h-4 bg-white border-l border-b border-slate-100 rotate-45"></div>
                        </div>
                        @endforeach
                        
                        @if($comments->isEmpty())
                            <div class="text-center py-12 text-slate-400 italic">Belum ada komentar ditampilkan.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="join" class="py-24 bg-gradient-to-br from-slate-900 to-slate-800 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-10"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 data-aos="zoom-in" data-aos-duration="1200" class="text-3xl md:text-5xl font-extrabold text-white mb-6">Siap Mengembangkan Bisnis Anda?</h2>
            <p data-aos="fade-up" data-aos-delay="200" data-aos-duration="1200" class="text-xl text-slate-300 mb-10 max-w-2xl mx-auto">
                Bergabunglah dengan ratusan mitra lain yang telah mempercayakan manajemen jaringannya kepada HOT POT.
            </p>
            <div data-aos="fade-up" data-aos-delay="400" data-aos-duration="1200" class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="https://wa.me/62859175387852" target="_blank" class="px-8 py-4 btn-gradient text-white font-bold rounded-full shadow-lg transition transform hover:-translate-y-1">
                    <i class="fab fa-whatsapp mr-2"></i> Daftar via WhatsApp
                </a>
                <a href="{{ route('login') }}" class="px-8 py-4 bg-transparent border-2 border-white/20 hover:bg-white/10 text-white font-bold rounded-full transition hover:border-white/40">
                    Login Member
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-100 pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-12">
                <div class="col-span-1 md:col-span-2" data-aos="fade-right" data-aos-duration="1000">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center text-white text-sm font-bold animate-pulse">
                            <i class="fas fa-fire"></i>
                        </div>
                        <span class="font-bold text-xl text-slate-900">HOT POT</span>
                    </div>
                    <p class="text-slate-500 max-w-xs leading-relaxed">
                        Solusi manajemen ISP dan Hotspot terdepan di Indonesia. Dibangun dengan cinta oleh DEPOOTCOM.
                    </p>
                </div>
                <div data-aos="fade-up" data-aos-delay="100" data-aos-duration="1000">
                    <h4 class="font-bold text-slate-900 mb-4">Product</h4>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li><a href="#" class="hover:text-brand-600 transition hover:translate-x-1 inline-block">Features</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition hover:translate-x-1 inline-block">Pricing</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition hover:translate-x-1 inline-block">Showcase</a></li>
                    </ul>
                </div>
                <div data-aos="fade-up" data-aos-delay="200" data-aos-duration="1000">
                    <h4 class="font-bold text-slate-900 mb-4">Contact</h4>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li><a href="https://wa.me/62859175387852" class="hover:text-brand-600 transition hover:translate-x-1 inline-block">WhatsApp Support</a></li>
                        <li><a href="#" class="hover:text-brand-600 transition hover:translate-x-1 inline-block">Email Team</a></li>
                        <li>
                            <p class="mt-4 font-bold text-brand-600 animate-pulse">BUILDER MR.KOALA</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-100 flex flex-col md:flex-row justify-between items-center text-sm text-slate-400">
                <p>&copy; {{ date('Y') }} HOT POT by DEPOOTCOM. All rights reserved.</p>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <a href="#" class="hover:text-slate-600 transition transform hover:scale-110"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="hover:text-slate-600 transition transform hover:scale-110"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-slate-600 transition transform hover:scale-110"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Live Chat Button -->
    <a href="https://wa.me/62859175387852" target="_blank" class="whatsapp-float" title="Live Chat WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            once: true,
            duration: 1000,
        });

        // Interactive network canvas with mouse repulsion and click ripple
        const canvas = document.getElementById('network-canvas');
        const ctx = canvas.getContext('2d');
        let width, height;
        let particles = [];
        const particleCount = 100;
        const connectionDistance = 150;
        let mouse = { x: null, y: null, radius: 100 };

        function initParticles() {
            particles = [];
            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: Math.random() * width,
                    y: Math.random() * height,
                    vx: (Math.random() - 0.5) * 0.3,
                    vy: (Math.random() - 0.5) * 0.3,
                    radius: Math.random() * 2 + 1,
                });
            }
        }

        function resizeCanvas() {
            width = window.innerWidth;
            height = window.innerHeight;
            canvas.width = width;
            canvas.height = height;
            initParticles();
        }

        function drawNetwork() {
            ctx.clearRect(0, 0, width, height);

            // Update particle positions with mouse repulsion
            particles.forEach(p => {
                // Mouse repulsion
                if (mouse.x && mouse.y) {
                    const dx = p.x - mouse.x;
                    const dy = p.y - mouse.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist < mouse.radius) {
                        const angle = Math.atan2(dy, dx);
                        const force = (mouse.radius - dist) / mouse.radius;
                        p.x += Math.cos(angle) * force * 2;
                        p.y += Math.sin(angle) * force * 2;
                    }
                }

                p.x += p.vx;
                p.y += p.vy;

                // Wrap around edges with slight randomness
                if (p.x < 0) { p.x = width; p.vx *= 0.99; }
                if (p.x > width) { p.x = 0; p.vx *= 0.99; }
                if (p.y < 0) { p.y = height; p.vy *= 0.99; }
                if (p.y > height) { p.y = 0; p.vy *= 0.99; }

                // Draw particle
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(225, 29, 72, 0.2)';
                ctx.fill();
            });

            // Draw connections
            ctx.strokeStyle = 'rgba(225, 29, 72, 0.1)';
            ctx.lineWidth = 0.5;
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);
                    if (distance < connectionDistance) {
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = `rgba(225, 29, 72, ${0.1 * (1 - distance / connectionDistance)})`;
                        ctx.stroke();
                    }
                }
            }

            requestAnimationFrame(drawNetwork);
        }

        // Track mouse movement
        window.addEventListener('mousemove', (e) => {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        });

        window.addEventListener('mouseleave', () => {
            mouse.x = null;
            mouse.y = null;
        });

        // Click ripple effect
        window.addEventListener('click', (e) => {
            const ripple = document.createElement('div');
            ripple.className = 'ripple';
            ripple.style.left = e.clientX + 'px';
            ripple.style.top = e.clientY + 'px';
            ripple.style.width = ripple.style.height = '50px';
            document.body.appendChild(ripple);
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });

        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
        drawNetwork();

        // Top terminal simulation
        const topTerminalLines = [
            "MikroTik RouterOS 7.6 (stable)",
            "System uptime: 14d 7h 23m",
            "CPU load: 12% | Memory: 34%",
            "Active hotspot users: 10",
            "DHCP leases: 24",
            "Interface ether1: 1.2 Gbps",
            "Hotspot server ready.",
            "Monitoring live traffic..."
        ];

        let topTermIdx = 0;
        let topTermChar = 0;
        let topTermCurrent = "";
        let topTermDeleting = false;
        const topLine1 = document.getElementById('top-terminal-line-1');
        const topLine2 = document.getElementById('top-terminal-line-2');
        const topLine3 = document.getElementById('top-terminal-line-3');
        const topLine4 = document.getElementById('top-terminal-line-4');
        const topCursor = document.getElementById('top-terminal-cursor');

        function updateTopTerminal() {
            if (topLine1) {
                if (topTermIdx < topTerminalLines.length) {
                    const full = topTerminalLines[topTermIdx];
                    if (!topTermDeleting) {
                        topTermCurrent = full.substring(0, topTermChar + 1);
                        topTermChar++;
                        if (topTermChar === full.length) {
                            topTermDeleting = true;
                            setTimeout(updateTopTerminal, 2000);
                        } else {
                            setTimeout(updateTopTerminal, 60);
                        }
                    } else {
                        topTermCurrent = full.substring(0, topTermChar - 1);
                        topTermChar--;
                        if (topTermChar === 0) {
                            topTermDeleting = false;
                            topTermIdx = (topTermIdx + 1) % topTerminalLines.length;
                            setTimeout(updateTopTerminal, 500);
                        } else {
                            setTimeout(updateTopTerminal, 30);
                        }
                    }

                    // Update top terminal lines
                    topLine1.textContent = `> ${topTermCurrent}`;
                    topLine2.textContent = topTermIdx + 1 < topTerminalLines.length ? topTerminalLines[topTermIdx + 1] : '';
                    topLine3.textContent = topTermIdx + 2 < topTerminalLines.length ? topTerminalLines[topTermIdx + 2] : '';
                    topLine4.textContent = topTermIdx + 3 < topTerminalLines.length ? topTerminalLines[topTermIdx + 3] : '';
                }
            }
        }

        // Start top terminal after a short delay
        setTimeout(updateTopTerminal, 2000);

        // Animation delay utility
        document.querySelectorAll('.animation-delay-200').forEach(el => {
            el.style.animationDelay = '200ms';
        });
        document.querySelectorAll('.animation-delay-400').forEach(el => {
            el.style.animationDelay = '400ms';
        });
        document.querySelectorAll('.animation-delay-2000').forEach(el => {
            el.style.animationDelay = '2000ms';
        });
    </script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Terima Kasih!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#e11d48',
                timer: 4000,
                timerProgressBar: true
            });
        @endif
    </script>
</body>
</html>