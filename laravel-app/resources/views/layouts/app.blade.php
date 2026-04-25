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
    <meta name="theme-color" content="#0b0e14">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap">
    <title>@yield('title') — NexShape Arena</title>
    <script>
        document.documentElement.setAttribute("data-theme", "dark");
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modern-layout.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="{{ asset('js/sidebar-toggle.js') }}" defer></script>
    <style>
        :root {
            --accent: {{ $accentColor }};
            --accent-dim: {{ $accentColor }}cc; /* Adiciona transparência para o hover */
            --primary-gradient: linear-gradient(135deg, {{ $accentColor }} 0%, {{ $accentColor }}cc 100%);
        }
    </style>
    @stack('styles')
</head>
<body class="{{ request()->is('professional*') ? 'portal-pro' : '' }} {{ request()->routeIs('login', 'register', 'password.*', 'verification.notice', 'registration.pending', 'registration.rejected') ? 'min-h-screen overflow-x-hidden overflow-y-auto bg-[#0b0e14]' : '' }}">
    <a class="skip-link" href="#main">Ir para o conteúdo</a>

    @if($loggedIn && !request()->routeIs('home') && !request()->routeIs('verification.notice') && !request()->routeIs('registration.pending') && !request()->routeIs('registration.rejected'))
        @include('partials.impersonation-banner')
        <div class="app-container">
            @include('partials.sidebar')

            <div class="main-area">
                @include('partials.topbar')

                @php($activeAnnouncements = \App\Models\Announcement::active())
                @foreach($activeAnnouncements as $announcement)
                    <div class="announcement-bar announcement-{{ $announcement->type }}" style="background: {{ $announcement->type == 'danger' ? '#f85149' : ($announcement->type == 'warning' ? '#d29922' : ($announcement->type == 'success' ? '#2ea043' : '#388bfd')) }}; color: white; text-align: center; padding: 0.75rem; font-weight: 600; font-size: 0.875rem;">
                        {{ $announcement->content }}
                    </div>
                @endforeach

                <main id="main" class="content-wrapper">
                    @yield('content')
                </main>
            </div>
        </div>
    @elseif(request()->routeIs('login', 'register', 'password.*', 'verification.notice', 'registration.pending', 'registration.rejected'))
        <main id="main" class="min-h-screen w-full">
            @yield('content')
        </main>
    @else
        <header class="site-header">
            <div class="shell header-inner">
                <a class="logo" href="{{ route('home') }}" style="margin:0;">
                    <img src="{{ $customLogo ?: asset('images/logo_Academia.png') }}" alt="Logo Academia" class="logo-img" style="height: 58px; width: auto;">
                </a>
                
                <button class="nav-toggle" id="menuToggle" aria-label="Abrir menu">
                    <svg style="width:24px;height:24px" viewBox="0 0 24 24"><path fill="currentColor" d="M3,6H21V8H3V6M3,11H21V13H3V11M3,16H21V18H3V16Z" /></svg>
                </button>

                <nav class="site-nav" id="siteNav">
                    <a href="#features">Recursos</a>
                    <a href="#pricing">Preços</a>
                    <div class="nav-divider"></div>
                    <a href="{{ route('login') }}" class="btn btn-sm btn-ghost">Entrar</a>
                </nav>

                <script>
                    document.getElementById('menuToggle').addEventListener('click', function() {
                        document.getElementById('siteNav').classList.toggle('is-open');
                    });
                </script>
            </div>
        </header>

        <main id="main" class="shell main-content">
            @yield('content')
        </main>
    @endif

    @if((!$loggedIn || request()->routeIs('home')) && !request()->routeIs('login', 'register', 'password.*', 'verification.notice', 'registration.pending', 'registration.rejected'))
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
                    <li><a href="{{ route('legal.privacy') }}" class="muted">Privacidade</a></li>
                    <li><a href="{{ route('legal.terms') }}" class="muted">Termos de Uso</a></li>
                    <li><a href="{{ route('legal.cookies') }}" class="muted">Cookies</a></li>
                </ul>
            </div>
            <div class="footer-col" style="display: flex; flex-direction: column; align-items: flex-end; justify-content: flex-end;">
                 <img src="{{ asset('images/logo.png') }}" alt="Desenvolvedor" style="height: 128px; width: auto; filter: opacity(0.95);">
            </div>
        </div>
        
        <div class="footer-row" style="padding-top: 1rem; border-top: 1px solid var(--border);">
            <p class="footer-tagline">&copy; {{ date('Y') }} ProjetoAcademia. Todos os direitos reservados.</p>

        </div>
    </footer>
    @endif

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
        // Real-time Polling
        @if(auth()->check())
        function checkNotifications() {
            fetch('{{ route('notifications.unread-counts') }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    // Update Emails
                    const emailBadge = document.querySelector('.nav-link[href*="internal-email"] .badge');
                    const sidebarEmailBadge = document.querySelector('.nav-link-email.active .badge') || document.querySelector('.nav-link-email[href*="inbox"] .badge');
                    const sidebarMainEmailBadge = document.querySelector('.nav-link[href*="internal-email"] .bg-red-500');
                    
                    if (data.emails > 0) {
                        if (emailBadge) { emailBadge.textContent = data.emails; emailBadge.style.display = 'inline-block'; }
                        if (sidebarEmailBadge) { sidebarEmailBadge.textContent = data.emails; sidebarEmailBadge.style.display = 'inline-block'; }
                        if (sidebarMainEmailBadge) { sidebarMainEmailBadge.textContent = data.emails; sidebarMainEmailBadge.style.display = 'inline-block'; }
                    } else {
                        if (emailBadge) emailBadge.style.display = 'none';
                        if (sidebarEmailBadge) sidebarEmailBadge.style.display = 'none';
                        if (sidebarMainEmailBadge) sidebarMainEmailBadge.style.display = 'none';
                    }

                    // Update Messages
                    const sidebarMsgBadge = document.querySelector('.nav-link[href*="/messages"] .bg-blue-500');
                    if (data.messages > 0) {
                        if (sidebarMsgBadge) {
                            sidebarMsgBadge.textContent = data.messages;
                            sidebarMsgBadge.style.display = 'inline-block';
                        }
                    } else {
                        if (sidebarMsgBadge) sidebarMsgBadge.style.display = 'none';
                    }
                })
                .catch(err => console.error('Error fetching unread counts:', err));
        }
        setInterval(checkNotifications, 30000); // Check every 30 seconds
        checkNotifications();
        @endif
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('sw.js') }}")
                    .then(reg => console.log('Service Worker registrado!', reg))
                    .catch(err => console.log('Erro ao registrar SW:', err));
            });
        }
    </script>
    <!-- Premium Upgrade Modal (Global) -->
    <div id="premiumModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 bg-zinc-950/80 backdrop-blur-sm animate-fade-in" style="display: none;">
        <div class="bg-zinc-900 border border-zinc-800 w-full max-w-lg rounded-[2.5rem] p-8 shadow-2xl animate-dashboard-entry text-center space-y-8">
            <div class="w-20 h-20 bg-amber-500/10 rounded-full flex items-center justify-center mx-auto text-amber-500 shadow-lg shadow-amber-500/10">
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
            </div>
            
            <div class="space-y-3">
                <h3 class="text-3xl font-black text-white tracking-tight">Recurso <span class="text-amber-500">Premium</span></h3>
                <p class="text-zinc-500 font-medium">Esta funcionalidade exclusiva faz parte do plano **Performance Elite**. Evolua seu treino com IA e dados avançados.</p>
            </div>

            <div class="grid grid-cols-1 gap-3">
                <a href="{{ route('plano') }}" class="w-full py-4 bg-amber-500 text-zinc-950 font-black rounded-2xl hover:bg-amber-400 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-crown text-xs"></i>
                    QUERO ME TORNAR PREMIUM
                </a>
                <button onclick="document.getElementById('premiumModal').style.display = 'none'" class="w-full py-4 bg-zinc-800 text-zinc-400 font-bold rounded-2xl hover:bg-zinc-700 transition-all">
                    Talvez mais tarde
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Interceptor de cliques em botões premium
            document.querySelectorAll('[data-premium-locked]').forEach(el => {
                el.addEventListener('click', function(e) {
                    if (window.isPremiumUser === false) {
                        e.preventDefault();
                        e.stopPropagation();
                        const pModal = document.getElementById('premiumModal');
                        if (pModal) pModal.style.display = 'flex';
                    }
                });
            });

            // Fecha modal ao clicar fora
            const modal = document.getElementById('premiumModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                    }
                });
            }
        });
        
        window.isPremiumUser = {{ auth()->check() && auth()->user()->hasPremiumAccess() ? 'true' : 'false' }};
    </script>

    @if(auth()->check())
        @include('partials.onboarding_modal')
        @include('partials.ai-credits-modal')
    @endif
    @include('partials.confirm-delete-modal')
    @include('partials.toast')
    @include('partials.error-modal')
</body>
</html>
