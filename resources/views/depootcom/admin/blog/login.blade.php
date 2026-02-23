<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Admin Login - The Lab Notes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #0c0f1d; color: #f8fafc; overflow: hidden; }
        .glass { background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .gradient-text { background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-primary { background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); transition: all 0.3s ease; }
        .btn-primary:hover { opacity: 0.9; transform: scale(1.02); box-shadow: 0 0 20px rgba(236, 72, 153, 0.4); }
        
        /* Lab Decorative Elements */
        .grid-bg { background-image: radial-gradient(rgba(236, 72, 153, 0.1) 1px, transparent 1px); background-size: 30px 30px; }
        .pink-ring { width: 300px; height: 300px; border: 2px dashed rgba(236, 72, 153, 0.2); border-radius: 50%; position: absolute; top: -150px; left: -150px; animation: spin 60s linear infinite; }
        
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 grid-bg">
    <div class="pink-ring"></div>

    <div class="w-full max-w-md relative" data-aos="fade-up">
        <div class="glass p-10 md:p-14 rounded-[4rem] border-white/10 shadow-2xl relative overflow-hidden">
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="inline-block px-4 py-1.5 rounded-full border border-pink-500/30 text-pink-400 text-[10px] font-black uppercase tracking-[0.3em] mb-6">
                    Restricted Access
                </div>
                <h1 class="text-4xl font-black mb-2">The <span class="gradient-text">Lab</span> Login</h1>
                <p class="text-slate-400 text-sm">Masuk untuk mengelola artikel dan riset di ekosistem blog Depootcom.</p>
            </div>

            <form action="{{ route('depootcom.login.post') }}" method="POST" class="space-y-6">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-500/10 border border-red-500/50 text-red-400 p-4 rounded-3xl text-xs">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-2">
                    <label for="email" class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-3">Identifier</label>
                    <div class="relative">
                        <i class="fas fa-fingerprint absolute left-6 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                        <input type="text" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                            class="w-full bg-white/5 border border-white/10 rounded-3xl px-14 py-4 focus:outline-none focus:border-pink-500 transition-colors font-bold text-sm text-white placeholder-slate-700"
                            placeholder="username / email">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-[10px] font-black uppercase tracking-widest text-slate-500 ml-3">Security Key</label>
                    <div class="relative">
                        <i class="fas fa-shield-halved absolute left-6 top-1/2 -translate-y-1/2 text-slate-500 text-sm"></i>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            class="w-full bg-white/5 border border-white/10 rounded-3xl px-14 py-4 focus:outline-none focus:border-pink-500 transition-colors font-bold text-sm text-white placeholder-slate-700"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full btn-primary py-5 rounded-3xl font-black uppercase text-sm tracking-[0.2em] shadow-xl text-white">
                        Authorize <i class="fas fa-arrow-right ml-2 text-xs"></i>
                    </button>
                </div>
            </form>

            <div class="mt-12 text-center">
                <a href="{{ route('depootcom.blog.index') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-pink-400 transition-colors">
                    <i class="fas fa-chevron-left mr-2"></i> Kembali ke Blog
                </a>
            </div>
        </div>
        
        <!-- Footer Info -->
        <div class="mt-8 text-center">
            <p class="text-[10px] text-slate-600 font-bold uppercase tracking-widest">
                System: DP-BLG-SYS v2.1 // DEPOOTCOM
            </p>
        </div>
    </div>
</body>
</html>
