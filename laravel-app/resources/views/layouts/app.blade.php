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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
        <header class="fixed top-0 left-0 right-0 z-[100] bg-zinc-950/70 backdrop-blur-2xl border-b border-zinc-900/50 transition-all duration-500">
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
                    <a href="#features" class="text-[10px] font-black text-zinc-500 hover:text-white uppercase tracking-[0.2em] transition-all">Recursos</a>
                    <a href="#pricing" class="text-[10px] font-black text-zinc-500 hover:text-white uppercase tracking-[0.2em] transition-all">Preços</a>
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
                        <li><a href="#features" class="text-zinc-500 hover:text-white transition-all text-xs font-bold uppercase tracking-widest italic">Recursos</a></li>
                        <li><a href="#pricing" class="text-zinc-500 hover:text-white transition-all text-xs font-bold uppercase tracking-widest italic">Planos</a></li>
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

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
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
    <script src="{{ asset('js/app.js') }}" defer></script>


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
    <div id="premiumModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-6 bg-zinc-950/90 backdrop-blur-xl animate-fade-in" style="display: none;">
        <div class="bg-zinc-900 border border-zinc-800 w-full max-w-lg rounded-[3.5rem] p-10 shadow-3xl animate-fade-in-up text-center space-y-8 relative overflow-hidden">
            <div class="absolute -top-10 -left-10 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl"></div>
            
            <div class="w-24 h-24 bg-emerald-500 text-zinc-950 rounded-[2rem] flex items-center justify-center mx-auto shadow-2xl shadow-emerald-500/20 transform -rotate-12">
                <i data-lucide="crown" class="w-12 h-12"></i>
            </div>
            
            <div class="space-y-3">
                <h3 class="text-4xl font-black text-white tracking-tighter uppercase italic">Protocolo <span class="text-emerald-500">Premium</span></h3>
                <p class="text-zinc-500 font-medium leading-relaxed">Esta funcionalidade exclusiva faz parte do plano **Performance Elite**. Evolua seu treino com processamento neural e biometria avançada.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 pt-4">
                <a href="{{ route('plano') }}" class="w-full py-6 bg-emerald-500 text-zinc-950 font-black rounded-3xl hover:bg-emerald-400 transition-all flex items-center justify-center gap-3 shadow-xl text-xs uppercase tracking-[0.2em]">
                    <i data-lucide="zap" class="w-4 h-4 fill-current"></i>
                    ATIVAR ACESSO ELITE
                </a>
                <button onclick="document.getElementById('premiumModal').style.display = 'none'" class="w-full py-5 bg-zinc-950 border border-zinc-800 text-zinc-600 font-black rounded-3xl hover:text-white transition-all text-[10px] uppercase tracking-widest">
                    FECHAR
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            const modal = document.getElementById('premiumModal');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.style.display = 'none';
                    }
                });
            }
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
        
        window.isPremiumUser = {{ auth()->check() && auth()->user()->hasPremiumAccess() ? 'true' : 'false' }};
    </script>

    @if(auth()->check())
        @include('partials.onboarding_modal')
        @include('partials.ai-credits-modal')
    @endif
    @include('partials.confirm-delete-modal')
    @include('partials.legal-modal')
    @include('partials.toast')
    @include('partials.error-modal')
    <x-demo-badge />
</body>
</html>
