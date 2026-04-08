<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <div class="bg-blue-600 text-white rounded-lg p-1.5 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <span class="label">NexShape</span>
        </a>
    </div>

    <div class="sidebar-content">
        <!-- Dashboard & Social -->
        <div class="nav-group">
            <div class="nav-label">Geral</div>
            <div class="nav-item">
                <a href="{{ route('dashboard') }}"
                    class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span class="label">Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('leaderboard.index') }}"
                    class="nav-link {{ request()->routeIs('leaderboard.*') ? 'active' : '' }}">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    <span class="label">Ranking</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('chat.page') }}"
                    class="nav-link {{ request()->routeIs('chat.page') ? 'active' : '' }}">
                    <svg class="icon text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9l-.707.707M12 18c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8z">
                        </path>
                    </svg>
                    <span class="label">IA Chat</span>
                </a>
            </div>
        </div>

        <!-- Usuários -->
        <div class="nav-group">
            <div class="nav-label">Usuários</div>
            <div
                class="nav-item has-submenu {{ request()->is('profile*') || request()->is('assessments*') || request()->is('progression*') || request()->is('weight*') || request()->is('body-analysis*') || request()->is('active-rest*') ? 'open' : '' }}">
                <a href="#" class="nav-link">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="label">Meu Perfil</span>
                    <svg class="icon ml-auto w-4 h-4 transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu">
                    <li><a href="{{ route('profile') }}"
                            class="submenu-link {{ request()->routeIs('profile') ? 'active' : '' }}">Perfil</a></li>
                    <li><a href="{{ route('assessments.index') }}"
                            class="submenu-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}">Avaliação
                            Física</a></li>
                    <li><a href="{{ route('body-analysis.index') }}"
                            class="submenu-link {{ request()->routeIs('body-analysis.*') ? 'active' : '' }}">Análise
                            Corporal (IA)</a></li>
                    <li><a href="{{ route('progression.charts') }}"
                            class="submenu-link {{ request()->routeIs('progression.*') ? 'active' : '' }}">Histórico /
                            Evolução</a></li>
                    <li><a href="{{ route('weight') }}"
                            class="submenu-link {{ request()->routeIs('weight') ? 'active' : '' }}">Peso</a></li>
                    <li><a href="{{ route('active-rest.index') }}"
                            class="submenu-link {{ request()->routeIs('active-rest.*') ? 'active' : '' }}">Descanso Ativo</a></li>
                    <li><a href="{{ route('patient.portal.index') }}"
                            class="submenu-link {{ request()->routeIs('patient.portal.*') ? 'active' : '' }}">Meu Plano</a></li>
                </ul>
            </div>
        </div>

        <!-- Treinos -->
        <div class="nav-group">
            <div class="nav-label">Treinos</div>
            <div class="nav-item has-submenu {{ request()->is('exercise*') ? 'open' : '' }}">
                <a href="#" class="nav-link">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span class="label">Planejamento</span>
                    <svg class="icon ml-auto w-4 h-4 transition-transform duration-200" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
                <ul class="submenu">
                    <li><a href="{{ route('exercise') }}"
                            class="submenu-link {{ request()->routeIs('exercise') ? 'active' : '' }}">Plano de
                            Treino</a></li>
                    <li><a href="{{ route('exercise') }}"
                            class="submenu-link {{ request()->routeIs('exercise') ? 'active' : '' }}">Mural de Exercícios</a></li>
                    <li><a href="{{ route('progression.plans.index') }}"
                            class="submenu-link {{ request()->routeIs('progression.plans.*') ? 'active' : '' }}">Meus Treinos</a></li>
                    <li><a href="{{ route('progression.charts') }}"
                            class="submenu-link {{ request()->routeIs('progression.charts') ? 'active' : '' }}">Gráficos de Carga</a></li>
                </ul>
            </div>
        </div>

        <!-- Nutrição -->
        <div class="nav-group">
            <div class="nav-label">Nutrição</div>
            <div class="nav-item">
                <a href="{{ route('diary') }}" class="nav-link {{ request()->routeIs('diary') ? 'active' : '' }}">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3">
                        </path>
                    </svg>
                    <span class="label">Plano Alimentar</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('hydration.index') }}"
                    class="nav-link {{ request()->routeIs('hydration.*') ? 'active' : '' }}">
                    <svg class="icon text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span class="label">Controle de Hidratação</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('nutrition.index') }}"
                    class="nav-link {{ request()->routeIs('nutrition.*') ? 'active' : '' }}">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="label">Objetivos</span>
                </a>
            </div>
        </div>

        <!-- Comunicação -->
        <div class="nav-group">
            <div class="nav-label">Mensagens</div>
            <div class="nav-item">
                <a href="{{ route('messages.index') }}"
                    class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z">
                        </path>
                    </svg>
                    <span class="label">Chat Direto</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('internal-email.inbox') }}"
                    class="nav-link {{ request()->is('internal-email*') ? 'active' : '' }}">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span class="label">Correio Interno</span>
                    @php($unreadEmails = \App\Models\InternalEmail::where('destinatario_id', auth()->id())->where('lida', false)->count())
                    @if($unreadEmails > 0)
                        <span
                            class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full ml-auto">{{ $unreadEmails }}</span>
                    @endif
                </a>
            </div>
        </div>

        <!-- Relatórios -->
        <div class="nav-group">
            <div class="nav-label">Análise</div>
            <div class="nav-item">
                <a href="{{ route('report') }}" class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="label">Relatórios Gerenciais</span>
                </a>
            </div>
        </div>

        @if(auth()->user()->hasPremiumAccess())
            <!-- Portal Pro -->
            <div class="nav-group">
                <div class="nav-label">Exclusivo</div>
                <div class="nav-item has-submenu {{ request()->is('professional*') ? 'open' : '' }}">
                    <a href="#" class="nav-link" style="color: #60a5fa;">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z">
                            </path>
                        </svg>
                        <span class="label font-bold">Portal Pro</span>
                        <svg class="icon ml-auto w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{ route('professional.dashboard') }}" class="submenu-link {{ request()->routeIs('professional.dashboard') ? 'active' : '' }}">Visão Geral</a></li>
                        <li><a href="{{ route('professional.patients.index') }}" class="submenu-link {{ request()->routeIs('professional.patients.*') ? 'active' : '' }}">Meus Alunos / Pacientes</a></li>
                        <li><a href="{{ route('professional.ai-wizard') }}" class="submenu-link {{ request()->routeIs('professional.ai-wizard') ? 'active' : '' }}">Assistente IA Profissional</a></li>
                        <li><a href="{{ route('professional.branding.index') }}" class="submenu-link {{ request()->routeIs('professional.branding.*') ? 'active' : '' }}">Personalização da Marca</a></li>
                        <li><a href="{{ route('professional.billing.index') }}" class="submenu-link {{ request()->routeIs('professional.billing.*') ? 'active' : '' }}">Plano de Assinatura</a></li>
                    </ul>
                </div>
            </div>
        @endif

        @if(auth()->user()->is_admin)
            <!-- Administração -->
            <div class="nav-group">
                <div class="nav-label">Administração</div>
                <div class="nav-item has-submenu {{ request()->is('admin*') ? 'open' : '' }}">
                    <a href="#" class="nav-link">
                        <svg class="icon text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="label">Gestão Central</span>
                        <svg class="icon ml-auto w-4 h-4 transition-transform duration-200" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </a>
                    <ul class="submenu">
                        <li><a href="{{ route('admin.dashboard') }}" class="submenu-link">Dashboard</a></li>
                        <li><a href="{{ route('admin.users') }}" class="submenu-link">Usuários</a></li>
                        <li><a href="{{ route('admin.settings') }}" class="submenu-link">Permissões e Configuração</a></li>
                        <li><a href="{{ route('admin.exercises.catalog') }}" class="submenu-link">Catálogo</a></li>
                        <li><a href="{{ route('admin.announcements') }}" class="submenu-link">Avisos</a></li>
                        <li><a href="{{ route('admin.system-errors') }}" class="submenu-link">Logs de Erro</a></li>
                        <li><a href="{{ route('admin.ai.monitoring') }}" class="submenu-link">Monitoramento IA</a></li>
                        <li><a href="{{ route('admin.lgpd.index') }}" class="submenu-link">Painel LGPD e Privacidade</a></li>
                    </ul>
                </div>
            </div>
        @endif
    </div>

    <div class="sidebar-footer" style="padding: 1.5rem; border-top: 1px solid var(--border-color);">

        <form action="{{ route('logout') }}" method="post" class="nav-logout-form">
            @csrf
            <button type="submit" class="nav-link w-full text-left"
                style="background: none; border: none; cursor: pointer;">
                <svg class="icon text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                <span class="label text-red-500">Sair da Conta</span>
            </button>
        </form>
    </div>
</aside>