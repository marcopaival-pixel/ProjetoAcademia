@extends('layouts.app')

@section('title', 'Finalizar Assinatura — NexShape')

@section('content')
<div class="py-12 md:py-24 px-6 max-w-6xl mx-auto animate-fade-in">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
        
        <!-- Coluna Esquerda: Resumo e Confiança -->
        <div class="space-y-12">
            <div class="space-y-6">
                <a href="{{ route('patient.subscription.plans') }}" class="inline-flex items-center gap-2 text-zinc-500 hover:text-emerald-400 transition-colors text-xs font-black uppercase tracking-widest group">
                    <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                    Voltar aos planos
                </a>
                <h1 class="text-4xl md:text-5xl font-black text-white tracking-tighter leading-none">
                    Finalize sua <span class="text-emerald-400">Inscrição</span>
                </h1>
                <p class="text-zinc-500 text-lg">Você está a um passo de acessar a elite da tecnologia fitness.</p>
            </div>

            <!-- Card Resumo do Plano -->
            <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] p-8 space-y-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-emerald-500/5 rounded-full blur-2xl -mr-16 -mt-16"></div>
                
                <div class="flex items-start justify-between relative z-10">
                    <div class="space-y-1">
                        <label class="text-[10px] text-emerald-500/50 font-black uppercase tracking-widest">Plano Selecionado</label>
                        <h3 class="text-2xl font-black text-white">{{ $plan->name }}</h3>
                    </div>
                    <div class="text-right">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Total</label>
                        <div class="text-2xl font-black text-white">R$ {{ number_format($plan->price, 2, ',', '.') }}<span class="text-xs text-zinc-500 ml-1">/mês</span></div>
                    </div>
                </div>

                <div class="space-y-4 pt-6 border-t border-white/5 relative z-10">
                    <div class="flex items-center gap-3 text-sm text-zinc-400">
                        <i class="fas fa-check text-emerald-500"></i>
                        Acesso total aos módulos AI
                    </div>
                    <div class="flex items-center gap-3 text-sm text-zinc-400">
                        <i class="fas fa-check text-emerald-500"></i>
                        Suporte prioritário via WhatsApp
                    </div>
                    <div class="flex items-center gap-3 text-sm text-zinc-400">
                        <i class="fas fa-check text-emerald-500"></i>
                        Cancelamento grátis a qualquer momento
                    </div>
                </div>

                <!-- Cupom de Desconto -->
                <div class="pt-6 relative z-10">
                    <div class="flex gap-2">
                        <input type="text" placeholder="CUPOM" class="flex-1 bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-xs font-bold text-white uppercase tracking-widest focus:border-emerald-500/50 outline-none transition-all placeholder:text-zinc-700">
                        <button class="px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-white font-black rounded-xl text-[10px] uppercase tracking-widest transition-all">Aplicar</button>
                    </div>
                </div>
            </div>

            <!-- Trust Badges -->
            <div class="grid grid-cols-2 gap-4">
                <div class="p-6 bg-emerald-500/5 border border-emerald-500/10 rounded-3xl flex flex-col items-center gap-3 text-center">
                    <i class="fas fa-shield-alt text-2xl text-emerald-500"></i>
                    <span class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Pagamento Criptografado</span>
                </div>
                <div class="p-6 bg-zinc-900/40 border border-white/5 rounded-3xl flex flex-col items-center gap-3 text-center">
                    <i class="fas fa-lock text-2xl text-zinc-600"></i>
                    <span class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">Servidor 100% Seguro</span>
                </div>
            </div>
        </div>

        <!-- Coluna Direita: Formulário de Pagamento -->
        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[3rem] p-8 md:p-12 shadow-2xl space-y-10 relative">
            <div class="space-y-2">
                <h2 class="text-2xl font-black text-white tracking-tight">Detalhes do Pagamento</h2>
                <div class="flex gap-3 pt-2">
                    <i class="fab fa-cc-visa text-2xl text-zinc-700 hover:text-white transition-colors cursor-help" title="Visa"></i>
                    <i class="fab fa-cc-mastercard text-2xl text-zinc-700 hover:text-white transition-colors cursor-help" title="Mastercard"></i>
                    <i class="fab fa-cc-amex text-2xl text-zinc-700 hover:text-white transition-colors cursor-help" title="American Express"></i>
                    <i class="fab fa-cc-diners-club text-2xl text-zinc-700 hover:text-white transition-colors cursor-help" title="Diners Club"></i>
                </div>
            </div>

            <!-- Visual Card Preview (Desktop Only) -->
            <div class="hidden md:block relative aspect-[1.58/1] w-full max-w-[400px] mx-auto group">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 to-teal-900 rounded-3xl shadow-2xl p-8 flex flex-col justify-between overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <div class="w-12 h-10 bg-amber-400/80 rounded-lg shadow-inner"></div>
                        <i class="fas fa-wifi text-xl text-white/50 rotate-90"></i>
                    </div>
                    <div class="space-y-4 relative z-10">
                        <div id="preview-number" class="text-2xl font-mono text-white tracking-[0.2em]">•••• •••• •••• ••••</div>
                        <div class="flex justify-between items-end">
                            <div class="space-y-1">
                                <div class="text-[8px] text-white/40 uppercase tracking-widest">Titular</div>
                                <div id="preview-name" class="text-xs font-black text-white uppercase tracking-widest truncate max-w-[180px]">NOME NO CARTÃO</div>
                            </div>
                            <div class="text-right space-y-1">
                                <div class="text-[8px] text-white/40 uppercase tracking-widest">Validade</div>
                                <div id="preview-expiry" class="text-xs font-black text-white">MM/AA</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('patient.subscription.process') }}" method="POST" id="payment-form" class="space-y-6">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                
                <div class="space-y-4">
                    <!-- Nome no Cartão -->
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-4">Nome Impresso no Cartão</label>
                        <input type="text" name="card_name" id="card_name" required placeholder="Como no cartão" 
                            class="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:border-emerald-500/50 outline-none transition-all">
                    </div>

                    <!-- Número do Cartão -->
                    <div class="space-y-2">
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-4">Número do Cartão</label>
                        <div class="relative">
                            <input type="text" name="card_number" id="card_number" required placeholder="0000 0000 0000 0000" maxlength="19"
                                class="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:border-emerald-500/50 outline-none transition-all font-mono tracking-widest">
                            <div class="absolute right-6 top-1/2 -translate-y-1/2" id="card-icon">
                                <i class="fas fa-credit-card text-zinc-600"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Validade -->
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-4">Validade</label>
                            <input type="text" name="card_expiry" id="card_expiry" required placeholder="MM/AA" maxlength="5"
                                class="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:border-emerald-500/50 outline-none transition-all text-center">
                        </div>
                        <!-- CVV -->
                        <div class="space-y-2">
                            <label class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-4">CVV</label>
                            <div class="relative group">
                                <input type="text" name="card_cvv" id="card_cvv" required placeholder="•••" maxlength="4"
                                    class="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:border-emerald-500/50 outline-none transition-all text-center">
                                <i class="fas fa-question-circle absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600 text-xs cursor-help"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" id="btn-pay" class="group relative w-full py-5 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-[2rem] transition-all shadow-xl shadow-emerald-900/20 uppercase tracking-widest text-xs flex items-center justify-center gap-3 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-400/0 via-white/10 to-emerald-400/0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        <span id="btn-text">Confirmar Pagamento Seguro</span>
                        <div id="btn-loader" class="hidden">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                    <p class="text-[9px] text-zinc-600 text-center mt-6 uppercase tracking-widest font-bold">
                        Ao clicar, você concorda com nossos <a href="#" class="text-zinc-400 hover:text-emerald-400">Termos de Uso</a> e <a href="#" class="text-zinc-400 hover:text-emerald-400">Política de Reembolso</a>.
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Card Real-time Preview & Masking
    const inputName = document.getElementById('card_name');
    const inputNumber = document.getElementById('card_number');
    const inputExpiry = document.getElementById('card_expiry');
    const inputCvv = document.getElementById('card_cvv');

    const previewName = document.getElementById('preview-name');
    const previewNumber = document.getElementById('preview-number');
    const previewExpiry = document.getElementById('preview-expiry');

    const form = document.getElementById('payment-form');
    const btnPay = document.getElementById('btn-pay');
    const btnText = document.getElementById('btn-text');
    const btnLoader = document.getElementById('btn-loader');

    inputName.addEventListener('input', (e) => {
        previewName.textContent = e.target.value || 'NOME NO CARTÃO';
    });

    inputNumber.addEventListener('input', (e) => {
        let value = e.target.value.replace(/\D/g, '');
        let formatted = value.match(/.{1,4}/g)?.join(' ') || '';
        e.target.value = formatted;
        previewNumber.textContent = formatted || '•••• •••• •••• ••••';
        
        // Basic card brand detection
        const icon = document.querySelector('#card-icon i');
        if (value.startsWith('4')) {
            icon.className = 'fab fa-cc-visa text-blue-400';
        } else if (value.startsWith('5')) {
            icon.className = 'fab fa-cc-mastercard text-orange-400';
        } else {
            icon.className = 'fas fa-credit-card text-zinc-600';
        }
    });

    inputExpiry.addEventListener('input', (e) => {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
        previewExpiry.textContent = value || 'MM/AA';
    });

    form.addEventListener('submit', (e) => {
        btnText.textContent = 'Processando...';
        btnLoader.classList.remove('hidden');
        btnPay.classList.add('opacity-80', 'cursor-not-allowed');
    });
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
