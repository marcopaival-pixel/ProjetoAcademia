<div x-data="{ 
    show: false, 
    loading: false,
    packages: [],
    balance: {{ auth()->user()?->ai_credits ?? 0 }},
    async fetchPackages() {
        try {
            const response = await fetch('{{ route('ai-credits.packages') }}');
            this.packages = await response.json();
        } catch (e) {
            console.error('Erro ao buscar pacotes:', e);
        }
    },
    async buyPackage(packageId) {
        this.loading = true;
        try {
            const response = await fetch('{{ route('ai-credits.buy') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=&quot;csrf-token&quot;]').getAttribute('content')
                },
                body: JSON.stringify({ package_id: packageId })
            });
            const data = await response.json();
            if (data.success) {
                this.balance = data.new_balance;
                window.location.reload(); // Simplest way to update everywhere
            } else {
                alert(data.message || 'Erro ao processar compra');
            }
        } catch (e) {
            console.error('Erro ao comprar:', e);
            alert('Erro na comunicação com o servidor');
        } finally {
            this.loading = false;
        }
    }
}" 
x-show="show" 
x-on:open-ai-credits-modal.window="show = true; fetchPackages()"
class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-fade-in" 
style="display: none;"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0 scale-95"
x-transition:enter-end="opacity-100 scale-100"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100 scale-100"
x-transition:leave-end="opacity-0 scale-95">
    
    <div @click.away="show = false" class="bg-[#0b0e14] border border-white/10 w-full max-w-2xl rounded-[2.5rem] overflow-hidden shadow-2xl flex flex-col md:flex-row max-h-[90vh]">
        
        <!-- Lado Esquerdo: Info -->
        <div class="md:w-1/3 bg-purple-600/5 p-8 flex flex-col justify-between border-b md:border-b-0 md:border-r border-white/5">
            <div class="space-y-6">
                <div class="w-16 h-16 bg-purple-500/20 rounded-2xl flex items-center justify-center text-purple-500 shadow-lg shadow-purple-500/10">
                    <i class="fas fa-magic text-3xl"></i>
                </div>
                
                <div>
                    <h3 class="text-2xl font-black text-white tracking-tight">NexShape <span class="text-purple-500">IA</span></h3>
                    <p class="text-zinc-500 text-sm mt-2 font-medium">Potencialize seus resultados com Inteligência Artificial avançada.</p>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center gap-3 text-xs font-bold text-zinc-400">
                        <i class="fas fa-check-circle text-purple-500"></i>
                        <span>Planos Alimentares IA</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs font-bold text-zinc-400">
                        <i class="fas fa-check-circle text-purple-500"></i>
                        <span>Treinos Inteligentes</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs font-bold text-zinc-400">
                        <i class="fas fa-check-circle text-purple-500"></i>
                        <span>Análise de Refeições</span>
                    </div>
                </div>
            </div>

            <div class="mt-8 p-4 bg-white/5 rounded-2xl border border-white/5">
                <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500 block">Seu Saldo Atual</span>
                <span class="text-3xl font-black text-white" x-text="balance">0</span>
                <span class="text-[10px] font-bold text-purple-500 uppercase block mt-1">Créditos Disponíveis</span>
            </div>
        </div>

        <!-- Lado Direito: Pacotes -->
        <div class="md:w-2/3 p-8 bg-zinc-950/30 overflow-y-auto">
            <div class="flex items-center justify-between mb-8">
                <h4 class="text-lg font-black text-white tracking-tight uppercase">Escolha um Pacote</h4>
                <button @click="show = false" class="text-zinc-500 hover:text-white transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="pkg in packages" :key="pkg.id">
                    <div class="group relative bg-white/5 border border-white/5 rounded-3xl p-6 hover:bg-white/[0.08] hover:border-purple-500/30 transition-all cursor-pointer overflow-hidden" @click="buyPackage(pkg.id)">
                        <!-- Glow effect -->
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/10 blur-3xl group-hover:bg-purple-500/20 transition-all"></div>
                        
                        <div class="flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-zinc-900 rounded-2xl flex items-center justify-center text-purple-500 border border-white/5">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <div>
                                    <h5 class="text-white font-black tracking-tight" x-text="pkg.name">Pacote</h5>
                                    <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest" x-text="pkg.credits + ' Créditos de IA'">0 Créditos</span>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <span class="text-2xl font-black text-white" x-text="'R$ ' + pkg.price.replace('.', ',')">R$ 0,00</span>
                                <span class="text-[10px] font-bold text-purple-500 uppercase block tracking-widest">Pagamento Único</span>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all translate-y-2 group-hover:translate-y-0">
                            <span class="text-[10px] font-black text-purple-500 uppercase tracking-widest">Clique para comprar agora</span>
                            <i class="fas fa-arrow-right text-[8px] text-purple-500"></i>
                        </div>
                    </div>
                </template>

                <div x-show="packages.length === 0" class="py-12 text-center">
                    <div class="animate-pulse flex flex-col items-center gap-4">
                        <div class="w-12 h-12 bg-white/5 rounded-full"></div>
                        <div class="h-2 w-32 bg-white/5 rounded"></div>
                    </div>
                </div>
            </div>

            <p class="text-[10px] text-zinc-600 font-medium mt-8 text-center uppercase tracking-widest">
                <i class="fas fa-shield-alt mr-1"></i> Pagamento seguro e liberação imediata
            </p>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="loading" class="absolute inset-0 bg-black/60 backdrop-blur-sm z-[10000] flex items-center justify-center">
        <div class="flex flex-col items-center gap-4">
            <div class="w-12 h-12 border-4 border-purple-500/20 border-t-purple-500 rounded-full animate-spin"></div>
            <span class="text-xs font-black text-white uppercase tracking-widest animate-pulse">Processando Compra...</span>
        </div>
    </div>
</div>
