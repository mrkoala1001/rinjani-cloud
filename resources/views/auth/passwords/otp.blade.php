<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - HOT POT Manager</title>
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
                <div class="inline-flex p-4 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg mb-6 transform hover:rotate-6 transition-transform">
                    <i class="fas fa-shield-alt text-white text-3xl"></i>
                </div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Verifikasi OTP</h1>
                <p class="text-slate-500 mt-2 font-medium">Masukkan 6 digit kode yang dikirim ke <br><span class="text-slate-800 font-bold">{{ $whatsapp }}</span></p>
            </div>

            @if(session('success'))
                <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-r-lg text-sm mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg text-sm mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.otp.verify') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="whatsapp" value="{{ $whatsapp }}">
                
                <div class="space-y-4">
                    <div class="relative">
                        <input class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-5 text-center text-3xl font-black tracking-[1em] text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all placeholder-slate-300" 
                               id="otp" type="text" name="otp" maxlength="6" required autofocus placeholder="000000">
                    </div>
                    <p class="text-center text-[11px] text-slate-400">Pastikan nomor WA Anda aktif dan tidak terblokir oleh provider.</p>
                </div>

                <button class="w-full bg-slate-900 hover:bg-black text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transform active:scale-[0.98] transition-all flex items-center justify-center gap-3" 
                        type="submit">
                    Verifikasi Kode
                    <i class="fas fa-check-circle text-sm"></i>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-sm text-slate-500">Tidak menerima kode? 
                    <a href="{{ route('password.request') }}" class="text-blue-600 font-bold hover:underline">Kirim ulang</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
