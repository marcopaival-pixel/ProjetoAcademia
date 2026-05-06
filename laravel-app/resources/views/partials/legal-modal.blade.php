{{-- Modal: Termos & Privacidade Global --}}
<div x-data="{ 
    nexLegalOpen: false, 
    nexLegalTab: 'privacy' 
}" 
@open-legal.window="
    nexLegalTab = $event.detail;
    nexLegalOpen = true;
    $nextTick(() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
"
@keydown.escape.window="nexLegalOpen = false"
class="relative z-[9999]">

    <script>
        window.openLegalProtocol = (tab) => {
            window.dispatchEvent(new CustomEvent('open-legal', { detail: tab }));
        }
    </script>

    <div x-show="nexLegalOpen" 
         x-cloak
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[10000] flex items-center justify-center p-4 sm:p-6 lg:p-10">
        
        <div @click="nexLegalOpen = false" class="absolute inset-0 bg-zinc-950/95 backdrop-blur-2xl"></div>
        
        <div x-show="nexLegalOpen"
             x-transition:enter="transition ease-out duration-500 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-8"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="relative w-full max-w-4xl max-h-[85vh] bg-zinc-900 border border-zinc-800 rounded-[3rem] shadow-3xl overflow-hidden flex flex-col">
            
            <!-- Modal Header -->
            <div class="p-8 border-b border-zinc-800 flex items-center justify-between shrink-0 bg-zinc-900/50">
                <div class="flex items-center gap-6">
                    <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500 shadow-inner">
                        <i x-show="nexLegalTab === 'terms'" data-lucide="file-text" class="w-6 h-6"></i>
                        <i x-show="nexLegalTab === 'privacy'" data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter" x-text="nexLegalTab === 'terms' ? 'Termos de Uso' : 'Privacidade (LGPD)'"></h3>
                        <p class="text-[9px] text-zinc-500 font-black uppercase tracking-[0.2em] mt-1">Sincronização com as normas vigentes</p>
                    </div>
                </div>
                
                <button @click="nexLegalOpen = false" class="w-12 h-12 bg-zinc-950 border border-zinc-800 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-white transition-all hover:border-emerald-500/30">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-10 md:p-14 custom-scrollbar bg-zinc-900/40">
                <div class="max-w-none">
                    <!-- Terms Content -->
                    <div x-show="nexLegalTab === 'terms'" class="space-y-12">
                        <div class="space-y-10">
                            <section class="group">
                                <h4 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4">
                                    <span class="w-8 h-px bg-emerald-500/30"></span> 01. Aceitação do Protocolo
                                </h4>
                                <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">Ao acessar e utilizar a plataforma NEX SHAPE, você concorda integralmente com estes termos. Se você não concorda com qualquer cláusula, não deve prosseguir com o uso do ecossistema.</p>
                            </section>
                            
                            <section class="group">
                                <h4 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4">
                                    <span class="w-8 h-px bg-emerald-500/30"></span> 02. Finalidade da Plataforma
                                </h4>
                                <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">O NEX SHAPE é uma ferramenta de gestão de performance e dados biológicos. As informações fornecidas não substituem o diagnóstico médico ou nutricional direto. Sempre consulte um especialista certificado.</p>
                            </section>
                            
                            <section class="group">
                                <h4 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4">
                                    <span class="w-8 h-px bg-emerald-500/30"></span> 03. Identidade & Segurança
                                </h4>
                                <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">Sua conta é pessoal e intransferível. O uso de scripts, automações ou qualquer tentativa de burlar os algoritmos de performance resultará na revogação imediata da licença de uso.</p>
                            </section>
                            
                            <section class="group">
                                <h4 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4">
                                    <span class="w-8 h-px bg-emerald-500/30"></span> 04. Propriedade Intelectual
                                </h4>
                                <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">Todos os protocolos de treino, dietas estruturadas e algoritmos proprietários são ativos protegidos. A reprodução sem autorização é passível de sanções legais.</p>
                                <p class="text-[10px] text-zinc-600 italic mt-4">Última atualização: Maio de 2024</p>
                            </section>
                        </div>
                    </div>
                    
                    <!-- Privacy Content -->
                    <div x-show="nexLegalTab === 'privacy'" class="space-y-12">
                        <div class="space-y-10">
                            <section class="group">
                                <h4 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4">
                                    <span class="w-8 h-px bg-emerald-500/30"></span> 01. Coleta de Bio-Dados
                                </h4>
                                <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">Processamos dados fornecidos por você (nome, medidas corpóreas, anamnese) e dados de uso técnico para otimizar os algoritmos de IA que geram seus planos personalizados.</p>
                            </section>
                            
                            <section class="group">
                                <h4 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4">
                                    <span class="w-8 h-px bg-emerald-500/30"></span> 02. Fluxo de Tratamento (LGPD)
                                </h4>
                                <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">Seus dados nunca são vendidos. Eles circulam apenas entre a plataforma e os especialistas que você autorizar o vínculo. A criptografia é aplicada em todas as camadas de persistência.</p>
                            </section>
                            
                            <section class="group">
                                <h4 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4">
                                    <span class="w-8 h-px bg-emerald-500/30"></span> 03. Retenção & Exclusão
                                </h4>
                                <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">Mantemos seus dados apenas pelo tempo necessário para cumprir o protocolo de performance ou enquanto sua conta estiver ativa. Você pode solicitar a exclusão total de seus dados a qualquer momento.</p>
                            </section>
 
                            <div class="bg-emerald-500/5 border border-emerald-500/10 p-8 rounded-[2rem] mt-10 flex gap-6 items-center">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-500 shrink-0">
                                    <i data-lucide="info" class="w-5 h-5"></i>
                                </div>
                                <p class="text-[10px] text-zinc-400 leading-relaxed font-medium italic">
                                    O NEX SHAPE opera sob conformidade rigorosa com a Lei Federal nº 13.709/2018 (LGPD). Sua privacidade é um pilar da nossa arquitetura.
                                </p>
                            </div>
                            <p class="text-[10px] text-zinc-600 italic mt-4">Última atualização: Maio de 2024</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="p-8 border-t border-zinc-800 bg-zinc-950/80 shrink-0 text-center">
                <button @click="nexLegalOpen = false" class="px-12 py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black text-[10px] uppercase tracking-[0.2em] rounded-xl transition-all shadow-xl shadow-emerald-500/20 active:scale-95">
                    ENTENDI E ACEITO O PROTOCOLO
                </button>
            </div>
        </div>
    </div>
</div>
