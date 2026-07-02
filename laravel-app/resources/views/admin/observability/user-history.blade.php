@extends('layouts.admin')

@section('title', 'Histórico de Atividades do Usuário')

@section('content')
<div class="space-y-8 animate-fade-in text-white">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-black text-white tracking-tight">Histórico de Atividades</h2>
        <p class="text-zinc-500 text-sm mt-1">Rastreamento cronológico de ações e navegação para depuração rápida.</p>
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar: Buscar e Recentes -->
        <div class="space-y-6">
            <!-- Search Card -->
            <div class="bg-zinc-900/40 border border-white/5 p-6 rounded-[2rem] shadow-xl">
                <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-4">Filtrar Usuário</h3>
                <form method="GET" class="space-y-3">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-2 text-sm text-white focus:outline-none focus:border-emerald-500" 
                           placeholder="E-mail ou nome...">
                    <button type="submit" class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-black uppercase rounded-xl transition-all">
                        Buscar
                    </button>
                </form>
            </div>

            <!-- Recent Users -->
            <div class="bg-zinc-900/40 border border-white/5 p-6 rounded-[2rem] shadow-xl">
                <h3 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-4">Usuários Ativos Recentes</h3>
                <div class="space-y-2">
                    @foreach($recentUsers as $ru)
                        <a href="{{ route('admin.observability.user-history', $ru->id) }}" class="flex items-center gap-3 p-3 bg-zinc-950/40 hover:bg-white/5 border border-white/5 rounded-2xl transition-all">
                            <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-xs font-bold text-zinc-400">
                                {{ substr($ru->name, 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-white truncate">{{ $ru->name }}</p>
                                <span class="text-[9px] text-zinc-500 font-mono block truncate">{{ $ru->email }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Area: Timeline -->
        <div class="lg:col-span-2 space-y-6">
            @if($user)
                <!-- User Profile Banner -->
                <div class="bg-zinc-900/40 border border-white/5 p-6 rounded-[2rem] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-xl">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-lg font-bold">
                            {{ substr($user->name, 0, 2) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white leading-tight">{{ $user->name }}</h3>
                            <span class="text-xs text-zinc-500 font-mono block mt-0.5">{{ $user->email }}</span>
                        </div>
                    </div>
                    @if($user->academyCompany)
                        <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase rounded-lg border border-emerald-500/20">
                            {{ $user->academyCompany->name }}
                        </span>
                    @endif
                </div>

                <!-- Timeline Visualizer -->
                <div class="bg-zinc-900/40 border border-white/5 p-8 rounded-[2.5rem] shadow-xl relative">
                    <h3 class="text-lg font-black tracking-tight text-white mb-8 flex items-center gap-3">
                        <i class="fas fa-stream text-emerald-500"></i> Rastro de Navegação
                    </h3>

                    @if($timeline->isEmpty())
                        <p class="text-center text-zinc-500 text-sm py-16">Nenhuma atividade registrada para este usuário nas últimas 24 horas.</p>
                    @else
                        <!-- Vertical Timeline Line -->
                        <div class="absolute left-12 top-24 bottom-12 w-0.5 bg-zinc-800"></div>

                        <div class="space-y-8 relative">
                            @foreach($timeline as $item)
                                <div class="flex gap-6 items-start">
                                    <!-- Timeline Node Icon -->
                                    <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center z-10 shadow-lg {{ $item['class'] }}">
                                        <i class="fas {{ $item['icon'] }} text-xs"></i>
                                    </div>
                                    <!-- Content -->
                                    <div class="flex-1 bg-zinc-950/40 border border-white/5 p-5 rounded-2xl">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                            <h4 class="text-xs font-black text-white tracking-wide uppercase">{{ $item['title'] }}</h4>
                                            <span class="text-[9px] text-zinc-500 font-bold tabular-nums">{{ $item['date']->format('d/m/Y H:i:s') }}</span>
                                        </div>
                                        <p class="text-xs text-zinc-400 mt-2 font-mono break-all leading-relaxed">{{ $item['details'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <!-- No User Selected State -->
                <div class="bg-zinc-900/40 border border-white/5 p-16 rounded-[2.5rem] text-center shadow-xl">
                    <div class="w-16 h-16 bg-zinc-800 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-600">
                        <i class="fas fa-user-circle text-2xl"></i>
                    </div>
                    <h3 class="text-white font-black">Nenhum Usuário Selecionado</h3>
                    <p class="text-zinc-500 text-sm mt-1 max-w-sm mx-auto">Busque por um e-mail ou nome ou selecione um usuário ativo recente ao lado para auditar.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
