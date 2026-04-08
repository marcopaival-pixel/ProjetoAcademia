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
        
        <div class="search-bar hidden md:flex">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" placeholder="Buscar no sistema...">
        </div>
    </div>

    <div class="topbar-right" style="display: flex; align-items: center; gap: 1.25rem;">
        <!-- Alternador de Tema -->
        <div class="theme-switcher" role="group" aria-label="Tema da interface" style="display: flex; gap: 0.25rem; background: var(--bg-main, #f3f4f6); padding: 0.25rem; border-radius: 0.5rem; border: 1px solid var(--border-color, #e5e7eb);" id="header-theme-toggle">
            <button type="button" data-theme-val="dark" class="btn-theme{{ ($projetoTheme ?? 'dark') === 'dark' ? ' is-active' : '' }}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; border: none; border-radius: 0.25rem;">Escuro</button>
            <button type="button" data-theme-val="light" class="btn-theme{{ ($projetoTheme ?? '') === 'light' ? ' is-active' : '' }}" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; border: none; border-radius: 0.25rem;">Claro</button>
        </div>

        <script>
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

                    // Sincroniza com iframes (sub-telas) se existirem
                    document.querySelectorAll('iframe').forEach(iframe => {
                        try {
                            if (iframe.contentWindow) {
                                iframe.contentWindow.document.documentElement.setAttribute('data-theme', newTheme);
                            }
                        } catch(e) {}
                    });

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
                        
                        document.querySelectorAll('iframe').forEach(iframe => {
                            try {
                                if (iframe.contentWindow) {
                                    iframe.contentWindow.document.documentElement.setAttribute('data-theme', newTheme);
                                }
                            } catch(err) {}
                        });
                    }
                }
            });
        </script>

        <!-- Notificações -->
        <a href="{{ route('internal-email.inbox') }}" class="topbar-icon-btn" aria-label="Notifications" title="Correio Interno">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            @php($unreadCount = \App\Models\InternalEmail::where('destinatario_id', auth()->id())->where('lida', false)->count())
            @if($unreadCount > 0)
                <span class="badge">{{ $unreadCount }}</span>
            @endif
        </a>

        <!-- Mensagens/Chat -->
        <a href="{{ route('messages.index') }}" class="topbar-icon-btn" aria-label="Messages">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        </a>

        <!-- Perfil -->
        <div class="user-profile-btn">
            <div class="user-info text-right hidden sm:block">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <span class="user-role">{{ auth()->user()->is_admin ? 'Administrador' : 'Atleta' }}</span>
            </div>
            <img src="{{ auth()->user()->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&color=7F9CF5&background=EBF4FF' }}" alt="Avatar" class="user-avatar">
        </div>
    </div>
</header>
