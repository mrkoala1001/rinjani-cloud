<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru - HOT POT Manager</title>
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
                <div class="inline-flex p-4 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg mb-6 transform hover:rotate-6 transition-transform">
                    <i class="fas fa-user-plus text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Daftar Akun</h1>
                <p class="text-slate-500 mt-2 font-medium">Mulai manajemen hotspot Anda hari ini.</p>
            </div>

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4 text-left">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg text-sm mb-4">
                        <ul class="list-disc list-inside opacity-90">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-2">
                    <label class="block text-slate-700 text-sm font-bold ml-1" for="name">
                        Nama Lengkap
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fas fa-address-card"></i>
                        </span>
                        <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-11 pr-4 text-slate-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400" 
                               id="name" type="text" name="name" value="{{ old('name') }}" required placeholder="Nama Lengkap Anda">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-slate-700 text-sm font-bold ml-1" for="username">
                        Username
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fas fa-user-circle"></i>
                        </span>
                        <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-11 pr-4 text-slate-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400" 
                               id="username" type="text" name="username" value="{{ old('username') }}" required placeholder="Username unik">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-slate-700 text-sm font-bold ml-1" for="whatsapp">
                        Nomor WhatsApp
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fab fa-whatsapp"></i>
                        </span>
                        <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-11 pr-4 text-slate-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400" 
                               id="whatsapp" type="text" name="whatsapp" value="{{ old('whatsapp') }}" required placeholder="Contoh: 08123456789">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-slate-700 text-sm font-bold ml-1" for="password">
                            Password
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-11 pr-4 text-slate-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400" 
                                   id="password" type="password" name="password" required placeholder="••••••••">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-slate-700 text-sm font-bold ml-1" for="password_confirmation">
                            Konfirmasi
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                <i class="fas fa-shield-alt"></i>
                            </span>
                            <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pl-11 pr-4 text-slate-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400" 
                                   id="password_confirmation" type="password" name="password_confirmation" required placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform active:scale-[0.98] transition-all flex items-center justify-center gap-2 group" 
                            type="submit">
                        Daftar Jetzt 
                        <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-sm">
                <p class="text-slate-500">Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">Masuk di sini</a></p>
            </div>
        </div>
        
        <div class="mt-8 text-center text-sm text-slate-400 font-medium">
            &copy; 2026 HOT POT Manager. Built with <i class="fas fa-heart text-red-400"></i> by DEPOOTCOM.
        </div>
    </div>
</body>
</html>
