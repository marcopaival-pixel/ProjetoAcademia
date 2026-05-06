<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acesso Restrito</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #09090b;
            color: #fafafa;
            overflow: hidden;
        }
        .glass {
            background: rgba(18, 18, 21, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glow-amber {
            box-shadow: 0 0 50px -10px rgba(245, 158, 11, 0.2);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <!-- Background Decor -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -right-1/4 w-1/2 h-1/2 bg-amber-500/10 blur-[120px]"></div>
    </div>

    <div class="relative w-full max-w-lg animate-in fade-in zoom-in duration-700">
        <div class="glass p-12 rounded-[3rem] text-center shadow-2xl glow-amber border-amber-500/10">
            <!-- Icon -->
            <div class="relative inline-block mb-10">
                <div class="absolute inset-0 bg-amber-500/20 blur-2xl rounded-full"></div>
                <div class="relative w-24 h-24 bg-zinc-900 border border-amber-500/20 rounded-[2.5rem] flex items-center justify-center text-amber-500 shadow-inner">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m0 0v2m0-2h2m-2 0H10m4-11a4 4 0 11-8 0 4 4 0 018 0zM11 20H3L5 20a4 4 0 013-1l.5.5A4 4 0 0110 18v-2h2a2 2 0 012 2v2z"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-black text-white mb-6 tracking-tight uppercase">Acesso Restrito</h1>
            
            <p class="text-zinc-400 font-medium text-base leading-relaxed mb-10">
                {{ $exception->getMessage() ?: 'Lamentamos, mas esta área é reservada exclusivamente a administradores do projeto.' }}
            </p>

            <div class="flex flex-col gap-4">
                @if(auth()->check() && !auth()->user()->isAdministrator())
                    <a href="{{ route('dashboard') }}" class="w-full bg-zinc-100 hover:bg-white text-zinc-950 font-black py-4 rounded-2xl transition-all shadow-lg uppercase tracking-widest text-xs flex items-center justify-center gap-2">
                        Voltar ao Início
                    </a>
                @else
                    <a href="{{ route('admin.login') }}" class="w-full bg-amber-600 hover:bg-amber-500 text-zinc-950 font-black py-4 rounded-2xl transition-all shadow-lg shadow-amber-600/20 uppercase tracking-widest text-xs">
                        Autenticar como Administrador
                    </a>
                @endif
            </div>

            <div class="mt-12 pt-8 border-t border-white/5">
                <div class="flex items-center justify-center gap-3">
                    <span class="text-[9px] font-bold text-zinc-700 uppercase tracking-widest">Código do Erro</span>
                    <code class="text-[9px] font-black text-amber-500 bg-amber-500/5 px-2 py-0.5 rounded border border-amber-500/10">403</code>
                </div>
            </div>
        </div>

        <p class="mt-8 text-center text-[10px] font-black text-zinc-800 uppercase tracking-[0.3em]">
            &copy; {{ date('Y') }} NEXSHAPE PLATFORM
        </p>
    </div>
</body>
</html>
