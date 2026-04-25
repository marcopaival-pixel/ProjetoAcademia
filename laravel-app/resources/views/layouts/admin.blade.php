@php
    $accentColor = \App\Models\AdminSetting::get('accent_color', '#238636');
    $loggedIn = auth()->check();
    $customLogo = \App\Models\AdminSetting::get('logo_url', '');
@endphp
<!DOCTYPE html>
<html lang="pt-PT" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modern-layout.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="{{ asset('js/sidebar-toggle.js') }}" defer></script>
    <style>
        :root {
            --accent: {{ $accentColor }};
            --accent-dim: {{ $accentColor }}cc;
        }
        body.admin-panel-body {
            background-color: #0a1128;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
        }
        .admin-panel-body .sidebar {
            border-right: 1px solid rgba(245, 158, 11, 0.1);
        }
        .admin-panel-body .nav-link.active {
            background: rgba(245, 158, 11, 0.1) !important;
            color: #f59e0b !important;
            border-left: 3px solid #f59e0b !important;
        }
        .admin-panel-body .topbar {
            border-bottom: 1px solid rgba(245, 158, 11, 0.1);
        }
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
                <div class="flex flex-wrap gap-4 justify-between items-center mb-8">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <button type="button"
                            class="shrink-0 flex items-center justify-center w-11 h-11 rounded-xl bg-zinc-900/80 border border-white/10 text-zinc-400 hover:text-amber-500 hover:border-amber-500/40 transition-all"
                            title="Voltar à página anterior"
                            aria-label="Voltar à página anterior"
                            data-fallback-url="{{ route('admin.dashboard') }}"
                            onclick="adminLayoutGoBack(this)">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <h1 class="text-3xl font-bold truncate">@yield('title')</h1>
                    </div>
                </div>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Alpine.js Plugins -->
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
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
    @include('partials.toast')

    @stack('scripts')
</body>
</html>
