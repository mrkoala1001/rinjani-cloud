<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - KOALA Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
        }
        .animated-bg {
            background: linear-gradient(-45deg, #1e293b, #0f172a, #1d4ed8, #1e1b4b);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glow {
            box-shadow: 0 0 50px rgba(59, 130, 246, 0.5);
        }
    </style>
</head>
<body class="animated-bg min-h-screen flex items-center justify-center p-6 text-white text-center">
    <div class="max-w-xl w-full">
        <div class="mb-8 float inline-block">
            <div class="w-24 h-24 bg-blue-600 rounded-3xl flex items-center justify-center shadow-2xl glow transform rotate-12">
                <i class="fas @yield('icon') text-4xl"></i>
            </div>
        </div>
        
        <h1 class="text-8xl font-black mb-4 tracking-tighter opacity-20">@yield('code')</h1>
        
        <div class="glass p-8 md:p-12 rounded-[2.5rem] shadow-2xl relative z-10">
            <h2 class="text-3xl md:text-4xl font-extrabold mb-6 leading-tight">
                @yield('message')
            </h2>
            
            <p class="text-slate-400 mb-10 font-medium">
                @yield('description')
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-2xl transition-all hover:scale-105 active:scale-95 shadow-lg shadow-blue-500/25">
                    <i class="fas fa-home mr-2"></i>Kembali ke Dashboard
                </a>
                @yield('extra_button')
            </div>
        </div>
        
        <div class="mt-8 text-slate-500 text-sm font-bold flex items-center justify-center gap-2">
            <i class="fas fa-microchip"></i>
            <span>KOALA NETWORK CORE v2</span>
        </div>
    </div>
</body>
</html>
