<header class="topbar">
    <div class="topbar-left">
        <button id="toggleSidebar" class="toggle-sidebar-btn" aria-label="Toggle Sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 md:hidden">
            <div class="bg-blue-600 text-white rounded p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <span class="font-bold text-sm tracking-tighter italic">nexshape</span>
        </a>
        
        <form action="{{ route('global.search') }}" method="GET" class="search-bar flex">
            <button type="submit" class="bg-transparent border-none p-0 cursor-pointer flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-400 hover:text-blue-500 transition-colors pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar no sistema..." class="bg-transparent border-none outline-none text-sm text-white w-full ml-2">
        </form>
    </div>

    <div class="topbar-right" style="display: flex; align-items: center; gap: 1.25rem;">
        <!-- Alternador de Tema -->
        <div class="theme-switcher" role="group" aria-label="Tema da interface" style="display: flex; gap: 0.25rem; background: var(--bg-main, #f3f4f6); padding: 0.25rem; border-radius: 0.5rem; border: 1px solid var(--border-color, #e5e7eb);" id="header-theme-toggle">
            <button type="button" data-theme-val="dark" class="btn-theme{{ ($projetoTheme ?? 'dark') === 'dark' ? ' is-active' : '' }}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; border: none; border-radius: 0.25rem;">Escuro</button>
            <button type="button" data-theme-val="light" class="btn-theme{{ ($projetoTheme ?? '') === 'light' ? ' is-active' : '' }}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; border: none; border-radius: 0.25rem;">Claro</button>
        </div>

        <script>
            /** Só iframes da mesma origem (evita YouTube/Vimeo e avisos do Chrome com páginas de erro / chrome-error). */
            function syncThemeToSameOriginIframes(newTheme) {
                document.querySelectorAll('iframe').forEach(iframe => {
                    try {
                        const raw = iframe.getAttribute('src');
                        if (raw) {
                            const u = new URL(raw, window.location.href);
                            if (u.origin !== window.location.origin) {
                                return;
                            }
                        }
                        if (iframe.contentWindow && iframe.contentWindow.document && iframe.contentWindow.document.documentElement) {
                            iframe.contentWindow.document.documentElement.setAttribute('data-theme', newTheme);
                        }
                    } catch (e) { /* cross-origin ou frame bloqueado */ }
                });
            }

            document.querySelectorAll('#header-theme-toggle .btn-theme').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const newTheme = this.getAttribute('data-theme-val');
                    
                    // Atualiza visualmente o próprio toggle
                    document.querySelectorAll('#header-theme-toggle .btn-theme').forEach(b => b.classList.remove('is-active'));
                    this.classList.add('is-active');

                    // Aplica na tela principal
                    document.documentElement.setAttribute('data-theme', newTheme);

                    // Sincroniza com outras abas do navegador
                    localStorage.setItem('academia_theme_sync', newTheme + '_' + Date.now());

                    syncThemeToSameOriginIframes(newTheme);

                    // Salva no backend silenciosamente
                    fetch('{{ route("theme") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ theme: newTheme, next: window.location.pathname })
                    }).catch(err => console.error('Erro ao salvar tema:', err));
                });
            });

            // Escuta por mudanças feitas em outras abas
            window.addEventListener('storage', function(e) {
                if (e.key === 'academia_theme_sync' && e.newValue) {
                    const newTheme = e.newValue.split('_')[0];
                    if (newTheme === 'dark' || newTheme === 'light') {
                        document.documentElement.setAttribute('data-theme', newTheme);
                        document.querySelectorAll('#header-theme-toggle .btn-theme').forEach(b => {
                            b.classList.toggle('is-active', b.getAttribute('data-theme-val') === newTheme);
                        });
                        syncThemeToSameOriginIframes(newTheme);
                    }
                }
            });
        </script>

        @if(request()->is('admin*') && auth()->user()?->is_admin)
            @php($pendingRegsTop = \App\Models\User::where('registration_approval_status', 'pending')->where('is_admin', false)->count())
            <a href="{{ route('admin.registrations.pending') }}" class="topbar-icon-btn relative" aria-label="Cadastros pendentes" title="Cadastros pendentes de aprovação">
                <i class="fas fa-user-clock text-lg text-amber-500"></i>
                @if($pendingRegsTop > 0)
                    <span class="absolute -top-0.5 -right-0.5 min-w-[1.1rem] h-[1.1rem] flex items-center justify-center rounded-full bg-amber-500 text-black text-[9px] font-black">{{ $pendingRegsTop > 9 ? '9+' : $pendingRegsTop }}</span>
                @endif
            </a>
        @endif

        <!-- Créditos de IA -->
        <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-ai-credits-modal'))" class="ai-credits-badge flex items-center gap-2 px-3 py-1.5 rounded-xl {{ auth()->user()->ai_credits < 15 ? 'bg-red-500/10 border-red-500/30 text-red-500 animate-pulse' : 'bg-purple-500/10 border-purple-500/20 text-purple-500 hover:bg-purple-500/20' }} transition-all duration-200 cursor-pointer shadow-sm group">
            <i class="fas {{ auth()->user()->ai_credits < 15 ? 'fa-exclamation-triangle' : 'fa-magic' }} text-xs group-hover:rotate-12 transition-transform"></i>
            <span class="text-[10px] font-black uppercase tracking-widest">
                @if(auth()->user()->ai_credits < 15)
                    <span class="hidden lg:inline">Atenção:</span>
                @endif
                {{ auth()->user()->ai_credits }} <span class="hidden sm:inline">Créditos</span>
            </span>
        </button>

        <!-- Notificações -->
        <a href="{{ route('internal-email.inbox') }}" class="topbar-icon-btn" aria-label="Notifications" title="Correio Interno">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            @if(auth()->check())
                @php($unreadCount = \App\Models\InternalEmail::where('recipient_id', auth()->id())->where('is_read', false)->count())
                @if($unreadCount > 0)
                    <span class="badge">{{ $unreadCount }}</span>
                @endif
            @endif
        </a>

        <!-- Mensagens/Chat -->
        <a href="{{ route('messages.index') }}" class="topbar-icon-btn" aria-label="Messages">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        </a>

        @if(auth()->user()?->is_admin && !request()->is('admin*'))
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-500 hover:bg-amber-500/20 transition-all duration-200" title="Aceder ao Painel Admin">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="text-xs font-bold uppercase tracking-wider hidden lg:inline">Painel Admin</span>
            </a>
        @endif

        <!-- Perfil (Estilo NexShape) -->
        <div class="user-profile-btn group flex items-center gap-3">
            @if(auth()->user()->roles->count() > 1)
            <!-- Profile Switcher -->
            <div class="relative mr-2" x-data="{ open: false }">
                <button x-on:click="open = !open" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-500 hover:bg-blue-500/20 transition-all duration-200 shadow-sm" title="Alternar Perfil">
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ session('active_role', 'Perfil') }}</span>
                    <i class="fas fa-chevron-down text-[8px] transition-transform" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <div x-show="open" x-on:click.away="open = false" 
                     class="absolute right-0 mt-2 w-48 bg-[#0b0e14] border border-white/10 rounded-2xl shadow-2xl overflow-hidden z-[100] animate-fade-in"
                     style="display: none;">
                    <div class="p-2 space-y-1">
                        @foreach(auth()->user()->roles as $role)
                            <form action="{{ route('profile.select') }}" method="POST">
                                @csrf
                                <input type="hidden" name="role" value="{{ $role->name }}">
                                <button type="submit" class="w-full text-left px-4 py-2 text-[10px] font-bold uppercase tracking-widest rounded-xl transition-colors {{ session('active_role') == $role->name ? 'bg-blue-600 text-white' : 'text-zinc-500 hover:bg-white/5 hover:text-white' }}">
                                    {{ $role->label }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="user-info text-right hidden sm:block mr-2">
                <span class="user-name text-white font-black tracking-tight leading-none">{{ auth()->user()?->name }}</span>
                <span class="user-role text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1 block">{{ auth()->user()->isAdministrator() ? 'Administrador' : (auth()->user()->roles()->where('name', session('active_role'))->first()?->label ?? (auth()->user()->roles->first()?->label ?? 'Aluno')) }}</span>
            </div>
            <div class="relative">
                <img src="{{ auth()->user()?->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()?->name ?? 'User').'&color=3b82f6&background=0b0e14' }}" alt="Avatar" class="user-avatar border-2 border-white/5 group-hover:border-blue-500/50 transition-all shadow-xl">
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-[#06080c] rounded-full"></span>
            </div>
        </div>
    </div>
</header>
