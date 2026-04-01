@php($loggedIn = auth()->check())
<!DOCTYPE html>
<html lang="pt-BR" data-theme="{{ $projetoTheme }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — ProjetoAcademia</title>
    <script>
        (function () {
            var n = @json(\App\Support\Theme::COOKIE);
            if (document.cookie.indexOf(n + "=") !== -1) {
                return;
            }
            try {
                if (window.matchMedia("(prefers-color-scheme: light)").matches) {
                    document.documentElement.setAttribute("data-theme", "light");
                }
            } catch (e) {}
        })();
    </script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <a class="skip-link" href="#main">Ir para o conteúdo</a>
    <header class="site-header">
        <div class="shell header-inner">
            <a class="logo" href="{{ $loggedIn ? route('dashboard') : route('home') }}">ProjetoAcademia</a>
            @if($loggedIn)
                <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="site-nav" id="nav-toggle">Menu</button>
                <nav class="site-nav" id="site-nav" aria-label="Principal">
                    <a href="{{ route('dashboard') }}" @if(($navCurrent ?? '') === 'dashboard') aria-current="page" @endif>Hoje</a>
                    <a href="{{ route('diary') }}" @if(($navCurrent ?? '') === 'diary') aria-current="page" @endif>Alimentação</a>
                    <a href="{{ route('exercise') }}" @if(($navCurrent ?? '') === 'exercise') aria-current="page" @endif>Exercícios</a>
                    <a href="{{ route('weight') }}" @if(($navCurrent ?? '') === 'weight') aria-current="page" @endif>Peso</a>
                    <a href="{{ route('report') }}" @if(($navCurrent ?? '') === 'report') aria-current="page" @endif>Relatório</a>
                    <a href="{{ route('export') }}" @if(($navCurrent ?? '') === 'export') aria-current="page" @endif>Exportar</a>
                    <a href="{{ route('plano') }}" @if(($navCurrent ?? '') === 'plano') aria-current="page" @endif>Meu Plano</a>
                    <a href="{{ route('chat.page') }}" @if(($navCurrent ?? '') === 'chat') aria-current="page" @endif>💬 Chat IA</a>
                    <a href="{{ route('profile') }}" @if(($navCurrent ?? '') === 'profile') aria-current="page" @endif>Perfil</a>
                    <form action="{{ route('logout') }}" method="post" class="nav-logout-form">@csrf<button type="submit" class="nav-logout-btn">Sair</button></form>
                </nav>
            @endif
        </div>
    </header>
    <main id="main" class="shell main-content">
        @yield('content')
    </main>
    <footer class="site-footer shell">
        <div class="footer-row">
            <p class="footer-tagline">ProjetoAcademia — acompanhamento alimentar e exercícios.</p>
            <div class="theme-switcher" role="group" aria-label="Tema da interface">
                <span class="muted theme-switcher-label">Aparência @if(! $themeExplicit) <span class="theme-auto-hint">(sistema)</span>@endif</span>
                <form method="post" action="{{ route('theme') }}" class="theme-switcher-form">
                    @csrf
                    <input type="hidden" name="next" value="{{ $themeNext }}">
                    <input type="hidden" name="theme" value="dark">
                    <button type="submit" class="btn-theme{{ $themeExplicit && $projetoTheme === 'dark' ? ' is-active' : '' }}">Escuro</button>
                </form>
                <form method="post" action="{{ route('theme') }}" class="theme-switcher-form">
                    @csrf
                    <input type="hidden" name="next" value="{{ $themeNext }}">
                    <input type="hidden" name="theme" value="light">
                    <button type="submit" class="btn-theme{{ $themeExplicit && $projetoTheme === 'light' ? ' is-active' : '' }}">Claro</button>
                </form>
            </div>
        </div>
    </footer>
    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
