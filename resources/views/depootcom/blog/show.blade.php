<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} - Depootcom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #0c0f1d; color: #f8fafc; }
        .glass { background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .prose { color: #94a3b8; max-width: 100%; }
        .prose h1, .prose h2, .prose h3 { color: white; font-weight: 900; }
        .prose strong { color: #ec4899; }
        .gradient-text { background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body class="overflow-x-hidden">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 top-0 left-0 glass border-b border-white/5 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <a href="/" class="text-2xl font-bold tracking-tighter flex items-center">
                <span class="w-8 h-8 rounded-lg bg-pink-500 mr-3 flex items-center justify-center text-white text-sm shadow-lg shadow-pink-500/20">D</span>
                DEPOOT<span class="text-pink-500">COM</span>
            </a>
            <div class="hidden lg:flex space-x-10 items-center font-semibold text-sm uppercase tracking-wide">
                <a href="/" class="hover:text-pink-400 transition-colors">Beranda</a>
                <a href="/blog" class="hover:text-pink-400 transition-colors">Blog</a>
            </div>
            <a href="/" class="text-xs font-black uppercase tracking-widest text-slate-500"><i class="fas fa-arrow-left mr-2"></i> Kembali</a>
        </div>
    </nav>

    <main class="pt-40 pb-40 px-6">
        <article class="max-w-4xl mx-auto">
            <header class="mb-12 space-y-6">
                <div class="flex items-center space-x-4 text-[10px] font-black uppercase tracking-[0.3em] text-pink-500">
                    <span>{{ $post->published_at ? $post->published_at->format('d M Y') : 'Baru Terbit' }}</span>
                </div>
                <h1 class="text-5xl md:text-7xl font-black leading-tight">{{ $post->title }}</h1>
                @if($post->excerpt)
                <p class="text-xl text-slate-400 italic leading-relaxed">{{ $post->excerpt }}</p>
                @endif

                @if($post->featured_image)
                <div class="pt-8">
                    <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-full h-auto rounded-[2.5rem] shadow-2xl shadow-blue-500/10 border border-white/5" alt="{{ $post->title }}">
                </div>
                @endif
            </header>

            <div class="prose text-lg leading-relaxed space-y-8">
                {!! nl2br($post->content) !!}
            </div>

            <footer class="mt-20 pt-10 border-t border-white/5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-500">Penulis</p>
                        <p class="text-sm font-bold text-white mt-1">{{ $post->author->name ?? 'Admin Depootcom' }}</p>
                    </div>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 glass rounded-xl flex items-center justify-center hover:text-pink-500 transition-all"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-10 h-10 glass rounded-xl flex items-center justify-center hover:text-pink-500 transition-all"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </footer>
        </article>
    </main>

    <footer class="py-12 border-t border-white/5 text-center text-[10px] font-black uppercase tracking-widest text-slate-600">
        <p>&copy; 2026 DEPOOTCOM. Solusi Masa Depan.</p>
    </footer>

</body>
</html>
