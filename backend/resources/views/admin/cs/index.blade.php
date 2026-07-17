@extends('layouts.admin')

@section('title', 'Saúde do Cliente (CS)')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Monitoramento de Saúde</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Visão geral do engajamento dos clientes</p>
        </div>
        <a href="{{ route('admin.cs.retention') }}" class="px-6 py-3 bg-zinc-900 border border-white/5 rounded-xl text-[10px] text-white font-black uppercase tracking-widest hover:border-emerald-500/50 transition-all flex items-center gap-2 shadow-2xl">
            <i class="fas fa-hand-holding-heart text-emerald-500"></i> Planos de Retenção
        </a>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8 text-center">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-4">Saúde Média</p>
            <h3 class="text-4xl font-black text-emerald-500">{{ number_format($stats['avg_health'], 0) }}%</h3>
        </div>
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8 text-center ring-1 ring-red-500/20">
            <p class="text-[10px] text-red-500 font-black uppercase tracking-widest mb-4">Risco Alto (Churn)</p>
            <h3 class="text-4xl font-black text-white">{{ $stats['high_risk'] }}</h3>
        </div>
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8 text-center ring-1 ring-amber-500/20">
            <p class="text-[10px] text-amber-500 font-black uppercase tracking-widest mb-4">Risco Médio</p>
            <h3 class="text-4xl font-black text-white">{{ $stats['medium_risk'] }}</h3>
        </div>
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8 text-center">
            <p class="text-[10px] text-emerald-500 font-black uppercase tracking-widest mb-4">Clientes Ativos</p>
            <h3 class="text-4xl font-black text-white">{{ $stats['low_risk'] }}</h3>
        </div>
    </div>

    <!-- At Risk Table -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
        <div class="px-8 py-6 bg-white/[0.02] border-b border-white/5">
            <h4 class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Atenção Prioritária (Risco Médio e Alto)</h4>
        </div>
        
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/[0.01] border-bottom border-white/5">
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Cliente / Email</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Última Atividade</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Health Score</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Risco</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Ação</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($atRiskUsers as $user)
                <tr class="hover:bg-white/[0.01] transition-all group">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-zinc-950 flex items-center justify-center text-zinc-500 border border-white/5 text-sm font-black">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-white">{{ $user->name }}</span>
                                <span class="text-[10px] text-zinc-600 font-bold uppercase tracking-tight">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="text-[10px] text-zinc-400 font-bold">{{ $user->last_activity_at ? $user->last_activity_at->diffForHumans() : 'Nunca' }}</span>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <div class="flex flex-col items-center gap-2">
                             @php
                                $healthColor = $user->health_score > 70 ? 'emerald' : ($user->health_score > 40 ? 'amber' : 'red');
                            @endphp
                            <span class="text-xs font-black text-white">{{ $user->health_score }}%</span>
                            <div class="w-16 h-1 bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full bg-{{ $healthColor }}-500" style="width: {{ $user->health_score }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="px-3 py-1 bg-{{ $user->churn_risk == 'High' ? 'red' : 'amber' }}-500/10 border border-{{ $user->churn_risk == 'High' ? 'red' : 'amber' }}-500/20 text-{{ $user->churn_risk == 'High' ? 'red' : 'amber' }}-500 text-[8px] font-black uppercase rounded-lg">
                            {{ $user->churn_risk }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <button class="px-4 py-2 bg-zinc-800 border border-white/5 rounded-xl text-[8px] text-zinc-400 font-black uppercase tracking-widest hover:bg-zinc-700 transition-all">
                             Notificar
                        </button>
                    </td>
                </tr>
                @empty
                 <tr>
                    <td colspan="5" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-heart text-4xl text-emerald-500/20 mb-6"></i>
                            <p class="text-zinc-600 font-bold uppercase tracking-widest text-[10px]">Todos os clientes estão com saúde em dia!</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($atRiskUsers->hasPages())
        <div class="px-8 py-6 bg-white/[0.01] border-t border-white/5">
            {{ $atRiskUsers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
