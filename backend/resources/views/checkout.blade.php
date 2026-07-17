@extends('layouts.app')

@section('title', 'Checkout — ' . $plan->name)

@section('content')
<div class="min-h-screen py-20 px-6 max-w-[1000px] mx-auto font-['Outfit']" 
     x-data="checkoutFlow()"
     x-cloak>
    
    <!-- Header & Progress -->
    <div class="text-center space-y-8 mb-16">
        <div class="inline-flex items-center gap-3 px-4 py-2 rounded-2xl bg-zinc-900 border border-zinc-800 shadow-xl">
            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i>
            <span class="text-[10px] font-black text-white uppercase tracking-[0.2em]" x-text="pagamentoAtivo ? 'Checkout 100% Seguro' : 'Ativação Gratuita Temporária'"></span>
        </div>
        
        <h1 class="text-5xl font-black text-white tracking-tighter uppercase italic">
            <span x-text="pagamentoAtivo ? 'Finalize sua Assinatura' : 'Ative seu Acesso Elite'"></span>
        </h1>

        <!-- Progress Bar -->
        <div class="relative max-w-2xl mx-auto pt-10">
            <div class="flex justify-between items-center relative z-10">
                <template x-for="step in (pagamentoAtivo ? 5 : 3)" :key="step">
                    <div class="flex flex-col items-center gap-3">
                        <div :class="currentStep >= step ? 'bg-emerald-500 text-zinc-950 scale-110' : 'bg-zinc-800 text-zinc-500'"
                             class="w-10 h-10 rounded-full flex items-center justify-center font-black transition-all duration-500 shadow-lg"
                             x-text="step">
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-widest"
                              :class="currentStep >= step ? 'text-emerald-500' : 'text-zinc-600'"
                              x-text="getStepLabel(step)">
                        </span>
                    </div>
                </template>
            </div>
            <!-- Background Line -->
            <div class="absolute top-[50px] left-0 w-full h-0.5 bg-zinc-800 -z-0"></div>
            <!-- Progress Line -->
            <div class="absolute top-[50px] left-0 h-0.5 bg-emerald-500 transition-all duration-500 -z-0"
                 :style="'width: ' + ((currentStep - 1) * (pagamentoAtivo ? 25 : 50)) + '%'"></div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-zinc-900/30 backdrop-blur-3xl p-8 md:p-12 rounded-[3.5rem] border border-white/5 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/5 blur-[80px] rounded-full"></div>

        <!-- ETAPA 1: Resumo do Plano -->
        <div x-show="currentStep === 1" x-transition.opacity.duration.400ms class="space-y-10">
            <div class="flex flex-col md:flex-row gap-10 items-center">
                <div class="w-full md:w-1/2 space-y-6">
                    <div class="space-y-2">
                        <span class="text-emerald-500 font-black text-[10px] uppercase tracking-[0.3em] italic">Você escolheu</span>
                        <h2 class="text-4xl font-black text-white italic tracking-tighter">{{ $plan->name }}</h2>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-zinc-700 text-xs font-black uppercase">R$</span>
                        <span class="text-5xl font-black text-white tracking-tighter" x-text="pagamentoAtivo ? '{{ number_format($plan->price, 2, ',', '.') }}' : '0,00'"></span>
                        <span class="text-zinc-700 text-sm font-black uppercase tracking-widest">/mês</span>
                    </div>
                    <p class="text-zinc-500 italic font-medium leading-relaxed">{{ $plan->description }}</p>
                </div>
                <div class="w-full md:w-1/2 bg-zinc-950/50 p-8 rounded-[2.5rem] border border-zinc-800 space-y-4">
                    <h3 class="text-xs font-black text-zinc-400 uppercase tracking-widest italic mb-6">Benefícios Inclusos</h3>
                    <ul class="grid grid-cols-1 gap-4">
                        @foreach($plan->planFeatures as $feature)
                        <li class="flex items-center gap-3 text-zinc-300 text-sm font-medium italic">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                            {{ str_replace('_', ' ', ucfirst($feature->feature_key)) }}
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button @click="nextStep()" class="w-full py-6 bg-white text-zinc-950 font-black rounded-3xl hover:bg-emerald-500 transition-all active:scale-95 shadow-2xl text-sm tracking-widest uppercase italic">
                Continuar para Dados
            </button>
        </div>

        <!-- ETAPA 2: Dados do Usuário -->
        <div x-show="currentStep === 2" x-transition.opacity.duration.400ms class="space-y-10">
            @if(!Auth::check())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Nome Completo</label>
                    <input type="text" x-model="formData.name" placeholder="Ex: João Silva"
                           class="w-full bg-zinc-950 border border-zinc-800 text-white p-5 rounded-2xl focus:border-emerald-500 transition-all outline-none italic font-medium">
                </div>
                <div class="space-y-4">
                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">E-mail</label>
                    <input type="email" x-model="formData.email" placeholder="email@exemplo.com"
                           class="w-full bg-zinc-950 border border-zinc-800 text-white p-5 rounded-2xl focus:border-emerald-500 transition-all outline-none italic font-medium">
                </div>
                <div class="space-y-4 md:col-span-2">
                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Sua Senha</label>
                    <input type="password" x-model="formData.password" placeholder="Mínimo 8 caracteres"
                           class="w-full bg-zinc-950 border border-zinc-800 text-white p-5 rounded-2xl focus:border-emerald-500 transition-all outline-none italic font-medium">
                </div>
            </div>
            <div class="p-6 bg-blue-500/10 border border-blue-500/20 rounded-3xl flex items-center gap-4">
                <i data-lucide="info" class="w-6 h-6 text-blue-400"></i>
                <p class="text-xs text-blue-300 font-medium italic">Sua conta será criada automaticamente ao finalizar.</p>
            </div>
            @else
            <div class="flex items-center gap-6 p-8 bg-zinc-950/50 rounded-3xl border border-zinc-800">
                <div class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-500">
                    <i data-lucide="user" class="w-8 h-8"></i>
                </div>
                <div>
                    <h4 class="text-xl font-black text-white italic tracking-tighter">{{ Auth::user()->name }}</h4>
                    <p class="text-zinc-500 text-sm italic">{{ Auth::user()->email }}</p>
                </div>
                <div class="ml-auto">
                    <span class="px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-[9px] font-black uppercase tracking-widest">Sessão Ativa</span>
                </div>
            </div>
            @endif

            <div class="flex gap-4">
                <button @click="prevStep()" class="flex-1 py-6 bg-zinc-950 border border-zinc-800 text-zinc-500 font-black rounded-3xl hover:text-white transition-all text-sm tracking-widest uppercase italic">Voltar</button>
                <button @click="nextStep()" 
                        :disabled="isProcessing"
                        class="flex-[2] py-6 bg-white text-zinc-950 font-black rounded-3xl hover:bg-emerald-500 transition-all active:scale-95 shadow-2xl text-sm tracking-widest uppercase italic disabled:opacity-50">
                    <span x-show="!isProcessing" x-text="pagamentoAtivo ? 'Ir para Pagamento' : 'Ativar Acesso Agora'"></span>
                    <span x-show="isProcessing">Processando...</span>
                </button>
            </div>
        </div>

        <!-- ETAPA 3: Pagamento (Só visível se pagamentoAtivo) -->
        <template x-if="pagamentoAtivo">
            <div x-show="currentStep === 3" x-transition.opacity.duration.400ms class="space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button @click="formData.payment_method = 'credit_card'" 
                            :class="formData.payment_method === 'credit_card' ? 'border-emerald-500 bg-emerald-500/5' : 'border-zinc-800 bg-zinc-950/50'"
                            class="p-6 rounded-3xl border flex flex-col items-center gap-4 group transition-all">
                        <i data-lucide="credit-card" :class="formData.payment_method === 'credit_card' ? 'text-emerald-500' : 'text-zinc-600'" class="w-10 h-10 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest" :class="formData.payment_method === 'credit_card' ? 'text-white' : 'text-zinc-500'">Cartão</span>
                    </button>
                    <button @click="formData.payment_method = 'pix'" 
                            :class="formData.payment_method === 'pix' ? 'border-emerald-500 bg-emerald-500/5' : 'border-zinc-800 bg-zinc-950/50'"
                            class="p-6 rounded-3xl border flex flex-col items-center gap-4 group transition-all">
                        <i data-lucide="qr-code" :class="formData.payment_method === 'pix' ? 'text-emerald-500' : 'text-zinc-600'" class="w-10 h-10 group-hover:scale-110 transition-transform"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest" :class="formData.payment_method === 'pix' ? 'text-white' : 'text-zinc-500'">PIX</span>
                    </button>
                    <button @click="formData.payment_method = 'boleto'" 
                            :class="formData.payment_method === 'boleto' ? 'border-emerald-500 bg-emerald-500/5' : 'border-zinc-800 bg-zinc-950/50'"
                            class="p-6 rounded-3xl border flex flex-col items-center gap-4 group transition-all opacity-50 cursor-not-allowed">
                        <i data-lucide="barcode" class="w-10 h-10 text-zinc-600"></i>
                        <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Boleto (Em breve)</span>
                    </button>
                </div>

                <!-- Card Fields -->
                <div x-show="formData.payment_method === 'credit_card'" x-transition class="space-y-6 pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4 md:col-span-2">
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Número do Cartão</label>
                            <input type="text" x-model="formData.card_number" placeholder="0000 0000 0000 0000"
                                   class="w-full bg-zinc-950 border border-zinc-800 text-white p-5 rounded-2xl focus:border-emerald-500 transition-all outline-none">
                        </div>
                        <div class="space-y-4 md:col-span-2">
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Nome Impresso</label>
                            <input type="text" x-model="formData.card_name" placeholder="Como no cartão"
                                   class="w-full bg-zinc-950 border border-zinc-800 text-white p-5 rounded-2xl focus:border-emerald-500 transition-all outline-none uppercase">
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">Validade (MM/AA)</label>
                            <input type="text" x-model="formData.card_expiry" placeholder="MM/AA"
                                   class="w-full bg-zinc-950 border border-zinc-800 text-white p-5 rounded-2xl focus:border-emerald-500 transition-all outline-none">
                        </div>
                        <div class="space-y-4">
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-4">CVV</label>
                            <input type="text" x-model="formData.card_cvv" placeholder="123"
                                   class="w-full bg-zinc-950 border border-zinc-800 text-white p-5 rounded-2xl focus:border-emerald-500 transition-all outline-none">
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button @click="prevStep()" class="flex-1 py-6 bg-zinc-950 border border-zinc-800 text-zinc-500 font-black rounded-3xl hover:text-white transition-all text-sm tracking-widest uppercase italic">Voltar</button>
                    <button @click="nextStep()" class="flex-[2] py-6 bg-white text-zinc-950 font-black rounded-3xl hover:bg-emerald-500 transition-all active:scale-95 shadow-2xl text-sm tracking-widest uppercase italic">Revisar Assinatura</button>
                </div>
            </div>
        </template>

        <!-- ETAPA 4: Revisão (Só visível se pagamentoAtivo) -->
        <template x-if="pagamentoAtivo">
            <div x-show="currentStep === 4" x-transition.opacity.duration.400ms class="space-y-10">
                <div class="space-y-8">
                    <div class="p-8 bg-zinc-950/50 rounded-3xl border border-zinc-800 space-y-6">
                        <div class="flex justify-between items-center border-b border-white/5 pb-4">
                            <span class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Plano</span>
                            <span class="text-white font-black italic tracking-tighter">{{ $plan->name }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/5 pb-4">
                            <span class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Método</span>
                            <span class="text-white font-black italic tracking-tighter uppercase" x-text="formData.payment_method.replace('_', ' ')"></span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-zinc-500 text-xs font-black uppercase tracking-widest">Total Hoje</span>
                            <span class="text-3xl font-black text-emerald-500 tracking-tighter">R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4">
                    <button @click="prevStep()" class="flex-1 py-6 bg-zinc-950 border border-zinc-800 text-zinc-500 font-black rounded-3xl hover:text-white transition-all text-sm tracking-widest uppercase italic">Corrigir</button>
                    <button @click="finalize()" 
                            :disabled="isProcessing"
                            class="flex-[2] py-6 bg-emerald-500 text-zinc-950 font-black rounded-3xl hover:bg-white transition-all active:scale-95 shadow-[0_0_40px_rgba(16,185,129,0.3)] text-sm tracking-widest uppercase italic">
                        <span x-show="!isProcessing">Finalizar Assinatura</span>
                        <span x-show="isProcessing">Processando...</span>
                    </button>
                </div>
            </div>
        </template>

        <!-- ETAPA FINAL: Sucesso -->
        <div x-show="currentStep === (pagamentoAtivo ? 5 : 3)" x-transition.opacity.duration.400ms class="text-center space-y-10 py-10">
            <div class="w-24 h-24 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-500 mx-auto animate-bounce-slow">
                <i data-lucide="check-circle-2" class="w-12 h-12"></i>
            </div>
            <div class="space-y-4">
                <h2 class="text-4xl font-black text-white italic tracking-tighter">Assinatura Ativada!</h2>
                <p class="text-zinc-500 text-lg italic leading-relaxed max-w-md mx-auto">
                    Seja bem-vindo ao ecossistema NexShape. Sua jornada para o próximo nível começa agora.
                </p>
            </div>

            <div x-show="pagamentoAtivo && formData.payment_method === 'pix'" class="p-8 bg-zinc-950 border border-zinc-800 rounded-[2.5rem] space-y-6 animate-fade-in">
                <p class="text-xs font-black text-zinc-400 uppercase tracking-widest italic">Escaneie o QR Code para ativar</p>
                <div class="w-48 h-48 bg-white p-4 rounded-3xl mx-auto shadow-2xl">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=NexShapePaymentSimulated" alt="PIX QR Code" class="w-full h-full">
                </div>
            </div>

            <a href="{{ route('dashboard') }}" class="inline-block px-12 py-6 bg-white text-zinc-950 font-black rounded-3xl hover:bg-emerald-500 transition-all active:scale-95 shadow-2xl text-sm tracking-widest uppercase italic">
                Acessar Dashboard
            </a>
        </div>
    </div>

    <!-- Security Badges -->
    <div class="mt-16 flex flex-wrap justify-center gap-12 opacity-40 grayscale group-hover:grayscale-0 transition-all duration-700">
        <div class="flex items-center gap-3">
            <i data-lucide="shield" class="w-5 h-5 text-white"></i>
            <span class="text-[9px] font-black text-white uppercase tracking-widest">SSL 256-bit Encrypted</span>
        </div>
        <div class="flex items-center gap-3">
            <i data-lucide="lock" class="w-5 h-5 text-white"></i>
            <span class="text-[9px] font-black text-white uppercase tracking-widest">PCI DSS Compliant</span>
        </div>
    </div>
</div>

<script>
function checkoutFlow() {
    return {
        currentStep: 1,
        isProcessing: false,
        pagamentoAtivo: {{ $pagamentoAtivo ? 'true' : 'false' }},
        formData: {
            plan_id: '{{ $plan->id }}',
            payment_method: 'credit_card',
            name: '',
            email: '',
            password: '',
            card_number: '',
            card_name: '',
            card_expiry: '',
            card_cvv: ''
        },

        getStepLabel(step) {
            if (!this.pagamentoAtivo) {
                const labels = ['', 'Resumo', 'Dados', 'Sucesso'];
                return labels[step];
            }
            const labels = ['', 'Resumo', 'Dados', 'Pagamento', 'Revisão', 'Sucesso'];
            return labels[step];
        },

        nextStep() {
            if (!this.pagamentoAtivo && this.currentStep === 2) {
                this.finalize();
                return;
            }
            if (this.currentStep < 4) this.currentStep++;
        },

        prevStep() {
            if (this.currentStep > 1) this.currentStep--;
        },

        async finalize() {
            this.isProcessing = true;
            
            try {
                const response = await fetch('{{ route('checkout.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formData)
                });

                const result = await response.json();

                if (result.success) {
                    if (result.redirect) {
                        window.location.href = result.redirect;
                        return;
                    }
                    this.currentStep = this.pagamentoAtivo ? 5 : 3;
                    if (window.lucide) window.lucide.createIcons();
                } else {
                    alert(result.message || 'Ocorreu um erro ao processar.');
                }
            } catch (error) {
                console.error(error);
                alert('Erro na comunicação com o servidor.');
            } finally {
                this.isProcessing = false;
            }
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
    input::placeholder { color: #3f3f46; font-style: italic; }
    
    .animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
