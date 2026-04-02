@php
    $loggedIn = auth()->check();
    $accentColor = \App\Models\AdminSetting::get('accent_color', '#3d9cf5');
    $customLogo = \App\Models\AdminSetting::get('logo_url', '');
@endphp
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
    <style>
        :root {
            --accent: {{ $accentColor }};
            --accent-dim: {{ $accentColor }}cc; /* Adiciona transparência para o hover */
            --primary-gradient: linear-gradient(135deg, {{ $accentColor }} 0%, {{ $accentColor }}cc 100%);
        }
    </style>
</head>
<body>
    <a class="skip-link" href="#main">Ir para o conteúdo</a>
    <header class="site-header">
        <div class="shell header-inner">
            <a class="logo" href="{{ $loggedIn ? route('dashboard') : route('home') }}" style="margin:0;">
                <img src="{{ $customLogo ?: asset('images/logo_Academia.png') }}" alt="Logo Academia" class="logo-img" style="height: 58px; width: auto;">
            </a>
            
            @php($isHome = Route::is('home'))
            
            @if($loggedIn && !$isHome)
                <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="site-nav" id="nav-toggle">Menu</button>
                <nav class="site-nav" id="site-nav" aria-label="Principal">
                    <a href="{{ route('dashboard') }}" @if(($navCurrent ?? '') === 'dashboard') aria-current="page" @endif>Dashboard</a>
                    <a href="{{ route('diary') }}" @if(($navCurrent ?? '') === 'diary') aria-current="page" @endif>Alimentação</a>
                    <a href="{{ route('exercise') }}" @if(($navCurrent ?? '') === 'exercise') aria-current="page" @endif>Exercícios</a>
                    <a href="{{ route('weight') }}" @if(($navCurrent ?? '') === 'weight') aria-current="page" @endif>Peso</a>
                    <a href="{{ route('report') }}" @if(($navCurrent ?? '') === 'report') aria-current="page" @endif>Relatórios</a>
                    <a href="{{ route('chat.page') }}" @if(($navCurrent ?? '') === 'chat') aria-current="page" @endif>IA Chat</a>
                    <div class="nav-divider"></div>
                    <a href="{{ route('profile') }}" @if(($navCurrent ?? '') === 'profile') aria-current="page" @endif class="btn btn-sm btn-ghost">Perfil</a>
                    <form action="{{ route('logout') }}" method="post" class="nav-logout-form">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-danger">Sair</button>
                    </form>
                </nav>
            @else
                <nav class="site-nav">
                    <a href="#features">Recursos</a>
                    <a href="#pricing">Preços</a>
                    <div class="nav-divider"></div>
                    <a href="{{ route('login') }}" class="btn btn-sm btn-ghost">Entrar</a>
                    <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Começar agora</a>
                </nav>
            @endif
        </div>
    </header>

    @if($loggedIn)
        @php($activeAnnouncements = \App\Models\Announcement::active())
        @foreach($activeAnnouncements as $announcement)
            <div class="announcement-bar announcement-{{ $announcement->type }}" style="background: {{ $announcement->type == 'danger' ? '#f85149' : ($announcement->type == 'warning' ? '#d29922' : ($announcement->type == 'success' ? '#2ea043' : '#388bfd')) }}; color: white; text-align: center; padding: 0.75rem; font-weight: 600; font-size: 0.875rem;">
                {{ $announcement->content }}
            </div>
        @endforeach
    @endif

    <main id="main" class="shell main-content">
        @yield('content')
    </main>

    @if(!$loggedIn)
    <footer class="site-footer shell animate-fade-up">
        <div class="footer-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(15rem, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            <div class="footer-col">
                <a class="logo" href="{{ route('home') }}" style="margin:0;">
                    <img src="{{ asset('images/logo_Rodape.png') }}" alt="Logo Academia" class="logo-img" style="height: 36px; width: auto;">
                </a>
                <p class="muted" style="margin-top: 0.5rem;">Sua jornada para um corpo mais saudável e uma mente mais forte começa com o acompanhamento correto.</p>
            </div>
            <div class="footer-col">
                <h4 style="color: var(--text); margin-bottom: 1rem;">Produto</h4>
                <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 0.5rem;">
                    <li><a href="#features" class="muted">Recursos</a></li>
                    <li><a href="#pricing" class="muted">Preços</a></li>
                    <li><a href="{{ route('register') }}" class="muted">Criar conta</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4 style="color: var(--text); margin-bottom: 1rem;">Suporte</h4>
                <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 0.5rem;">
                    <li><a href="#" class="muted">Central de Ajuda</a></li>
                    <li><a href="#" class="muted">Privacidade</a></li>
                    <li><a href="#" class="muted">Termos de Uso</a></li>
                </ul>
            </div>
            <div class="footer-col" style="display: flex; flex-direction: column; align-items: flex-end; justify-content: flex-end;">
                 <img src="{{ asset('images/logo.png') }}" alt="Desenvolvedor" style="height: 128px; width: auto; filter: opacity(0.95);">
            </div>
        </div>
        
        <div class="footer-row" style="padding-top: 1rem; border-top: 1px solid var(--border);">
            <p class="footer-tagline">&copy; {{ date('Y') }} ProjetoAcademia. Todos os direitos reservados.</p>
            <div class="theme-switcher" role="group" aria-label="Tema da interface">
                <span class="muted theme-switcher-label">Aparência</span>
                <form method="post" action="{{ route('theme') }}" class="theme-switcher-form">
                    @csrf
                    <input type="hidden" name="next" value="{{ $themeNext }}">
                    <input type="hidden" name="theme" value="dark">
                    <button type="submit" class="btn-theme{{ $projetoTheme === 'dark' ? ' is-active' : '' }}">Escuro</button>
                </form>
                <form method="post" action="{{ route('theme') }}" class="theme-switcher-form">
                    @csrf
                    <input type="hidden" name="next" value="{{ $themeNext }}">
                    <input type="hidden" name="theme" value="light">
                    <button type="submit" class="btn-theme{{ $projetoTheme === 'light' ? ' is-active' : '' }}">Claro</button>
                </form>
            </div>
        </div>
    </footer>
    @endif

    <script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
