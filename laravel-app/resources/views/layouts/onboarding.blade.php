@php
    $accentColor = \App\Models\AdminSetting::get('accent_color', '#10b981');
@endphp
<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — NEX SHAPE</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap">
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root { --accent: {{ $accentColor }}; }
        body { background-color: #080a0f !important; font-family: 'Outfit', sans-serif; overflow-x: hidden; }
        
        [x-cloak] { display: none !important; }

        @keyframes fade-up {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fade-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>
<body class="bg-[#080a0f] text-white selection:bg-emerald-500/30 selection:text-emerald-400">
    <div class="min-h-screen flex flex-col relative overflow-hidden">
        
        <!-- Premium Ambient Glows -->
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none"></div>
        
        <!-- Global Grid Overlay -->
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 40px 40px;"></div>

        <header class="max-w-6xl mx-auto w-full flex justify-between items-center px-6 h-24 relative z-50">
            <div class="flex items-center gap-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20 transform group-hover:-rotate-6 transition-transform">
                        <i data-lucide="shield-check" class="w-6 h-6 text-zinc-950"></i>
                    </div>
                    <h1 class="text-xl font-black text-white tracking-tighter uppercase italic group-hover:text-emerald-500 transition-colors">nexshape</h1>
                </a>
                
                @hasSection('back_route')
                    <a href="@yield('back_route')" class="hidden md:flex items-center gap-2 text-[9px] font-black text-zinc-500 uppercase tracking-[0.3em] hover:text-white transition-all group">
                        <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                        Voltar
                    </a>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 px-4 py-2 rounded-xl flex items-center gap-3">
                    <span class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.2em]">Sessão: <span class="text-white">@yield('step_number')</span></span>
                    <div class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></div>
                </div>
                <div class="w-10 h-10 rounded-full bg-zinc-900 border border-zinc-800 flex items-center justify-center text-emerald-500 shadow-xl">
                    <i data-lucide="user" class="w-5 h-5"></i>
                </div>
            </div>
        </header>

        <main class="max-w-xl mx-auto w-full px-6 flex-1 flex flex-col justify-center relative z-10 py-12">
            @yield('onboarding_content')
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
</body>
</html>
