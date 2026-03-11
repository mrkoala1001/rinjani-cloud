<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - HOT POT Manager</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 font-sans text-slate-900 min-h-screen flex justify-center items-center p-6 antialiased">
    
    <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-blue-400/10 rounded-full blur-[100px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-orange-400/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="w-full max-w-md">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl shadow-slate-200/50 border border-white p-8 md:p-10 transform -translate-y-4">
            
            <div class="text-center mb-8">
                <div class="inline-flex p-4 bg-gradient-to-br from-green-400 to-blue-600 rounded-2xl shadow-lg mb-6 transform hover:rotate-6 transition-transform">
                    <i class="fas fa-lock-open text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Password Baru</h1>
                <p class="text-slate-500 mt-2 font-medium">Buat password baru yang kuat untuk akun Anda.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg text-sm mb-6">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update.custom') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="whatsapp" value="{{ $whatsapp }}">
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="space-y-2">
                    <label class="block text-slate-700 text-sm font-bold ml-1" for="password">
                        Password Baru
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fas fa-key"></i>
                        </span>
                        <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-4 pl-11 pr-4 text-slate-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400" 
                               id="password" type="password" name="password" required autofocus placeholder="Minimal 8 karakter">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-slate-700 text-sm font-bold ml-1" for="password_confirmation">
                        Konfirmasi Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                            <i class="fas fa-check-double"></i>
                        </span>
                        <input class="w-full bg-slate-50 border border-slate-200 rounded-xl py-4 pl-11 pr-4 text-slate-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-400" 
                               id="password_confirmation" type="password" name="password_confirmation" required placeholder="Ulangi password baru">
                    </div>
                </div>

                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform active:scale-[0.98] transition-all flex items-center justify-center gap-3" 
                        type="submit">
                    Simpan & Login
                    <i class="fas fa-save text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
