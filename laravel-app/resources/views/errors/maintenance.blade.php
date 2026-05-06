<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manutenção em Curso - NexShape</title>
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
        .glow-indigo {
            box-shadow: 0 0 50px -10px rgba(99, 102, 241, 0.2);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .float {
            animation: float 6s ease-in-out infinite;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">
    <!-- Background Decor -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-1/4 -left-1/4 w-1/2 h-1/2 bg-indigo-500/10 blur-[120px]"></div>
        <div class="absolute -bottom-1/4 -right-1/4 w-1/2 h-1/2 bg-purple-500/10 blur-[120px]"></div>
    </div>

    <div class="relative w-full max-w-lg animate-in fade-in zoom-in duration-1000">
        <div class="glass p-12 rounded-[3rem] text-center shadow-2xl glow-indigo">
            <!-- Animated Icon -->
            <div class="relative inline-block mb-10 float">
                <div class="absolute inset-0 bg-indigo-500/20 blur-2xl rounded-full"></div>
                <div class="relative w-24 h-24 bg-zinc-900 border border-indigo-500/20 rounded-[2.5rem] flex items-center justify-center text-indigo-400 shadow-inner">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"></path>
                    </svg>
                </div>
            </div>

            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 mb-6">
                <div class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(99,102,241,0.5)]"></div>
                <span class="text-[10px] font-black uppercase tracking-widest">Atualização Programada</span>
            </div>

            <h1 class="text-3xl font-black text-white mb-6 tracking-tight uppercase leading-tight">Estamos aprimorando <br>sua experiência</h1>
            
            <p class="text-zinc-400 font-medium text-base leading-relaxed mb-10">
                {{ $message ?? 'O sistema está em manutenção programada para implementação de novas funcionalidades e melhorias de segurança. Voltaremos em breve!' }}
            </p>

            <div class="bg-white/5 border border-white/5 rounded-2xl p-4 flex items-center justify-center gap-4">
                <div class="text-left border-r border-white/10 pr-4">
                    <p class="text-[8px] font-black text-zinc-500 uppercase tracking-widest mb-1">Status</p>
                    <p class="text-[10px] font-bold text-white uppercase tracking-widest">Deploying v2.4</p>
                </div>
                <div class="text-left">
                    <p class="text-[8px] font-black text-zinc-500 uppercase tracking-widest mb-1">Previsão</p>
                    <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">~15 Minutos</p>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-white/5">
                <p class="text-[10px] font-black text-zinc-700 uppercase tracking-[0.3em]">
                    &copy; {{ date('Y') }} NEXSHAPE PLATFORM
                </p>
            </div>
        </div>
    </div>
</body>
</html>
