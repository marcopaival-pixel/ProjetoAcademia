<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Core Intelligence — Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            background-color: #080a0f;
            font-family: 'Outfit', sans-serif;
            background-image: 
                radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.08) 0, transparent 40%),
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.05) 0, transparent 40%);
            background-attachment: fixed;
            -webkit-font-smoothing: antialiased;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 3.5rem;
        }
        .input-glow:focus {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.1);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 relative overflow-hidden selection:bg-emerald-500/30 selection:text-emerald-500">
    
    <!-- Background Decor -->
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-[120px] animate-pulse pointer-events-none"></div>

    <div class="w-full max-w-[480px] relative z-10 animate-fade-in">
        
        <!-- Logo Area -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-[2.5rem] bg-zinc-950 border border-white/10 mb-8 shadow-2xl relative group">
                <div class="absolute inset-0 bg-emerald-500/20 rounded-[2.5rem] blur-xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="relative w-12 h-12 bg-emerald-500 text-zinc-950 rounded-2xl flex items-center justify-center rotate-3 group-hover:rotate-0 transition-transform duration-500">
                    <i data-lucide="zap" class="w-7 h-7 fill-current"></i>
                </div>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic leading-none">
                NEX<span class="text-emerald-500">SHAPE</span>
            </h1>
            <div class="flex items-center justify-center gap-3 mt-3">
                <span class="h-px w-6 bg-zinc-800"></span>
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.4em] italic">Core Intelligence</span>
                <span class="h-px w-6 bg-zinc-800"></span>
            </div>
        </div>

        <div class="glass-card p-12 shadow-3xl relative overflow-hidden">
            <!-- Subtle internal glow -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/5 rounded-full blur-3xl"></div>

            @if($errors->any())
                <div class="mb-8 p-5 bg-rose-500/10 border border-rose-500/20 rounded-3xl text-rose-500 text-[10px] font-black uppercase tracking-widest flex items-center gap-4 animate-shake">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 flex items-center justify-center shrink-0">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    </div>
                    <span>Credenciais inválidas. Verifique os dados e tente novamente.</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-8 p-5 bg-emerald-500/10 border border-emerald-500/20 rounded-3xl text-emerald-500 text-[10px] font-black uppercase tracking-widest flex items-center gap-4">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                    </div>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-8">
                @csrf
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between px-1">
                        <label for="email" class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Identidade Master</label>
                        <i data-lucide="user-cog" class="w-3 h-3 text-zinc-700"></i>
                    </div>
                    <input type="email" id="email" name="email" required autofocus 
                        class="w-full bg-zinc-950/50 border border-white/5 rounded-[1.5rem] px-6 py-5 text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all placeholder:text-zinc-800 input-glow"
                        placeholder="admin@nexshape.pro">
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between px-1">
                        <label for="password" class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Chave Críptica</label>
                        <i data-lucide="key-round" class="w-3 h-3 text-zinc-700"></i>
                    </div>
                    <div class="relative group">
                        <input type="password" id="password" name="password" required 
                            class="w-full bg-zinc-950/50 border border-white/5 rounded-[1.5rem] px-6 py-5 text-white text-sm outline-none focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all placeholder:text-zinc-800 input-glow"
                            placeholder="••••••••••••">
                        <button type="button" onclick="togglePass()" class="absolute right-6 top-1/2 -translate-y-1/2 text-zinc-700 hover:text-emerald-500 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-6 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-[1.8rem] transition-all active:scale-[0.97] shadow-2xl shadow-emerald-500/20 uppercase tracking-[0.2em] text-xs flex items-center justify-center gap-3 group">
                        <span>Aceder ao Núcleo</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>

            <div class="mt-12 text-center border-t border-white/5 pt-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 text-[10px] font-black text-zinc-600 hover:text-white transition-all uppercase tracking-[0.2em] group">
                    <i data-lucide="chevron-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                    <span>Voltar ao Portal Público</span>
                </a>
            </div>
        </div>
        
        <div class="mt-12 flex flex-col items-center gap-4">
            <p class="text-[10px] font-black text-zinc-700 uppercase tracking-[0.5em] italic">
                Secure Environment v2.4
            </p>
            <div class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/20"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/40"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500/60"></span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function togglePass() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>

    <style>
        .animate-fade-in { animation: fadeIn 1s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .animate-shake { animation: shake 0.4s ease-in-out; }
    </style>
</body>
</html>
