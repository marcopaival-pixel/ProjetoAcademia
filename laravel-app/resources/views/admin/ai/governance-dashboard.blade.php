@extends('layouts.app')

@section('title', 'Governança de IA — NexShape Admin')

@section('content')
<div class="px-6 py-10 mx-auto max-w-7xl animate-fade-in">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="px-3 py-1 rounded-full bg-violet-500/10 text-violet-400 text-[10px] font-black uppercase tracking-widest border border-violet-500/20">Governança IA</span>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter italic uppercase">
                AI <span class="text-violet-500">GOVERNANCE</span>
            </h1>
            <p class="text-zinc-500 text-sm mt-2">Tokens, custos USD, créditos, agentes e clínicas — visão unificada.</p>
        </div>
        <form action="{{ route('admin.financial.ai-credits.governance') }}" method="GET">
            <select name="days" onchange="this.form.submit()" class="bg-zinc-900 border border-zinc-800 text-zinc-400 text-[10px] font-black uppercase tracking-widest px-4 py-2.5 rounded-xl">
                <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 dias</option>
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 dias</option>
                <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 dias</option>
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-12">
        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-3xl">
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Custo USD (período)</p>
            <p class="text-4xl font-black text-white">${{ number_format($metrics['total_cost_usd'], 2) }}</p>
            <p class="text-xs text-emerald-500 mt-2">Hoje: ${{ number_format($metrics['cost_today_usd'], 2) }}</p>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-3xl">
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Tokens (período)</p>
            <p class="text-4xl font-black text-white">{{ number_format($metrics['total_tokens']) }}</p>
            <p class="text-xs text-blue-400 mt-2">Hoje: {{ number_format($metrics['tokens_today']) }}</p>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-3xl">
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Créditos consumidos</p>
            <p class="text-4xl font-black text-white">{{ number_format($metrics['credits_consumed']) }}</p>
            <p class="text-xs text-amber-400 mt-2">Hoje: {{ number_format($metrics['credits_today']) }}</p>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-3xl">
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2">Requisições / Taxa erro</p>
            <p class="text-4xl font-black text-white">{{ number_format($metrics['total_requests']) }}</p>
            <p class="text-xs {{ $metrics['error_rate'] > 5 ? 'text-rose-500' : 'text-zinc-500' }} mt-2">{{ $metrics['error_rate'] }}% erros/limites</p>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-3xl col-span-1 md:col-span-2 lg:col-span-1">
            <p class="text-[10px] font-black text-violet-400 uppercase tracking-widest mb-2">Margem Líquida SaaS (IA)</p>
            <p class="text-2xl font-black text-emerald-400">R$ {{ number_format($metrics['net_margin_brl'], 2, ',', '.') }}</p>
            <p class="text-xs text-zinc-500 mt-2">Impacto IA: <span class="text-violet-400 font-bold">{{ number_format($metrics['ia_revenue_pct'], 1) }}%</span> da Rec.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <div class="bg-zinc-950 border border-zinc-800 rounded-3xl p-8">
            <h2 class="text-lg font-black text-white uppercase mb-6">Ranking — Agentes (custo USD)</h2>
            <div class="space-y-3">
                @forelse($byAgent as $row)
                <div class="flex justify-between items-center py-2 border-b border-zinc-900">
                    <span class="text-zinc-300 font-bold">{{ $row->agent_name }}</span>
                    <span class="text-emerald-400 font-mono">${{ number_format($row->total_cost, 4) }} · {{ number_format($row->total_tokens) }} tok</span>
                </div>
                @empty
                <p class="text-zinc-600">Sem dados no período.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-zinc-950 border border-zinc-800 rounded-3xl p-8">
            <h2 class="text-lg font-black text-white uppercase mb-6">Ranking — Clínicas (custo USD)</h2>
            <div class="space-y-3">
                @forelse($byClinic as $row)
                <div class="flex justify-between items-center py-2 border-b border-zinc-900">
                    <span class="text-zinc-300">Clínica #{{ $row->clinic_id }}</span>
                    <span class="text-violet-400 font-mono">${{ number_format($row->total_cost, 4) }}</span>
                </div>
                @empty
                <p class="text-zinc-600">Sem clinic_id nos logs.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-zinc-950 border border-zinc-800 rounded-3xl p-8">
            <h2 class="text-lg font-black text-white uppercase mb-6">Consumo por funcionalidade (créditos)</h2>
            @forelse($byFeature as $row)
            <div class="flex justify-between py-2 border-b border-zinc-900">
                <span class="text-zinc-400">{{ $row->action_type }}</span>
                <span class="text-amber-400">{{ number_format($row->total_credits) }} cr · {{ $row->count }}x</span>
            </div>
            @empty
            <p class="text-zinc-600">Nenhum uso registrado em ai_credits_usage_logs.</p>
            @endforelse
        </div>

        <div class="bg-zinc-950 border border-zinc-800 rounded-3xl p-8">
            <h2 class="text-lg font-black text-white uppercase mb-6">Top utilizadores (USD)</h2>
            @forelse($topUsers as $row)
            <div class="flex justify-between py-2 border-b border-zinc-900">
                <span class="text-zinc-300">{{ $row->user->name ?? 'User #'.$row->user_id }}</span>
                <span class="text-rose-400 font-mono">${{ number_format($row->total_cost, 4) }}</span>
            </div>
            @empty
            <p class="text-zinc-600">Sem dados.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-8 flex gap-4">
        <a href="{{ route('admin.financial.ai-credits.orchestrator.dashboard') }}" class="text-sm text-emerald-500 hover:underline">→ Orchestrator técnico</a>
        <a href="{{ route('admin.financial.ai-credits.dashboard') }}" class="text-sm text-amber-500 hover:underline">→ Créditos financeiros</a>
    </div>
</div>
@endsection
