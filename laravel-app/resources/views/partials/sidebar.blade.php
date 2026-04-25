@inject('menuService', 'App\Services\MenuService')
@php
    $user = auth()->user();
    
    // Safety check for guests
    if (!$user) {
        $menuGroups = [];
        $isPremium = false;
        $unreadMessages = 0;
    } else {
        $menuGroups = $menuService->getAccordionMenus($user);
        $isPremium = $user->hasPremiumAccess();
        
        // Contagem de Mensagens Não Lidas (Global)
        $unreadMessages = \App\Models\Message::whereHas('conversation', function($q) use ($user) {
            $q->where('user_one_id', $user->id)->orWhere('user_two_id', $user->id);
        })->where('sender_id', '!=', $user->id)->where('is_read', false)->count();
    }
@endphp

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

    <div class="sidebar-content" x-data="{ 
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
        }
    }">

        @foreach($menuGroups as $group)
            @php
                $groupHasActive = collect($group['items'] ?? [])->contains(fn ($item) => ! empty($item['is_active']));
            @endphp
            <div class="nav-group mb-2">
                {{-- Group Header --}}
                <div x-on:click="toggleGroup('{{ $group['id'] }}')" 
                     class="nav-group-header flex items-center justify-between transition-all {{ $groupHasActive ? 'nav-group-header--active' : '' }}"
                     :class="isGroupOpen('{{ $group['id'] }}') ? 'opacity-100' : 'opacity-80'">
                    <div class="flex items-center gap-3">
                        <i class="{{ $group['icon'] }} w-5 text-center transition-colors"
                           :class="isGroupOpen('{{ $group['id'] }}') ? 'text-blue-500' : 'opacity-70'"></i>
                        <span class="label font-bold tracking-wider uppercase text-[10px]"
                              :class="isGroupOpen('{{ $group['id'] }}') ? 'text-white' : ''">{{ $group['label'] }}</span>
                    </div>
                    <div class="text-[10px] transition-all">
                        <i class="fas" 
                           :class="isGroupOpen('{{ $group['id'] }}') ? 'fa-minus opacity-100 text-blue-500' : 'fa-plus opacity-30'"></i>
                    </div>
                </div>

                {{-- Group Items --}}
                <div x-show="isGroupOpen('{{ $group['id'] }}')" 
                     x-collapse 
                     class="submenu">
                    @foreach($group['items'] as $item)
                        <div class="nav-item">
                            <a href="{{ route($item['route']) }}" 
                               {{ $item['is_locked'] ? 'data-premium-locked' : '' }}
                               class="nav-link {{ $item['is_active'] ? 'active' : '' }} {{ $item['is_locked'] ? 'opacity-80' : '' }} flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 flex justify-center items-center">
                                        <i class="{{ $item['icon'] }} text-center {{ $item['is_active'] ? 'text-blue-400' : 'opacity-70' }}"></i>
                                    </div>
                                    <span class="label text-sm">{{ $item['label'] }}</span>
                                </div>

                                @if($item['is_locked'])
                                    <span class="premium-badge badge-pro">
                                        <i class="fas fa-lock"></i>
                                        PRO
                                    </span>
                                @elseif($item['is_premium'] ?? false)
                                    <span class="premium-badge badge-vip">
                                        <i class="fas fa-crown"></i>
                                        VIP
                                    </span>
                                @elseif($item['badge'])
                                    <span class="bg-blue-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

    </div>

    <div class="sidebar-footer p-2 border-t border-white/5">
        <form action="{{ route('logout') }}" method="post" class="nav-logout-form">
            @csrf
            <button type="submit" class="nav-link w-full text-left group">
                <svg class="w-5 h-5 text-red-500 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                <span class="label text-red-500 font-semibold text-sm">Sair da Conta</span>
            </button>
        </form>
    </div>
</aside>
