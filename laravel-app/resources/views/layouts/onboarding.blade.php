@php
    $accentColor = \App\Models\AdminSetting::get('accent_color', '#3d9cf5');
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — NexShape</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        :root {
            --accent: {{ $accentColor }};
        }
        body { background-color: #0b0e14 !important; overflow-x: hidden; }
        @keyframes fade-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fade-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>
</head>
<body class="bg-[#0b0e14]">
    <div class="min-h-screen bg-[#0b0e14] text-white flex flex-col pt-12 px-6 relative overflow-hidden">
        <!-- Decorative Background Elements -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-blue-600/10 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-emerald-500/5 rounded-full blur-[100px] animate-pulse"></div>
        
        <!-- Global Grid Pattern -->
        <div class="absolute inset-0 opacity-[0.02] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 50px 50px;"></div>

        <header class="max-w-5xl mx-auto w-full flex justify-between items-center mb-12 relative z-10">
            <div class="flex items-center gap-6">
                <h1 class="text-2xl font-black text-blue-500 tracking-tighter italic">nexshape</h1>
                @hasSection('back_route')
                    <a href="@yield('back_route')" class="hidden md:flex items-center gap-2 text-[10px] font-black text-zinc-500 uppercase tracking-widest hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        Voltar
                    </a>
                @endif
            </div>
            <div class="flex items-center gap-4">
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.3em]">@yield('step_number')</span>
                <div class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-blue-400 backdrop-blur-md">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                </div>
            </div>
        </header>


        <main class="max-w-xl mx-auto w-full pt-10 relative z-10 pb-20">
            @yield('onboarding_content')
        </main>
    </div>
</body>
</html>
