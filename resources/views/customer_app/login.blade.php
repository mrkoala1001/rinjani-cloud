<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pelanggan - HOT POT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-indigo-900 font-sans text-slate-900 min-h-screen flex justify-center items-center p-6 antialiased">
    
    <!-- Background Decoration -->
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[60%] h-[60%] bg-blue-400/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[60%] h-[60%] bg-purple-400/20 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-sm">
        <div class="bg-white/95 backdrop-blur-xl rounded-[2.5rem] shadow-2xl border border-white/20 p-8 md:p-10">
            
            <div class="text-center mb-8">
                <div class="inline-flex p-4 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl shadow-xl mb-6">
                    <i class="fas fa-mobile-alt text-white text-3xl"></i>
                </div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">App Pelanggan</h1>
                <p class="text-slate-500 mt-2 font-medium text-sm">Akses kontrol layanan Anda</p>
            </div>

            <form method="POST" action="{{ route('customer_app.login.post') }}" class="space-y-5">
                @csrf
                
                @if (session('error'))
                    <div class="bg-red-50 text-red-600 p-4 rounded-2xl text-xs font-bold border border-red-100 flex items-center gap-3">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="space-y-2">
                    <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest ml-1" for="username">
                        Username App
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fas fa-id-card"></i>
                        </span>
                        <input class="w-full bg-slate-100/50 border border-slate-200 rounded-2xl py-4 pl-11 pr-4 text-slate-700 font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all placeholder-slate-400 text-sm" 
                               id="username" type="text" name="username" value="{{ old('username') }}" required autofocus placeholder="Masukkan username app">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-slate-500 text-[10px] font-black uppercase tracking-widest ml-1" for="password">
                        Password App
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input class="w-full bg-slate-100/50 border border-slate-200 rounded-2xl py-4 pl-11 pr-4 text-slate-700 font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all placeholder-slate-400 text-sm" 
                               id="password" type="password" name="password" required placeholder="••••••••">
                    </div>
                </div>

                <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-6 rounded-2xl shadow-lg shadow-indigo-200 transform active:scale-[0.98] transition-all flex items-center justify-center gap-2 group mt-4" 
                        type="submit">
                    MASUK KE APP
                    <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>
        </div>
        
        <p class="mt-8 text-center text-xs text-indigo-300 font-bold uppercase tracking-widest">
            &copy; 2026 HOT POT MANAGER
        </p>
    </div>
</body>
</html>
