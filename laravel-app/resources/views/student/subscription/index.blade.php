@extends('layouts.app')

@section('title', 'Financeiro & Assinatura — NexShape')

@section('content')
<div class="py-12 space-y-12 animate-fade-in max-w-[1200px] mx-auto px-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest">
                <i class="fas fa-wallet text-[8px]"></i>
                Gestão Financeira
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter leading-none">Minha <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-teal-400">Assinatura</span></h1>
            <p class="text-zinc-500 text-lg font-medium">Gerencie seu plano, pagamentos e histórico de cobranças.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 p-4 rounded-2xl text-emerald-400 text-sm font-bold animate-slide-up">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 p-4 rounded-2xl text-red-400 text-sm font-bold animate-slide-up">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Detalhes do Plano -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-10 shadow-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
                
                <div class="relative z-10 space-y-8">
                    <div class="flex flex-wrap items-start justify-between gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Plano Atual</label>
                            <h2 class="text-4xl font-black text-white tracking-tight">{{ $subscription?->plan?->name ?? 'N/A' }}</h2>
                            @php
                                $status = $subscription?->status ?? 'inactive';
                                $isActive = $status === 'active' || str_contains(strtolower($status), 'ativo');
                            @endphp
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg {{ $isActive ? 'bg-emerald-500/20 text-emerald-400' : 'bg-zinc-500/20 text-zinc-500' }} text-[10px] font-bold uppercase tracking-wider mt-2">
                                <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-emerald-400 animate-pulse' : 'bg-zinc-600' }}"></span>
                                {{ ucfirst($status) }}
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest block mb-1">Valor da Assinatura</label>
                            <div class="text-3xl font-black text-white uppercase tracking-tighter">R$ {{ number_format($subscription?->plan?->price ?? 0, 2, ',', '.') }}<span class="text-sm text-zinc-500 font-bold ml-1">/ mês</span></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-8 border-t border-white/5">
                        <div class="space-y-1">
                            <span class="text-[10px] text-zinc-600 font-black uppercase tracking-widest block">Próxima Cobrança</span>
                            <span class="text-white font-bold">{{ $subscription?->next_billing_date ? $subscription->next_billing_date->format('d/m/Y') : 'N/A' }}</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] text-zinc-600 font-black uppercase tracking-widest block">Início da Assinatura</span>
                            <span class="text-white font-bold">{{ $subscription?->start_date ? $subscription->start_date->format('d/m/Y') : 'N/A' }}</span>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] text-zinc-600 font-black uppercase tracking-widest block">Modo de Pagamento</span>
                            <span class="text-white font-bold flex items-center gap-2">
                                @if(($subscription?->payment_method ?? 'N/A') === 'card')
                                    <i class="fas fa-credit-card text-emerald-400"></i> Cartão de Crédito
                                @elseif(($subscription?->payment_method ?? 'N/A') === 'pix')
                                    <i class="fab fa-pix text-emerald-400"></i> PIX
                                @elseif(($subscription?->payment_method ?? 'N/A') === 'boleto')
                                    <i class="fas fa-barcode text-emerald-400"></i> Boleto
                                @else
                                    <i class="fas fa-slash text-zinc-600"></i> {{ $subscription?->payment_method ?? 'N/A' }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alterar Pagamento -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-10 shadow-2xl">
                <h3 class="text-xl font-black text-white tracking-tight mb-8">Alterar Forma de Pagamento</h3>
                
                <form action="{{ route('patient.subscription.update-payment') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="method" value="card" class="peer sr-only" @if(($subscription?->payment_method ?? '') === 'card') checked @endif>
                            <div class="p-6 rounded-2xl bg-black/40 border border-white/5 peer-checked:border-emerald-500/50 peer-checked:bg-emerald-500/5 transition-all flex flex-col items-center gap-3">
                                <i class="fas fa-credit-card text-2xl text-zinc-600 group-hover:text-emerald-400 transition-colors"></i>
                                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Cartão</span>
                            </div>
                        </label>
                        
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="method" value="pix" class="peer sr-only" @if(($subscription?->payment_method ?? '') === 'pix') checked @endif>
                            <div class="p-6 rounded-2xl bg-black/40 border border-white/5 peer-checked:border-emerald-500/50 peer-checked:bg-emerald-500/5 transition-all flex flex-col items-center gap-3">
                                <i class="fas fa-qrcode text-2xl text-zinc-600 group-hover:text-emerald-400 transition-colors"></i>
                                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">PIX</span>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group">
                            <input type="radio" name="method" value="boleto" class="peer sr-only" @if(($subscription?->payment_method ?? '') === 'boleto') checked @endif>
                            <div class="p-6 rounded-2xl bg-black/40 border border-white/5 peer-checked:border-emerald-500/50 peer-checked:bg-emerald-500/5 transition-all flex flex-col items-center gap-3">
                                <i class="fas fa-barcode text-2xl text-zinc-600 group-hover:text-emerald-400 transition-colors"></i>
                                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Boleto</span>
                            </div>
                        </label>
                    </div>

                    <div id="card_info_fields" class="@if(($subscription?->payment_method ?? '') !== 'card') hidden @endif pt-6 space-y-4 animate-fade-in">
                        <p class="text-zinc-500 text-xs italic">A alteração de cartão requer a vinculação de um novo cartão seguro.</p>
                        <input type="hidden" name="card_token" value="MOCK_TOKEN_HOLDER">
                        <button type="button" onclick="showCardModal()" class="w-full py-4 bg-zinc-800 border border-white/10 rounded-2xl text-white font-bold text-sm hover:bg-zinc-700 transition-all flex items-center justify-center gap-3">
                            <i class="fas fa-plus-circle"></i> Cadastrar Novo Cartão
                        </button>
                    </div>

                    <div class="pt-6 border-t border-white/5 flex justify-end">
                        <button type="submit" class="px-8 py-4 bg-emerald-600 text-white font-black rounded-2xl hover:bg-emerald-500 transition-all shadow-xl shadow-emerald-900/20 uppercase tracking-widest text-[10px]">
                            Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>

            <!-- Alterar Plano -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-10 shadow-2xl">
                <h3 class="text-xl font-black text-white tracking-tight mb-8">Gerenciar Plano</h3>
                
                <form action="{{ route('patient.subscription.change-plan') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($allPlans as $plan)
                            <label class="relative cursor-pointer group">
                                <input type="radio" name="plan_id" value="{{ $plan->id }}" class="peer sr-only" @if($subscription?->plan_id == $plan->id) checked @endif>
                                <div class="p-6 rounded-2xl bg-black/40 border border-white/5 peer-checked:border-emerald-500/50 peer-checked:bg-emerald-500/5 transition-all flex flex-col items-center gap-3">
                                    <div class="flex items-center justify-between w-full">
                                        <span class="text-xs text-white font-black uppercase tracking-widest">{{ $plan->name }}</span>
                                        <span class="text-emerald-400 font-bold">R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                                    </div>
                                    <p class="text-[9px] text-zinc-500 text-center">Acesse todos os módulos @if($plan->price > $subscription?->plan?->price) (Upgrade) @else (Downgrade) @endif</p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="pt-6 border-t border-white/5 flex justify-end">
                        <button type="submit" class="px-8 py-4 bg-zinc-800 text-white font-black rounded-2xl hover:bg-zinc-700 transition-all border border-white/5 uppercase tracking-widest text-[10px]">
                            Confirmar Novo Plano
                        </button>
                    </div>
                </form>
            </div>

            <!-- Histórico -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-10 shadow-2xl">
                <h3 class="text-xl font-black text-white tracking-tight mb-8">Histórico da Assinatura</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="border-b border-white/5">
                                <th class="pb-4 text-[10px] text-zinc-500 font-black uppercase tracking-widest">Data</th>
                                <th class="pb-4 text-[10px] text-zinc-500 font-black uppercase tracking-widest">Evento</th>
                                <th class="pb-4 text-[10px] text-zinc-500 font-black uppercase tracking-widest text-right">Status Final</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($subscription?->logs ?? [] as $log)
                                <tr>
                                    <td class="py-4 text-xs text-white font-medium">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-4">
                                        <span class="text-xs text-zinc-400 font-bold">{{ strtoupper(str_replace('_', ' ', $log->event)) }}</span>
                                        @if($log->amount > 0)
                                            <span class="ml-2 px-2 py-0.5 bg-emerald-500/10 text-emerald-400 rounded text-[10px]">R$ {{ number_format($log->amount, 2, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 text-right">
                                        <span class="text-[10px] font-black uppercase text-emerald-400">{{ $log->new_status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-10 text-center text-zinc-600 text-xs italic">Nenhuma movimentação registrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cartão Cadastrado & Widgets -->
        <div class="space-y-8">
            <!-- Glassmorphic Card Widget -->
            <div class="bg-gradient-to-br from-zinc-800 to-black p-6 rounded-[1.5rem] border border-white/10 shadow-2xl relative overflow-hidden group hover:scale-[1.02] transition-transform duration-500 max-w-[400px] aspect-[1.58/1] flex flex-col justify-between mx-auto md:mx-0">
                <div class="absolute top-0 right-0 p-4 opacity-20">
                    <i class="fas fa-wifi text-2xl text-emerald-400 rotate-90"></i>
                </div>
                
                <div class="relative z-10 flex flex-col justify-between h-full">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-8 bg-gradient-to-tr from-amber-400 to-amber-200 rounded shadow-inner"></div>
                        <span class="text-white font-black italic tracking-tighter text-lg uppercase">{{ $subscription?->card_brand ?? 'NexShape' }}</span>
                    </div>

                    <div class="space-y-1">
                        <div class="text-zinc-400 text-[8px] font-black uppercase tracking-[0.2em] opacity-50">Número do Cartão</div>
                        <div class="text-white text-lg font-mono tracking-widest flex gap-3">
                            <span>••••</span> <span>••••</span> <span>••••</span> <span class="text-emerald-400 font-bold">{{ $subscription?->card_last_four ?? '0000' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-white/5">
                        <div class="space-y-0.5">
                            <div class="text-zinc-600 text-[7px] font-black uppercase tracking-widest leading-none">Titular</div>
                            <div class="text-white text-[10px] font-bold uppercase tracking-tight truncate max-w-[150px]">{{ auth()->user()->name }}</div>
                        </div>
                        <div class="space-y-0.5 text-right">
                            <div class="text-zinc-600 text-[7px] font-black uppercase tracking-widest leading-none">Validade</div>
                            <div class="text-white text-[10px] font-bold">{{ $subscription?->card_expiry ?? '--/--' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancelamento -->
            <div class="bg-red-500/5 border border-red-500/10 rounded-[2rem] p-8 space-y-4">
                <h4 class="text-red-400 font-black text-sm uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-times-circle"></i>
                    Deseja cancelar?
                </h4>
                <p class="text-zinc-500 text-[10px] leading-relaxed">
                    Ao cancelar, você manterá acesso a todas as funcionalidades premium até o fim do seu período de faturamento atual.
                </p>
                <button type="button" onclick="showCancelModal()" class="w-full py-3 text-red-500/50 hover:text-red-500 text-[10px] font-black uppercase tracking-[0.2em] transition-all">
                    Interromper Assinatura
                </button>
            </div>

            <!-- Info Box -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2rem] p-8 space-y-6">
                <h4 class="text-white font-black text-sm uppercase tracking-widest flex items-center gap-2">
                    <i class="fas fa-shield-alt text-emerald-500"></i>
                    Pagamento Seguro
                </h4>
                <p class="text-zinc-500 text-xs leading-relaxed">
                    Seus dados de pagamento são processados através de criptografia de ponta a ponta. Nunca armazenamos o número completo do seu cartão em nossos servidores.
                </p>
                <div class="pt-4 space-y-3">
                    <div class="flex items-center gap-3 text-zinc-400 text-[10px] font-bold">
                        <i class="fas fa-lock text-[8px]"></i> SSL 256 bits
                    </div>
                    <div class="flex items-center gap-3 text-zinc-400 text-[10px] font-bold">
                        <i class="fas fa-check-double text-[8px]"></i> PCI DSS Compliant
                    </div>
                </div>
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
                <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-exclamation-triangle text-3xl text-red-500"></i>
                </div>
                <h3 class="text-2xl font-black text-white tracking-tight">Tem certeza de que deseja cancelar?</h3>
                <p class="text-zinc-500 text-sm">Sentiremos sua falta! Você perderá acesso a diversos benefícios exclusivos:</p>
            </div>

            <div class="bg-black/20 rounded-3xl p-6 space-y-4">
                <div class="flex items-center gap-3 text-zinc-400 text-xs">
                    <i class="fas fa-check text-emerald-500"></i> Relatórios NexIntelligence AI
                </div>
                <div class="flex items-center gap-3 text-zinc-400 text-xs">
                    <i class="fas fa-check text-emerald-500"></i> Planos de Treino Personalizados
                </div>
                <div class="flex items-center gap-3 text-zinc-400 text-xs">
                    <i class="fas fa-check text-emerald-500"></i> Acompanhamento Nutricional Premium
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button type="button" onclick="hideCancelModal()" class="px-8 py-5 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] shadow-xl shadow-emerald-900/20">
                    Manter Assinatura
                </button>
                <form action="{{ route('patient.subscription.cancel') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-8 py-5 bg-zinc-800 hover:bg-red-600 text-white font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] border border-white/5">
                        Confirmar Cancelamento
                    </button>
                </form>
            </div>
            
            <p class="text-center text-[10px] text-zinc-600">
                Seu acesso permanecerá ativo até o final do período vigente: <span class="text-white">{{ $subscription?->end_date ? $subscription->end_date->format('d/m/Y') : 'próximo vencimento' }}</span>.
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
            
            <div class="text-center space-y-2">
                <h3 class="text-2xl font-black text-white tracking-tight">Novo Cartão de Crédito</h3>
                <p class="text-zinc-500 text-sm text-balance">Insira os dados do seu novo cartão com segurança.</p>
            </div>

            <div class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Número do Cartão</label>
                    <input type="text" name="card_number" id="modal_card_number" required placeholder="0000 0000 0000 0000" maxlength="19"
                        class="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:border-emerald-500/50 outline-none transition-all font-mono tracking-widest">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">Validade</label>
                        <input type="text" name="card_expiry" id="modal_card_expiry" required placeholder="MM/AA" maxlength="5"
                            class="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:border-emerald-500/50 outline-none transition-all text-center">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-2">CVV</label>
                        <input type="text" name="card_cvv" required placeholder="•••" maxlength="4"
                            class="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:border-emerald-500/50 outline-none transition-all text-center">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-4">
                <button type="button" onclick="hideCardModal()" class="px-8 py-4 bg-zinc-800 hover:bg-zinc-700 text-white font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] border border-white/5">
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

    // Máscaras para o modal
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

    document.querySelectorAll('input[name="method"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const cardFields = document.getElementById('card_info_fields');
            if(e.target.value === 'card') {
                cardFields.classList.remove('hidden');
            } else {
                cardFields.classList.add('hidden');
            }
        });
    });
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    .animate-slide-up { animation: slideUp 0.5s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
