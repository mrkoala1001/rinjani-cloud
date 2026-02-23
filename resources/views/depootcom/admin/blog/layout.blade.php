<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - Depootcom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f1f5f9; color: #1e293b; }
        .sidebar { background: #1e293b; color: #f8fafc; }
        .nav-link { transition: all 0.2s; }
        .nav-link:hover { background: rgba(255,255,255,0.05); }
        .nav-link.active { background: #3b82f6; color: white; }
    </style>
    @stack('styles')
</head>
<body class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 sidebar flex flex-col shadow-xl flex-shrink-0">
        <div class="p-6 border-b border-slate-700">
            <h1 class="text-xl font-bold tracking-tighter uppercase">
                Depoot<span class="text-blue-400">Blog</span>
            </h1>
        </div>
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="{{ route('depootcom.admin.dashboard') }}" class="nav-link {{ request()->routeIs('depootcom.admin.dashboard') ? 'active' : '' }} flex items-center p-3 rounded-lg text-sm font-semibold">
                <i class="fas fa-chart-pie w-6"></i> Dashboard
            </a>
            <a href="{{ route('depootcom.admin.blog.index') }}" class="nav-link {{ request()->routeIs('depootcom.admin.blog.*') ? 'active' : '' }} flex items-center p-3 rounded-lg text-sm font-semibold">
                <i class="fas fa-file-alt w-6"></i> Posts
            </a>
            <a href="{{ route('depootcom.admin.categories.index') }}" class="nav-link {{ request()->routeIs('depootcom.admin.categories.*') ? 'active' : '' }} flex items-center p-3 rounded-lg text-sm font-semibold">
                <i class="fas fa-tags w-6"></i> Categories
            </a>
            <a href="/" class="nav-link flex items-center p-3 rounded-lg text-sm font-semibold mt-auto opacity-60">
                <i class="fas fa-external-link-alt w-6"></i> Lihat Web
            </a>
        </nav>
        <div class="p-4 border-t border-slate-700">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center p-3 text-red-400 hover:bg-red-400/10 rounded-lg text-sm font-semibold">
                    <i class="fas fa-sign-out-alt w-6"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Header -->
        <header class="bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center h-16 flex-shrink-0">
            <h2 class="font-bold text-slate-500 uppercase tracking-widest text-[10px]">Portal / @yield('page_title', 'Admin')</h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-bold text-slate-700">{{ auth()->user()->name }}</span>
                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            @if(session('success'))
                <div class="max-w-6xl mx-auto mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm flex justify-between items-center">
                    <p class="text-green-700 font-bold text-sm">{{ session('success') }}</p>
                    <i class="fas fa-check-circle text-green-500"></i>
                </div>
            @endif

            <div class="max-w-6xl mx-auto">
                @yield('content')
            </div>
        </div>
    </main>

    @stack('scripts')
</body>
</html>
