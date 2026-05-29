@if(auth()->check() && !request()->routeIs('home', 'login', 'register', 'password.*', 'verification.notice', 'registration.pending', 'registration.rejected') && auth()->user()->hasRole('aluno') && auth()->user()->onboarding_status !== 'completed')
<div x-data="onboardingModal()" 
     x-show="isOpen" 
     x-on:open-onboarding.window="currentStep = 0; isOpen = true"
     class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-md"
     x-init="init()"
     style="display: none;">
    
    <div class="bg-zinc-900 border border-zinc-800 w-full max-w-2xl rounded-[2.5rem] overflow-hidden shadow-2xl">
        <!-- Barra de Progresso Superior -->
        <div class="h-1.5 w-full bg-zinc-800">
            <div class="h-full bg-blue-500 transition-all duration-500" :style="`width: ${progress}%`"></div>
        </div>

        <div class="p-8 md:p-12 space-y-8">
            <!-- Cabeçalho -->
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-3xl font-black text-white tracking-tight" x-text="steps[currentStep].title"></h3>
                    <p class="text-zinc-500 font-medium mt-1" x-text="steps[currentStep].subtitle"></p>
                </div>
                <div class="text-zinc-700 font-black text-2xl" x-text="`${currentStep + 1}/${steps.length}`"></div>
            </div>

            <!-- Conteúdo dos Steps -->
            <div class="min-h-[300px]">
                <!-- Step 0: Welcome -->
                <template x-if="currentStep === 0">
                    <div class="space-y-6 flex flex-col items-center justify-center text-center py-8">
                        <div class="w-24 h-24 bg-blue-500/10 rounded-full flex items-center justify-center text-blue-500 mb-4">
                            <i class="fas fa-rocket text-4xl"></i>
                        </div>
                        <h4 class="text-2xl font-bold text-white">Bem-vindo ao NexShape!</h4>
                        <p class="text-zinc-400 max-w-md">Para personalizarmos sua experiência e cálculos de macros, precisamos de algumas informações básicas.</p>
                        <div class="bg-zinc-800/50 p-4 rounded-2xl border border-zinc-700/50 w-full">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center text-white shrink-0">
                                    <span x-text="percentage"></span>%
                                </div>
                                <div class="text-left">
                                    <p class="text-white font-bold">Perfil Completo</p>
                                    <p class="text-xs text-zinc-500">Complete e ganhe acesso total</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Step 1: Basics -->
                <template x-if="currentStep === 1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-4">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-zinc-400 uppercase tracking-wider ml-1">Data de Nascimento</label>
                            <input type="date" x-model="formData.birth_date" class="w-full bg-zinc-800 border border-zinc-700 rounded-2xl p-4 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-zinc-400 uppercase tracking-wider ml-1">Sexo</label>
                            <div class="grid grid-cols-2 gap-3">
                                <button x-on:click="formData.sex = 'M'" :class="formData.sex === 'M' ? 'bg-blue-600 border-blue-500 text-white' : 'bg-zinc-800 border-zinc-700 text-zinc-400'" class="p-4 rounded-2xl border font-bold transition-all">Masculino</button>
                                <button x-on:click="formData.sex = 'F'" :class="formData.sex === 'F' ? 'bg-blue-600 border-blue-500 text-white' : 'bg-zinc-800 border-zinc-700 text-zinc-400'" class="p-4 rounded-2xl border font-bold transition-all">Feminino</button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Step 2: Physical -->
                <template x-if="currentStep === 2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-4">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-zinc-400 uppercase tracking-wider ml-1">Altura (cm)</label>
                            <input type="number" x-model="formData.height_cm" placeholder="Ex: 175" class="w-full bg-zinc-800 border border-zinc-700 rounded-2xl p-4 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-zinc-400 uppercase tracking-wider ml-1">Peso Atual (kg)</label>
                            <input type="number" step="0.1" x-model="formData.weight_kg" placeholder="Ex: 75.5" class="w-full bg-zinc-800 border border-zinc-700 rounded-2xl p-4 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all outline-none">
                        </div>
                    </div>
                </template>

                <!-- Step 3: Goals -->
                <template x-if="currentStep === 3">
                    <div class="space-y-6 py-2">
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Seu Objetivo Primário</label>
                            
                            <div class="grid grid-cols-2 gap-3">
                                @foreach(\App\Models\UserProfile::getAvailableGoals() as $slug => $data)
                                    <button x-on:click="formData.goal = '{{ $slug }}'" 
                                            :class="formData.goal === '{{ $slug }}' ? 'bg-blue-600 border-blue-500 text-white' : 'bg-zinc-800/50 border-zinc-700 text-zinc-400'"
                                            class="flex flex-col items-center justify-center p-4 rounded-2xl border transition-all hover:bg-zinc-800 group h-24">
                                        <i class="fas fa-{{ $data['icon'] }} mb-2 text-sm" :class="formData.goal === '{{ $slug }}' ? 'text-white' : 'text-zinc-600 group-hover:text-zinc-400'"></i>
                                        <span class="text-[10px] font-black uppercase text-center tracking-tight">{{ $data['label'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Peso Ideal (kg)</label>
                                <input type="number" step="0.1" x-model="formData.target_weight_kg" placeholder="Ex: 70" class="w-full bg-zinc-800 border border-zinc-700 rounded-2xl p-4 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all outline-none">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Treinos por Semana</label>
                                <input type="number" min="0" max="7" x-model="formData.training_days_per_week" class="w-full bg-zinc-800 border border-zinc-700 rounded-2xl p-4 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all outline-none">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Rodapé / Botões -->
            <div class="flex flex-col md:flex-row gap-3 pt-4">
                <button x-on:click="skip()" class="px-8 py-4 bg-zinc-800 text-zinc-400 font-bold rounded-2xl hover:bg-zinc-700 transition-all order-3 md:order-1">
                    Pular por enquanto
                </button>
                <div class="flex-grow order-2"></div>
                
                <button x-show="currentStep > 0" x-on:click="currentStep--" class="px-8 py-4 bg-zinc-800 text-white font-bold rounded-2xl hover:bg-zinc-700 transition-all order-1 md:order-2">
                    Anterior
                </button>

                <button x-on:click="next()" 
                        :disabled="loading" 
                        class="px-8 py-4 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-500 transition-all flex items-center justify-center gap-2 order-1 md:order-3 min-w-[160px]">
                    <span x-show="!loading" x-text="currentStep === steps.length - 1 ? 'Finalizar' : (currentStep === 0 ? 'Começar' : 'Continuar')"></span>
                    <i x-show="loading" class="fas fa-circle-notch fa-spin"></i>
                    <i x-show="!loading && currentStep < steps.length - 1" class="fas fa-arrow-right text-xs"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function onboardingModal() {
    return {
        isOpen: {{ (auth()->user()->hasRole('aluno') && (auth()->user()->onboarding_status === 'pending' || session('show_onboarding_modal'))) ? 'true' : 'false' }},
        currentStep: 0,
        loading: false,
        percentage: {{ auth()->user()->profile_completion_percentage ?? 0 }},
        formData: {
            birth_date: '{{ auth()->user()->profile?->birth_date?->format("Y-m-d") ?? "" }}',
            sex: '{{ auth()->user()->profile?->sex ?? "" }}',
            height_cm: '{{ auth()->user()->profile?->height_cm ?? "" }}',
            weight_kg: '{{ auth()->user()->weightEntries()->latest("weighed_at")->first()?->weight_kg ?? "" }}',
            activity_level: '{{ auth()->user()->profile?->activity_level ?? "moderate" }}',
            goal: '{{ auth()->user()->profile?->goal ?? "maintain" }}',
            target_weight_kg: '{{ auth()->user()->profile?->target_weight_kg ?? "" }}',
            training_days_per_week: '{{ auth()->user()->profile?->training_days_per_week ?? "3" }}',
        },
        steps: [
            { title: 'Excelência NexShape', subtitle: 'Inicie sua jornada para a alta performance.' },
            { title: 'Dados Bio-Identitários', subtitle: 'Informações essenciais para personalização.' },
            { title: 'Análise Biométrica', subtitle: 'Seus indicadores físicos atuais.' },
            { title: 'Direcionamento Estratégico', subtitle: 'Onde você quer chegar em 90 dias?' }
        ],
        get progress() {
            return ((this.currentStep + 1) / this.steps.length) * 100;
        },
        init() {
        },
        async save() {
            this.loading = true;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                const response = await fetch('{{ route("onboarding.api.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(this.formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.percentage = data.percentage;
                    return true;
                } else {
                    let errorMessage = data.message || 'Erro ao salvar os dados.';
                    if (data.errors) {
                        const firstError = Object.values(data.errors)[0][0];
                        errorMessage = firstError;
                    }
                    window.dispatchEvent(new CustomEvent('toast', { 
                        detail: { message: errorMessage, type: 'error' }
                    }));
                }
            } catch (error) {
                console.error('Save failed:', error);
                window.dispatchEvent(new CustomEvent('toast', { 
                    detail: { message: 'Erro de comunicação com o servidor.', type: 'error' }
                }));
            } finally {
                this.loading = false;
            }
            return false;
        },
        async next() {
            // Validação simples de campos obrigatórios por passo
            if (this.currentStep === 1) {
                if (!this.formData.birth_date || !this.formData.sex) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Por favor, preencha a data de nascimento e o sexo.', type: 'warning' }}));
                    return;
                }
            }
            if (this.currentStep === 2) {
                if (!this.formData.height_cm || !this.formData.weight_kg) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Por favor, informe sua altura e peso.', type: 'warning' }}));
                    return;
                }
            }
            if (this.currentStep === 3) {
                if (!this.formData.goal || !this.formData.target_weight_kg) {
                    window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Por favor, informe seu objetivo e peso ideal.', type: 'warning' }}));
                    return;
                }
            }

            if (this.currentStep === 0) {
                this.currentStep++;
                return;
            }

            if (this.currentStep === this.steps.length - 1) {
                const saved = await this.save();
                if (saved) {
                    try {
                        const response = await fetch('{{ route("onboarding.api.complete") }}', { 
                            method: 'POST', 
                            headers: { 
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            } 
                        });
                        const completeData = await response.json();
                        if (completeData.success) {
                            this.isOpen = false;
                            window.location.reload();
                        } else {
                            throw new Error(completeData.message || 'Falha ao concluir onboarding');
                        }
                    } catch (e) {
                        console.error('Finalization error:', e);
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Erro ao finalizar: ' + e.message, type: 'error' }}));
                    }
                }
                return;
            }

            // Para passos intermediários, salva e avança sem bloquear
            this.save();
            this.currentStep++;
        },
        async skip() {
            await fetch('{{ route("onboarding.api.skip") }}', { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') } 
            });
            this.isOpen = false;
        }
    }
}
</script>
@endif
