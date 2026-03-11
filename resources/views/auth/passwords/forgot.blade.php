<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - HOT POT Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans text-slate-900 min-h-screen flex justify-center items-center p-6 antialiased">
    
    <!-- Background Decoration -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-400/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-orange-400/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl shadow-slate-200/50 border border-white p-8 md:p-10 transform -translate-y-4">
            
            <div class="text-center mb-8">
                <div class="inline-flex p-4 bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl shadow-lg mb-6 transform hover:rotate-6 transition-transform">
                    <i class="fas fa-key text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Lupa Password?</h1>
                <p class="text-slate-500 mt-2 font-medium">Masukkan Username atau WhatsApp Anda untuk menerima kode reset.</p>
            </div>

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg text-sm mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.wa.send') }}" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="block text-slate-700 text-sm font-bold ml-1" for="identifier">
                        Username / WA
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-4 pl-11 pr-4 text-slate-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400" 
                               id="identifier" type="text" name="identifier" required placeholder="Contoh: 08123456789 atau username">
                    </div>
                </div>

                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform active:scale-[0.98] transition-all flex items-center justify-center gap-3 group" 
                        type="submit">
                    Kirim Kode OTP
                    <i class="fas fa-paper-plane text-sm group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <div class="relative py-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="px-2 bg-white/80 text-slate-400">Atau</span>
                </div>
            </div>

            <div class="text-center space-y-4">
                <a href="https://wa.me/6281234567890?text=Halo%20Mr.%20Koala,%20saya%20lupa%20password%20akun%20HotPot%20saya.%20Mohon%20bantuannya." 
                   target="_blank"
                   class="inline-flex items-center gap-2 text-sm font-bold text-green-600 hover:text-green-700 transition-colors">
                    <i class="fab fa-whatsapp"></i> Chat Admin Manual
                </a>
                <br>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors pt-2">
                    <i class="fas fa-arrow-left"></i> Kembali ke Login
                </a>
            </div>
        </div>
        
        <div class="mt-8 text-center text-sm text-slate-400 font-medium">
            &copy; 2026 HOT POT Manager. Built with <i class="fas fa-heart text-red-400"></i> by DEPOOTCOM.
        </div>
    </div>
</body>
</html>
