<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Depootcom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #0c0f1d; color: #f8fafc; }
        .glass { background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .glass-card { background: rgba(30, 41, 59, 0.3); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.4s; }
        .glass-card:hover { transform: translateY(-10px); border-color: rgba(236, 72, 153, 0.4); }
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
                <a href="/blog" class="text-pink-500">Blog</a>
            </div>
            <a href="/login" class="text-xs font-black uppercase tracking-widest text-slate-500 hover:text-pink-400"><i class="fas fa-lock mr-2"></i> Admin</a>
        </div>
    </nav>

    <!-- Header -->
    <header class="pt-40 pb-20 px-6">
        <div class="max-w-7xl mx-auto text-center" data-aos="fade-up">
            <h1 class="text-6xl md:text-8xl font-black mb-6 leading-none">The <span class="gradient-text">Lab</span> Notes</h1>
            <p class="text-slate-400 text-xl max-w-2xl mx-auto">Eksplorasi teknologi, tutorial, dan update terbaru dari ekosistem Depootcom.</p>
        </div>
    </header>

    <!-- Blog Grid -->
    <main class="max-w-7xl mx-auto px-6 pb-40">
        <div class="grid md:grid-cols-3 gap-8">
            @forelse($posts as $post)
            <article class="glass-card rounded-[2.5rem] overflow-hidden group" data-aos="fade-up">
                <div class="h-64 bg-slate-800 relative overflow-hidden">
                    <div class="absolute inset-0 flex items-center justify-center text-slate-700/50">
                        <i class="fas fa-newspaper text-8xl group-hover:scale-110 transition-transform duration-700"></i>
                    </div>
                </div>
                <div class="p-8 space-y-4">
                    <div class="flex items-center text-[10px] font-black uppercase tracking-widest text-pink-500">
                        <span>{{ $post->published_at ? $post->published_at->format('d M Y') : 'Baru' }}</span>
                    </div>
                    <h2 class="text-2xl font-black group-hover:text-pink-400 transition-colors">
                        <a href="{{ route('depootcom.blog.show', $post->slug) }}">{{ $post->title }}</a>
                    </h2>
                    <p class="text-slate-400 text-sm leading-relaxed line-clamp-3">
                        {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 150) }}
                    </p>
                    <div class="pt-4">
                        <a href="{{ route('depootcom.blog.show', $post->slug) }}" class="text-xs font-black uppercase tracking-[0.2em] flex items-center group-hover:translate-x-3 transition-transform">
                            Baca Selengkapnya <i class="fas fa-arrow-right ml-4 text-pink-500"></i>
                        </a>
                    </div>
                </div>
            </article>
            @empty
            <div class="col-span-3 py-20 text-center glass-card rounded-[3rem]">
                <i class="fas fa-terminal text-6xl text-slate-800 mb-6"></i>
                <h3 class="text-2xl font-black">Belum ada artikel.</h3>
                <p class="text-slate-500 mt-2">Nantikan update teknologi menarik dari kami segera!</p>
            </div>
            @endforelse
        </div>

        <div class="mt-20">
            {{ $posts->links() }}
        </div>
    </main>

    <footer class="py-12 border-t border-white/5 text-center text-[10px] font-black uppercase tracking-widest text-slate-600">
        <p>&copy; 2026 DEPOOTCOM. Bangun Solusi Tanpa Batas.</p>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init({ duration: 1000, once: true });</script>
</body>
</html>
