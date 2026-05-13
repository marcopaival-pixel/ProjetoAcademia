@php
    $accentColor = \App\Models\AdminSetting::get('accent_color', '#10b981');
    $loggedIn = auth()->check();
    $customLogo = \App\Models\AdminSetting::get('logo_url', '');
@endphp
<!DOCTYPE html>
<html lang="pt-PT" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#080a0f">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap">
    <title>Painel Admin - @yield('title', 'Dashboard') — NEX SHAPE PRO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/sidebar-toggle.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --accent: {{ $accentColor }};
            --accent-dim: {{ $accentColor }}cc;
        }
        body.admin-panel-body {
            background-color: #080a0f;
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.05) 0, transparent 40%),
                radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
            background-attachment: fixed;
            color: #fafafa;
            font-family: 'Outfit', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 2.5rem;
        }

        .main-area {
            background: transparent;
        }

        .content-wrapper {
            padding: 2.5rem;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Standardize scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.2); }
    </style>
    @stack('styles')
</head>
<body class="admin-panel-body">
    @include('partials.impersonation-banner')
    <div class="app-container">
        @include('partials.admin-sidebar')

        <div class="main-area">
            @include('partials.topbar')

            <main id="main" class="content-wrapper">
                <div class="flex flex-wrap gap-6 justify-between items-center mb-10">
                    <div class="flex items-center gap-5 min-w-0 flex-1">
                        <button type="button"
                            class="shrink-0 flex items-center justify-center w-12 h-12 rounded-2xl bg-zinc-950 border border-white/5 text-zinc-500 hover:text-emerald-500 hover:border-emerald-500/20 transition-all shadow-xl"
                            title="Voltar à página anterior"
                            aria-label="Voltar à página anterior"
                            data-fallback-url="{{ route('admin.dashboard') }}"
                            onclick="adminLayoutGoBack(this)">
                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                        </button>
                        <div>
                            <h1 class="text-2xl font-bold text-white tracking-tight truncate leading-none">@yield('title')</h1>
                            <p class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em] mt-2 italic">NexShape <span class="text-emerald-500">Admin Intelligence</span></p>
                        </div>
                    </div>
                    
                    @stack('header_actions')
                </div>

                <div class="animate-fade-in-up">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Alpine.js Plugins -->
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js" defer></script>
    <!-- Alpine.js Core (load after plugins) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        function adminLayoutGoBack(btn) {
            var fallback = (btn && btn.getAttribute('data-fallback-url')) || @json(route('admin.dashboard'));
            if (window.history.length > 1) {
                window.history.back();
                return;
            }
            window.location.href = fallback;
        }
    </script>
    @include('partials.confirm-delete-modal')
    @include('partials.confirm-action-modal')
    @include('partials.toast')

    @stack('scripts')
</body>
</html>

