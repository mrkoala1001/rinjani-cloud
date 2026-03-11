<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HOT POT Manager</title>
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
            
            <div class="text-center mb-10">
                <div class="inline-flex p-4 bg-gradient-to-br from-red-500 to-orange-600 rounded-2xl shadow-lg mb-6 transform hover:rotate-6 transition-transform">
                    <i class="fas fa-fire text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">HOT POT</h1>
                <p class="text-slate-500 mt-2 font-medium">Welcome back! Please login.</p>
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                @csrf
                
                @if (session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg text-sm mb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-exclamation-triangle font-bold"></i>
                            <span class="font-bold">Opps!</span>
                        </div>
                        <p class="opacity-90">{{ session('error') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg text-sm animate-pulse mb-4">
                        <div class="flex items-center gap-2 mb-1">
                            <i class="fas fa-exclamation-circle font-bold"></i>
                            <span class="font-bold">Login Failed</span>
                        </div>
                        <ul class="list-disc list-inside opacity-90">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-2">
                    <label class="block text-slate-700 text-sm font-bold ml-1" for="email">
                        Username / Email
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fas fa-user-circle"></i>
                        </span>
                        <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-11 pr-4 text-slate-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400" 
                               id="email" type="text" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Username atau Email">
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center ml-1">
                        <label class="block text-slate-700 text-sm font-bold" for="password">
                            Password
                        </label>
                        <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-800 transition">Forgot?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3.5 pl-11 pr-4 text-slate-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400" 
                               id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••••••">
                    </div>
                </div>

                <button class="w-full bg-slate-900 hover:bg-black text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform active:scale-[0.98] transition-all flex items-center justify-center gap-2 group" 
                        type="submit">
                    Sign In 
                    <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white/80 text-slate-500">Atau login dengan</span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('google.login') }}" class="w-full bg-white hover:bg-slate-50 text-slate-700 font-bold py-4 px-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transform active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                        <svg class="h-5 w-5" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            <path d="M1 1h22v22H1z" fill="none"/>
                        </svg>
                        Google
                    </a>
                </div>
            </div>
        </div>
        
        <div class="mt-8 text-center text-sm text-slate-400 font-medium">
            &copy; 2026 HOT POT Manager. Built with <i class="fas fa-heart text-red-400"></i> by DEPOOTCOM.
        </div>
    </div>

    <script>
        // PWA Fix: Reload if loaded from browser cache (back button or PWA resume)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        // Auto force-refresh page every 110 minutes to prevent CSRF timeout (Default expires in 120m)
        setTimeout(function() {
            window.location.reload();
        }, 110 * 60 * 1000);
    </script>
</body>
</html>
