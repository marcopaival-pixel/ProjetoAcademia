@extends('layouts.app')

@section('title', ($title ?? 'Correio') . ' — NexShape Connect')

@section('content')
<div class="py-10 animate-fade-in max-w-[1400px] mx-auto px-6" style="color: var(--text-main);">
    <!-- Main Email Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-10 items-start min-h-[700px]">
        
        <!-- Left Pane: Navigation & Compose -->
        <div class="lg:col-span-1 space-y-8 sticky top-32">
            <a href="{{ route('internal-email.create') }}" class="group relative flex items-center justify-center gap-3 w-full py-6 font-black rounded-[2rem] overflow-hidden transition-all hover:scale-[1.02] active:scale-[0.98] shadow-lg" style="background: var(--text-main); color: var(--bg-main);">
                <i class="fas fa-edit text-xs"></i>
                ESCREVER
                <div class="absolute inset-x-0 bottom-0 h-1 bg-blue-600 transform translate-y-full group-hover:translate-y-0 transition-transform"></div>
            </a>

            <div class="backdrop-blur-3xl p-4 rounded-[2.5rem] shadow-xl" style="background: var(--sidebar-bg); border: 1px solid var(--border-color);">
                <nav class="space-y-1">
                    @php
                        $navItems = [
                            ['route' => 'internal-email.inbox', 'icon' => 'fas fa-inbox', 'label' => 'Entrada', 'badge' => true],
                            ['route' => 'internal-email.sent', 'icon' => 'fas fa-paper-plane', 'label' => 'Enviados'],
                            ['route' => 'internal-email.outbox', 'icon' => 'fas fa-clock', 'label' => 'Saída'],
                            ['route' => 'internal-email.trash', 'icon' => 'fas fa-trash-alt', 'label' => 'Lixeira'],
                        ];
                    @endphp

                    @foreach($navItems as $item)
                        @php $isActive = Route::is($item['route']); @endphp
                        <a href="{{ route($item['route']) }}" class="flex items-center justify-between p-4 rounded-2xl transition-all {{ $isActive ? 'bg-blue-600 text-white font-black' : 'hover:opacity-75' }}" style="{{ !$isActive ? 'color: var(--text-muted);' : '' }}">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ $isActive ? 'text-white' : '' }}" style="{{ !$isActive ? 'background: var(--bg-main); color: var(--text-muted);' : '' }}">
                                    <i class="{{ $item['icon'] }} text-[10px]"></i>
                                </div>
                                <span class="text-xs uppercase tracking-widest">{{ $item['label'] }}</span>
                            </div>
                            @if(isset($item['badge']) && $item['badge'])
                                @php $globalUnread = \App\Models\InternalEmail::inbox(auth()->id())->where('lida', false)->count(); @endphp
                                @if($globalUnread > 0)
                                    <span class="px-2 py-0.5 rounded-full bg-blue-600 text-[8px] text-white font-black">{{ $globalUnread }}</span>
                                @endif
                            @endif
                        </a>
                    @endforeach
                </nav>

                <div class="mt-8 pt-8 border-t px-4 mb-4" style="border-color: var(--border-color);">
                    <h6 class="text-[9px] font-black uppercase tracking-[0.2em] mb-4" style="color: var(--text-muted);">Sistema</h6>
                    <div class="flex items-center gap-3 p-3 rounded-xl border group hover:border-blue-500/20 transition-all cursor-pointer" style="background: var(--bg-main); border-color: var(--border-color);">
                        <div class="w-6 h-6 rounded flex items-center justify-center transition-colors group-hover:text-blue-500" style="background: var(--sidebar-bg); color: var(--text-muted);">
                            <i class="fas fa-robot text-[8px]"></i>
                        </div>
                        <span class="text-[10px] font-bold uppercase" style="color: var(--text-muted);">Alertas Auto</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Pane: Inbox/Reader -->
        <div class="lg:col-span-3 h-full">
            <div class="backdrop-blur-3xl rounded-[3rem] overflow-hidden flex flex-col min-h-[700px] shadow-2xl" style="background: var(--sidebar-bg); border: 1px solid var(--border-color);">
                <!-- Toolbar -->
                <header class="p-8 border-b flex flex-col md:flex-row md:items-center justify-between gap-6" style="border-color: var(--border-color); background: var(--bg-main);">
                    <div class="flex items-center gap-4">
                        @yield('toolbar-left')
                        <form action="{{ url()->current() }}" method="GET" class="relative group">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-700 text-[10px] group-focus-within:text-blue-500 transition-colors" style="color: var(--text-muted);"></i>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar conversa..." 
                                class="p-3 pl-10 rounded-xl text-xs outline-none focus:ring-1 focus:ring-blue-600 transition-all min-w-[200px] md:min-w-[300px]" style="background: var(--sidebar-bg); border: 1px solid var(--border-color); color: var(--text-main);">
                        </form>
                    </div>
                    <div class="flex items-center gap-6">
                        @yield('toolbar-right')
                        @if(isset($messages) && method_exists($messages, 'total'))
                            <div class="flex flex-col items-end">
                                <span class="text-[10px] font-black uppercase tracking-widest leading-none" style="color: var(--text-muted);">Amostragem</span>
                                <span class="text-[9px] font-bold mt-1" style="color: var(--text-main);">{{ $messages->firstItem() ?? 0 }} - {{ $messages->lastItem() ?? 0 }} <span style="color: var(--text-muted);">de {{ $messages->total() }}</span></span>
                            </div>
                        @endif
                    </div>
                </header>

                <!-- Dynamic Content Layer -->
                <div class="flex-grow relative overflow-y-auto custom-scrollbar">
                    @yield('email-content')
                </div>

                <!-- Footer Pagination -->
                @if(isset($messages) && method_exists($messages, 'links'))
                    <div class="px-8 py-6 border-t" style="background: var(--bg-main); border-color: var(--border-color);">
                        {{ $messages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(59, 130, 246, 0.5); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(59, 130, 246, 0.8); }
</style>
@endsection
