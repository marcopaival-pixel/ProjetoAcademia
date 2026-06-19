@extends('layouts.app')

@section('title', 'Central de Relatórios — NexShape Pro')

@section('content')
<div class="py-10 space-y-12 animate-fade-in mx-auto px-4 md:px-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 pb-8 border-b border-white/5">
        <div>
            <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-400 text-[10px] font-black uppercase tracking-widest border border-indigo-500/20">Business Intelligence</span>
                @if(!$isPremium)
                    <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-500 text-[10px] font-black uppercase tracking-widest border border-amber-500/20 italic">Plano Free</span>
                @else
                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 italic">Plano Premium Ativo</span>
                @endif
            </div>
            <h1 class="text-5xl font-black tracking-tighter text-white">
                Módulo de <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-purple-400">Relatórios</span>
            </h1>
            <p class="text-zinc-500 font-medium mt-4 text-lg max-w-2xl">Acesse insights detalhados sobre sua base de {{ mb_strtolower($patientsLabel) }}, performance técnica e saúde financeira.</p>
        </div>

        @if(!$isPremium)
        <div class="bg-gradient-to-br from-indigo-600 to-purple-700 p-6 rounded-[2rem] shadow-2xl flex items-center gap-6 border border-white/10">
            <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-white">
                <i class="fas fa-rocket text-xl"></i>
            </div>
            <div>
                <p class="text-white font-black text-sm leading-tight">Desbloqueie o Premium</p>
                <p class="text-white/60 text-[10px] font-bold uppercase tracking-widest">Relatórios Gerenciais e Exportação</p>
                <a href="{{ route('plano') }}" class="mt-3 inline-block px-4 py-2 bg-white text-zinc-900 text-[9px] font-black rounded-xl uppercase tracking-widest hover:bg-zinc-200 transition-all">Fazer Upgrade</a>
            </div>
        </div>
        @endif
    </div>

    @if(session('premium_required'))
    <div class="bg-amber-500/10 border border-amber-500/20 p-6 rounded-3xl flex items-center gap-5 text-amber-500 animate-bounce">
        <i class="fas fa-lock text-xl"></i>
        <div>
            <p class="font-black text-sm uppercase tracking-widest">Funcionalidade Premium</p>
            <p class="text-xs font-bold opacity-80 mt-1">Essa funcionalidade está disponível apenas no plano Premium. Ative agora para ter acesso total.</p>
        </div>
    </div>
    @endif

    @php
        $freeReports = collect($reports['free'] ?? [])->where('kind', 'report');
        $freeShortcuts = collect($reports['free'] ?? [])->where('kind', 'shortcut');
    @endphp

    <!-- Relatórios (dados) -->
    <div class="space-y-8">
        <div class="flex items-center gap-4">
            <h2 class="text-2xl font-black text-white italic">Relatórios</h2>
            <div class="h-px flex-1 bg-white/5"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($freeReports as $report)
            <a href="{{ route($report['route']) }}" class="group bg-zinc-900/40 backdrop-blur-2xl border border-white/5 p-8 rounded-[2.5rem] hover:bg-white/5 transition-all hover:-translate-y-1 relative overflow-hidden">
                <div class="flex items-start justify-between relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-800 flex items-center justify-center text-zinc-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-lg">
                        <i class="{{ $report['icon'] }} text-xl"></i>
                    </div>
                    @if(isset($report['id']) && $report['id'] == 'basic_history')
                        <span class="px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-400 text-[8px] font-black uppercase tracking-widest border border-blue-500/20">30 dias</span>
                    @endif
                </div>
                <h3 class="text-xl font-black text-white mt-6 mb-2">{{ $report['label'] }}</h3>
                <p class="text-zinc-500 text-xs font-medium">Dados agregados conforme o seu plano.</p>
                <div class="mt-6 flex items-center text-[10px] font-black text-indigo-400 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                    Acessar <i class="fas fa-chevron-right ml-2 text-[8px]"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Atalhos -->
    <div class="space-y-8">
        <div class="flex items-center gap-4">
            <h2 class="text-2xl font-black text-white italic">Atalhos</h2>
            <div class="h-px flex-1 bg-white/5"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($freeShortcuts as $report)
            <a href="{{ route($report['route']) }}" class="group bg-zinc-900/40 backdrop-blur-2xl border border-white/5 p-8 rounded-[2.5rem] hover:bg-white/5 transition-all hover:-translate-y-1 relative overflow-hidden">
                <div class="w-14 h-14 rounded-2xl bg-zinc-800 flex items-center justify-center text-zinc-400 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-lg mb-4">
                    <i class="{{ $report['icon'] }} text-xl"></i>
                </div>
                <h3 class="text-xl font-black text-white mb-2">{{ $report['label'] }}</h3>
                @if(!empty($report['note']))
                    <p class="text-zinc-500 text-xs font-medium mb-2">{{ $report['note'] }}</p>
                @else
                    <p class="text-zinc-500 text-xs font-medium">Área relacionada do painel.</p>
                @endif
                <div class="mt-6 flex items-center text-[10px] font-black text-indigo-400 uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                    Abrir <i class="fas fa-chevron-right ml-2 text-[8px]"></i>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Premium Reports Section -->
    <div class="space-y-8">
        <div class="flex items-center gap-4">
            <h2 class="text-2xl font-black text-white italic">Relatórios Avançados</h2>
            <div class="h-px flex-1 bg-white/5"></div>
            @if(!$isPremium)
                <span class="px-4 py-1.5 bg-indigo-600 text-white text-[10px] font-black rounded-full shadow-lg shadow-indigo-600/20 uppercase tracking-widest">NexShape Premium</span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($reports['premium'] as $report)
            <div class="group relative bg-zinc-900/20 border {{ $isPremium ? 'border-white/5 hover:bg-zinc-900/40' : 'border-indigo-500/10 grayscale-[0.5] opacity-80 hover:opacity-100' }} p-8 rounded-[2.5rem] transition-all hover:-translate-y-1 overflow-hidden">
                @if(!$isPremium)
                    <div class="absolute inset-0 bg-zinc-950/40 backdrop-blur-[2px] z-20 flex items-center justify-center">
                        <i class="fas fa-lock text-white/30 text-4xl group-hover:scale-110 transition-transform"></i>
                    </div>
                @endif

                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-2xl {{ $isPremium ? 'bg-indigo-600/10 text-indigo-400' : 'bg-zinc-800 text-zinc-600' }} flex items-center justify-center mb-6">
                        <i class="{{ $report['icon'] }} text-lg"></i>
                    </div>
                    <h3 class="text-lg font-black text-white mb-2 leading-tight">{{ $report['label'] }}</h3>
                    <p class="text-zinc-600 text-[10px] font-bold uppercase tracking-widest mb-6">Módulo Premium</p>
                    
                    @if($isPremium)
                        @if(!empty($report['route']))
                            <a href="{{ route($report['route'], $report['route_params'] ?? []) }}" class="px-5 py-2.5 bg-indigo-600 text-white text-[9px] font-black rounded-xl uppercase tracking-widest hover:bg-indigo-500 transition-all block text-center">Abrir</a>
                        @else
                            <span class="block text-center px-5 py-2.5 bg-zinc-800/80 text-zinc-500 text-[9px] font-black rounded-xl uppercase tracking-widest">Em desenvolvimento</span>
                        @endif
                    @else
                        <a href="{{ route('plano') }}" class="px-5 py-2.5 bg-zinc-800 text-zinc-400 text-[9px] font-black rounded-xl uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all block text-center">Ativar Premium</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection



