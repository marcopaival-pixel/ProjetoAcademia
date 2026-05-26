@php
    $loggedIn = auth()->check();
    $accentColor = \App\Models\AdminSetting::get('accent_color', '#10b981');
    $customLogo = \App\Models\AdminSetting::get('logo_url', '');

    // Lógica do Botão Voltar Global
    $primaryRoutes = [
        'dashboard', 'home', 'admin.dashboard', 'patient.portal', 
        'profile.selection', 'clinic.selector', 'onboarding.welcome', 
        'onboarding.finish', 'patient.unified.dashboard', 
        'patient.dashboard.choice', 'patient.professional.selection'
    ];
    $currentRouteName = request()->route()?->getName() ?? '';
    $isPrimaryPage = in_array($currentRouteName, $primaryRoutes) || str_starts_with($currentRouteName, 'onboarding.');
    $showGlobalBack = $loggedIn && !$isPrimaryPage && !request()->routeIs('verification.notice');

    // Capturar origem PaivaTech
    if (request()->has('from') && request()->get('from') === 'paivatech') {
        session(['paivatech_origin' => 'paivatech']);
    }
    $showPaivaBacklink = session('paivatech_origin') === 'paivatech' || request()->get('from') === 'paivatech';
@endphp

<!DOCTYPE html>
<html lang="pt-BR" data-theme="{{ $projetoTheme }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#080a0f">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap">
    <title>@yield('title') — NEX SHAPE PRO</title>
    <script>
        document.documentElement.setAttribute("data-theme", "dark");
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/sidebar-toggle.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>

    @stack('styles')
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        [x-cloak] { display: none !important; }
    </style>
</head>
@php
    $activeRole = session('active_role');
    $user = auth()->user();
    $experienceClass = 'experience-aluno'; // Default
    
    if ($user) {
        if ($activeRole === 'professional' || ($user->hasRole(['professional', 'instructor', 'manager', 'receptionist', 'supervisor']) && !$activeRole)) {
            $experienceClass = 'experience-clinica';
        } elseif ($activeRole === 'paciente' || ($user->hasRole('paciente') && !$activeRole)) {
            $experienceClass = 'experience-clinica'; // Pacientes também usam a experiência clínica/profissional
        } elseif ($activeRole === 'aluno' || ($user->hasRole('aluno') && !$activeRole)) {
            $experienceClass = 'experience-aluno';
        }
    }
@endphp
<body class="{{ $experienceClass }} {{ request()->is('professional*') ? 'portal-pro' : '' }} {{ request()->routeIs('login', 'register', 'password.*', 'verification.notice', 'registration.pending', 'registration.rejected') ? 'min-h-screen overflow-x-hidden overflow-y-auto' : '' }}">
    {{-- Barra de Retorno PaivaTech (Versão Robusta) --}}
    <div id="paiva-backlink-bar" class="bg-[#080a0f] border-b border-zinc-900/50 py-2.5 px-6 sm:px-8 flex items-center justify-between relative z-[2000] animate-fade-in" style="display: none;">
        <div class="flex items-center gap-4">
            <span class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.3em] hidden sm:block">Uma solução da PaivaTech Solutions</span>
            <a href="{{ (request()->getHost() === 'localhost' || request()->getHost() === '127.0.0.1') ? 'http://localhost:3000' : 'https://paivatechsolutions.com.br' }}" class="group flex items-center gap-2 text-[10px] font-black text-emerald-500 hover:text-emerald-400 uppercase tracking-[0.2em] transition-all">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5 transition-transform group-hover:-translate-x-1"></i>
                Voltar ao Portal
            </a>
        </div>
        <div class="flex items-center gap-4">
            <div class="h-4 w-px bg-zinc-900 hidden sm:block"></div>
            <button onclick="dismissPaivaBacklink()" class="text-zinc-800 hover:text-zinc-600 transition-colors">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>
    </div>

    <script>
        function dismissPaivaBacklink() {
            const bar = document.getElementById('paiva-backlink-bar');
            if (bar) bar.style.display = 'none';
            sessionStorage.setItem('paivatech_dismissed', 'true');
            document.querySelectorAll('.fixed-top-header, .topbar').forEach(el => el.style.top = '0');
        }

        (function() {
            const hasParam = new URLSearchParams(window.location.search).get('from') === 'paivatech';
            const hasSession = sessionStorage.getItem('paivatech_origin') === 'paivatech';
            const isDismissed = sessionStorage.getItem('paivatech_dismissed') === 'true';

            if ((hasParam || hasSession) && !isDismissed) {
                if (hasParam) sessionStorage.setItem('paivatech_origin', 'paivatech');
                
                document.addEventListener('DOMContentLoaded', function() {
                    const bar = document.getElementById('paiva-backlink-bar');
                    if (bar) {
                        bar.style.display = 'flex';
                        // Ajustar elementos fixos
                        document.querySelectorAll('.fixed-top-header, .topbar').forEach(el => {
                            el.style.top = '41px';
                        });
                    }
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                });
            }
        })();
    </script>


    <a class="skip-link" href="#main">Ir para o conteúdo</a>


    @if($loggedIn && !request()->routeIs('home') && !request()->routeIs('verification.notice') && !request()->routeIs('registration.pending') && !request()->routeIs('registration.rejected'))
        @include('partials.impersonation-banner')
        <div class="app-container">
            @include('partials.sidebar')

            <div class="main-area">
                @include('partials.topbar')

                @php($activeAnnouncements = \App\Models\Announcement::active())
                @foreach($activeAnnouncements as $announcement)
                    <div class="announcement-bar animate-fade-in" style="background: {{ $announcement->type == 'danger' ? '#f43f5e' : ($announcement->type == 'warning' ? '#f59e0b' : ($announcement->type == 'success' ? '#10b981' : '#3b82f6')) }}; color: white; text-align: center; padding: 1rem; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; border-bottom: 1px solid rgba(255,255,255,0.1);">
                        {{ $announcement->content }}
                    </div>
                @endforeach


                <main id="main" class="content-wrapper">
                    @if($showGlobalBack && !View::hasSection('no_global_back'))
                        <div class="px-4 sm:px-6 lg:px-8 max-w-[1600px] mx-auto pt-8">
                            <x-back-button />
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    @elseif(request()->routeIs('login', 'register', 'password.*', 'verification.notice', 'registration.pending', 'registration.rejected'))
        <main id="main" class="min-h-screen w-full">
            @yield('content')
        </main>
    @else
        <header class="fixed-top-header fixed top-0 left-0 right-0 z-[100] bg-zinc-950/70 backdrop-blur-2xl border-b border-zinc-900/50 transition-all duration-500" style="{{ $showPaivaBacklink ? 'top: 41px;' : '' }}">

            <div class="max-w-7xl mx-auto px-6 h-24 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-4 group">
                    <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 transform group-hover:-rotate-6 transition-transform duration-500">
                        <img src="{{ $customLogo ?: asset('images/logo_Academia.webp') }}" alt="N" class="w-8 h-8 object-contain brightness-0 invert">
                    </div>
                    <div class="hidden sm:block">
                        <span class="text-xl font-black text-white tracking-tighter uppercase italic">NEX <span class="text-emerald-500">SHAPE</span></span>
                        <p class="text-[8px] text-zinc-500 font-black uppercase tracking-[0.3em] -mt-1">Pro Performance</p>
                    </div>
                </a>
                
                <nav class="hidden md:flex items-center gap-10">
                    <a href="{{ route('home') }}#features" class="text-[10px] font-black text-zinc-500 hover:text-white uppercase tracking-[0.2em] transition-all">Recursos</a>
                    <a href="{{ route('home') }}#pricing" class="text-[10px] font-black text-zinc-500 hover:text-white uppercase tracking-[0.2em] transition-all">Preços</a>
                    <div class="w-px h-6 bg-zinc-800 mx-2"></div>
                    <a href="{{ route('login') }}" class="text-[10px] font-black text-zinc-400 hover:text-emerald-500 uppercase tracking-[0.2em] transition-all">Autenticar</a>
                    <a href="{{ route('register') }}" class="px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all shadow-xl shadow-emerald-500/10 active:scale-95">
                        Começar Agora
                    </a>
                </nav>

                <button class="md:hidden w-12 h-12 bg-zinc-900 border border-zinc-800 rounded-xl flex items-center justify-center text-zinc-400" id="menuToggle">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </header>

        <main id="main" class="pt-24 min-h-screen">
            @yield('content')
        </main>
    @endif

    @if((!$loggedIn || request()->routeIs('home')) && !request()->routeIs('login', 'register', 'password.*', 'verification.notice', 'registration.pending', 'registration.rejected'))
    <footer class="bg-[#080a0f] border-t border-zinc-900 pt-24 pb-12 relative overflow-hidden">
        <!-- Background Ambient Glow -->
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-emerald-500/5 blur-[120px] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-16 mb-20">
                <!-- Branding Section -->
                <div class="md:col-span-4 space-y-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-4 group">
                        <span class="text-2xl font-black text-white tracking-tighter uppercase italic">NEX <span class="text-emerald-500">SHAPE</span></span>
                    </a>
                    <p class="text-zinc-500 text-sm leading-relaxed font-medium italic">
                        Sua jornada para o ápice da performance começa com inteligência. Unimos ciência, dados e tecnologia para transformar seu potencial em resultados reais.
                    </p>
                    <div class="flex items-center gap-4">
                        @foreach(['instagram', 'x-twitter', 'youtube'] as $social)
                        <a href="#" class="w-12 h-12 rounded-2xl bg-zinc-800 border border-white/10 flex items-center justify-center text-zinc-100 hover:text-zinc-950 hover:bg-emerald-500 hover:border-emerald-500 transition-all duration-300 group shadow-lg">
                            <i class="fa-brands fa-{{ $social }} text-xl group-hover:scale-110 transition-transform"></i>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="md:col-span-2 space-y-8">
                    <h4 class="text-[10px] font-black text-white uppercase tracking-[0.3em] italic">Ecossistema</h4>
                    <ul class="space-y-4">
                        <li><a href="{{ route('home') }}#features" class="text-zinc-500 hover:text-white transition-all text-xs font-bold uppercase tracking-widest italic">Recursos</a></li>
                        <li><a href="{{ route('home') }}#pricing" class="text-zinc-500 hover:text-white transition-all text-xs font-bold uppercase tracking-widest italic">Planos</a></li>
                        <li><a href="{{ route('register') }}" class="text-zinc-500 hover:text-white transition-all text-xs font-bold uppercase tracking-widest italic">Criar Conta</a></li>
                    </ul>
                </div>

                <div class="md:col-span-2 space-y-8">
                    <h4 class="text-[10px] font-black text-white uppercase tracking-[0.3em] italic">Jurídico</h4>
                    <ul class="space-y-4">
                        <li><button type="button" onclick="window.openLegalProtocol('privacy')" class="text-zinc-500 hover:text-white transition-all text-xs font-bold uppercase tracking-widest italic outline-none">Privacidade</button></li>
                        <li><button type="button" onclick="window.openLegalProtocol('terms')" class="text-zinc-500 hover:text-white transition-all text-xs font-bold uppercase tracking-widest italic outline-none">Termos</button></li>
                        <li><a href="{{ route('legal.cookies') }}" class="text-zinc-500 hover:text-white transition-all text-xs font-bold uppercase tracking-widest italic">Cookies</a></li>
                    </ul>
                </div>

                <!-- Paivatech Logo (Preserved as requested) -->
                <div class="md:col-span-4 flex items-center justify-end">
                     <img src="{{ asset('images/logo.webp') }}" alt="Logo" class="h-32 w-auto opacity-20 grayscale hover:grayscale-0 hover:opacity-100 transition-all duration-700">
                </div>
            </div>
            
            <!-- Bottom Bar -->
            <div class="pt-12 border-t border-zinc-900/50 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    <p class="text-zinc-700 text-[10px] font-black uppercase tracking-[0.2em] italic">&copy; {{ date('Y') }} NEX SHAPE PRO. ALL RIGHTS RESERVED.</p>
                </div>
                <div class="flex items-center gap-6">
                    <p class="text-zinc-800 text-[9px] font-black uppercase tracking-[0.4em] italic">PRECISION BY AI NEURAL</p>
                </div>
            </div>
        </div>
    </footer>
    @endif

    <!-- Alpine.js Plugins (MUST load before core) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    <!-- Alpine.js Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            // Helper para parse seguro de JSON
            const safeParse = (key, fallback) => {
                try {
                    const item = localStorage.getItem(key);
                    return item ? JSON.parse(item) : fallback;
                } catch (e) {
                    console.error('Erro ao ler localStorage:', key, e);
                    return fallback;
                }
            };

            // Registrar componente de navegação da sidebar
            Alpine.data('sidebarNav', () => ({
                openGroups: safeParse('sidebar_open_groups', []),
                openProfile: false,
                
                toggleGroup(id) {
                    if (this.openGroups.includes(id)) {
                        this.openGroups = this.openGroups.filter(g => g !== id);
                    } else {
                        this.openGroups.push(id);
                    }
                    localStorage.setItem('sidebar_open_groups', JSON.stringify(this.openGroups));
                },
                
                isGroupOpen(id) {
                    // Garantia de que openGroups é um array e o id está lá
                    return Array.isArray(this.openGroups) && this.openGroups.includes(id);
                },
                
                init() {
                    // Carregar grupos iniciais vindos do servidor (data-initial-groups no elemento)
                    const el = this.$el;
                    if (el && el.dataset.initialGroups) {
                        try {
                            const initialGroups = JSON.parse(el.dataset.initialGroups);
                            if (Array.isArray(initialGroups)) {
                                initialGroups.forEach(id => {
                                    if (!this.openGroups.includes(id)) {
                                        this.openGroups.push(id);
                                    }
                                });
                                localStorage.setItem('sidebar_open_groups', JSON.stringify(this.openGroups));
                            }
                        } catch (e) {
                            console.warn('Sidebar: Falha ao processar grupos iniciais:', e);
                        }
                    }
                    
                    // Re-inicializar ícones Lucide apenas se necessário
                    // Removido daqui para evitar conflito com o carregamento global
                }
            }));
        });
    </script>


    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuToggle = document.getElementById('menuToggle');
            const siteNav = document.querySelector('header nav');
            if (menuToggle && siteNav) {
                menuToggle.addEventListener('click', () => {
                    siteNav.classList.toggle('hidden');
                    siteNav.classList.toggle('flex');
                    siteNav.classList.toggle('flex-col');
                    siteNav.classList.toggle('absolute');
                    siteNav.classList.toggle('top-24');
                    siteNav.classList.toggle('left-0');
                    siteNav.classList.toggle('right-0');
                    siteNav.classList.toggle('bg-zinc-950');
                    siteNav.classList.toggle('p-6');
                    siteNav.classList.toggle('border-b');
                    siteNav.classList.toggle('border-zinc-800');
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
    <script src="{{ asset('js/demo-tour.js') }}"></script>
    {{-- Removido script redundante: asset('js/app.js') já está no @vite --}}


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
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
                .catch(err => {});
        }
        setInterval(checkNotifications, 30000);
        checkNotifications();
        @endif
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('sw.js') }}").catch(err => {});
            });
        }
    </script>
    <!-- Premium Upgrade Modal (Global) -->
    <div id="premiumModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-6 bg-zinc-950/40 backdrop-blur-md animate-fade-in" style="display: none;">
        <div class="bg-zinc-900/90 border border-white/10 w-full max-w-xl rounded-[3rem] p-1 shadow-[0_0_50px_-12px_rgba(16,185,129,0.2)] animate-fade-in-up relative overflow-hidden group">
            <!-- Background Decorative Glows -->
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-emerald-500/20 rounded-full blur-[80px] pointer-events-none group-hover:bg-emerald-500/30 transition-all duration-700"></div>
            <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-blue-500/10 rounded-full blur-[80px] pointer-events-none group-hover:bg-blue-500/20 transition-all duration-700"></div>

            <div class="relative bg-zinc-950/50 backdrop-blur-xl rounded-[2.9rem] p-10 md:p-12 space-y-10 border border-white/5">
                <!-- Icon & Badge -->
                <div class="flex flex-col items-center gap-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-emerald-600 text-zinc-950 rounded-3xl flex items-center justify-center shadow-2xl shadow-emerald-500/40 transform -rotate-6 group-hover:rotate-0 transition-transform duration-500">
                        <i data-lucide="crown" class="w-10 h-10 fill-current"></i>
                    </div>
                    <div class="px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20">
                        <span class="text-[10px] font-black text-emerald-500 uppercase tracking-[0.3em]">Experiência Elite</span>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="text-center space-y-4">
                    <h3 class="text-4xl md:text-5xl font-black text-white tracking-tighter uppercase italic leading-none">
                        POTENCIALIZAR <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-blue-400">RESULTADOS</span>
                    </h3>
                    <p class="text-zinc-500 font-medium leading-relaxed max-w-md mx-auto text-sm md:text-base">
                        Esta funcionalidade é exclusiva para membros **NexShape Premium**. Desbloqueie o poder total da inteligência artificial e biometria avançada.
                    </p>
                </div>

                <!-- Benefits List -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-md mx-auto">
                    @foreach([
                        ['icon' => 'zap', 'text' => 'IA Preditiva'],
                        ['icon' => 'activity', 'text' => 'Laudos Bio'],
                        ['icon' => 'trending-up', 'text' => 'Trends Elite'],
                        ['icon' => 'shield-check', 'text' => 'Suporte VIP']
                    ] as $benefit)
                    <div class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-zinc-900/50 border border-white/5 group/benefit hover:border-emerald-500/30 transition-all">
                        <div class="w-6 h-6 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                            <i data-lucide="{{ $benefit['icon'] }}" class="w-3.5 h-3.5"></i>
                        </div>
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">{{ $benefit['text'] }}</span>
                    </div>
                    @endforeach
                </div>

                <!-- Actions -->
                <div class="flex flex-col gap-4 pt-4">
                    <a href="{{ route('plano') }}" class="w-full group/btn relative py-6 bg-emerald-500 text-zinc-950 font-black rounded-3xl overflow-hidden transition-all shadow-2xl shadow-emerald-500/20 active:scale-[0.98]">
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-400 to-emerald-600 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
                        <div class="relative flex items-center justify-center gap-3 text-xs uppercase tracking-[0.2em]">
                            <i data-lucide="sparkles" class="w-4 h-4 fill-current"></i>
                            ATIVAR PROTOCOLO ELITE
                        </div>
                    </a>

                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('plano') }}" class="py-4 bg-zinc-900 border border-white/5 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl flex items-center justify-center hover:bg-zinc-800 transition-all">
                            Ver Planos
                        </a>
                        <a href="{{ route('ai-credits.index') }}" class="py-4 bg-zinc-900 border border-white/5 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl flex items-center justify-center hover:bg-zinc-800 transition-all">
                            Comprar Créditos
                        </a>
                    </div>
                    
                    <button onclick="document.getElementById('premiumModal').style.display = 'none'" class="w-full py-4 text-zinc-600 hover:text-white font-black text-[10px] uppercase tracking-[0.3em] transition-all">
                        CONTINUAR NO PLANO FREE
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.isPremiumUser = {{ auth()->check() && auth()->user()->hasPremiumAccess() ? 'true' : 'false' }};

        document.addEventListener('DOMContentLoaded', function() {
            // Interceptar cliques em elementos bloqueados para mostrar o modal
            // Usando fase de bubble (false) para evitar interferência prematura no DOM
            document.addEventListener('click', function(e) {
                const lockedElement = e.target.closest('[data-premium-locked]');
                if (lockedElement) {
                    e.preventDefault();
                    e.stopPropagation();
                    const modal = document.getElementById('premiumModal');
                    if (modal) {
                        modal.style.display = 'flex';
                    }
                    return false;
                }
            }, false);

            // Estilizar elementos bloqueados
            document.querySelectorAll('[data-premium-locked]').forEach(el => {
                el.style.cursor = 'pointer';
                el.classList.add('premium-locked-overlay');
            });

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    @if(auth()->check())
        @include('partials.onboarding_modal')
        @include('partials.ai-credits-modal')
        @include('partials.omnichat-widget')
        @include('partials.community-post-modal')
    @endif
    @include('partials.confirm-delete-modal')
    @include('partials.legal-modal')
    @include('partials.toast')
    @include('partials.error-modal')
    @include('partials.success-modal')
    <x-demo-badge />

    <!-- Global AI Brand Orbit (Senior Design Touch) -->
    @if($loggedIn && !request()->routeIs('home'))
    <div class="fixed bottom-8 right-8 z-[100] pointer-events-none select-none opacity-40 hover:opacity-100 transition-opacity duration-700 hidden lg:block">
        <div class="relative flex items-center justify-center w-32 h-32">
            <!-- Center Symbol -->
            <div class="w-12 h-12 bg-emerald-500/10 rounded-full flex items-center justify-center border border-emerald-500/20 backdrop-blur-sm">
                <i data-lucide="zap" class="w-5 h-5 text-emerald-500 animate-pulse"></i>
            </div>
            
            <!-- Orbiting Text -->
            <svg class="absolute inset-0 w-full h-full animate-spin-slow" viewBox="0 0 100 100">
                <defs>
                    <path id="globalOrbitPath" d="M 50, 50 m -40, 0 a 40,40 0 1,1 80,0 a 40,40 0 1,1 -80,0" />
                </defs>
                <text class="text-[4.5px] font-black fill-emerald-500/60 uppercase tracking-[0.4em] italic">
                    <textPath href="#globalOrbitPath" startOffset="0%">
                        NEXSHAPE AI • OTIMIZANDO PERFORMANCE •
                    </textPath>
                </text>
            </svg>
        </div>
    </div>
    <style>
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin-slow 25s linear infinite;
        }
    </style>
    @endif
    @include('partials.js-masks')
</body>
</html>
