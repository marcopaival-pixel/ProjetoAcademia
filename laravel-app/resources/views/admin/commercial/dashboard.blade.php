@extends('layouts.admin')

@section('title', 'Dashboard Comercial')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div>
        <h2 class="text-3xl font-black text-white tracking-tight">Inteligência Comercial</h2>
        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">Visão geral de leads, conversões e pipeline financeiro</p>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8 group hover:bg-zinc-900/60 transition-all">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-4">Pipeline Ativo</p>
            <h3 class="text-3xl font-black text-white tracking-tighter">R$ {{ number_format($kpis['pipeline_value'], 2, ',', '.') }}</h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="text-[9px] text-emerald-500 font-bold bg-emerald-500/10 px-2 py-0.5 rounded-full">Potencial</span>
            </div>
        </div>

        <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8 group hover:bg-zinc-900/60 transition-all">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-4">Vendas Concluídas</p>
            <h3 class="text-3xl font-black text-white tracking-tighter text-blue-500">R$ {{ number_format($kpis['closed_value'], 2, ',', '.') }}</h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="text-[9px] text-zinc-500 font-bold uppercase">Total Acumulado</span>
            </div>
        </div>

        <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8 group hover:bg-zinc-900/60 transition-all">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-4">Taxa de Conversão</p>
            <h3 class="text-4xl font-black text-white tracking-tighter">{{ number_format($kpis['conversion_rate'], 1) }}%</h3>
            <div class="w-full h-1 bg-zinc-800 rounded-full mt-6 overflow-hidden">
                <div class="h-full bg-blue-500" style="width: {{ $kpis['conversion_rate'] }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8 group hover:bg-zinc-900/60 transition-all">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-4">Total de Leads</p>
            <h3 class="text-4xl font-black text-white tracking-tighter">{{ $kpis['total_leads'] }}</h3>
            <div class="mt-4">
                <span class="text-[9px] text-blue-400 font-bold bg-blue-500/10 px-2 py-0.5 rounded-full">+{{ $kpis['new_leads_month'] }} este mês</span>
            </div>
        </div>
    </div>

    <!-- Graphical Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sales Funnel -->
        <div class="lg:col-span-2 bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-10">
            <h4 class="text-sm font-black text-white uppercase tracking-widest mb-10">Funil de Vendas</h4>
            
            <div class="space-y-6">
                @php($max = max($funnelData) ?: 1)
                @foreach(['Novo', 'Em contato', 'Em negociação', 'Convertido'] as $status)
                    @php($percent = ($funnelData[$status] / $max) * 100)
                    @php($color = ['Novo' => 'blue-600', 'Em contato' => 'amber-500', 'Em negociação' => 'purple-500', 'Convertido' => 'emerald-500'][$status])
                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <span class="text-[10px] text-zinc-400 font-black uppercase tracking-widest italic">{{ $status }}</span>
                            <span class="text-sm font-black text-white">{{ $funnelData[$status] }}</span>
                        </div>
                        <div class="w-full h-10 bg-zinc-950 border border-white/5 rounded-2xl overflow-hidden p-1">
                            <div class="h-full bg-{{ $color }} rounded-xl transition-all duration-1000" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Latest Transactions -->
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-10">
            <h4 class="text-sm font-black text-white uppercase tracking-widest mb-10">Últimas Propostas</h4>
            <div class="space-y-6">
                @forelse($latestProposals as $proposal)
                <div class="flex items-center justify-between group">
                    <div>
                        <p class="text-xs font-bold text-white leading-tight group-hover:text-blue-400 transition-colors">{{ $proposal->lead->nome }}</p>
                        <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">{{ $proposal->plan->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black text-white">R$ {{ number_format($proposal->valor - $proposal->desconto, 0, ',', '.') }}</p>
                        <span class="text-[7px] font-black uppercase text-{{ ['Aprovada' => 'emerald', 'Rejeitada' => 'red', 'Enviada' => 'blue', 'Pendente' => 'zinc'][$proposal->status] ?? 'zinc' }}-500">
                            {{ $proposal->status }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-zinc-700 text-xs italic text-center py-10">Sem propostas recentes</p>
                @endforelse
            </div>
            
            <a href="{{ route('admin.proposals.index') }}" class="mt-10 block w-full py-4 bg-zinc-950 border border-white/5 rounded-2xl text-[9px] text-center text-zinc-500 font-black uppercase tracking-widest hover:text-white transition-all italic">Ver todas as propostas &rarr;</a>
        </div>
    </div>
</div>
@endsection
