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
