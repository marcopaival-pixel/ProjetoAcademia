@extends('layouts.app')

@section('title', 'Central Financeira — NexShape')

@section('content')
<div class="py-12 space-y-10 animate-fade-in max-w-[1200px] mx-auto px-4 sm:px-6">
    <!-- Header Premium -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest shadow-[0_0_15px_rgba(16,185,129,0.15)]">
                <i class="fas fa-gem text-[10px]"></i>
                Central Financeira Premium
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tighter leading-none">Minha <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-teal-400">Assinatura</span></h1>
            <p class="text-zinc-400 text-sm md:text-base font-medium max-w-xl">Gerencie seu plano, pagamentos, faturas e acompanhe todo o seu histórico financeiro de forma centralizada.</p>
        </div>
        
        <div class="flex gap-4">
            <button class="px-6 py-3 bg-zinc-800/50 hover:bg-zinc-800 border border-white/5 rounded-2xl text-white text-xs font-bold transition-all flex items-center gap-2 shadow-lg backdrop-blur-md">
                <i class="fas fa-headset text-emerald-400"></i> Suporte Financeiro
            </button>
        </div>
    </div>

    <!-- Alertas/Notificações (Exemplo: Pagamento Pendente ou Renovação) -->
    @php
        $status = $subscription?->status ?? 'inactive';
        $isActive = $status === 'active' || str_contains(strtolower($status), 'ativo');
        $isPending = str_contains(strtolower($status), 'pendente');
    @endphp
    
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 p-5 rounded-2xl text-emerald-400 text-sm font-bold animate-slide-up flex items-center gap-3 shadow-lg shadow-emerald-500/5">
            <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-check"></i>
            </div>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error') || $isPending)
        <div class="bg-amber-500/10 border border-amber-500/20 p-5 rounded-2xl text-amber-400 text-sm font-bold animate-slide-up flex items-center gap-3 shadow-lg shadow-amber-500/5">
            <div class="w-8 h-8 rounded-full bg-amber-500/20 flex items-center justify-center shrink-0">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            {{ session('error') ?? 'Você possui uma fatura pendente. Evite a suspensão dos seus benefícios regularizando o pagamento.' }}
            @if($isPending)
                <button class="ml-auto px-4 py-2 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 rounded-xl text-[10px] uppercase tracking-widest font-black transition-all">
                    Pagar Agora
                </button>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Coluna Principal (Plano, Gráfico, Timeline) -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Dashboard Plano Atual (SaaS Style) -->
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/5 rounded-full blur-[100px] -mr-32 -mt-32 pointer-events-none transition-all group-hover:bg-emerald-500/10"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row gap-10 items-start justify-between">
                    <div class="space-y-6 flex-1">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Plano Atual</label>
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md {{ $isActive ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20' }} border text-[9px] font-black uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-emerald-400 animate-pulse' : 'bg-red-500' }}"></span>
                                    {{ $isActive ? 'Ativo' : ($isPending ? 'Pendente' : 'Inativo') }}
                                </div>
                            </div>
                            <h2 class="text-3xl md:text-5xl font-black text-white tracking-tight leading-none mb-4">{{ $subscription?->plan?->name ?? 'Nenhum Plano' }}</h2>
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black text-white tracking-tighter">R$ {{ number_format($subscription?->plan?->price ?? 0, 2, ',', '.') }}</span>
                                <span class="text-sm text-zinc-500 font-bold">/ mês</span>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-6 pt-6 border-t border-white/5">
                            <div class="space-y-1">
                                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest flex items-center gap-1.5">
                                    <i class="fas fa-calendar-check text-emerald-500/50"></i> Próxima Cobrança
                                </span>
                                <span class="text-white font-bold text-sm">{{ $subscription?->next_billing_date ? $subscription->next_billing_date->format('d/m/Y') : '--/--/----' }}</span>
                            </div>
                            <div class="space-y-1">
                                <span class="text-[9px] text-zinc-600 font-black uppercase tracking-widest flex items-center gap-1.5">
                                    <i class="fas fa-hourglass-half text-emerald-500/50"></i> Ciclo Atual
                                </span>
                                <span class="text-white font-bold text-sm">30 Dias</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mini Gráfico Financeiro (Visual SaaS) -->
                    <div class="w-full md:w-48 shrink-0 bg-black/40 rounded-3xl p-5 border border-white/5 space-y-4">
                        <div class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex justify-between items-center">
                            <span>Uso do Ciclo</span>
                            <i class="fas fa-chart-line text-emerald-500"></i>
                        </div>
                        <div class="flex items-end justify-between h-20 gap-1.5">
                            <div class="w-full bg-emerald-500/20 rounded-t-sm h-[30%] hover:bg-emerald-500/40 transition-colors relative group cursor-pointer">
                                <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-black text-white text-[8px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity">Sem 1</div>
                            </div>
                            <div class="w-full bg-emerald-500/20 rounded-t-sm h-[50%] hover:bg-emerald-500/40 transition-colors relative group cursor-pointer">
                                <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-black text-white text-[8px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity">Sem 2</div>
                            </div>
                            <div class="w-full bg-emerald-500 rounded-t-sm h-[80%] hover:bg-emerald-400 transition-colors shadow-[0_0_10px_rgba(16,185,129,0.3)] relative group cursor-pointer">
                                <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-emerald-500 text-black font-bold text-[8px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity">Atual</div>
                            </div>
                            <div class="w-full bg-white/5 rounded-t-sm h-[100%]"></div>
                        </div>
                        <div class="text-[9px] text-zinc-500 text-center font-bold">12 dias restantes</div>
                    </div>
                </div>
            </div>

            <!-- Alterar Plano -->
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-lg font-black text-white tracking-tight flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-emerald-400">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        Alterar Plano
                    </h3>
                </div>
                
                <form action="{{ route('patient.subscription.change-plan') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($allPlans as $plan)
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="plan_id" value="{{ $plan->id }}" class="peer sr-only" @if($subscription?->plan_id == $plan->id) checked @endif>
                                <div class="p-6 rounded-2xl bg-black/40 border border-white/5 peer-checked:border-emerald-500/50 peer-checked:bg-emerald-500/5 peer-checked:shadow-[0_0_20px_rgba(16,185,129,0.1)] transition-all flex flex-col gap-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-white font-black uppercase tracking-widest">{{ $plan->name }}</span>
                                        @if($subscription?->plan_id == $plan->id)
                                            <i class="fas fa-check-circle text-emerald-500"></i>
                                        @endif
                                    </div>
                                    <div class="text-2xl font-black text-emerald-400">R$ {{ number_format($plan->price, 2, ',', '.') }}</div>
                                    <p class="text-[10px] text-zinc-500 leading-relaxed border-t border-white/5 pt-3">
                                        @if($plan->price > ($subscription?->plan?->price ?? 0))
                                            Faça o upgrade e libere mais funcionalidades exclusivas para acelerar seus resultados.
                                        @else
                                            Acesso essencial à plataforma e funcionalidades básicas.
                                        @endif
                                    </p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="px-8 py-3.5 bg-white text-black font-black rounded-2xl hover:bg-zinc-200 transition-all uppercase tracking-widest text-[10px] shadow-lg">
                            Confirmar Novo Plano
                        </button>
                    </div>
                </form>
            </div>

            <!-- Timeline de Faturas / Histórico -->
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-lg font-black text-white tracking-tight flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-emerald-400">
                            <i class="fas fa-history"></i>
                        </div>
                        Timeline Financeira
                    </h3>
                    <button class="text-[10px] text-zinc-500 hover:text-white uppercase font-black tracking-widest transition-colors">
                        Ver Tudo
                    </button>
                </div>
                
                <div class="space-y-6 relative before:absolute before:inset-y-0 before:ml-6 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-white/10 before:to-transparent before:pointer-events-none">
                    
                    @forelse(($subscription && $subscription->logs && $subscription->logs->isNotEmpty()) ? $subscription->logs : collect([ (object)['created_at' => now(), 'event' => 'pagamento_realizado', 'amount' => $subscription?->plan?->price ?? 99.90, 'new_status' => 'ativo'] ]) as $index => $log)
                        <!-- Item Timeline -->
                        <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                            <!-- Ícone central -->
                            <div class="flex items-center justify-center w-12 h-12 rounded-full border-4 border-zinc-900 bg-zinc-800 text-emerald-400 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 transition-transform group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-black group-hover:border-emerald-500/30">
                                @if(str_contains($log->event, 'pagamento'))
                                    <i class="fas fa-receipt"></i>
                                @elseif(str_contains($log->event, 'cancelamento'))
                                    <i class="fas fa-times"></i>
                                @else
                                    <i class="fas fa-sync-alt"></i>
                                @endif
                            </div>
                            
                            <!-- Card Content -->
                            <div class="w-[calc(100%-4rem)] md:w-[calc(50%-3rem)] bg-black/40 border border-white/5 p-5 rounded-2xl shadow-lg transition-all group-hover:border-emerald-500/30 group-hover:-translate-y-1">
                                <div class="flex justify-between items-start mb-1">
                                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">{{ $log->created_at->format('d M, Y') }}</span>
                                    <span class="text-xs text-white font-bold px-2 py-1 bg-white/5 rounded-md">R$ {{ number_format($log->amount ?? 0, 2, ',', '.') }}</span>
                                </div>
                                <h4 class="text-white text-sm font-bold mb-3">{{ ucfirst(str_replace('_', ' ', $log->event)) }}</h4>
                                
                                <div class="flex items-center justify-between border-t border-white/5 pt-3">
                                    <div class="inline-flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full {{ str_contains(strtolower($log->new_status), 'ativo') ? 'bg-emerald-500' : 'bg-zinc-500' }}"></span>
                                        <span class="text-[9px] text-zinc-400 font-bold uppercase">{{ $log->new_status }}</span>
                                    </div>
                                    @if(str_contains($log->event, 'pagamento'))
                                        <button class="text-[9px] text-emerald-400 hover:text-emerald-300 font-black uppercase tracking-widest transition-colors flex items-center gap-1">
                                            <i class="fas fa-download"></i> Comprovante
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-zinc-500 text-sm font-medium">Nenhum evento financeiro registrado.</div>
                    @endforelse

                </div>
            </div>
        </div>

        <!-- Coluna Lateral (Métodos de Pagamento, PIX, Segurança) -->
        <div class="space-y-8">
            
            <!-- Widget de Cartão Cadastrado Premium -->
            <div class="bg-gradient-to-br from-zinc-800 to-black p-8 rounded-[2.5rem] border border-white/10 shadow-2xl relative overflow-hidden group hover:shadow-[0_20px_50px_rgba(0,0,0,0.5)] transition-all duration-500">
                <div class="absolute top-0 right-0 p-6 opacity-10 group-hover:opacity-20 transition-opacity">
                    <i class="fab fa-cc-{{ strtolower($subscription?->card_brand ?? 'visa') }} text-8xl"></i>
                </div>
                
                <h4 class="text-[10px] text-zinc-400 font-black uppercase tracking-widest mb-6 flex items-center gap-2 relative z-10">
                    <i class="fas fa-credit-card"></i> Cartão Principal
                </h4>

                <div class="relative z-10 space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-9 bg-gradient-to-tr from-amber-200 to-amber-500 rounded-md shadow-inner flex items-center justify-center opacity-90">
                            <i class="fas fa-microchip text-black/40 text-xl"></i>
                        </div>
                        <i class="fas fa-wifi text-xl text-white/50 rotate-90"></i>
                    </div>

                    <div>
                        <div class="text-zinc-500 text-[9px] font-black uppercase tracking-[0.2em] mb-1">Número do Cartão</div>
                        <div class="text-white text-xl font-mono tracking-widest flex gap-4 drop-shadow-md">
                            <span>••••</span> <span>••••</span> <span>••••</span> <span class="text-emerald-400 font-bold">{{ $subscription?->card_last_four ?? '0000' }}</span>
                        </div>
                    </div>

                    <div class="flex items-end justify-between pt-2">
                        <div class="space-y-1">
                            <div class="text-zinc-500 text-[8px] font-black uppercase tracking-widest leading-none">Titular do Cartão</div>
                            <div class="text-white text-xs font-bold uppercase tracking-tight truncate max-w-[150px] drop-shadow-md">{{ auth()->user()->name }}</div>
                        </div>
                        <div class="space-y-1 text-right">
                            <div class="text-zinc-500 text-[8px] font-black uppercase tracking-widest leading-none">Validade</div>
                            <div class="text-white text-xs font-bold drop-shadow-md">{{ $subscription?->card_expiry ?? '--/--' }}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-white/10 relative z-10 flex gap-3">
                    <button onclick="showCardModal()" class="flex-1 py-3 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-colors backdrop-blur-md">
                        Trocar Cartão
                    </button>
                    @if($subscription?->card_last_four)
                        <button class="px-4 py-3 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-xl transition-colors">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Widget PIX Copia e Cola -->
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-8 shadow-2xl">
                <h4 class="text-[10px] text-zinc-400 font-black uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i class="fab fa-pix text-emerald-400"></i> Pagamento via PIX
                </h4>
                
                <div class="bg-black/40 rounded-2xl p-6 flex flex-col items-center justify-center text-center border border-white/5 mb-6 group hover:border-emerald-500/30 transition-colors">
                    <div class="w-24 h-24 bg-white p-2 rounded-xl mb-4 group-hover:scale-105 transition-transform shadow-[0_0_20px_rgba(16,185,129,0.1)]">
                        <!-- Mock QRCode image -->
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=MockPixCode" alt="QR Code PIX" class="w-full h-full opacity-90">
                    </div>
                    <p class="text-xs text-zinc-400 mb-2">Escaneie o QR Code ou copie o código abaixo para renovar sua assinatura.</p>
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] text-zinc-500 font-black uppercase tracking-widest ml-1">PIX Copia e Cola</label>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly value="00020126580014br.gov.bcb.pix0136mock-pix-code-1234-5678-90ab-cdef0000" class="flex-1 bg-zinc-800 border border-white/10 rounded-xl px-4 py-3 text-xs text-zinc-400 font-mono focus:outline-none truncate">
                        <button class="w-11 h-11 shrink-0 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-xl flex items-center justify-center transition-colors" title="Copiar PIX" onclick="navigator.clipboard.writeText('00020126580014br.gov.bcb.pix0136mock-pix-code-1234-5678-90ab-cdef0000'); alert('Código PIX copiado!')">
                            <i class="far fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Alterar Modo de Pagamento Padrão -->
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-8 shadow-2xl">
                <h4 class="text-[10px] text-zinc-400 font-black uppercase tracking-widest mb-6">Método de Faturamento Padrão</h4>
                
                <form action="{{ route('patient.subscription.update-payment') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="space-y-3">
                        <label class="relative flex items-center justify-between p-4 rounded-xl border border-white/5 cursor-pointer hover:bg-white/5 transition-colors group has-[:checked]:bg-emerald-500/5 has-[:checked]:border-emerald-500/50">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-credit-card text-zinc-500 group-has-[:checked]:text-emerald-400"></i>
                                <span class="text-xs text-white font-bold">Cartão de Crédito</span>
                            </div>
                            <input type="radio" name="method" value="card" class="text-emerald-500 focus:ring-emerald-500 bg-zinc-800 border-zinc-700" @if(($subscription?->payment_method ?? 'card') === 'card') checked @endif>
                        </label>
                        
                        <label class="relative flex items-center justify-between p-4 rounded-xl border border-white/5 cursor-pointer hover:bg-white/5 transition-colors group has-[:checked]:bg-emerald-500/5 has-[:checked]:border-emerald-500/50">
                            <div class="flex items-center gap-3">
                                <i class="fab fa-pix text-zinc-500 group-has-[:checked]:text-emerald-400"></i>
                                <span class="text-xs text-white font-bold">PIX (Mensal)</span>
                            </div>
                            <input type="radio" name="method" value="pix" class="text-emerald-500 focus:ring-emerald-500 bg-zinc-800 border-zinc-700" @if(($subscription?->payment_method ?? '') === 'pix') checked @endif>
                        </label>

                        <label class="relative flex items-center justify-between p-4 rounded-xl border border-white/5 cursor-pointer hover:bg-white/5 transition-colors group has-[:checked]:bg-emerald-500/5 has-[:checked]:border-emerald-500/50">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-barcode text-zinc-500 group-has-[:checked]:text-emerald-400"></i>
                                <span class="text-xs text-white font-bold">Boleto Bancário</span>
                            </div>
                            <input type="radio" name="method" value="boleto" class="text-emerald-500 focus:ring-emerald-500 bg-zinc-800 border-zinc-700" @if(($subscription?->payment_method ?? '') === 'boleto') checked @endif>
                        </label>
                    </div>

                    <button type="submit" class="w-full mt-4 py-3.5 bg-zinc-800 hover:bg-zinc-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-colors border border-white/5">
                        Salvar Preferência
                    </button>
                </form>
            </div>

            <!-- Área de Risco / Cancelamento -->
            <div class="bg-red-500/5 border border-red-500/10 rounded-[2rem] p-8 text-center space-y-4 hover:bg-red-500/10 transition-colors">
                <i class="fas fa-heart-broken text-2xl text-red-500/50 mb-2"></i>
                <h4 class="text-white font-black text-sm uppercase tracking-tight">Pensando em nos deixar?</h4>
                <p class="text-zinc-400 text-xs leading-relaxed max-w-[250px] mx-auto">
                    Ao cancelar, você perde acesso aos seus treinos, inteligência artificial e evolução.
                </p>
                <button type="button" onclick="showCancelModal()" class="mt-4 px-6 py-2.5 text-red-400 hover:text-white hover:bg-red-500 border border-red-500/20 hover:border-red-500 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                    Interromper Assinatura
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Modal de Cancelamento Premium -->
<div id="cancelModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-6 sm:p-0">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="hideCancelModal()"></div>
    
    <div class="relative w-full max-w-lg bg-zinc-900 border border-white/10 rounded-[2.5rem] shadow-2xl overflow-hidden animate-slide-up">
        <div class="p-10 space-y-8">
            <div class="text-center space-y-4">
                <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6 shadow-[0_0_30px_rgba(239,68,68,0.2)]">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                </div>
                <h3 class="text-2xl font-black text-white tracking-tight">Tem certeza de que deseja cancelar?</h3>
                <p class="text-zinc-500 text-sm">Sentiremos sua falta! Você perderá acesso a diversos benefícios exclusivos:</p>
            </div>

            <div class="bg-black/30 rounded-3xl p-6 space-y-4 border border-white/5">
                <div class="flex items-center gap-3 text-zinc-400 text-xs font-medium">
                    <i class="fas fa-check-circle text-red-500"></i> Relatórios NexIntelligence AI bloqueados
                </div>
                <div class="flex items-center gap-3 text-zinc-400 text-xs font-medium">
                    <i class="fas fa-check-circle text-red-500"></i> Planos de Treino suspensos
                </div>
                <div class="flex items-center gap-3 text-zinc-400 text-xs font-medium">
                    <i class="fas fa-check-circle text-red-500"></i> Histórico de evolução congelado
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button type="button" onclick="hideCancelModal()" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] shadow-xl shadow-emerald-900/20">
                    Manter Assinatura
                </button>
                <form action="{{ route('patient.subscription.cancel') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-8 py-4 bg-zinc-800 hover:bg-red-600 text-white font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] border border-white/5">
                        Confirmar Cancelamento
                    </button>
                </form>
            </div>
            
            <p class="text-center text-[10px] text-zinc-600 font-medium">
                Seu acesso permanecerá ativo até o final do período vigente: <span class="text-white font-bold">{{ $subscription?->end_date ? $subscription->end_date->format('d/m/Y') : 'próximo vencimento' }}</span>.
            </p>
        </div>
    </div>
</div>

<!-- Modal de Cadastro de Cartão -->
<div id="cardModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-6 sm:p-0">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md" onclick="hideCardModal()"></div>
    
    <div class="relative w-full max-w-lg bg-zinc-900 border border-white/10 rounded-[2.5rem] shadow-2xl overflow-hidden animate-slide-up">
        <form action="{{ route('patient.subscription.update-payment') }}" method="POST" class="p-10 space-y-8">
            @csrf
            <input type="hidden" name="method" value="card">
            
            <div class="text-center space-y-2 mb-8">
                <div class="w-16 h-16 bg-emerald-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-credit-card text-2xl text-emerald-400"></i>
                </div>
                <h3 class="text-2xl font-black text-white tracking-tight">Atualizar Cartão</h3>
                <p class="text-zinc-500 text-sm">Insira os dados do seu novo cartão. O processamento é 100% seguro e criptografado.</p>
            </div>

            <div class="space-y-5 bg-black/20 p-6 rounded-3xl border border-white/5">
                <div class="space-y-2">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Número do Cartão</label>
                    <div class="relative">
                        <i class="fas fa-credit-card absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500"></i>
                        <input type="text" name="card_number" id="modal_card_number" required placeholder="0000 0000 0000 0000" maxlength="19"
                            class="w-full bg-zinc-900 border border-white/10 rounded-xl pl-12 pr-4 py-3.5 text-sm font-bold text-white focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 outline-none transition-all font-mono tracking-widest">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Validade</label>
                        <input type="text" name="card_expiry" id="modal_card_expiry" required placeholder="MM/AA" maxlength="5"
                            class="w-full bg-zinc-900 border border-white/10 rounded-xl px-4 py-3.5 text-sm font-bold text-white focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 outline-none transition-all text-center">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">CVV</label>
                        <div class="relative">
                            <input type="text" name="card_cvv" required placeholder="•••" maxlength="4"
                                class="w-full bg-zinc-900 border border-white/10 rounded-xl px-4 py-3.5 text-sm font-bold text-white focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/50 outline-none transition-all text-center">
                            <i class="fas fa-info-circle absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600" title="Código de 3 dígitos no verso do cartão"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 text-[10px] font-medium text-zinc-500 justify-center">
                <i class="fas fa-lock text-emerald-500"></i> SSL 256-bit Encryption
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5">
                <button type="button" onclick="hideCardModal()" class="px-8 py-4 bg-transparent hover:bg-white/5 text-white font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] border border-white/10">
                    Cancelar
                </button>
                <button type="submit" class="px-8 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] shadow-xl shadow-emerald-900/20">
                    Salvar Cartão
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showCancelModal() {
        const modal = document.getElementById('cancelModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function hideCancelModal() {
        const modal = document.getElementById('cancelModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function showCardModal() {
        const modal = document.getElementById('cardModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function hideCardModal() {
        const modal = document.getElementById('cardModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Máscaras
    const modalNumber = document.getElementById('modal_card_number');
    const modalExpiry = document.getElementById('modal_card_expiry');

    if(modalNumber) {
        modalNumber.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            let formatted = value.match(/.{1,4}/g)?.join(' ') || '';
            e.target.value = formatted;
        });
    }

    if(modalExpiry) {
        modalExpiry.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
    }
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
    .animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    
    /* Scrollbar style para telas pequenas se necessário */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #3f3f46; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #52525b; }
</style>
@endsection
