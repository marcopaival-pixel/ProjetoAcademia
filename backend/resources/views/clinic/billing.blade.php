@extends('layouts.app')

@section('title', 'Faturamento e Planos')

@section('style')
<style>
    .glass-card {
        background: rgba(20, 22, 28, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] text-white pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <div class="mb-10 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black tracking-tighter italic uppercase">Faturamento Consolidado</h1>
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-widest">Planos e Assinaturas da Unidade</p>
            </div>
            <div class="px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-500 text-[10px] font-black uppercase">
                Status: {{ $subscription ? 'Ativo' : 'Nenhuma Assinatura Corporativa' }}
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Active Plan -->
            <div class="lg:col-span-2 space-y-8">
                <div class="glass-card rounded-[2.5rem] p-8">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-8 flex items-center gap-2">
                        <i class="fas fa-crown text-amber-500"></i> Plano Atual
                    </h3>

                    @if($subscription)
                        <div class="flex items-center justify-between p-6 bg-zinc-900 rounded-3xl mb-8">
                            <div>
                                <h4 class="text-xl font-black italic">{{ $subscription->plan->name }}</h4>
                                <p class="text-[10px] text-zinc-500 uppercase font-black">Cobrança Consolidada por Profissional</p>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black">R$ {{ number_format($subscription->plan->price + ($subscription->plan->price_per_professional * max(0, $teamCount - $subscription->plan->min_professionals)), 2, ',', '.') }}</p>
                                <p class="text-[9px] text-zinc-500 uppercase font-black">Total Mensal Próximo Ciclo</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                                <p class="text-[9px] text-zinc-500 uppercase font-black mb-1">Profissionais</p>
                                <p class="text-sm font-bold">{{ $teamCount }} / {{ $subscription->max_professionals ?? '∞' }}</p>
                            </div>
                            <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                                <p class="text-[9px] text-zinc-500 uppercase font-black mb-1">Método</p>
                                <p class="text-sm font-bold">{{ $subscription->card_brand }} •••• {{ $subscription->card_last_four }}</p>
                            </div>
                            <div class="p-4 bg-white/5 rounded-2xl border border-white/5">
                                <p class="text-[9px] text-zinc-500 uppercase font-black mb-1">Vencimento</p>
                                <p class="text-sm font-bold">{{ $subscription->next_billing_date ? $subscription->next_billing_date->format('d/m/Y') : '-' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 px-8 bg-zinc-900/50 rounded-3xl border border-dashed border-zinc-800">
                            <i class="fas fa-exclamation-triangle text-amber-500 text-3xl mb-4"></i>
                            <h4 class="text-sm font-black uppercase mb-2">Sem Assinatura Ativa</h4>
                            <p class="text-[10px] text-zinc-500 uppercase font-bold max-w-sm mx-auto mb-6">Sua clínica ainda não possui um plano corporativo. Os profissionais estão pagando individualmente ou usando o plano free.</p>
                            <a href="#plans" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-[10px] font-black uppercase transition-all">Ver Planos B2B</a>
                        </div>
                    @endif
                </div>

                <!-- Plan Selection -->
                <div id="plans" class="space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-shopping-cart text-blue-500"></i> Planos Corporativos Disponíveis
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($plans as $plan)
                        <div class="glass-card rounded-[2.5rem] p-8 border-2 {{ ($subscription && $subscription->plan_id == $plan->id) ? 'border-blue-600' : 'border-transparent' }} flex flex-col justify-between">
                            <div>
                                <h4 class="text-xl font-black italic mb-2">{{ $plan->name }}</h4>
                                <div class="flex items-baseline gap-1 mb-6">
                                    <span class="text-2xl font-black">R$ {{ number_format($plan->price, 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-zinc-500 font-bold uppercase">Base / Mês</span>
                                </div>
                                
                                <ul class="space-y-3 mb-8">
                                    <li class="flex items-center gap-2 text-[10px] font-black uppercase text-zinc-400">
                                        <i class="fas fa-check text-blue-500"></i> + R$ {{ number_format($plan->price_per_professional, 0, ',', '.') }} por profissional extra
                                    </li>
                                    <li class="flex items-center gap-2 text-[10px] font-black uppercase text-zinc-400">
                                        <i class="fas fa-check text-blue-500"></i> Branding da Clínica Full
                                    </li>
                                    <li class="flex items-center gap-2 text-[10px] font-black uppercase text-zinc-400">
                                        <i class="fas fa-check text-blue-500"></i> Agenda Multi-Profissional
                                    </li>
                                </ul>
                            </div>
                            
                            <button class="w-full py-4 rounded-2xl text-[10px] font-black uppercase transition-all {{ ($subscription && $subscription->plan_id == $plan->id) ? 'bg-zinc-800 text-zinc-500 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-500 text-white' }}">
                                {{ ($subscription && $subscription->plan_id == $plan->id) ? 'Plano Atual' : 'Selecionar Plano' }}
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="glass-card rounded-[2.5rem] p-8">
                    <h3 class="text-sm font-black uppercase tracking-widest mb-4">Vantagens B2B</h3>
                    <p class="text-[11px] text-zinc-400 leading-relaxed font-bold uppercase mb-6">Ao centralizar o faturamento, sua clínica garante o controle total sobre quem acessa o sistema e unifica a experiência do paciente.</p>
                    <div class="space-y-4">
                        <div class="p-4 bg-zinc-900 rounded-2xl">
                            <p class="text-[9px] text-zinc-500 uppercase font-black mb-1">Próxima Fatura Estimada</p>
                            <p class="text-sm font-bold">R$ {{ $subscription ? number_format($subscription->plan->price + ($subscription->plan->price_per_professional * max(0, $teamCount - $subscription->plan->min_professionals)), 2, ',', '.') : '0,00' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
