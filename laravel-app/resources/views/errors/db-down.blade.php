<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instabilidade Técnica - NexShape</title>
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
        .glow-emerald {
            box-shadow: 0 0 50px -10px rgba(16, 185, 129, 0.2);
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.05); }
        }
        .pulse-bg {
            animation: pulse-slow 8s infinite ease-in-out;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <!-- Background Decor -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -left-1/4 w-1/2 h-1/2 bg-emerald-500/10 blur-[120px] pulse-bg"></div>
        <div class="absolute -bottom-1/4 -right-1/4 w-1/2 h-1/2 bg-blue-500/10 blur-[120px] pulse-bg" style="animation-delay: -4s"></div>
    </div>

    <div class="relative w-full max-w-lg animate-in fade-in slide-in-from-bottom-8 duration-1000">
        <div class="glass p-12 rounded-[3rem] text-center shadow-2xl glow-emerald">
            <!-- Icon with Glow -->
            <div class="relative inline-block mb-10">
                <div class="absolute inset-0 bg-emerald-500/20 blur-2xl rounded-full"></div>
                <div class="relative w-24 h-24 bg-zinc-900 border border-emerald-500/20 rounded-[2rem] flex items-center justify-center text-emerald-500 shadow-inner">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <!-- Status Badge -->
                <div class="absolute -bottom-2 -right-2 bg-zinc-950 border border-emerald-500/20 px-3 py-1 rounded-full flex items-center gap-2 shadow-xl">
                    <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Recuperando</span>
                </div>
            </div>

            <h1 class="text-4xl font-black text-white mb-6 tracking-tight uppercase italic italic">Conexão interrompida</h1>
            
            <p class="text-zinc-400 font-medium text-lg leading-relaxed mb-10">
                Estamos enfrentando uma instabilidade temporária na nossa base de dados. <br>
                <span class="text-zinc-500 text-sm">Nossa equipe técnica já foi alertada e está trabalhando na recuperação imediata.</span>
            </p>

            <div class="flex flex-col gap-4">
                <button onclick="location.reload()" class="w-full bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-black py-4 rounded-2xl transition-all shadow-lg shadow-emerald-600/20 uppercase tracking-widest text-xs">
                    Tentar Novamente
                </button>
                
                <a href="https://status.nexshape.com.br" class="text-[10px] font-black text-zinc-600 hover:text-white uppercase tracking-[0.2em] transition-colors">
                    Ver Status do Sistema
                </a>
            </div>

            <div class="mt-12 pt-8 border-t border-white/5">
                <div class="flex items-center justify-center gap-3">
                    <span class="text-[10px] font-bold text-zinc-700 uppercase tracking-widest">ID do Incidente</span>
                    <code class="text-[10px] font-black text-zinc-400 bg-white/5 px-2 py-0.5 rounded border border-white/5 uppercase">DB_{{ time() }}</code>
                </div>
            </div>
        </div>

        <p class="mt-8 text-center text-[10px] font-black text-zinc-800 uppercase tracking-[0.3em]">
            &copy; {{ date('Y') }} NEXSHAPE PLATFORM
        </p>
    </div>
</body>
</html>
