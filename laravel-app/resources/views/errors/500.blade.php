<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro Interno - NexShape</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        .glow-rose {
            box-shadow: 0 0 50px -10px rgba(244, 63, 94, 0.2);
        }
        @keyframes glitch {
            0% { transform: translate(0); }
            20% { transform: translate(-2px, 2px); }
            40% { transform: translate(-2px, -2px); }
            60% { transform: translate(2px, 2px); }
            80% { transform: translate(2px, -2px); }
            100% { transform: translate(0); }
        }
        .glitch-hover:hover {
            animation: glitch 0.3s cubic-bezier(.25,.46,.45,.94) both infinite;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <!-- Background Decor -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-rose-500/5 blur-[150px]"></div>
    </div>

    <div class="relative w-full max-w-lg animate-in fade-in slide-in-from-top-4 duration-700">
        <div class="glass p-12 rounded-[3rem] text-center shadow-2xl glow-rose">
            <!-- Icon -->
            <div class="relative inline-block mb-10 glitch-hover">
                <div class="absolute inset-0 bg-rose-500/20 blur-2xl rounded-full"></div>
                <div class="relative w-24 h-24 bg-zinc-900 border border-rose-500/20 rounded-[2.5rem] flex items-center justify-center text-rose-500 shadow-inner">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-black text-white mb-6 tracking-tight uppercase">Algo saiu do esperado</h1>
            
            <p class="text-zinc-400 font-medium text-base leading-relaxed mb-10">
                Ocorreu um erro interno no processamento da sua solicitação. <br>
                <span class="text-zinc-500 text-sm italic">Já registramos os detalhes técnicos e nossa equipe está investigando a causa.</span>
            </p>

            <div class="flex flex-col gap-4">
                <a href="/" class="w-full bg-zinc-100 hover:bg-white text-zinc-950 font-black py-4 rounded-2xl transition-all shadow-lg uppercase tracking-widest text-xs flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Voltar ao Início
                </a>
            </div>

            <div class="mt-12 pt-8 border-t border-white/5 flex flex-col items-center gap-2">
                <div class="flex items-center gap-3">
                    <span class="text-[9px] font-bold text-zinc-700 uppercase tracking-widest">Código do Erro</span>
                    <code class="text-[9px] font-black text-rose-400 bg-rose-500/5 px-2 py-0.5 rounded border border-rose-500/10">500</code>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-[9px] font-bold text-zinc-700 uppercase tracking-widest">Referência</span>
                    <code class="text-[9px] font-black text-zinc-400 bg-white/5 px-2 py-0.5 rounded border border-white/5 uppercase">{{ substr(md5(time()), 0, 8) }}</code>
                </div>
            </div>
        </div>

        <p class="mt-8 text-center text-[10px] font-black text-zinc-800 uppercase tracking-[0.3em]">
            &copy; {{ date('Y') }} NEXSHAPE PLATFORM
        </p>
    </div>
</body>
</html>
