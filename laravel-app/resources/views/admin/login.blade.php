<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NexShape Admin — Governança e Inteligência</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@700;800&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            background-color: #0b0e14;
            font-family: 'Inter', sans-serif;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .accent-text {
            background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-600/10 rounded-full blur-[120px] animate-pulse"></div>
    <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-emerald-500/5 rounded-full blur-[100px] animate-pulse"></div>
    <div class="absolute inset-0 opacity-[0.02] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 50px 50px;"></div>

    <div class="w-full max-w-md relative z-10 animate-[fadeIn_0.8s_ease-out]">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-zinc-900/50 border border-white/10 mb-6 shadow-2xl backdrop-blur-xl">
                <img src="{{ asset('images/logo_Academia.png') }}" class="h-10 w-auto" alt="NexShape">
            </div>
            <h1 class="text-3xl font-black text-white tracking-tighter italic">nex<span class="text-blue-500">shape</span> <span class="text-zinc-500 not-italic ml-2 font-normal text-xl tracking-normal">Admin</span></h1>
            <p class="text-zinc-500 text-sm mt-3 font-medium uppercase tracking-[0.2em]">Painel de Governança Central</p>
        </div>

        <div class="glass-card p-10 rounded-[2.5rem] shadow-3xl">
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-500 text-xs font-bold flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Credenciais divergentes. Acesso negado.
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-500 text-xs font-bold flex items-center gap-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label for="email" class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Identidade Administrativa</label>
                    <input type="email" id="email" name="email" required autofocus 
                        class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-800"
                        placeholder="admin@nexshape.com">
                </div>

                <div class="space-y-2">
                    <label for="password" class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Chave de Acesso</label>
                    <input type="password" id="password" name="password" required 
                        class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-800"
                        placeholder="••••••••••••">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-3xl transition-all active:scale-[0.98] shadow-2xl shadow-blue-600/20 uppercase tracking-widest text-xs">
                        Autenticar no Sistema
                    </button>
                </div>
            </form>

            <div class="mt-10 text-center border-t border-white/5 pt-8">
                <a href="{{ url('/') }}" class="text-[10px] font-black text-zinc-500 hover:text-white transition-colors uppercase tracking-[0.2em] flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Voltar para a Arena
                </a>
            </div>
        </div>
        
        <p class="text-center mt-8 text-[10px] font-medium text-zinc-600 uppercase tracking-widest">
            &copy; {{ date('Y') }} NexShape Intelligence Unit
        </p>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</body>
</html>
