@inject('menuService', 'App\Services\MenuService')
@php
    $user = auth()->user();
    
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
    }
@endphp

<aside class="sidebar bg-zinc-950 border-r border-zinc-900 shadow-2xl flex flex-col h-screen" id="sidebar">
    <!-- Header/Logo -->
    <div class="sidebar-header p-10">
        @php
            $isClinica = $experienceClass === 'experience-clinica';
            $homeRoute = 'dashboard';
            if ($user && $user->hasRole('paciente')) {
                $homeRoute = 'patient.portal';
            } elseif ($user && $user->isAdministrator()) {
                $homeRoute = 'admin.dashboard';
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

    <!-- Navigation Scroll Area -->
    <div class="sidebar-content flex-1 overflow-y-auto px-4 custom-scrollbar" x-data="{ 
        openGroups: JSON.parse(localStorage.getItem('sidebar_open_groups') || '[]'),
        toggleGroup(id) {
            if (this.openGroups.includes(id)) {
                this.openGroups = this.openGroups.filter(g => g !== id);
            } else {
                this.openGroups.push(id);
            }
            localStorage.setItem('sidebar_open_groups', JSON.stringify(this.openGroups));
        },
        isGroupOpen(id) {
            return this.openGroups.includes(id);
        },
        init() {
            @foreach($menuGroups as $group)
                @if(collect($group['items'])->where('is_active', true)->isNotEmpty())
                    if (!this.isGroupOpen('{{ $group['id'] }}')) {
                        this.openGroups.push('{{ $group['id'] }}');
                        localStorage.setItem('sidebar_open_groups', JSON.stringify(this.openGroups));
                    }
                @endif
            @endforeach
            lucide.createIcons();
        }
    }">

        @foreach($menuGroups as $group)
            @php
                $groupHasActive = collect($group['items'] ?? [])->contains(fn ($item) => ! empty($item['is_active']));
            @endphp
            <div class="nav-group mb-4">
                {{-- Group Header --}}
                <div x-on:click="toggleGroup('{{ $group['id'] }}')" 
                     class="nav-group-header flex items-center justify-between p-4 rounded-2xl cursor-pointer transition-all hover:bg-zinc-900 group"
                     :class="isGroupOpen('{{ $group['id'] }}') || {{ $groupHasActive ? 'true' : 'false' }} ? 'bg-zinc-900/50' : ''">
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-xl bg-zinc-950 border border-zinc-900 flex items-center justify-center text-zinc-700 transition-all"
                             :class="isGroupOpen('{{ $group['id'] }}') ? '{{ $isClinica ? 'text-blue-500 border-blue-500/20' : 'text-emerald-500 border-emerald-500/20' }} shadow-lg {{ $isClinica ? 'shadow-blue-500/5' : 'shadow-emerald-500/5' }}' : ''">
                            <i class="{{ $group['icon'] }} w-4 h-4"></i>
                        </div>
                        <span class="text-[10px] font-black tracking-[0.2em] uppercase transition-colors"
                               :class="isGroupOpen('{{ $group['id'] }}') ? 'text-white' : 'text-zinc-600 group-hover:text-zinc-400'">{{ $group['label'] }}</span>
                    </div>
                    <div class="transition-all transform" :class="isGroupOpen('{{ $group['id'] }}') ? 'rotate-180 text-emerald-500' : 'text-zinc-800'">
                        <i data-lucide="chevron-down" class="w-3 h-3"></i>
                    </div>
                </div>

                {{-- Group Items --}}
                <div x-show="isGroupOpen('{{ $group['id'] }}')" 
                     x-collapse 
                     class="submenu space-y-1 mt-2 pl-4">
                    @foreach($group['items'] as $item)
                        <div class="nav-item">
                             <a href="{{ route($item['route']) }}" 
                               {{ $item['is_locked'] ? 'data-premium-locked' : '' }}
                               class="nav-link flex items-center justify-between px-6 py-3.5 rounded-2xl transition-all relative overflow-hidden group {{ $item['is_active'] ? ($isClinica ? 'bg-blue-500/10 text-blue-400 border border-blue-500/10 shadow-lg' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/10 shadow-lg') : 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-900/30' }}">
                                
                                @if($item['is_active'])
                                    <div class="absolute left-0 top-0 bottom-0 w-1 {{ $isClinica ? 'bg-blue-500' : 'bg-emerald-500' }}"></div>
                                @endif

                                <div class="flex items-center gap-4">
                                    <i class="{{ $item['icon'] }} w-4 h-4 transition-transform group-hover:scale-110"></i>
                                    <span class="text-[11px] font-black uppercase tracking-widest">{{ $item['label'] }}</span>
                                </div>

                                @if($item['is_locked'])
                                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-zinc-950 border border-zinc-900 text-[8px] font-black text-zinc-700 uppercase tracking-widest group-hover:border-emerald-500/20 group-hover:text-emerald-500 transition-all">
                                        <i data-lucide="lock" class="w-2.5 h-2.5"></i>
                                        ELITE
                                    </div>
                                @elseif($item['is_premium'] ?? false)
                                    <div class="px-2.5 py-1 rounded-lg bg-amber-500 text-zinc-950 text-[8px] font-black uppercase tracking-widest shadow-lg shadow-amber-500/10">
                                        VIP
                                    </div>
                                @elseif($item['badge'])
                                    <span class="w-5 h-5 flex items-center justify-center bg-emerald-500 text-zinc-950 text-[9px] font-black rounded-lg shadow-lg shadow-emerald-500/20">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Suporte & Ajuda -->
        <div class="nav-group mb-4 space-y-1">
            <a href="{{ route('support.tickets.index') }}" 
               class="nav-link flex items-center gap-4 p-4 rounded-2xl transition-all {{ request()->routeIs('support.tickets.*') ? ($isClinica ? 'bg-blue-500/10 text-blue-400 border border-blue-500/10 shadow-lg' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/10 shadow-lg') : 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-900/30' }}">
                <div class="w-8 h-8 rounded-xl bg-zinc-950 border border-zinc-900 flex items-center justify-center text-zinc-700 transition-all {{ request()->routeIs('support.tickets.*') ? ($isClinica ? 'text-blue-500 border-blue-500/20' : 'text-emerald-500 border-emerald-500/20') : '' }}">
                    <i data-lucide="life-buoy" class="w-4 h-4"></i>
                </div>
                <span class="text-[10px] font-black tracking-[0.2em] uppercase {{ request()->routeIs('support.tickets.*') ? 'text-white' : '' }}">Suporte Técnico</span>
            </a>
            
            <a href="{{ route('kb.index') }}" 
               class="nav-link flex items-center gap-4 p-4 rounded-2xl transition-all {{ request()->routeIs('kb.*') ? ($isClinica ? 'bg-blue-500/10 text-blue-400 border border-blue-500/10 shadow-lg' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/10 shadow-lg') : 'text-zinc-500 hover:text-zinc-300 hover:bg-zinc-900/30' }}">
                <div class="w-8 h-8 rounded-xl bg-zinc-950 border border-zinc-900 flex items-center justify-center text-zinc-700 transition-all {{ request()->routeIs('kb.*') ? ($isClinica ? 'text-blue-500 border-blue-500/20' : 'text-emerald-500 border-emerald-500/20') : '' }}">
                    <i data-lucide="help-circle" class="w-4 h-4"></i>
                </div>
                <span class="text-[10px] font-black tracking-[0.2em] uppercase {{ request()->routeIs('kb.*') ? 'text-white' : '' }}">Central de Ajuda</span>
            </a>
        </div>
    </div>

    <!-- Footer / Logout -->
    <div class="sidebar-footer p-6 border-t border-zinc-900 bg-zinc-950/50">
        <form action="{{ route('logout') }}" method="post" class="nav-logout-form">
            @csrf
            <button type="submit" class="group flex items-center gap-4 w-full p-4 rounded-2xl transition-all hover:bg-rose-500/5 hover:border-rose-500/20 border border-transparent">
                <div class="w-10 h-10 bg-zinc-900 rounded-xl flex items-center justify-center text-zinc-700 group-hover:text-rose-500 group-hover:bg-zinc-950 transition-all">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </div>
                <div class="flex flex-col items-start">
                    <span class="text-[11px] font-black text-zinc-600 group-hover:text-rose-400 uppercase tracking-widest">Encerrar</span>
                    <span class="text-[9px] font-bold text-zinc-800 uppercase tracking-widest group-hover:text-rose-500/50">Sessão Ativa</span>
                </div>
            </button>
        </form>
    </div>
</aside>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.05); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.1); }
    
    .nav-group-header:hover .w-8 { border-color: rgba(16, 185, 129, 0.2); color: #10b981; }
</style>
