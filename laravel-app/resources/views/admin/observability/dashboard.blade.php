@extends('layouts.admin')

@section('title', 'Saúde do Sistema | Observabilidade Matrix')

@section('content')
<div class="space-y-8 animate-fade-in text-white">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">Saúde do Sistema</h2>
            <p class="text-zinc-500 text-sm mt-1">Visão holística e telemetria de integridade da plataforma.</p>
        </div>
        <div class="flex gap-3">
            @if($pulseActive)
                <a href="{{ url(config('pulse.path', 'pulse')) }}" target="_blank" class="px-4 py-2 bg-purple-600/10 text-purple-400 text-xs font-black uppercase rounded-xl border border-purple-500/20 hover:bg-purple-600 hover:text-white transition-all">
                    <i class="fas fa-heartbeat mr-2"></i> Laravel Pulse
                </a>
            @endif
            @if($sentryActive)
                <a href="https://sentry.io" target="_blank" class="px-4 py-2 bg-pink-600/10 text-pink-400 text-xs font-black uppercase rounded-xl border border-pink-500/20 hover:bg-pink-600 hover:text-white transition-all">
                    <i class="fas fa-bug mr-2"></i> Sentry Console
                </a>
            @endif
        </div>
    </div>

    <!-- 1. Grid de Métricas Principais -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Usuários Online -->
        <div class="bg-zinc-900/40 border border-white/5 p-6 rounded-[2rem] shadow-xl backdrop-blur-sm">
            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block">Usuários Online</span>
            <div class="flex items-baseline gap-2 mt-4">
                <span class="text-3xl font-black tracking-tight text-white tabular-nums">{{ number_format($usersOnline) }}</span>
                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse inline-block mb-1"></span>
            </div>
            <p class="text-xs text-zinc-500 mt-2">Atividade nos últimos 15 minutos.</p>
        </div>

        <!-- Cadastros Hoje -->
        <div class="bg-zinc-900/40 border border-white/5 p-6 rounded-[2rem] shadow-xl backdrop-blur-sm">
            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block">Cadastros Hoje</span>
            <div class="flex items-baseline gap-2 mt-4">
                <span class="text-3xl font-black tracking-tight text-white tabular-nums">{{ number_format($registrationsToday) }}</span>
            </div>
            <p class="text-xs text-zinc-500 mt-2">Registros criados desde a meia-noite.</p>
        </div>

        <!-- Taxa de Erro de Requisições -->
        <div class="bg-zinc-900/40 border border-white/5 p-6 rounded-[2rem] shadow-xl backdrop-blur-sm">
            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block">Taxa de Erro (API)</span>
            <div class="flex items-baseline gap-2 mt-4">
                <span class="text-3xl font-black tracking-tight @if($errorRate > 5) text-red-500 @else text-emerald-400 @endif tabular-nums">
                    {{ $errorRate }}%
                </span>
                <span class="text-xs text-zinc-500">({{ $failedRequests }} de {{ $totalRequests }} req.)</span>
            </div>
            <div class="w-full bg-zinc-800 h-1.5 rounded-full mt-3 overflow-hidden">
                <div class="h-full @if($errorRate > 5) bg-red-500 @else bg-emerald-400 @endif" style="width: {{ min(100, max(2, $errorRate)) }}%"></div>
            </div>
        </div>

        <!-- Tempo Médio de Resposta -->
        <div class="bg-zinc-900/40 border border-white/5 p-6 rounded-[2rem] shadow-xl backdrop-blur-sm">
            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block">Tempo Médio (Latência)</span>
            <div class="flex items-baseline gap-2 mt-4">
                <span class="text-3xl font-black tracking-tight text-white tabular-nums">{{ $avgDuration }}</span>
                <span class="text-zinc-500 text-sm">ms</span>
            </div>
            <p class="text-xs text-zinc-500 mt-2">Média de processamento nas últimas 24h.</p>
        </div>
    </div>

    <!-- 2. Status de Serviços de Infraestrutura -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-zinc-900/40 border border-white/5 p-6 rounded-[2rem] flex items-center justify-between shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-white">Laravel Pulse</h4>
                    <span class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider">Telemetria de Servidor</span>
                </div>
            </div>
            <span class="px-2.5 py-1 text-[9px] font-black uppercase rounded-lg border {{ $pulseActive ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-zinc-800 text-zinc-500 border-zinc-700' }}">
                {{ $pulseActive ? 'Ativo' : 'Inativo' }}
            </span>
        </div>

        <div class="bg-zinc-900/40 border border-white/5 p-6 rounded-[2rem] flex items-center justify-between shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-pink-500/10 border border-pink-500/20 flex items-center justify-center text-pink-400">
                    <i class="fas fa-bug"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-white">Sentry SDK</h4>
                    <span class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider">Rastreamento de Exceções</span>
                </div>
            </div>
            <span class="px-2.5 py-1 text-[9px] font-black uppercase rounded-lg border {{ $sentryActive ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-zinc-800 text-zinc-500 border-zinc-700' }}">
                {{ $sentryActive ? 'Ativo' : 'Configurado' }}
            </span>
        </div>

        <div class="bg-zinc-900/40 border border-white/5 p-6 rounded-[2rem] flex items-center justify-between shadow-xl">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                    <i class="fas fa-tasks"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-white">Laravel Horizon</h4>
                    <span class="text-[10px] text-zinc-500 uppercase font-bold tracking-wider">Monitor de Filas</span>
                </div>
            </div>
            <span class="px-2.5 py-1 text-[9px] font-black uppercase rounded-lg border {{ $horizonActive ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-zinc-800 text-zinc-500 border-zinc-700' }}">
                {{ $horizonActive ? 'Ativo' : 'Desativado' }}
            </span>
        </div>
    </div>

    <!-- Métricas de Negócio & Alertas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Métricas de Cadastro/Negócio -->
        <div class="bg-zinc-900/40 border border-white/5 p-8 rounded-[2.5rem] shadow-xl backdrop-blur-sm">
            <h3 class="text-lg font-black tracking-tight text-white mb-6 flex items-center gap-3">
                <i class="fas fa-chart-line text-emerald-500"></i> Métricas de Cadastro (Negócio)
            </h3>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="bg-zinc-950/50 p-5 rounded-2xl border border-white/5">
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Iniciados</span>
                    <span class="text-2xl font-black text-white tabular-nums">{{ number_format($registrationsStarted) }}</span>
                </div>
                <div class="bg-zinc-950/50 p-5 rounded-2xl border border-white/5">
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Concluídos</span>
                    <span class="text-2xl font-black text-white tabular-nums">{{ number_format($registrationsCompleted) }}</span>
                </div>
                <div class="bg-zinc-950/50 p-5 rounded-2xl border border-white/5">
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Sucesso</span>
                    <span class="text-2xl font-black text-emerald-400 tabular-nums">{{ $registrationsSuccessRate }}%</span>
                </div>
            </div>
        </div>

        <!-- Canais de Alerta Ativos -->
        <div class="bg-zinc-900/40 border border-white/5 p-8 rounded-[2.5rem] shadow-xl backdrop-blur-sm">
            <h3 class="text-lg font-black tracking-tight text-white mb-6 flex items-center gap-3">
                <i class="fas fa-bell text-yellow-500"></i> Alertas Operacionais Automáticos
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-zinc-950/50 p-5 rounded-2xl border border-white/5 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] font-black text-zinc-500 uppercase block">Slack Ops</span>
                        <span class="text-xs font-bold text-zinc-300 mt-1 block">Canal Integrado</span>
                    </div>
                    <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded {{ $slackWebhookConfigured ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-zinc-800 text-zinc-500 border border-zinc-700' }}">
                        {{ $slackWebhookConfigured ? 'ON' : 'OFF' }}
                    </span>
                </div>
                <div class="bg-zinc-950/50 p-5 rounded-2xl border border-white/5 flex items-center justify-between">
                    <div>
                        <span class="text-[9px] font-black text-zinc-500 uppercase block">WhatsApp Ops</span>
                        <span class="text-xs font-bold text-zinc-300 mt-1 block font-mono text-[10px]">{{ $whatsappAlertsActive ? 'Ativo' : 'Desativado' }}</span>
                    </div>
                    <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded {{ $whatsappAlertsActive ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-zinc-800 text-zinc-500 border border-zinc-700' }}">
                        {{ $whatsappAlertsActive ? 'ON' : 'OFF' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Heatmap de Erros vs Acesso Rápido -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Concentração de Falhas (Heatmap) -->
        <div class="bg-zinc-900/40 border border-white/5 p-8 rounded-[2.5rem] shadow-xl backdrop-blur-sm">
            <h3 class="text-lg font-black tracking-tight text-white mb-6 flex items-center gap-3">
                <i class="fas fa-fire-alt text-orange-500"></i> Concentração de Falhas (Rotas)
            </h3>
            
            <div class="space-y-4">
                @forelse($errorHeatmap as $heatmap)
                    @php
                        $percentage = $totalRequests > 0 ? round(($heatmap->total / $totalRequests) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between text-xs font-bold text-zinc-400 mb-1">
                            <span class="font-mono text-zinc-300">{{ $heatmap->path }}</span>
                            <span class="text-zinc-500 font-bold tabular-nums">{{ $heatmap->total }} erros</span>
                        </div>
                        <div class="w-full bg-zinc-900 h-2.5 rounded-full overflow-hidden">
                            <div class="h-full bg-orange-500 rounded-full" style="width: {{ min(100, max(5, $percentage * 3)) }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-zinc-500 text-sm py-12">Nenhuma falha de rota acumulada nas últimas 24h.</p>
                @endforelse
            </div>
        </div>

        <!-- Links e Atalhos Rápidos -->
        <div class="bg-zinc-900/40 border border-white/5 p-8 rounded-[2.5rem] shadow-xl backdrop-blur-sm">
            <h3 class="text-lg font-black tracking-tight text-white mb-6">Fontes de Auditoria</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="{{ route('admin.observability.admin-logs') }}" class="p-4 bg-zinc-950 hover:bg-white/5 border border-white/5 hover:border-white/10 rounded-2xl transition-all group">
                    <span class="text-[10px] font-black text-zinc-500 group-hover:text-white uppercase tracking-widest block mb-1">Operadores</span>
                    <span class="text-sm font-bold text-zinc-300">Logs de Admin</span>
                </a>
                <a href="{{ route('admin.observability.auth-logs') }}" class="p-4 bg-zinc-950 hover:bg-white/5 border border-white/5 hover:border-white/10 rounded-2xl transition-all group">
                    <span class="text-[10px] font-black text-zinc-500 group-hover:text-white uppercase tracking-widest block mb-1">Segurança</span>
                    <span class="text-sm font-bold text-zinc-300">Auditoria de Auth</span>
                </a>
                <a href="{{ route('admin.observability.api-logs') }}" class="p-4 bg-zinc-950 hover:bg-white/5 border border-white/5 hover:border-white/10 rounded-2xl transition-all group">
                    <span class="text-[10px] font-black text-zinc-500 group-hover:text-white uppercase tracking-widest block mb-1">Integrações</span>
                    <span class="text-sm font-bold text-zinc-300">Chamadas de API</span>
                </a>
                <a href="{{ route('admin.observability.client-errors') }}" class="p-4 bg-zinc-950 hover:bg-white/5 border border-white/5 hover:border-white/10 rounded-2xl transition-all group">
                    <span class="text-[10px] font-black text-zinc-500 group-hover:text-white uppercase tracking-widest block mb-1">Frontend</span>
                    <span class="text-sm font-bold text-zinc-300">Erros de JS / Client</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4. Últimos Erros de Sistema (Backend) -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden shadow-xl">
        <div class="p-6 border-b border-white/5 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-black tracking-tight text-white">Exceções Recentes do Sistema</h3>
                <p class="text-zinc-500 text-xs mt-0.5">Alertas críticos registrados no kernel do backend.</p>
            </div>
            <a href="{{ route('admin.system-errors') }}" class="px-4 py-2 bg-zinc-950 text-white text-xs font-black uppercase rounded-xl border border-white/10 hover:border-blue-500/50 transition-all">
                Ver todos os erros
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Protocolo</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Mensagem de Kernel</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Identidade</th>
                        <th class="p-4 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Localização</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($recentErrors as $err)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-4">
                                <span class="px-2.5 py-1 bg-red-500/10 text-red-400 text-[9px] font-black uppercase rounded-lg border border-red-500/20">
                                    {{ $err->type }}
                                </span>
                                <span class="block text-[9px] text-zinc-600 font-bold tabular-nums mt-1">{{ $err->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="p-4 max-w-xs font-bold text-sm text-zinc-300 truncate" title="{{ $err->message }}">
                                {{ $err->message }}
                            </td>
                            <td class="p-4 text-xs text-zinc-400">
                                {{ $err->user?->name ?? 'GUEST' }}
                            </td>
                            <td class="p-4 font-mono text-[10px] text-blue-400">
                                <span class="uppercase font-black block text-blue-500">{{ $err->method }}</span>
                                <span class="text-zinc-500 block truncate max-w-[180px]">{{ $err->url }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-12 text-center text-zinc-500">Nenhuma exceção registrada nas últimas horas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
