@extends('layouts.app') <!-- Placeholder if needed, but I'll write a standalone for now to match index.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DEPOOTCOM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #0c0f1d; color: #f8fafc; overflow: hidden; }
        .glass { background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .gradient-text { background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-primary { background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); transition: all 0.3s ease; }
        .btn-primary:hover { opacity: 0.9; transform: scale(1.02); box-shadow: 0 0 20px rgba(236, 72, 153, 0.4); }
        .blob { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.2; z-index: -1; animation: float 20s infinite alternate; }
        .blob-pink { background: #ec4899; width: 400px; height: 400px; top: -100px; right: -100px; }
        .blob-purple { background: #8b5cf6; width: 500px; height: 500px; bottom: -150px; left: -150px; }
        @keyframes float { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(40px, 40px) scale(1.1); } }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="blob blob-pink"></div>
    <div class="blob blob-purple"></div>

    <div class="w-full max-w-md" data-aos="fade-up">
        <div class="glass p-10 md:p-14 rounded-[3rem] border-white/10 shadow-2xl relative overflow-hidden">
            <!-- Decorative line -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-pink-500 to-purple-600"></div>
            
            <div class="text-center mb-10">
                <a href="{{ route('depootcom.landing') }}" class="text-2xl font-bold tracking-tighter flex items-center justify-center mb-6">
                    <span class="w-8 h-8 rounded-lg bg-pink-500 mr-3 flex items-center justify-center text-white text-sm shadow-lg shadow-pink-500/20">D</span>
                    DEPOOT<span class="text-pink-500">COM</span>
                </a>
                <h1 class="text-3xl font-black mb-2">Welcome Back</h1>
                <p class="text-slate-400 text-sm">Please enter your credentials to access the admin panel.</p>
            </div>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-2xl text-xs">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-2">
                    <label for="email" class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-2">Username / Email</label>
                    <div class="relative">
                        <i class="fas fa-user absolute left-6 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                        <input type="text" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-14 py-4 focus:outline-none focus:border-pink-500 transition-colors font-bold text-sm text-white placeholder-slate-600"
                            placeholder="username@depootcom.com">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-6 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            class="w-full bg-white/5 border border-white/10 rounded-2xl px-14 py-4 focus:outline-none focus:border-pink-500 transition-colors font-bold text-sm text-white placeholder-slate-600"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" class="w-full btn-primary py-5 rounded-2xl font-black uppercase text-sm tracking-[0.2em] shadow-xl text-white">
                    Sign In
                </button>
            </form>

            <div class="mt-10 text-center">
                <p class="text-[10px] text-slate-600 font-bold uppercase tracking-widest">
                    &copy; 2026 DEPOOTCOM. Bangun Solusi Tanpa Batas.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
