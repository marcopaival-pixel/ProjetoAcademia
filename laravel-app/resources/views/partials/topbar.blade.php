<header class="topbar bg-zinc-950/80 backdrop-blur-xl border-b border-zinc-900 px-4 sm:px-6 md:px-8 h-16 md:h-20 flex items-center justify-between sticky top-0 z-[100] shadow-2xl">
    <div class="topbar-left flex items-center gap-3 md:gap-6">
        <button id="toggleSidebar" class="w-10 h-10 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-600 hover:text-emerald-500 hover:border-emerald-500/20 transition-all shadow-inner" aria-label="Toggle Sidebar">
            <i data-lucide="menu" class="w-5 h-5"></i>
        </button>

        @php
            $isClinica = ($experienceClass ?? '') === 'experience-clinica';
            $homeRoute = 'dashboard';
            $user = auth()->user();
            if ($user && $user->hasRole('paciente')) {
                $homeRoute = 'patient.portal';
            } elseif ($user && $user->isAdministrator()) {
                $homeRoute = 'admin.dashboard';
            }
        @endphp
        <a href="{{ route($homeRoute) }}" class="flex items-center gap-3 md:hidden">
            <div class="{{ $isClinica ? 'bg-blue-600 shadow-blue-500/20' : 'bg-emerald-500 shadow-emerald-500/20' }} text-zinc-950 rounded-lg p-1.5 shadow-lg">
                <i data-lucide="{{ $isClinica ? 'stethoscope' : 'zap' }}" class="w-4 h-4 fill-current"></i>
            </div>
            <span class="font-black text-sm tracking-tighter italic uppercase text-white">nex<span class="{{ $isClinica ? 'text-blue-500' : 'text-emerald-500' }}">shape</span></span>
        </a>

        @php
            $activeClinic = \App\Support\TenantContext::getClinic();
        @endphp

        @if($activeClinic)
            <div class="hidden sm:flex items-center gap-3 border-l border-zinc-900 pl-6 ml-2">
                @if($activeClinic->logo_path)
                    <img src="{{ asset('storage/' . $activeClinic->logo_path) }}" alt="{{ $activeClinic->name }}" class="h-8 w-auto rounded shadow-sm">
                @endif
                <div class="flex flex-col leading-none">
                    <span class="text-[10px] font-black text-white uppercase tracking-tighter">{{ $activeClinic->name }}</span>
                    <span class="text-[7px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">Clínica Ativa</span>
                </div>
            </div>
        @endif

        
        @if(!auth()->user()->hasRole('paciente'))
        
        {{-- SELETOR GLOBAL DE ALUNO (Apenas Profissional) --}}
        @if(auth()->user()->isProfessional())
            <x-global-patient-selector />
        @endif

        <div class="relative" x-data="{ 
            query: '{{ request('q') }}', 
            suggestions: [], 
            showSuggestions: false,
            fetchSuggestions() {
                if (this.query.length < 2) {
                    this.suggestions = [];
                    this.showSuggestions = false;
                    return;
                }
                fetch('{{ route('global.search.suggestions') }}?q=' + encodeURIComponent(this.query))
                    .then(res => res.json())
                    .then(data => {
                        this.suggestions = data;
                        this.showSuggestions = this.suggestions.length > 0;
                        this.$nextTick(() => { lucide.createIcons(); });
                    });
            }
        }" @click.away="showSuggestions = false">
            <form action="{{ route('global.search') }}" method="GET" id="searchForm" class="search-bar hidden md:flex items-center bg-zinc-950 border border-zinc-900 rounded-2xl px-5 py-2.5 w-64 xl:w-80 focus-within:border-{{ $isClinica ? 'blue-500/50' : 'emerald-500/50' }} focus-within:shadow-[0_0_15px_{{ $isClinica ? 'rgba(59,130,246,0.1)' : 'rgba(16,185,129,0.1)' }}] transition-all shadow-inner group">
                <i data-lucide="search" class="w-4 h-4 text-zinc-700 group-focus-within:text-{{ $isClinica ? 'blue-400' : 'emerald-500' }} transition-colors"></i>
                <input type="text" name="q" x-model="query" @input.debounce.300ms="fetchSuggestions()" autocomplete="off" placeholder="Buscar no sistema..." class="bg-transparent border-none outline-none text-[10px] font-black uppercase tracking-widest text-white placeholder:text-zinc-800 w-full ml-3">
            </form>

            {{-- Dropdown de Sugestões --}}
            <div x-show="showSuggestions" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="absolute top-full left-0 right-0 mt-3 bg-zinc-950 border border-zinc-900 rounded-2xl shadow-3xl overflow-hidden z-[1000] p-2 space-y-1">
                <template x-for="item in suggestions" :key="item.label">
                    <a :href="item.url" class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-zinc-900 transition-all group">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-white uppercase tracking-wider" x-text="item.label"></span>
                            <span class="text-[8px] font-bold text-zinc-600 uppercase tracking-widest" x-text="item.category"></span>
                        </div>
                        <i data-lucide="arrow-up-right" class="w-3 h-3 text-zinc-800 group-hover:text-emerald-500 transition-colors"></i>
                    </a>
                </template>
                <div class="p-2 border-t border-zinc-900 mt-1">
                    <button type="submit" form="searchForm" class="w-full text-center py-2 text-[8px] font-black text-zinc-700 uppercase tracking-[0.2em] hover:text-white transition-colors">Ver todos os resultados</button>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="topbar-right flex items-center gap-3 md:gap-6">

        <!-- AI Credits / Buy Credits -->
        @auth
        @php
            $credits = (int) (auth()->user()->ai_credits ?? auth()->user()->creditos ?? 0);
            $compraAtiva = \App\Models\SystemSetting::isTrue('compra_creditos_ativa', true);
            $isPaciente = auth()->user()->hasRole('paciente');
            $isAdmin = auth()->user()->isAdministrator();
            $isRepresentative = session('active_role') === 'representative' || (auth()->user()->hasRole('representative') && session('active_role') === null);
        @endphp
        
        @if((!$isPaciente || $isAdmin) && !$isRepresentative)
        <div class="flex items-center gap-2">
            <a href="{{ route('credits.buy') }}" 
               class="flex items-center gap-3 px-4 py-2 rounded-xl transition-all duration-300 shadow-lg group {{ $credits < 15 ? 'bg-rose-500/10 border border-rose-500/20 text-rose-500 animate-pulse' : 'bg-purple-500/10 border border-purple-500/20 text-purple-500 hover:bg-purple-500 hover:text-white' }}"
               title="Comprar Créditos">
                <i data-lucide="{{ $credits < 15 ? 'alert-triangle' : 'zap' }}" class="w-4 h-4 group-hover:rotate-12 transition-transform"></i>
                <div class="flex flex-col items-start leading-none">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] tabular-nums">
                        {{ number_format($credits, 0, ',', '.') }} <span class="hidden sm:inline">Créditos</span>
                    </span>
                    <span class="text-[7px] font-bold opacity-60 uppercase tracking-widest mt-0.5">Créditos de IA</span>
                </div>
                @if($compraAtiva)
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5 opacity-50 group-hover:opacity-100 transition-opacity"></i>
                @endif
            </a>
            
            <div class="hidden xl:flex items-center gap-3 px-4 py-2 rounded-xl bg-zinc-950 border border-zinc-900 shadow-inner">
                <div class="flex flex-col items-end leading-none">
                    <span class="text-[9px] font-black text-white tabular-nums">{{ $aiUsageToday ?? 0 }}</span>
                    <span class="text-[6px] font-bold text-zinc-600 uppercase tracking-widest mt-0.5">Uso Hoje</span>
                </div>
                <div class="w-1.5 h-1.5 rounded-full {{ ($aiUsageToday ?? 0) > 0 ? 'bg-purple-500 shadow-[0_0_8px_rgba(168,85,247,0.5)]' : 'bg-zinc-800' }}"></div>
            </div>
        </div>
        @endif
        @endauth

        @auth
        <!-- Improved Messages Button -->
        <a href="{{ route('messages.index') }}" 
           class="flex items-center gap-3 px-4 py-2 rounded-xl bg-emerald-500 text-zinc-950 hover:bg-emerald-400 transition-all shadow-lg shadow-emerald-500/20 group relative" 
           title="Minhas Mensagens">
            <div class="relative">
                <i data-lucide="message-square" class="w-4 h-4"></i>
                @php
                    $unreadMessagesCount = \App\Models\Message::where('is_read', false)
                        ->where('sender_id', '!=', auth()->id())
                        ->whereHas('conversation', function($q) {
                            $q->where('user_one_id', auth()->id())
                              ->orWhere('user_two_id', auth()->id());
                        })->count();
                @endphp
                @if($unreadMessagesCount > 0)
                    <span class="absolute -top-1.5 -right-1.5 w-3 h-3 bg-rose-500 rounded-full border-2 border-emerald-500 animate-pulse"></span>
                @endif
            </div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] hidden md:inline">Mensagens</span>
            @if($unreadMessagesCount > 0)
                <span class="bg-zinc-950 text-emerald-500 text-[8px] font-black px-1.5 py-0.5 rounded-lg">{{ $unreadMessagesCount }}</span>
            @endif
        </a>

        @if(!$isRepresentative && (!$isPaciente || $isAdmin))
        <!-- Help Center Button -->
        <a href="{{ route('kb.index') }}" 
           class="w-10 h-10 flex items-center justify-center rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-600 hover:text-blue-500 hover:border-blue-500/20 transition-all shadow-inner group" 
           title="Central de Ajuda">
            <i data-lucide="help-circle" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
        </a>
        @endif
        @endauth

        <!-- Theme Switcher -->
        <div class="hidden lg:flex p-1 bg-zinc-950 border border-zinc-900 rounded-xl shadow-inner mr-2" id="header-theme-toggle">
            <button type="button" data-theme-val="dark" class="btn-theme flex items-center gap-2 px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all {{ ($projetoTheme ?? 'dark') === 'dark' ? ($isClinica ? 'bg-blue-600 text-white shadow-lg' : 'bg-emerald-500 text-zinc-950 shadow-lg') : 'text-zinc-600 hover:text-white' }}">
                <i data-lucide="moon" class="w-3 h-3"></i> Escuro
            </button>
            <button type="button" data-theme-val="light" class="btn-theme flex items-center gap-2 px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all {{ ($projetoTheme ?? '') === 'light' ? ($isClinica ? 'bg-blue-600 text-white shadow-lg' : 'bg-emerald-500 text-zinc-950 shadow-lg') : 'text-zinc-600 hover:text-white' }}">
                <i data-lucide="sun" class="w-3 h-3"></i> Claro
            </button>
        </div>

        <!-- Perfil do Usuário (Apenas Mobile/Tablet) -->
        <div class="flex items-center gap-4 lg:hidden">
            <div class="relative" x-data="{ openMobileUser: false }">
                <button @click="openMobileUser = !openMobileUser" class="relative group">
                    <div class="w-10 h-10 rounded-xl overflow-hidden border-2 border-zinc-900 group-hover:border-{{ ($isClinica || (auth()->user() && auth()->user()->hasRole('paciente'))) ? 'blue-500/50' : 'emerald-500/50' }} transition-all shadow-lg">
                        <img src="{{ auth()->user()?->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()?->name ?? 'User').'&color='.(($isClinica || (auth()->user() && auth()->user()->hasRole('paciente'))) ? '3b82f6' : '10b981').'&background=09090b&bold=true' }}" alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 {{ ($isClinica || (auth()->user() && auth()->user()->hasRole('paciente'))) ? 'bg-blue-500' : 'bg-emerald-500' }} border-4 border-zinc-950 rounded-full shadow-lg"></span>
                </button>
                
                <form action="{{ route('logout') }}" method="post" class="lg:hidden ml-2">
                    @csrf
                    <button type="submit" class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-500 shadow-lg" title="Sair">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>

                <!-- Dropdown Mobile -->
                <div x-show="openMobileUser" 
                     @click.away="openMobileUser = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     class="absolute right-0 mt-3 w-64 bg-zinc-950 border border-zinc-800 rounded-2xl shadow-3xl overflow-hidden z-[500] p-2 space-y-1">
                    
                    <div class="px-4 py-3 border-b border-zinc-900 mb-1 text-right">
                        <p class="text-[10px] font-black text-white uppercase tracking-widest truncate">{{ auth()->user()?->name }}</p>
                        <p class="text-[8px] text-zinc-600 font-bold uppercase tracking-[0.2em] mt-1">{{ session('active_role', 'Usuário') }}</p>
                    </div>

                    <a href="{{ route('profile') }}" class="flex items-center justify-end gap-3 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:bg-zinc-900 hover:text-white transition-all">
                        Meu Perfil <i data-lucide="user" class="w-4 h-4"></i>
                    </a>

                    @if(auth()->user()->roles->count() > 1)
                        <div class="px-4 py-2 mt-2 mb-1 text-right">
                            <span class="text-[8px] font-black text-zinc-700 uppercase tracking-[0.3em]">Alternar Perfil</span>
                        </div>
                        @foreach(auth()->user()->roles as $role)
                            <form action="{{ route('profile.select') }}" method="POST">
                                @csrf
                                <input type="hidden" name="role" value="{{ $role->name }}">
                                <button type="submit" class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all {{ session('active_role') == $role->name ? ($isClinica ? 'bg-blue-600 text-white' : 'bg-emerald-500 text-zinc-950') : 'text-zinc-600 hover:bg-zinc-900 hover:text-white' }}">
                                    @if(session('active_role') == $role->name) <i data-lucide="check" class="w-3 h-3"></i> @else <span></span> @endif
                                    {{ $role->label }}
                                </button>
                            </form>
                        @endforeach
                    @endif

                    @php
                        $profile = auth()->user()->professionalProfile;
                        $secondarySpecialties = $profile ? $profile->especialidades : collect();
                    @endphp

                    @if(session('active_role') === 'professional' && $profile && $secondarySpecialties->count() > 0)
                        <div class="px-4 py-2 mt-2 mb-1">
                            <span class="text-[8px] font-black text-zinc-700 uppercase tracking-[0.3em]">Contexto Profissional</span>
                        </div>
                        
                        {{-- Especialidade Principal --}}
                        @php $mainSpec = $profile->especialidade; @endphp
                        @if($mainSpec)
                            <form action="{{ route('profile.switch-specialty') }}" method="POST">
                                @csrf
                                <input type="hidden" name="especialidade_id" value="{{ $mainSpec->id }}">
                                <button type="submit" class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all {{ (!session('active_specialty_id') || session('active_specialty_id') == $mainSpec->id) ? ($isClinica ? 'bg-blue-600 text-white' : 'bg-emerald-500 text-zinc-950') : 'text-zinc-600 hover:bg-zinc-900 hover:text-white' }}">
                                    @if(!session('active_specialty_id') || session('active_specialty_id') == $mainSpec->id) <i data-lucide="check" class="w-3 h-3"></i> @else <span></span> @endif
                                    {{ $mainSpec->name }}
                                </button>
                            </form>
                        @endif

                        {{-- Especialidades Secundárias --}}
                        @foreach($secondarySpecialties as $spec)
                            <form action="{{ route('profile.switch-specialty') }}" method="POST">
                                @csrf
                                <input type="hidden" name="especialidade_id" value="{{ $spec->id }}">
                                <button type="submit" class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all {{ session('active_specialty_id') == $spec->id ? ($isClinica ? 'bg-blue-600 text-white' : 'bg-emerald-500 text-zinc-950') : 'text-zinc-600 hover:bg-zinc-900 hover:text-white' }}">
                                    @if(session('active_specialty_id') == $spec->id) <i data-lucide="check" class="w-3 h-3"></i> @else <span></span> @endif
                                    {{ $spec->name }}
                                </button>
                            </form>
                        @endforeach
                    @endif

                    <div class="border-t border-zinc-900 mt-2 pt-2">
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-end gap-3 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-rose-500 hover:bg-rose-500/10 transition-all">
                                Encerrar Sessão <i data-lucide="log-out" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Lucide re-init removido: já é tratado no app.blade.php globalmente

        // Theme Switcher Logic (preserved from original)
        document.querySelectorAll('#header-theme-toggle .btn-theme').forEach(btn => {
            btn.addEventListener('click', function(e) {
                const newTheme = this.getAttribute('data-theme-val');
                document.querySelectorAll('#header-theme-toggle .btn-theme').forEach(b => {
                    b.classList.remove('bg-emerald-500', 'text-zinc-950', 'shadow-lg');
                    b.classList.add('text-zinc-600', 'hover:text-white');
                });
                this.classList.add('bg-emerald-500', 'text-zinc-950', 'shadow-lg');
                this.classList.remove('text-zinc-600', 'hover:text-white');
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('academia_theme_sync', newTheme + '_' + Date.now());
                
                fetch('{{ route("theme") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ theme: newTheme, next: window.location.pathname })
                }).catch(err => {});
            });
        });
    });
</script>
