<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NexShape Premium Onboarding — @yield('title', 'Bem-vindo')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;300;400;600;700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        :root {
            --primary: #3b82f6;
            --accent: #10b981;
            --bg-dark: #09090b;
            --card-bg: rgba(24, 24, 27, 0.6);
            --border: rgba(255, 255, 255, 0.08);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: #d4d4d8;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: var(--card-bg);
            backdrop-filter: blur(24px);
            border: 1px solid var(--border);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .glass-hover:hover {
            border-color: rgba(255, 255, 255, 0.15);
            background: rgba(24, 24, 27, 0.8);
        }

        .step-pill {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .step-pill.active {
            background: linear-gradient(135deg, var(--primary) 0%, #1d4ed8 100%);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
            transform: scale(1.05);
        }

        .progress-container {
            height: 6px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
            transition: width 0.8s cubic-bezier(0.65, 0, 0.35, 1);
        }

        .input-premium {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            color: white;
            transition: all 0.3s ease;
        }

        .input-premium:focus {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .btn-premium {
            background: linear-gradient(135deg, var(--primary) 0%, #1d4ed8 100%);
            color: white;
            padding: 1.25rem 2.5rem;
            border-radius: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.4s ease;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.3);
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(59, 130, 246, 0.4);
            filter: brightness(1.1);
        }

        .btn-premium:active {
            transform: translateY(0);
        }

        .card-type {
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }

        .card-type.selected {
            border-color: var(--primary);
            background: rgba(59, 130, 246, 0.05);
            transform: scale(1.02);
        }

        .bg-mesh {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: 
                radial-gradient(circle at 0% 0%, rgba(59, 130, 246, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 100% 100%, rgba(16, 185, 129, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(0, 0, 0, 1) 0%, var(--bg-dark) 100%);
        }

        .animate-in {
            animation: slideIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen">
    <div class="bg-mesh"></div>

    <nav class="sticky top-0 z-50 py-6 px-8 flex items-center justify-between backdrop-blur-md border-b border-white/5">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-emerald-500 rounded-2xl flex items-center justify-center shadow-2xl">
                <i class="fas fa-rocket text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-white font-black text-xl tracking-tight">NexShape</h1>
                <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-[2px]">Premium Onboarding</p>
            </div>
        </div>

        <div class="hidden lg:flex items-center gap-3">
            @for($i = 1; $i <= 7; $i++)
                <div class="flex items-center">
                    <div class="step-pill w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                        {{ ($currentStep ?? 1) == $i ? 'active text-white' : (($currentStep ?? 1) > $i ? 'bg-emerald-500/20 text-emerald-500 border border-emerald-500/30' : 'bg-white/5 text-zinc-600 border border-white/5') }}">
                        @if(($currentStep ?? 1) > $i)
                            <i class="fas fa-check"></i>
                        @else
                            {{ $i }}
                        @endif
                    </div>
                    @if($i < 7)
                        <div class="w-8 h-[2px] {{ ($currentStep ?? 1) > $i ? 'bg-emerald-500/30' : 'bg-white/5' }}"></div>
                    @endif
                </div>
            @endfor
        </div>

        <div class="flex items-center gap-6">
            <div class="hidden sm:block text-right">
                <p class="text-[10px] text-zinc-500 uppercase font-black tracking-widest">Sessão Segura</p>
                <p class="text-xs text-white font-medium">AES-256 SSL</p>
            </div>
            <a href="/" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-zinc-500 hover:text-white hover:bg-white/10 transition-all">
                <i class="fas fa-times"></i>
            </a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-12 lg:py-20 flex flex-col items-center">
        <div class="w-full max-w-4xl">
            <!-- Header do Passo -->
            <div class="mb-12 animate-in">
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-full border border-blue-500/20">
                        Etapa {{ $currentStep ?? 1 }} de 7
                    </span>
                    <div class="progress-container flex-grow">
                        <div class="progress-bar" style="width: {{ (($currentStep ?? 1) / 7) * 100 }}%"></div>
                    </div>
                </div>
                <h2 class="text-4xl lg:text-5xl font-black text-white mb-4">@yield('step_title')</h2>
                <p class="text-zinc-400 text-lg lg:max-w-2xl leading-relaxed">@yield('step_description')</p>
            </div>

            <!-- Conteúdo do Formulário -->
            <div class="glass rounded-[40px] p-8 lg:p-16 animate-in" style="animation-delay: 0.1s">
                @if(session('error'))
                    <div class="mb-8 p-6 bg-red-500/10 border border-red-500/20 rounded-3xl flex items-center text-red-400">
                        <i class="fas fa-exclamation-triangle mr-4 text-xl"></i>
                        <p class="font-medium">{{ session('error') }}</p>
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Footer -->
            <div class="mt-12 flex flex-col sm:flex-row items-center justify-between gap-6 px-4 text-zinc-500 text-sm">
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-2"><i class="fas fa-lock text-emerald-500/50"></i> LGPD Compliant</span>
                    <span class="flex items-center gap-2"><i class="fas fa-cloud text-blue-500/50"></i> Amazon AWS S3</span>
                </div>
                <div class="flex items-center gap-4">
                    <a href="#" class="hover:text-white transition-colors">Termos</a>
                    <span class="w-1 h-1 rounded-full bg-zinc-800"></span>
                    <a href="#" class="hover:text-white transition-colors">Privacidade</a>
                    <span class="w-1 h-1 rounded-full bg-zinc-800"></span>
                    <span class="text-zinc-600">NexShape v3.0</span>
                </div>
            </div>
        </div>
    </main>

    @stack('scripts')
    <script>
        // Forçar desregistração de service workers no onboarding para evitar erros de cache/rede
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for(let registration of registrations) {
                    registration.unregister();
                }
            });
        }
    </script>
    @include('partials.js-masks')
</body>
</html>
