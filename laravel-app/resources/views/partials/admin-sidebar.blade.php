<aside class="sidebar bg-zinc-950 border-r border-zinc-900 shadow-2xl flex flex-col h-screen transition-all duration-300" 
    id="sidebar" 
    x-data="{ 
        search: '',
        isCollapsed: false,
        openMenus: [
            @if(request()->routeIs('admin.dashboard*', 'admin.financial.dashboard', 'admin.commercial.dashboard')) 'dashboard', @endif
            @if(request()->routeIs('admin.kanban.*', 'admin.support.*', 'admin.kb.*')) 'operacoes', @endif
            @if(request()->routeIs('admin.users*', 'admin.registrations.pending', 'admin.registrations.index')) 'usuarios', @endif
            @if(request()->routeIs('admin.pdf-companies.*', 'admin.clinic-onboarding.*', 'admin.impersonate-clinic.*', 'onboarding-premium.*')) 'clinicas', @endif
            @if(request()->routeIs('admin.plans.*', 'admin.financial.management', 'admin.coupons.*')) 'planos', @endif
            @if(request()->routeIs('admin.financial.ai-credits.*', 'admin.billing.credits')) 'ia_credits', @endif
            @if(request()->routeIs('admin.settings.payments', 'admin.settings.payments.webhooks', 'admin.representatives.*', 'admin.financial.reports')) 'financeiro', @endif
            @if(request()->routeIs('admin.leads.*', 'admin.proposals.*', 'admin.goals.*', 'admin.commercial.*')) 'vendas', @endif
            @if(request()->routeIs('admin.monitoring', 'admin.cs.*')) 'relatorios', @endif
            @if(request()->routeIs('admin.ai.monitoring', 'admin.operations.*')) 'ia_automacao', @endif
            @if(request()->routeIs('admin.omnichannel*', 'omni.*')) 'chatbot', @endif
            @if(request()->routeIs('admin.settings', 'admin.especialidades.*', 'admin.muscles.*', 'admin.exercises.*', 'admin.training.*')) 'configuracoes', @endif
            @if(request()->routeIs('admin.roles.*', 'admin.lgpd.*', 'admin.security.*')) 'seguranca', @endif
            @if(request()->routeIs('admin.system-errors', 'admin.settings.email.logs', 'admin.settings.payments.webhooks', 'admin.backups.*')) 'logs', @endif
            @if(request()->routeIs('admin.api-integrations.*', 'admin.settings.email.providers', 'admin.settings.email.templates.*')) 'integracoes', @endif
            @if(request()->routeIs('admin.configuration-center.*')) 'sistema_avancado', @endif
        ].filter(Boolean),
        toggleMenu(id) {
            if (this.openMenus.includes(id)) {
                this.openMenus = this.openMenus.filter(m => m !== id);
            } else {
                this.openMenus.push(id);
            }
        },
        isVisible(label) {
            if (!this.search) return true;
            return label.toLowerCase().includes(this.search.toLowerCase());
        },
        isGroupVisible(items) {
            if (!this.search) return true;
            return items.some(item => item.toLowerCase().includes(this.search.toLowerCase()));
        }
    }">
    <!-- Header / Logo -->
    <div class="sidebar-header p-8 border-b border-zinc-900 bg-zinc-950/50 flex items-center justify-between">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 group" x-show="!isCollapsed">
            <div class="w-10 h-10 bg-emerald-500 text-zinc-950 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20 transform group-hover:rotate-12 transition-all duration-500">
                <i data-lucide="zap" class="w-5 h-5 fill-current"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-lg font-black text-white tracking-tighter uppercase leading-none">NEX<span class="text-emerald-500">SHAPE</span></span>
                <span class="text-[7px] font-black text-zinc-700 uppercase tracking-[0.4em] mt-1 italic">ADMIN CORE</span>
            </div>
        </a>
        <div class="w-10 h-10 bg-emerald-500 text-zinc-950 rounded-xl flex items-center justify-center shadow-lg" x-show="isCollapsed">
            <i data-lucide="zap" class="w-5 h-5 fill-current"></i>
        </div>
        <button @click="isCollapsed = !isCollapsed" class="text-zinc-600 hover:text-white transition-colors">
            <i :data-lucide="isCollapsed ? 'panel-left-open' : 'panel-left-close'" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Search Bar -->
    <div class="px-6 py-4 border-b border-zinc-900/50" x-show="!isCollapsed">
        <div class="relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-zinc-600"></i>
            <input type="text" 
                   x-model="search" 
                   placeholder="Buscar menu..." 
                   class="w-full bg-zinc-900/50 border border-zinc-800 rounded-xl py-2 pl-9 pr-4 text-[10px] font-bold text-zinc-400 focus:outline-none focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all placeholder:text-zinc-700 uppercase tracking-widest">
        </div>
    </div>

    <!-- Quick Actions (Admin Only) -->
    @if(auth()->user()?->is_admin)
    <div class="px-4 py-4 border-b border-zinc-900/50 flex flex-col gap-2" :class="isCollapsed ? 'items-center' : ''">
        <!-- Onboarding -->
        <a href="{{ route('onboarding-premium.index') }}" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-zinc-950 transition-all shadow-lg group relative"
           :class="isCollapsed ? 'justify-center p-0 w-10 h-10' : ''"
           title="Novo Onboarding Premium">
            <i data-lucide="rocket" class="w-4 h-4 group-hover:animate-bounce"></i>
            <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Onboarding</span>
        </a>

        <!-- Pending Registrations -->
        @php($pendingRegs = \App\Models\User::where('registration_approval_status', 'pending')->where('is_admin', false)->count())
        <a href="{{ route('admin.registrations.pending') }}" 
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-500 hover:bg-amber-500 hover:text-zinc-950 transition-all shadow-lg group relative"
           :class="isCollapsed ? 'justify-center p-0 w-10 h-10' : ''"
           title="Aprovações Pendentes">
            <i data-lucide="user-plus" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
            <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Aprovações</span>
            
            @if($pendingRegs > 0)
                <span class="absolute -top-1 -right-1 min-w-[1.2rem] h-[1.2rem] flex items-center justify-center rounded-lg bg-amber-500 text-zinc-950 text-[9px] font-black shadow-lg border-2 border-zinc-950"
                      :class="isCollapsed ? '' : ''">
                    {{ $pendingRegs > 9 ? '9+' : $pendingRegs }}
                </span>
            @endif
        </a>
    </div>
    @endif

    <style>
        .admin-sidebar-nav { overflow-x: hidden !important; }
        .admin-sidebar-nav .nav-link, .admin-sidebar-nav .submenu-link {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none !important;
            color: #71717a; /* text-zinc-500 */
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
        .admin-sidebar-nav .submenu-link {
            color: #52525b; /* text-zinc-600 */
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
        .admin-sidebar-nav .submenu {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .chevron {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .open .chevron { transform: rotate(180deg); color: #10b981; }
        
        .nav-section-title {
            padding: 1.5rem 1rem 0.5rem;
            text-transform: uppercase;
            font-size: 8px;
            font-weight: 900;
            letter-spacing: 0.2em;
            color: #27272a;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.05); border-radius: 10px; }
    </style>

    <div class="sidebar-content flex-1 overflow-y-auto px-4 py-6 admin-sidebar-nav custom-scrollbar" :class="isCollapsed ? 'items-center' : ''">
        @php($__nav = fn (string $k): bool => ($adminNavVisible[$k] ?? true))

        <!-- GRUPO: DASHBOARD -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Dashboard', 'Geral', 'Financeiro', 'Comercial'])">
            <button @click="toggleMenu('dashboard')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('dashboard') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Dashboard</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('dashboard') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Geral')"><a href="{{ route('admin.dashboard') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-zinc-600 hover:text-white' }}">Geral</a></li>
                <li x-show="isVisible('Financeiro')"><a href="{{ route('admin.financial.dashboard') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.financial.dashboard') ? 'active' : 'text-zinc-600 hover:text-white' }}">Financeiro</a></li>
                <li x-show="isVisible('Comercial')"><a href="{{ route('admin.commercial.dashboard') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.commercial.dashboard') ? 'active' : 'text-zinc-600 hover:text-white' }}">Comercial</a></li>
                @can('viewPulse')
                <li x-show="isVisible('Pulse Monitor')"><a href="/pulse" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->is('pulse*') ? 'active' : 'text-zinc-600 hover:text-white' }}" target="_blank">Pulse Monitor</a></li>
                @endcan
            </ul>
        </div>

        <!-- GRUPO: OPERAÇÕES -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Operações', 'Tarefas', 'Agenda', 'Suporte'])">
            <button @click="toggleMenu('operacoes')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('operacoes') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Operações</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('operacoes') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Tarefas')"><a href="{{ route('admin.kanban.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.kanban.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Gestão de Tarefas</a></li>
                <li x-show="isVisible('Suporte')"><a href="{{ route('admin.support.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.support.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Chamados de Suporte</a></li>
                <li x-show="isVisible('Base de Conhecimento')"><a href="{{ route('admin.kb.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.kb.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Base de Conhecimento</a></li>
                <li x-show="isVisible('Avisos')"><a href="{{ route('admin.announcements') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.announcements') ? 'active' : 'text-zinc-600 hover:text-white' }}">Avisos do Sistema</a></li>
            </ul>
        </div>

        <!-- GRUPO: USUÁRIOS -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Usuários', 'Alunos', 'Pacientes', 'Profissionais', 'Funcionários', 'Aprovações', 'Ficha Cadastral'])">
            <button @click="toggleMenu('usuarios')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('usuarios') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Usuários</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('usuarios') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Alunos')"><a href="{{ route('admin.users') }}?role=aluno" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest text-zinc-600 hover:text-white">Alunos</a></li>
                <li x-show="isVisible('Pacientes')"><a href="{{ route('admin.users') }}?role=paciente" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest text-zinc-600 hover:text-white">Pacientes</a></li>
                <li x-show="isVisible('Profissionais')"><a href="{{ route('admin.users') }}?role=professional" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest text-zinc-600 hover:text-white">Profissionais</a></li>
                <li x-show="isVisible('Funcionários')"><a href="{{ route('admin.users') }}?role=receptionist" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest text-zinc-600 hover:text-white">Funcionários</a></li>
                <li x-show="isVisible('Aprovações')"><a href="{{ route('admin.registrations.pending') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.registrations.pending') ? 'active' : 'text-zinc-600 hover:text-white' }}">Aprovações</a></li>
                <li x-show="isVisible('Ficha Cadastral')"><a href="{{ route('admin.registrations.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.registrations.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Ficha Cadastral</a></li>
            </ul>
        </div>

        <!-- GRUPO: CLÍNICAS -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Clínicas', 'Gestão', 'Implantação', 'Premium', 'Impersonação'])">
            <button @click="toggleMenu('clinicas')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('clinicas') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="building-2" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Clínicas</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('clinicas') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Gestão de Clínicas')"><a href="{{ route('admin.pdf-companies.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.pdf-companies.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Gestão de Clínicas</a></li>
                <li x-show="isVisible('Implantação')"><a href="{{ route('admin.clinic-onboarding.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.clinic-onboarding.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Implantação (Wizard)</a></li>
                <li x-show="isVisible('Onboarding Premium')"><a href="{{ route('onboarding-premium.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('onboarding-premium.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Onboarding Premium</a></li>
            </ul>
        </div>

        <!-- GRUPO: PLANOS E ASSINATURAS -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Planos', 'Assinaturas', 'Cupons'])">
            <button @click="toggleMenu('planos')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('planos') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Planos & Assin.</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('planos') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Planos')"><a href="{{ route('admin.plans.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.plans.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Planos</a></li>
                <li x-show="isVisible('Assinaturas')"><a href="{{ route('admin.financial.management') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.financial.management') ? 'active' : 'text-zinc-600 hover:text-white' }}">Gestão de Assinaturas</a></li>
                <li x-show="isVisible('Cupons')"><a href="{{ route('admin.coupons.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.coupons.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Cupons</a></li>
            </ul>
        </div>

        <!-- GRUPO: CRÉDITOS IA -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Créditos IA', 'Saldo', 'Consumo'])">
            <button @click="toggleMenu('ia_credits')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('ia_credits') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="brain-circuit" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Créditos IA</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('ia_credits') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Dashboard')"><a href="{{ route('admin.financial.ai-credits.dashboard') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.financial.ai-credits.dashboard') ? 'active' : 'text-zinc-600 hover:text-white' }}">Dashboard</a></li>
                <li x-show="isVisible('Relatórios')"><a href="{{ route('admin.financial.ai-credits.report') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.financial.ai-credits.report') ? 'active' : 'text-zinc-600 hover:text-white' }}">Relatórios</a></li>
                <li x-show="isVisible('Preços e Pacotes')"><a href="{{ route('admin.billing.credits') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.billing.credits') ? 'active' : 'text-zinc-600 hover:text-white' }}">Pacotes & Preços</a></li>
            </ul>
        </div>

        <!-- GRUPO: FINANCEIRO -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Financeiro', 'Pagamentos', 'Representantes', 'Resgates', 'Relatórios'])">
            <button @click="toggleMenu('financeiro')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('financeiro') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Financeiro</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('financeiro') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Pagamentos')"><a href="{{ route('admin.settings.payments') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.settings.payments') ? 'active' : 'text-zinc-600 hover:text-white' }}">Config. Pagamentos</a></li>
                <li x-show="isVisible('Representantes')"><a href="{{ route('admin.representatives.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.representatives.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Representantes</a></li>
                <li x-show="isVisible('Resgates')"><a href="{{ route('admin.representatives.withdrawals') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.representatives.withdrawals') ? 'active' : 'text-zinc-600 hover:text-white' }}">Resgates</a></li>
                <li x-show="isVisible('Relatórios')"><a href="{{ route('admin.financial.reports') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.financial.reports') ? 'active' : 'text-zinc-600 hover:text-white' }}">Relatórios Financeiros</a></li>
                <li x-show="isVisible('Webhooks')"><a href="{{ route('admin.settings.payments.webhooks') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.settings.payments.webhooks') ? 'active' : 'text-zinc-600 hover:text-white' }}">Inspecionador de Webhooks</a></li>
            </ul>
        </div>

        <!-- GRUPO: VENDAS E CRM -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Vendas', 'CRM', 'Leads', 'Funil', 'Propostas', 'Metas'])">
            <button @click="toggleMenu('vendas')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('vendas') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Vendas & CRM</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('vendas') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Leads')"><a href="{{ route('admin.leads.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.leads.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Leads</a></li>
                <li x-show="isVisible('Funil de Vendas')"><a href="{{ route('admin.leads.funnel') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.leads.funnel') ? 'active' : 'text-zinc-600 hover:text-white' }}">Funil de Vendas</a></li>
                <li x-show="isVisible('Propostas')"><a href="{{ route('admin.proposals.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.proposals.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Propostas Comerciais</a></li>
                <li x-show="isVisible('Metas')"><a href="{{ route('admin.goals.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.goals.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Metas de Performance</a></li>
            </ul>
        </div>

        <!-- GRUPO: RELATÓRIOS -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Relatórios', 'Monitoramento', 'Performance', 'Retenção'])">
            <button @click="toggleMenu('relatorios')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('relatorios') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Relatórios</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('relatorios') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Monitoramento')"><a href="{{ route('admin.monitoring') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.monitoring') ? 'active' : 'text-zinc-600 hover:text-white' }}">Monitoramento Geral</a></li>
                <li x-show="isVisible('Performance / CS')"><a href="{{ route('admin.cs.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.cs.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Performance / CS</a></li>
                <li x-show="isVisible('Retenção')"><a href="{{ route('admin.cs.retention') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.cs.retention') ? 'active' : 'text-zinc-600 hover:text-white' }}">Retenção & Churn</a></li>
            </ul>
        </div>

        <!-- GRUPO: IA E AUTOMAÇÃO -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['IA', 'Automação', 'Workers', 'Filas'])">
            <button @click="toggleMenu('ia_automacao')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('ia_automacao') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="cpu" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">IA & Automação</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('ia_automacao') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Monitoramento IA')"><a href="{{ route('admin.ai.monitoring') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.ai.monitoring') ? 'active' : 'text-zinc-600 hover:text-white' }}">Monitoramento IA</a></li>
                <li x-show="isVisible('Workers & Filas')"><a href="{{ route('admin.operations.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.operations.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Workers & Operações</a></li>
            </ul>
        </div>

        <!-- GRUPO: CHATBOT -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Chatbot', 'Omni', 'Bots'])">
            <button @click="toggleMenu('chatbot')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('chatbot') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="message-square" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Chatbot</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('chatbot') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Painel Omni')"><a href="{{ route('admin.omnichannel') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.omnichannel') ? 'active' : 'text-zinc-600 hover:text-white' }}">Painel OmniChannel</a></li>
                <li x-show="isVisible('Bots')"><a href="{{ route('admin.omnichannel.bots') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.omnichannel.bots') ? 'active' : 'text-zinc-600 hover:text-white' }}">Gestão de Bots</a></li>
            </ul>
        </div>

        <!-- GRUPO: CONFIGURAÇÕES -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Configurações', 'Geral', 'Especialidades', 'Músculos', 'Exercícios', 'Treinamentos'])">
            <button @click="toggleMenu('configuracoes')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('configuracoes') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="settings-2" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Configurações</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('configuracoes') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Geral')"><a href="{{ route('admin.settings') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.settings') ? 'active' : 'text-zinc-600 hover:text-white' }}">Geral</a></li>
                <li x-show="isVisible('Especialidades')"><a href="{{ route('admin.especialidades.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.especialidades.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Especialidades</a></li>
                <li x-show="isVisible('Músculos')"><a href="{{ route('admin.muscles.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.muscles.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Catálogo de Músculos</a></li>
                <li x-show="isVisible('Exercícios')"><a href="{{ route('admin.exercises.catalog') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.exercises.catalog') ? 'active' : 'text-zinc-600 hover:text-white' }}">Catálogo de Exercícios</a></li>
                <li x-show="isVisible('Treinamentos')"><a href="{{ route('admin.training.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.training.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Treinamentos</a></li>
            </ul>
        </div>

        <!-- GRUPO: SEGURANÇA -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Segurança', 'Permissões', 'LGPD'])">
            <button @click="toggleMenu('seguranca')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('seguranca') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Segurança</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('seguranca') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Permissões')"><a href="{{ route('admin.roles.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.roles.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Permissões (Roles)</a></li>
                <li x-show="isVisible('LGPD')"><a href="{{ route('admin.lgpd.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.lgpd.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Auditoria LGPD</a></li>
                <li x-show="isVisible('Segurança Geral')"><a href="{{ route('admin.security.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.security.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Segurança Geral</a></li>
            </ul>
        </div>

        <!-- GRUPO: LOGS E AUDITORIA -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Logs', 'Auditoria', 'Erros', 'Backup'])">
            <button @click="toggleMenu('logs')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('logs') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Logs & Auditoria</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('logs') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Logs de Erro')"><a href="{{ route('admin.system-errors') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.system-errors') ? 'active' : 'text-zinc-600 hover:text-white' }}">Logs de Erro</a></li>
                <li x-show="isVisible('Logs de E-mail')"><a href="{{ route('admin.settings.email.logs') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.settings.email.logs') ? 'active' : 'text-zinc-600 hover:text-white' }}">Logs de E-mail</a></li>
                <li x-show="isVisible('Logs de Pagamento')"><a href="{{ route('admin.settings.payments.webhooks') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.settings.payments.webhooks') ? 'active' : 'text-zinc-600 hover:text-white' }}">Logs de Pagamento</a></li>
                <li x-show="isVisible('Backup')"><a href="{{ route('admin.backups.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.backups.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Backups do Sistema</a></li>
            </ul>
        </div>

        <!-- GRUPO: INTEGRAÇÕES -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Integrações', 'API', 'SMTP', 'Templates'])">
            <button @click="toggleMenu('integracoes')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('integracoes') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="plug-2" class="w-4 h-4"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest" x-show="!isCollapsed">Integrações</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('integracoes') && !isCollapsed" x-collapse>
                <li x-show="isVisible('APIs Externas')"><a href="{{ route('admin.api-integrations.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.api-integrations.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">APIs Externas</a></li>
                <li x-show="isVisible('Provedores SMTP')"><a href="{{ route('admin.settings.email.providers') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.settings.email.providers') ? 'active' : 'text-zinc-600 hover:text-white' }}">Provedores SMTP</a></li>
                <li x-show="isVisible('Templates de E-mail')"><a href="{{ route('admin.settings.email.templates.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.settings.email.templates.index') ? 'active' : 'text-zinc-600 hover:text-white' }}">Templates de E-mail</a></li>
            </ul>
        </div>

        <!-- GRUPO: SISTEMA AVANÇADO -->
        <div class="nav-item mb-1" x-show="isGroupVisible(['Sistema', 'Configurações Dinâmicas', 'Entidades', 'Auditoria'])">
            <button @click="toggleMenu('sistema_avancado')" class="nav-link w-full flex items-center justify-between px-4 py-3 rounded-xl text-zinc-500" :class="{ 'open active': openMenus.includes('sistema_avancado') }">
                <div class="flex items-center gap-3">
                    <i data-lucide="cpu-chip" class="w-4 h-4 text-emerald-500"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500" x-show="!isCollapsed">Configuration Center</span>
                </div>
                <i data-lucide="chevron-down" class="w-3 h-3 chevron" x-show="!isCollapsed"></i>
            </button>
            <ul class="submenu list-none p-0 m-0 space-y-1 mt-1 pl-4" x-show="openMenus.includes('sistema_avancado') && !isCollapsed" x-collapse>
                <li x-show="isVisible('Dashboard')"><a href="{{ route('admin.configuration-center.dashboard') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.configuration-center.dashboard') ? 'active' : 'text-zinc-600 hover:text-white' }}">Dashboard Central</a></li>
                <li x-show="isVisible('Entidades')"><a href="{{ route('admin.configuration-center.entities.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.configuration-center.entities.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Gestão de Entidades</a></li>
                <li x-show="isVisible('Auditoria')"><a href="{{ route('admin.configuration-center.audit.index') }}" class="submenu-link flex items-center px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest {{ request()->routeIs('admin.configuration-center.audit.*') ? 'active' : 'text-zinc-600 hover:text-white' }}">Log de Alterações</a></li>
            </ul>
        </div>


    </div>

    <!-- Sidebar Footer: Perfil do Usuário -->
    <div class="sidebar-footer p-4 border-t border-zinc-900 bg-zinc-950/50 relative">
        <div class="relative" x-data="{ openProfile: false }">
            <button @click="openProfile = !openProfile" 
                    class="w-full flex items-center gap-3 p-2.5 rounded-2xl bg-zinc-900/30 border border-white/5 hover:bg-zinc-900 hover:border-emerald-500/20 transition-all group"
                    :class="isCollapsed ? 'justify-center' : ''">
                
                <div class="relative shrink-0">
                    <div class="w-10 h-10 rounded-xl overflow-hidden border-2 border-zinc-800 group-hover:border-emerald-500/50 transition-all shadow-lg">
                        <img src="{{ auth()->user()->profile_photo_url }}" 
                             alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 border-4 border-zinc-950 rounded-full shadow-lg"></span>
                </div>

                <div class="flex-1 min-w-0 text-left" x-show="!isCollapsed" x-cloak x-transition>
                    <p class="text-[11px] font-black text-white truncate uppercase tracking-tight" title="{{ auth()->user()?->name }}">
                        {{ auth()->user()?->name }}
                    </p>
                    <p class="text-[8px] text-zinc-600 font-black uppercase tracking-widest mt-0.5 truncate">
                        {{ auth()->user()->roles()->where('name', session('active_role'))->first()?->label ?? 'Administrador' }}
                    </p>
                </div>
                
                <i data-lucide="chevron-up" class="w-4 h-4 text-zinc-700 group-hover:text-white transition-all" :class="openProfile ? 'rotate-180' : ''" x-show="!isCollapsed"></i>
            </button>

            <!-- Menu de Perfil (Popout) -->
            <div x-show="openProfile" 
                 @click.away="openProfile = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 class="absolute bottom-full left-0 w-full min-w-[240px] mb-3 bg-zinc-950 border border-zinc-800 rounded-2xl shadow-3xl overflow-hidden z-[500] p-2 space-y-1"
                 :class="isCollapsed ? 'left-14 bottom-0 w-64' : ''">
                
                <div class="px-4 py-3 border-b border-zinc-900 mb-1">
                    <p class="text-[10px] font-black text-white uppercase tracking-widest truncate">{{ auth()->user()?->name }}</p>
                    <p class="text-[8px] text-zinc-600 font-bold uppercase tracking-[0.2em] mt-1">{{ auth()->user()?->email }}</p>
                </div>

                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:bg-zinc-900 hover:text-white transition-all group">
                    <i data-lucide="user" class="w-4 h-4 group-hover:text-emerald-500"></i>
                    Meu Perfil
                </a>

                @if(auth()->user()->roles->count() > 1)
                    <div class="px-4 py-2 mt-2 mb-1 border-t border-zinc-900/50 pt-3">
                        <span class="text-[8px] font-black text-zinc-700 uppercase tracking-[0.3em]">Alternar Perfil</span>
                    </div>
                    @foreach(auth()->user()->roles as $role)
                        <form action="{{ route('profile.select') }}" method="POST">
                            @csrf
                            <input type="hidden" name="role" value="{{ $role->name }}">
                            <button type="submit" class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all {{ session('active_role') == $role->name ? 'bg-emerald-500 text-zinc-950 shadow-lg' : 'text-zinc-600 hover:bg-zinc-900 hover:text-white' }}">
                                {{ $role->label }}
                                @if(session('active_role') == $role->name)
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                @endif
                            </button>
                        </form>
                    @endforeach
                @endif

                <div class="border-t border-zinc-900 mt-2 pt-2">
                    <a href="{{ route('dashboard', ['view_as_user' => 1]) }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-emerald-500 hover:bg-emerald-500/10 transition-all">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        Portal do Usuário
                    </a>

                    <form action="{{ route('logout') }}" method="post">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-rose-500 hover:bg-rose-500/10 transition-all">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Encerrar Sessão
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</aside>

