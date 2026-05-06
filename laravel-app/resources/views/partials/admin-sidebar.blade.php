<aside class="sidebar bg-zinc-950 border-r border-zinc-900 shadow-2xl flex flex-col h-screen" id="sidebar" x-data="{ openMenus: [
    {{ request()->routeIs('admin.users*', 'admin.pdf-companies.*') ? "'usuarios'" : '' }},
    {{ request()->routeIs('admin.pdf-historico.*') ? "'atendimento'" : '' }},
    {{ request()->routeIs('admin.registrations.*', 'admin.supplements.*') ? "'cadastros'" : '' }},
    {{ request()->routeIs('admin.plans.*', 'admin.settings.payments', 'admin.financial.*') ? "'financeiro'" : '' }},
    {{ request()->routeIs('admin.especialidades.*', 'admin.roles.*', 'admin.settings', 'admin.monitoring', 'admin.system-errors', 'admin.operations.*', 'admin.backups.*') ? "'administrativo'" : '' }},
    {{ request()->routeIs('admin.settings.email.*', 'admin.api-integrations.*', 'admin.kb.*') ? "'comunicacao'" : '' }}
].filter(Boolean) }">
    <!-- Header / Logo -->
    <div class="sidebar-header p-10 border-b border-zinc-900 bg-zinc-950/50">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 group">
            <div class="w-12 h-12 bg-emerald-500 text-zinc-950 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-500/20 transform group-hover:rotate-12 transition-all duration-500">
                <i data-lucide="zap" class="w-6 h-6 fill-current"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-xl font-black text-white tracking-tighter uppercase leading-none">NEX<span class="text-emerald-500">SHAPE</span></span>
                <span class="text-[8px] font-black text-zinc-700 uppercase tracking-[0.4em] mt-1 italic">CORE INTELLIGENCE</span>
            </div>
        </a>
    </div>

    <style>
        .admin-sidebar-nav { overflow-x: hidden !important; }
        .admin-sidebar-nav .nav-link, .admin-sidebar-nav .submenu-link {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .admin-sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.03);
            color: #fff;
        }
        .admin-sidebar-nav .nav-link.active {
            color: #10b981;
            background: rgba(16, 185, 129, 0.05);
            border-right: 2px solid #10b981;
        }
        .admin-sidebar-nav .submenu-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.02);
        }
        .admin-sidebar-nav .submenu-link.active {
            color: #10b981;
            background: rgba(16, 185, 129, 0.05);
            font-weight: 800;
        }
        .chevron {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .open .chevron { transform: rotate(180deg); color: #10b981; }
        
        .nav-section-title {
            padding: 2rem 1.5rem 0.75rem;
            text-transform: uppercase;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: 0.3em;
            color: #27272a;
        }
    </style>

    <div class="sidebar-content flex-1 overflow-y-auto px-4 py-6 admin-sidebar-nav custom-scrollbar">
        @php($__nav = fn (string $k): bool => ($adminNavVisible[$k] ?? true))

        <div class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-zinc-500' }}">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
                <span class="text-[11px] font-black uppercase tracking-widest">Dashboard</span>
            </a>
        </div>

        @can('viewPulse')
        <div class="nav-item">
            <a href="/pulse" class="nav-link flex items-center gap-4 px-4 py-3.5 rounded-2xl {{ request()->is('pulse*') ? 'active' : 'text-zinc-500' }}" target="_blank">
                <i data-lucide="activity" class="w-5 h-5"></i>
                <span class="text-[11px] font-black uppercase tracking-widest">Pulse Monitor</span>
            </a>
        </div>
        @endcan

        <!-- COMUNIDADE -->
        <div class="nav-section-title">Comunidade</div>

        <div class="nav-item" :class="{ 'open': openMenus.includes('usuarios') }">
            <a href="#" class="nav-link flex items-center justify-between px-4 py-3.5 rounded-2xl text-zinc-500" x-on:click.prevent="openMenus.includes('usuarios') ? openMenus = openMenus.filter(m => m !== 'usuarios') : openMenus.push('usuarios')">
                <div class="flex items-center gap-4">
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">Usuários</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron"></i>
            </a>
            <ul class="submenu space-y-1 mt-2 pl-4" x-show="openMenus.includes('usuarios')" x-collapse>
                <li><a href="{{ route('admin.users') }}?role=aluno" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-600 hover:text-white">Alunos</a></li>
                <li><a href="{{ route('admin.users') }}?role=paciente" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-600 hover:text-white">Pacientes</a></li>
                <li><a href="{{ route('admin.users') }}?role=professional" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-600 hover:text-white">Profissionais</a></li>
                @if($__nav('admin_nav_pdf_companies'))
                <li><a href="{{ route('admin.pdf-companies.index') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.pdf-companies.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Clínicas</a></li>
                @endif
                <li><a href="{{ route('admin.users') }}?role=receptionist" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-600 hover:text-white">Funcionários</a></li>
            </ul>
        </div>

        <!-- CADASTROS -->
        <div class="nav-item" :class="{ 'open': openMenus.includes('cadastros') }">
            <a href="#" class="nav-link flex items-center justify-between px-4 py-3.5 rounded-2xl text-zinc-500" x-on:click.prevent="openMenus.includes('cadastros') ? openMenus = openMenus.filter(m => m !== 'cadastros') : openMenus.push('cadastros')">
                <div class="flex items-center gap-4">
                    <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">Cadastros</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron"></i>
            </a>
            <ul class="submenu space-y-1 mt-2 pl-4" x-show="openMenus.includes('cadastros')" x-collapse>
                <li><a href="{{ route('admin.registrations.index') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.registrations.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Ficha Cadastral</a></li>
                <li><a href="{{ route('admin.supplements.index') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.supplements.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Suplementos</a></li>
            </ul>
        </div>

        <!-- CLÍNICO -->
        <div class="nav-section-title">Clínico</div>

        <div class="nav-item" :class="{ 'open': openMenus.includes('atendimento') }">
            <a href="#" class="nav-link flex items-center justify-between px-4 py-3.5 rounded-2xl text-zinc-500" x-on:click.prevent="openMenus.includes('atendimento') ? openMenus = openMenus.filter(m => m !== 'atendimento') : openMenus.push('atendimento')">
                <div class="flex items-center gap-4">
                    <i data-lucide="stethoscope" class="w-5 h-5"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">Atendimento</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron"></i>
            </a>
            <ul class="submenu space-y-1 mt-2 pl-4" x-show="openMenus.includes('atendimento')" x-collapse>
                <li><a href="{{ route('admin.pdf-historico.index') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.pdf-historico.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Prontuários</a></li>
            </ul>
        </div>

        <!-- NEGÓCIOS -->
        <div class="nav-section-title">Negócios</div>

        <div class="nav-item" :class="{ 'open': openMenus.includes('financeiro') }">
            <a href="#" class="nav-link flex items-center justify-between px-4 py-3.5 rounded-2xl text-zinc-500" x-on:click.prevent="openMenus.includes('financeiro') ? openMenus = openMenus.filter(m => m !== 'financeiro') : openMenus.push('financeiro')">
                <div class="flex items-center gap-4">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">Financeiro</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron"></i>
            </a>
            <ul class="submenu space-y-1 mt-2 pl-4" x-show="openMenus.includes('financeiro')" x-collapse>
                <li><a href="{{ route('admin.financial.dashboard') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.financial.dashboard') ? 'active' : 'text-zinc-600 hover:text-white' }}">Dashboard</a></li>
                @if($__nav('admin_nav_plans'))
                <li><a href="{{ route('admin.plans.index') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.plans.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Planos</a></li>
                @endif
                <li><a href="{{ route('admin.financial.management') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.financial.management') ? 'active' : 'text-zinc-600 hover:text-white' }}">Assinaturas</a></li>
                @if($__nav('admin_nav_settings_payments'))
                <li><a href="{{ route('admin.settings.payments') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.settings.payments') ? 'active' : 'text-zinc-600 hover:text-white' }}">Pagamentos</a></li>
                @endif
                <li><a href="{{ route('admin.representatives.index') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.representatives.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Representantes</a></li>
                <li><a href="{{ route('admin.representatives.withdrawals') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.representatives.withdrawals') ? 'active' : 'text-zinc-600 hover:text-white' }}">Resgates</a></li>
            </ul>
        </div>

        <!-- GESTÃO -->
        <div class="nav-section-title">Gestão</div>

        <div class="nav-item" :class="{ 'open': openMenus.includes('administrativo') }">
            <a href="#" class="nav-link flex items-center justify-between px-4 py-3.5 rounded-2xl text-zinc-500" x-on:click.prevent="openMenus.includes('administrativo') ? openMenus = openMenus.filter(m => m !== 'administrativo') : openMenus.push('administrativo')">
                <div class="flex items-center gap-4">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">Administrativo</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron"></i>
            </a>
            <ul class="submenu space-y-1 mt-2 pl-4" x-show="openMenus.includes('administrativo')" x-collapse>
                @if($__nav('admin_nav_especialidades'))
                <li><a href="{{ route('admin.especialidades.index') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.especialidades.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Especialidades</a></li>
                @endif
                <li><a href="{{ route('admin.roles.index') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.roles.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Permissões</a></li>
                @if($__nav('admin_nav_settings'))
                <li><a href="{{ route('admin.settings') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.settings') && !request()->fullUrlIs('*#finance*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Configurações</a></li>
                <li><a href="{{ route('admin.settings') }}#finance" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-600 hover:text-white group">
                    <span>Financeiro</span>
                    <span class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity text-emerald-500 text-[8px]">Novo</span>
                </a></li>
                @endif
                @if($__nav('admin_nav_monitoring'))
                <li><a href="{{ route('admin.monitoring') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.monitoring') ? 'active' : 'text-zinc-600 hover:text-white' }}">Relatórios</a></li>
                @endif
                @if($__nav('admin_nav_system_errors'))
                <li><a href="{{ route('admin.system-errors') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.system-errors') ? 'active' : 'text-zinc-600 hover:text-white' }}">Logs de Erro</a></li>
                @endif
                @if($__nav('admin_nav_backups'))
                <li><a href="{{ route('admin.backups.index') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.backups.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Backup</a></li>
                @endif
            </ul>
        </div>

        <!-- COMUNICAÇÃO -->
        <div class="nav-section-title">Comunicação</div>

        <div class="nav-item" :class="{ 'open': openMenus.includes('comunicacao') }">
            <a href="#" class="nav-link flex items-center justify-between px-4 py-3.5 rounded-2xl text-zinc-500" x-on:click.prevent="openMenus.includes('comunicacao') ? openMenus = openMenus.filter(m => m !== 'comunicacao') : openMenus.push('comunicacao')">
                <div class="flex items-center gap-4">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">E-mail & Notif.</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron"></i>
            </a>
            <ul class="submenu space-y-1 mt-2 pl-4" x-show="openMenus.includes('comunicacao')" x-collapse>
                <li><a href="{{ route('admin.settings') }}#email-settings" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-600 hover:text-white">Global</a></li>
                @if($__nav('admin_nav_email'))
                <li><a href="{{ route('admin.settings.email.providers') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.settings.email.providers*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Provedores</a></li>
                @endif
                <li><a href="{{ route('admin.settings.email.templates.index') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.settings.email.templates.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Templates</a></li>
                <li><a href="{{ route('admin.settings.email.logs') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.settings.email.logs') ? 'active' : 'text-zinc-600 hover:text-white' }}">Logs</a></li>
                <li><a href="{{ route('admin.kb.index') }}" class="submenu-link flex items-center px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest {{ request()->routeIs('admin.kb.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Base de Conhecimento</a></li>
            </ul>
        </div>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer p-6 border-t border-zinc-900 bg-zinc-950/50">
        <a href="{{ route('dashboard', ['view_as_user' => 1]) }}" class="flex items-center gap-4 p-4 rounded-2xl bg-emerald-500/5 border border-emerald-500/10 text-emerald-500 hover:bg-emerald-500/10 transition-all mb-4 group">
            <i data-lucide="external-link" class="w-5 h-5 transition-transform group-hover:translate-x-1 group-hover:-translate-y-1"></i>
            <span class="text-[10px] font-black uppercase tracking-widest">Portal Usuário</span>
        </a>

        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="group flex items-center gap-4 w-full p-4 rounded-2xl transition-all hover:bg-rose-500/5 text-zinc-600 hover:text-rose-500">
                <i data-lucide="log-out" class="w-5 h-5"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Sair do Core</span>
            </button>
        </form>
    </div>
</aside>

