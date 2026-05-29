@php
    $menuService = app('App\Services\MenuService');
@endphp
@php
    $user = auth()->user();
    $initialOpenGroups = [];
    
    if (!$user) {
        $menuGroups = [];
        $isPremium = false;
        $unreadMessages = 0;
    } else {
        $menuGroups = $menuService->getAccordionMenus($user);
        $isPremium = $user->hasPremiumAccess();
        
        $unreadMessages = \App\Models\Message::whereHas('conversation', function($q) use ($user) {
            $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
        })->where('sender_id', '!=', $user->id)->where('is_read', false)->count();

        foreach($menuGroups as $group) {
            if (collect($group['items'] ?? [])->where('is_active', true)->isNotEmpty()) {
                $initialOpenGroups[] = $group['id'];
            }
        }
    }
@endphp

<aside class="sidebar bg-zinc-950 border-r border-zinc-900 shadow-2xl flex flex-col h-screen" 
       id="sidebar"
       x-data="sidebarNav"
       data-initial-groups="{{ json_encode($initialOpenGroups) }}">
    <!-- Header/Logo -->
    <div class="sidebar-header p-10">
        @php
            $activeRole = session('active_role');
            $isClinica = $experienceClass === 'experience-clinica';
            
            $homeRoute = 'dashboard';
            if ($activeRole === 'admin' || ($user && $user->isAdministrator() && !$activeRole)) {
                $homeRoute = 'admin.dashboard';
            } elseif ($activeRole === 'professional') {
                $homeRoute = 'professional.dashboard';
            } elseif ($activeRole === 'paciente' || ($user && $user->hasRole('paciente') && !$activeRole)) {
                $homeRoute = 'patient.unified.dashboard';
            }
        @endphp
        <a href="{{ route($homeRoute) }}" class="sidebar-logo flex items-center gap-4 group">
            <div class="w-12 h-12 {{ $isClinica ? 'bg-blue-600' : 'bg-emerald-500' }} text-zinc-950 rounded-2xl flex items-center justify-center shadow-lg {{ $isClinica ? 'shadow-blue-500/20' : 'shadow-emerald-500/20' }} transform group-hover:rotate-12 transition-all duration-500">
                <i data-lucide="{{ $isClinica ? 'stethoscope' : 'zap' }}" class="w-6 h-6 fill-current"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-xl font-black text-white tracking-tighter uppercase leading-none">NEX<span class="{{ $isClinica ? 'text-blue-500' : 'text-emerald-500' }}">SHAPE</span></span>
                <span class="text-[8px] font-black text-zinc-700 uppercase tracking-[0.4em] mt-1 italic">{{ $isClinica ? 'CLINIC EDITION' : 'PRO EDITION' }}</span>
            </div>
        </a>
    </div>

    <!-- Quick Actions (Admin Only) -->
    @if($user && $user->isAdministrator())
    <div class="px-6 mb-6 flex flex-col gap-2">
        <a href="{{ route('onboarding-premium.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-zinc-950 transition-all shadow-lg group relative"
           title="Novo Onboarding Premium">
            <i data-lucide="rocket" class="w-4 h-4 group-hover:animate-bounce"></i>
            <span class="text-[10px] font-black uppercase tracking-widest">Onboarding</span>
        </a>

        @php
            $pendingRegsTotal = \App\Models\User::where('registration_approval_status', 'pending')->where('is_admin', false)->count();
        @endphp
        @if($pendingRegsTotal > 0)
        <a href="{{ route('admin.registrations.pending') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-500 hover:bg-amber-500 hover:text-zinc-950 transition-all shadow-lg group relative"
           title="Aprovações Pendentes">
            <i data-lucide="user-plus" class="w-4 h-4 group-hover:scale-110 transition-transform"></i>
            <span class="text-[10px] font-black uppercase tracking-widest">Aprovações</span>
            <span class="absolute -top-1 -right-1 min-w-[1.2rem] h-[1.2rem] flex items-center justify-center rounded-lg bg-amber-500 text-zinc-950 text-[9px] font-black shadow-lg border-2 border-zinc-950">
                {{ $pendingRegsTotal > 9 ? '9+' : $pendingRegsTotal }}
            </span>
        </a>
        @endif
    </div>
    @endif

    <!-- Navigation Scroll Area -->
    <div class="sidebar-content flex-1 overflow-y-auto px-4 custom-scrollbar">


        @foreach($menuGroups as $group)
            @php
                $groupId = $group['id'];
                $groupItems = $group['items'] ?? [];
                $groupHasActiveItem = collect($groupItems)->where('is_active', true)->isNotEmpty();
                $headerActiveClass = $isClinica ? 'text-blue-500 border-blue-500/20 shadow-lg shadow-blue-500/5' : 'text-emerald-500 border-emerald-500/20 shadow-lg shadow-emerald-500/5';
                $groupActiveLogic = $groupHasActiveItem ? 'true' : 'false';
                $dotColorClass = $isClinica ? 'bg-blue-500 shadow-blue-500/50' : 'bg-emerald-500 shadow-emerald-500/50';
            @endphp
            <div class="nav-group mb-4">
                <div x-on:click="toggleGroup('{{ $groupId }}')" 
                     class="nav-group-header flex items-center justify-between p-4 rounded-2xl cursor-pointer transition-all hover:bg-zinc-900 group"
                     :class="isGroupOpen('{{ $groupId }}') || {{ $groupHasActiveItem ? 'true' : 'false' }} ? 'bg-zinc-900/50' : ''">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-xl bg-zinc-950 border border-zinc-900 flex items-center justify-center text-zinc-500 group-hover:text-emerald-500 transition-all"
                             :class="isGroupOpen('{{ $groupId }}') ? '{{ $headerActiveClass }}' : ''">
                            <i data-lucide="{{ $group['icon'] }}" class="w-4 h-4"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-zinc-400 group-hover:text-white uppercase tracking-[0.2em] transition-all">{{ $group['label'] }}</span>
                            @if(isset($group['subtitle']))
                                <span class="text-[7px] font-bold text-zinc-600 uppercase tracking-widest mt-0.5">{{ $group['subtitle'] }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($groupHasActiveItem)
                            <span class="w-1.5 h-1.5 rounded-full {{ $dotColorClass }} shadow-lg"></span>
                        @endif
                        <i data-lucide="chevron-down" 
                           class="w-3.5 h-3.5 text-zinc-700 transition-transform duration-300"
                           :class="isGroupOpen('{{ $groupId }}') ? 'rotate-180 text-emerald-500' : ''"></i>
                    </div>
                </div>

                <div x-show="isGroupOpen('{{ $groupId }}')" x-collapse class="submenu space-y-1 mt-2 pl-4">
                    @foreach($groupItems as $item)
                        @php
                            $activeClass = $isClinica ? 'bg-blue-500/10 text-blue-400 border border-blue-500/10 shadow-lg' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/10 shadow-lg';
                            $inactiveClass = 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-900/30';
                            $itemClass = $item['is_active'] ? $activeClass : $inactiveClass;
                            $lockedAttr = $item['is_locked'] ? 'data-premium-locked' : '';
                            $activeBorderClass = $isClinica ? 'bg-blue-500' : 'bg-emerald-500';
                        @endphp
                        <div class="nav-item">
                            <a href="{{ route($item['route']) }}" 
                               {{ $lockedAttr }}
                               class="nav-link flex items-center justify-between px-6 py-3.5 rounded-2xl transition-all relative overflow-hidden group {{ $itemClass }}">
                                
                                @if($item['is_active'])
                                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $activeBorderClass }}"></div>
                                @endif

                                <div class="flex items-center gap-4">
                                    <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 transition-transform group-hover:scale-110"></i>
                                    <span class="text-[11px] font-black uppercase tracking-widest">{{ $item['label'] }}</span>
                                </div>

                                @php
                                    $showLocked = !empty($item['is_locked']);
                                    $showPremium = !empty($item['is_premium']);
                                    $badgeValue = $item['badge'] ?? null;
                                @endphp

                                @if($showLocked)
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="lock" class="w-3 h-3 text-zinc-500"></i>
                                        <div class="flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-amber-500 text-zinc-950 border border-amber-500/30">
                                            <i data-lucide="crown" class="w-2.5 h-2.5 fill-current"></i>
                                            <span class="text-[7px] font-black uppercase tracking-widest">VIP</span>
                                        </div>
                                    </div>
                                @endif
                                @if(!$showLocked && $showPremium)
                                    <div class="flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-500">
                                        <i data-lucide="crown" class="w-2.5 h-2.5 fill-current"></i>
                                        <span class="text-[7px] font-black uppercase tracking-widest">VIP</span>
                                    </div>
                                @endif
                                @if(!$showLocked && !$showPremium && $badgeValue)
                                    <span class="w-5 h-5 flex items-center justify-center bg-emerald-500 text-zinc-950 text-[9px] font-black rounded-lg shadow-lg shadow-emerald-500/20">{{ $badgeValue }}</span>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        @if(($user && $user->hasRole('aluno') && (!$activeRole || $activeRole === 'aluno')) || (session('active_role') === 'aluno'))
            <div class="pt-4 pb-2">
                @if(!$isPremium)
                    <!-- Card Free User -->
                    <div class="p-4 rounded-2xl border border-zinc-800 bg-zinc-900/50 flex flex-col items-center text-center">
                        <div class="w-10 h-10 rounded-full bg-amber-500/10 flex items-center justify-center border border-amber-500/20 mb-3">
                            <i data-lucide="lock" class="w-4 h-4 text-amber-500"></i>
                        </div>
                        <h4 class="text-[11px] font-black text-white leading-tight mb-2">
                            Recurso exclusivo<br>
                            <span class="text-amber-500">para membros Premium</span>
                        </h4>
                        <p class="text-[9px] text-zinc-500 leading-snug mb-4">
                            Faça upgrade e desbloqueie<br>todas as funcionalidades<br>inteligentes do NexShape.
                        </p>
                        <a href="{{ route('plano') }}" class="w-full py-2 bg-amber-500 hover:bg-amber-400 text-zinc-950 font-black text-[10px] uppercase tracking-widest rounded-xl transition-all shadow-lg flex items-center justify-center gap-2 group">
                            <i data-lucide="crown" class="w-3.5 h-3.5 fill-current"></i>
                            Tornar-se Premium
                        </a>
                    </div>
                @else
                    <!-- Card Premium User -->
                    <div class="p-4 rounded-2xl border border-emerald-500/20 bg-emerald-500/5 flex flex-col items-center text-center shadow-[0_0_15px_-3px_rgba(16,185,129,0.1)] relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/10 blur-2xl rounded-full pointer-events-none"></div>
                        <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center border border-emerald-500/30 mb-3 relative z-10 shadow-lg shadow-emerald-500/20">
                            <i data-lucide="crown" class="w-4 h-4 text-emerald-500 fill-current"></i>
                        </div>
                        <h4 class="text-[12px] font-black text-white leading-tight mb-2 relative z-10">
                            Você é Premium ✨
                        </h4>
                        <p class="text-[9px] text-zinc-400 leading-snug mb-4 relative z-10">
                            Aproveite todos os recursos<br>exclusivos do NexShape.
                        </p>
                        <div class="w-full py-2 bg-emerald-500/20 border border-emerald-500/30 text-emerald-500 font-black text-[10px] uppercase tracking-widest rounded-xl flex items-center justify-center gap-2 relative z-10">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                            Premium Ativo
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </div>

    <!-- Sidebar Footer: Perfil do Usuário -->
    <div class="sidebar-footer p-4 border-t border-zinc-900 bg-zinc-950/50 relative">
        <div class="relative">
            <button @click="openProfile = !openProfile" 
                    class="w-full flex items-center gap-3 p-2.5 rounded-2xl bg-zinc-900/30 border border-white/5 hover:bg-zinc-900 hover:border-emerald-500/20 transition-all group">
                
                <div class="relative shrink-0">
                    <div class="w-10 h-10 rounded-xl overflow-hidden border-2 border-zinc-800 group-hover:border-{{ $isClinica ? 'blue-500' : 'emerald-500' }}/50 transition-all shadow-lg">
                        <img src="{{ auth()->user()?->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()?->name ?? 'User').'&color='.($isClinica ? '3b82f6' : '10b981').'&background=09090b&bold=true' }}" 
                             alt="Avatar" class="w-full h-full object-cover">
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-3.5 h-3.5 {{ $isClinica ? 'bg-blue-500' : 'bg-emerald-500' }} border-4 border-zinc-950 rounded-full shadow-lg"></span>
                </div>

                <div class="flex-1 min-w-0 text-left">
                    <p class="text-[11px] font-black text-white truncate uppercase tracking-tight" title="{{ auth()->user()?->name }}">
                        {{ auth()->user()?->name }}
                    </p>
                    <p class="text-[8px] text-zinc-600 font-black uppercase tracking-widest mt-0.5 truncate">
                        {{ auth()->user()->roles()->where('name', session('active_role'))->first()?->label ?? 'Usuário' }}
                    </p>
                </div>
                
                <i data-lucide="chevron-up" class="w-4 h-4 text-zinc-700 group-hover:text-white transition-all" :class="openProfile ? 'rotate-180' : ''"></i>
            </button>

            <!-- Menu de Perfil (Popout) -->
            <div x-show="openProfile" 
                 @click.away="openProfile = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 class="absolute bottom-full left-0 w-full min-w-[240px] mb-3 bg-zinc-950 border border-zinc-800 rounded-2xl shadow-3xl overflow-hidden z-[500] p-2 space-y-1">
                
                <div class="px-4 py-3 border-b border-zinc-900 mb-1">
                    <p class="text-[10px] font-black text-white uppercase tracking-widest truncate">{{ auth()->user()?->name }}</p>
                    <p class="text-[8px] text-zinc-600 font-bold uppercase tracking-[0.2em] mt-1">{{ auth()->user()?->email }}</p>
                </div>

                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-500 hover:bg-zinc-900 hover:text-white transition-all group">
                    <i data-lucide="user" class="w-4 h-4 group-hover:text-{{ $isClinica ? 'blue-500' : 'emerald-500' }}"></i>
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
                            <button type="submit" class="w-full flex items-center justify-between px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all {{ session('active_role') == $role->name ? ($isClinica ? 'bg-blue-600 text-white shadow-lg' : 'bg-emerald-500 text-zinc-950 shadow-lg') : 'text-zinc-600 hover:bg-zinc-900 hover:text-white' }}">
                                {{ $role->label }}
                                @if(session('active_role') == $role->name)
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                @endif
                            </button>
                        </form>
                    @endforeach
                @endif

                <div class="border-t border-zinc-900 mt-2 pt-2">
                    @if(auth()->user()->isAdministrator())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest text-amber-500 hover:bg-amber-500/10 transition-all">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        Painel Admin
                    </a>
                    @endif

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

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.05); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.1); }
    
    .nav-group-header:hover .w-8 { border-color: rgba(16, 185, 129, 0.2); color: #10b981; }
</style>
