<aside class="sidebar" id="sidebar" x-data="{ openMenus: [
    {{ request()->routeIs('admin.users*', 'admin.pdf-companies.*') ? "'usuarios'" : '' }},
    {{ request()->routeIs('admin.pdf-historico.*') ? "'atendimento'" : '' }},
    {{ request()->routeIs('admin.registrations.*', 'admin.supplements.*') ? "'cadastros'" : '' }},
    {{ request()->routeIs('admin.plans.*', 'admin.settings.payments', 'admin.financial.*') ? "'financeiro'" : '' }},
    {{ request()->routeIs('admin.especialidades.*', 'admin.roles.*', 'admin.settings', 'admin.monitoring', 'admin.system-errors') ? "'administrativo'" : '' }},
    {{ request()->routeIs('admin.settings.email.*', 'admin.api-integrations.*') ? "'config'" : '' }}
].filter(Boolean) }">
    <!-- Header / Logo: NexShape Branding -->
    <div class="sidebar-header bg-[#0d121f] border-b border-white/5">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-2">
            <div class="bg-blue-600 text-white rounded-lg p-1.5 flex items-center justify-center shadow-lg shadow-blue-500/20">
                <i class="fas fa-bolt text-lg"></i>
            </div>
            <span class="text-xl font-black text-white tracking-tighter">NexShape</span>
        </a>
    </div>

    <style>
        .admin-sidebar-nav { overflow-x: hidden !important; }
        .admin-sidebar-nav .nav-link, .admin-sidebar-nav .submenu-link {
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #94a3b8;
            transition: all 0.2s;
            border-radius: 0.75rem;
            margin: 0.25rem 0.5rem;
        }
        .admin-sidebar-nav .nav-link:hover, .admin-sidebar-nav .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }
        .admin-sidebar-nav .nav-link.active {
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
        }
        .admin-sidebar-nav .submenu {
            padding-left: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .admin-sidebar-nav .submenu-link {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }
        .admin-sidebar-nav .submenu-link.active {
            color: #3b82f6;
            font-weight: bold;
        }
        .chevron {
            margin-left: auto;
            transition: transform 0.2s;
            font-size: 0.7rem;
            opacity: 0.5;
        }
        .open .chevron { transform: rotate(180deg); opacity: 1; }
    </style>

    <div class="sidebar-content admin-sidebar-nav">
        @php($__nav = fn (string $k): bool => ($adminNavVisible[$k] ?? true))

        <!-- DASHBOARD -->
        <div class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large text-blue-500"></i>
                <span class="label text-sm font-medium">Dashboard</span>
            </a>
        </div>

        <!-- USUÁRIOS -->
        <div class="px-6 py-2 mt-4 mb-2">
            <span class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.25em]">Comunidade</span>
        </div>

        <div class="nav-item" :class="{ 'open': openMenus.includes('usuarios') }">
            <a href="#" class="nav-link" x-on:click.prevent="openMenus.includes('usuarios') ? openMenus = openMenus.filter(m => m !== 'usuarios') : openMenus.push('usuarios')">
                <i class="fas fa-users text-indigo-400"></i>
                <span class="label text-sm font-medium">Usuários</span>
                <i class="fas fa-chevron-down chevron"></i>
            </a>
            <ul class="submenu space-y-1" x-show="openMenus.includes('usuarios')" x-collapse>
                <li><a href="{{ route('admin.users') }}?role=aluno" class="submenu-link">Alunos</a></li>
                <li><a href="{{ route('admin.users') }}?role=paciente" class="submenu-link">Pacientes</a></li>
                <li><a href="{{ route('admin.users') }}?role=professional" class="submenu-link">Profissionais</a></li>
                @if($__nav('admin_nav_pdf_companies'))
                <li><a href="{{ route('admin.pdf-companies.index') }}" class="submenu-link {{ request()->routeIs('admin.pdf-companies.*') ? 'active' : '' }}">Clínicas</a></li>
                @endif
                <li><a href="{{ route('admin.users') }}?role=receptionist" class="submenu-link">Funcionários</a></li>
            </ul>
        </div>

        <!-- MENU DE CADASTROS -->
        <div class="nav-item" :class="{ 'open': openMenus.includes('cadastros') }">
            <a href="#" class="nav-link" x-on:click.prevent="openMenus.includes('cadastros') ? openMenus = openMenus.filter(m => m !== 'cadastros') : openMenus.push('cadastros')">
                <i class="fas fa-plus-circle text-cyan-400"></i>
                <span class="label text-sm font-medium">Cadastros</span>
                <i class="fas fa-chevron-down chevron"></i>
            </a>
            <ul class="submenu space-y-1" x-show="openMenus.includes('cadastros')" x-collapse>
                <li>
                    <a href="{{ route('admin.registrations.index') }}" class="submenu-link {{ request()->routeIs('admin.registrations.*') ? 'active' : '' }}">
                        Ficha Cadastral
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.supplements.index') }}" class="submenu-link {{ request()->routeIs('admin.supplements.*') ? 'active' : '' }}">
                        Suplementos
                    </a>
                </li>
            </ul>
        </div>

        <!-- ATENDIMENTO -->
        <div class="px-6 py-2 mt-4 mb-2">
            <span class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.25em]">Clínico</span>
        </div>

        <div class="nav-item" :class="{ 'open': openMenus.includes('atendimento') }">
            <a href="#" class="nav-link" x-on:click.prevent="openMenus.includes('atendimento') ? openMenus = openMenus.filter(m => m !== 'atendimento') : openMenus.push('atendimento')">
                <i class="fas fa-stethoscope text-emerald-400"></i>
                <span class="label text-sm font-medium">Atendimento</span>
                <i class="fas fa-chevron-down chevron"></i>
            </a>
            <ul class="submenu space-y-1" x-show="openMenus.includes('atendimento')" x-collapse>
                <li><a href="#" class="submenu-link">Agenda</a></li>
                <li><a href="#" class="submenu-link">Atendimentos</a></li>
                <li><a href="{{ route('admin.pdf-historico.index') }}" class="submenu-link {{ request()->routeIs('admin.pdf-historico.*') ? 'active' : '' }}">Prontuários / Laudos</a></li>
            </ul>
        </div>

        <!-- FINANCEIRO -->
        <div class="px-6 py-2 mt-4 mb-2">
            <span class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.25em]">Negócios</span>
        </div>

        <div class="nav-item" :class="{ 'open': openMenus.includes('financeiro') }">
            <a href="#" class="nav-link" x-on:click.prevent="openMenus.includes('financeiro') ? openMenus = openMenus.filter(m => m !== 'financeiro') : openMenus.push('financeiro')">
                <i class="fas fa-wallet text-amber-500"></i>
                <span class="label text-sm font-medium">Financeiro</span>
                <i class="fas fa-chevron-down chevron"></i>
            </a>
            <ul class="submenu space-y-1" x-show="openMenus.includes('financeiro')" x-collapse>
                <li><a href="{{ route('admin.financial.dashboard') }}" class="submenu-link {{ request()->routeIs('admin.financial.dashboard') ? 'active' : '' }}">Dashboard Financeiro</a></li>
                @if($__nav('admin_nav_plans'))
                <li><a href="{{ route('admin.plans.index') }}" class="submenu-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">Planos & Preços</a></li>
                @endif
                <li><a href="{{ route('admin.financial.management') }}" class="submenu-link {{ request()->routeIs('admin.financial.management') ? 'active' : '' }}">Gestão de Assinaturas</a></li>
                <li><a href="{{ route('admin.financial.ai-credits.dashboard') }}" class="submenu-link {{ request()->routeIs('admin.financial.ai-credits.*') ? 'active' : '' }}">Consumo de IA</a></li>
                <li><a href="{{ route('admin.financial.reports') }}" class="submenu-link {{ request()->routeIs('admin.financial.reports') ? 'active' : '' }}">Relatórios de Receita</a></li>
                @if($__nav('admin_nav_settings_payments'))
                <li><a href="{{ route('admin.settings.payments') }}" class="submenu-link {{ request()->routeIs('admin.settings.payments') ? 'active' : '' }}">Gateway de Pagamento</a></li>
                @endif
            </ul>
        </div>

        <!-- ADMINISTRATIVO -->
        <div class="px-6 py-2 mt-4 mb-2">
            <span class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.25em]">Gestão</span>
        </div>

        <div class="nav-item" :class="{ 'open': openMenus.includes('administrativo') }">
            <a href="#" class="nav-link" x-on:click.prevent="openMenus.includes('administrativo') ? openMenus = openMenus.filter(m => m !== 'administrativo') : openMenus.push('administrativo')">
                <i class="fas fa-cog text-zinc-500"></i>
                <span class="label text-sm font-medium">Administrativo</span>
                <i class="fas fa-chevron-down chevron"></i>
            </a>
            <ul class="submenu space-y-1" x-show="openMenus.includes('administrativo')" x-collapse>
                @if($__nav('admin_nav_especialidades'))
                <li><a href="{{ route('admin.especialidades.index') }}" class="submenu-link {{ request()->routeIs('admin.especialidades.*') ? 'active' : '' }}">Especialidades</a></li>
                @endif
                <li><a href="{{ route('admin.roles.index') }}" class="submenu-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">Permissões</a></li>
                @if($__nav('admin_nav_settings'))
                <li><a href="{{ route('admin.settings') }}" class="submenu-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">Configurações</a></li>
                @endif
                @if($__nav('admin_nav_monitoring'))
                <li><a href="{{ route('admin.monitoring') }}" class="submenu-link {{ request()->routeIs('admin.monitoring') ? 'active' : '' }}">Relatórios</a></li>
                @endif
                @if($__nav('admin_nav_system_errors'))
                <li><a href="{{ route('admin.system-errors') }}" class="submenu-link {{ request()->routeIs('admin.system-errors') ? 'active' : '' }}">Logs</a></li>
                @endif
            </ul>
        </div>

        <div class="pb-10 mt-6">
            <div class="px-6 py-2 border-t border-white/5 pt-4">
                <span class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.25em]">Suporte Técnico</span>
            </div>
            
            <div class="nav-item" :class="{ 'open': openMenus.includes('config') }">
                <a href="#" class="nav-link" x-on:click.prevent="openMenus.includes('config') ? openMenus = openMenus.filter(m => m !== 'config') : openMenus.push('config')">
                    <i class="fas fa-tools text-zinc-500"></i>
                    <span class="label text-sm font-medium">Configurações Avançadas</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <ul class="submenu space-y-1" x-show="openMenus.includes('config')" x-collapse>
                    @if($__nav('admin_nav_api_integrations'))
                    <li><a href="{{ route('admin.api-integrations.index') }}" class="submenu-link">APIs Externas</a></li>
                    @endif
                    @if($__nav('admin_nav_email'))
                    <li><a href="{{ route('admin.settings.email.providers') }}" class="submenu-link">E-mail SMTP</a></li>
                    @endif
                </ul>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.pdf-suite.index') }}" class="nav-link {{ request()->is('admin/pdf-*') ? 'active' : '' }}">
                    <i class="fas fa-file-pdf text-rose-400"></i>
                    <span class="label text-sm font-medium">PDF Analytics</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer p-6 border-t border-white/5 bg-black/20">
        <a href="{{ route('dashboard', ['view_as_user' => 1]) }}" class="flex items-center gap-3 px-4 py-3 bg-blue-600/10 border border-blue-500/20 text-blue-400 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-blue-600/20 transition-all mb-4">
            <i class="fas fa-external-link-alt"></i>
            <span>Portal Usuário</span>
        </a>

        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-red-500/60 hover:text-red-500 transition-colors text-xs font-black uppercase tracking-widest">
                <i class="fas fa-power-off"></i>
                <span>Sair do Sistema</span>
            </button>
        </form>
    </div>
</aside>
