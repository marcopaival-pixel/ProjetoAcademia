@extends('layouts.admin')

@section('title', 'Retenção e Expansão')

@section('content')
<div class="space-y-10 animate-fade-in">
    <div>
        <h2 class="text-3xl font-black text-white tracking-tight">Oportunidades Estratégicas</h2>
        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Identificação automática de Upsell e Risco de Churn</p>
    </div>

    <!-- Expansion (Upsell) Section -->
    <div class="space-y-6">
        <h3 class="text-lg font-black text-emerald-500 flex items-center gap-3">
            <i class="fas fa-arrow-trend-up"></i> Oportunidades de Expansão (Upsell)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($upsellOpportunities as $user)
            <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-3xl p-8 flex items-center justify-between shadow-xl">
                <div>
                    <h4 class="text-lg font-black text-white">{{ $user->name }}</h4>
                    <p class="text-xs text-zinc-400 font-medium">{{ $user->email }}</p>
                    <div class="mt-4 flex items-center gap-2">
                        <span class="px-3 py-1 bg-emerald-500/20 text-[9px] text-emerald-400 font-black uppercase rounded-lg border border-emerald-500/20">Uso Elevado</span>
                        <span class="text-[10px] text-zinc-500 font-bold italic">Sugerir Plano Premium</span>
                    </div>
                </div>
                <button class="px-6 py-3 bg-emerald-600 text-white text-[9px] font-black uppercase rounded-xl hover:bg-emerald-500 transition-all">
                    Iniciar Contato
                </button>
            </div>
            @empty
            <div class="col-span-full py-12 bg-zinc-950/20 border border-dashed border-white/5 rounded-[2.5rem] text-center text-zinc-600 text-xs font-bold uppercase">
                Nenhuma oportunidade de expansão detectada hoje
            </div>
            @endforelse
        </div>
    </div>

    <!-- Retention (High Risk) Section -->
    <div class="space-y-6">
        <h3 class="text-lg font-black text-red-500 flex items-center gap-3">
            <i class="fas fa-user-clock"></i> Protocolos de Retenção Crítica
        </h3>
        <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-white/5">
                        <th class="px-8 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Cliente</th>
                        <th class="px-8 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Último Acesso</th>
                        <th class="px-8 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Saúde</th>
                        <th class="px-8 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Ação Recomenda</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($retentionList as $user)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-8 py-6">
                            <span class="block text-sm font-black text-white">{{ $user->name }}</span>
                            <span class="text-[10px] text-zinc-600 font-bold uppercase">{{ $user->email }}</span>
                        </td>
                        <td class="px-8 py-6 text-xs text-zinc-400">
                            {{ $user->last_activity_at ? $user->last_activity_at->diffForHumans() : 'Nunca' }}
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs font-black text-red-500 uppercase italic">{{ $user->health_score }}% (Crítico)</span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <a href="mailto:{{ $user->email }}" class="inline-flex px-5 py-2.5 bg-red-600/10 text-red-500 text-[10px] font-black uppercase rounded-lg hover:bg-red-600 hover:text-white transition-all border border-red-500/20">
                                Enviar Retencional
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-10 text-center text-zinc-700 text-xs font-black uppercase">
                            Nenhum cliente em zona de churn extremo detectado
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
