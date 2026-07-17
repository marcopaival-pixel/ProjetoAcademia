@extends('layouts.app')

@section('title', 'Central Jurídica & Privacidade — NexShape')

@section('content')
<div class="min-h-screen bg-[#080a0f] py-12 px-4 relative overflow-hidden" x-data="{ tab: '{{ $activeTab ?? 'terms' }}' }">
    <!-- Ambient Glows -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-emerald-500/5 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-6xl mx-auto relative z-10">
        <!-- Header Section -->
        <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6 animate-fade-in">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 mb-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[9px] text-emerald-500 font-black uppercase tracking-[0.2em]">Compliance & Transparência</span>
                </div>
                <h1 class="text-5xl font-black text-white tracking-tighter uppercase italic">Central <span class="text-emerald-500">Jurídica</span></h1>
                <p class="text-zinc-500 font-medium italic">Sua segurança e a integridade dos seus dados são nosso protocolo principal.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="bg-zinc-900/50 backdrop-blur-xl border border-zinc-800 px-6 py-3 rounded-2xl">
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mb-1">Última Revisão</p>
                    <p class="text-xs text-zinc-300 font-bold uppercase tracking-tighter">{{ date('d/m/Y') }}</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-10">
            <!-- Sidebar Navigation -->
            <aside class="lg:w-80 flex-shrink-0 animate-slide-in-left">
                <div class="bg-zinc-900/40 backdrop-blur-2xl border border-zinc-800 rounded-[2.5rem] p-6 sticky top-8 shadow-3xl">
                    <nav class="space-y-2">
                        <button @click="tab = 'terms'" 
                                :class="tab === 'terms' ? 'bg-emerald-500 text-zinc-950 shadow-lg shadow-emerald-500/20 scale-[1.02]' : 'text-zinc-500 hover:bg-zinc-800/50 hover:text-zinc-300'"
                                class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] transition-all duration-300 group">
                            <i data-lucide="file-text" class="w-4 h-4 transition-transform group-hover:scale-110"></i>
                            Termos de Uso
                        </button>
                        
                        <button @click="tab = 'privacy'" 
                                :class="tab === 'privacy' ? 'bg-emerald-500 text-zinc-950 shadow-lg shadow-emerald-500/20 scale-[1.02]' : 'text-zinc-500 hover:bg-zinc-800/50 hover:text-zinc-300'"
                                class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] transition-all duration-300 group">
                            <i data-lucide="shield-check" class="w-4 h-4 transition-transform group-hover:scale-110"></i>
                            Privacidade
                        </button>

                        <button @click="tab = 'cookies'" 
                                :class="tab === 'cookies' ? 'bg-emerald-500 text-zinc-950 shadow-lg shadow-emerald-500/20 scale-[1.02]' : 'text-zinc-500 hover:bg-zinc-800/50 hover:text-zinc-300'"
                                class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] transition-all duration-300 group">
                            <i data-lucide="cookie" class="w-4 h-4 transition-transform group-hover:scale-110"></i>
                            Cookies
                        </button>

                        <div class="h-px bg-zinc-800 my-4 mx-4"></div>

                        @auth
                        <button @click="tab = 'lgpd'" 
                                :class="tab === 'lgpd' ? 'bg-zinc-100 text-zinc-950 shadow-lg shadow-white/5 scale-[1.02]' : 'text-zinc-500 hover:bg-zinc-800/50 hover:text-zinc-300'"
                                class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] transition-all duration-300 group">
                            <i data-lucide="fingerprint" class="w-4 h-4 transition-transform group-hover:scale-110"></i>
                            Gestão LGPD
                        </button>
                        @endauth
                    </nav>

                    <div class="mt-10 p-6 rounded-3xl bg-zinc-950/50 border border-zinc-800/50 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                                <i data-lucide="help-circle" class="w-4 h-4"></i>
                            </div>
                            <h4 class="text-[10px] font-black text-white uppercase tracking-widest">Suporte DPO</h4>
                        </div>
                        <p class="text-[10px] text-zinc-600 font-medium leading-relaxed italic">Dúvidas sobre o tratamento dos seus dados biológicos?</p>
                        <a href="mailto:dpo@nexshape.com.br" class="block w-full py-3 bg-zinc-900 border border-zinc-800 hover:border-emerald-500/30 text-[10px] font-bold text-zinc-400 hover:text-white text-center rounded-xl transition-all uppercase tracking-widest">
                            Falar com DPO
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 animate-fade-in">
                <div class="bg-zinc-900/20 backdrop-blur-3xl border border-zinc-800 rounded-[3rem] overflow-hidden shadow-4xl min-h-[600px]">
                    
                    <!-- Tab: Terms of Use -->
                    <div x-show="tab === 'terms'" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="p-10 md:p-16">
                        <div class="space-y-12">
                            <div class="flex items-center gap-6 pb-8 border-b border-zinc-800/50">
                                <div class="w-16 h-16 bg-emerald-500/10 rounded-[1.5rem] flex items-center justify-center text-emerald-500 transform -rotate-6">
                                    <i data-lucide="file-text" class="w-8 h-8"></i>
                                </div>
                                <h2 class="text-3xl font-black text-white uppercase italic tracking-tighter">Termos de <span class="text-emerald-500">Uso</span></h2>
                            </div>
                            
                            <div class="space-y-10">
                                <section class="group">
                                    <h3 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4 group-hover:translate-x-1 transition-transform">
                                        <span class="w-8 h-px bg-emerald-500/30"></span> 01. Aceitação do Protocolo
                                    </h3>
                                    <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">Ao acessar e utilizar a plataforma NEX SHAPE, você concorda integralmente com estes termos. Se você não concorda com qualquer cláusula, não deve prosseguir com o uso do ecossistema.</p>
                                </section>
                                
                                <section class="group">
                                    <h3 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4 group-hover:translate-x-1 transition-transform">
                                        <span class="w-8 h-px bg-emerald-500/30"></span> 02. Finalidade da Plataforma
                                    </h3>
                                    <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">O NEX SHAPE é uma ferramenta de gestão de performance e dados biológicos. As informações fornecidas não substituem o diagnóstico médico ou nutricional direto. Sempre consulte um especialista certificado.</p>
                                </section>
                                
                                <section class="group">
                                    <h3 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4 group-hover:translate-x-1 transition-transform">
                                        <span class="w-8 h-px bg-emerald-500/30"></span> 03. Identidade & Segurança
                                    </h3>
                                    <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">Sua conta é pessoal e intransferível. O uso de scripts, automações ou qualquer tentativa de burlar os algoritmos de performance resultará na revogação imediata da licença de uso.</p>
                                </section>
                                
                                <section class="group">
                                    <h3 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4 group-hover:translate-x-1 transition-transform">
                                        <span class="w-8 h-px bg-emerald-500/30"></span> 04. Propriedade Intelectual
                                    </h3>
                                    <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">Todos os protocolos de treino, dietas estruturadas e algoritmos proprietários são ativos protegidos. A reprodução sem autorização é passível de sanções legais.</p>
                                </section>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Privacy Policy -->
                    <div x-show="tab === 'privacy'" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="p-10 md:p-16">
                        <div class="space-y-12">
                            <div class="flex items-center gap-6 pb-8 border-b border-zinc-800/50">
                                <div class="w-16 h-16 bg-emerald-500/10 rounded-[1.5rem] flex items-center justify-center text-emerald-500 transform rotate-6">
                                    <i data-lucide="shield-check" class="w-8 h-8"></i>
                                </div>
                                <h2 class="text-3xl font-black text-white uppercase italic tracking-tighter">Política de <span class="text-emerald-500">Privacidade</span></h2>
                            </div>
                            
                            <div class="space-y-10">
                                <section class="group">
                                    <h3 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4 group-hover:translate-x-1 transition-transform">
                                        <span class="w-8 h-px bg-emerald-500/30"></span> 01. Bio-Data Collected
                                    </h3>
                                    <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">Processamos dados fornecidos por você (nome, medidas corpóreas, anamnese) e dados de uso técnico para otimizar os algoritmos de IA que geram seus planos.</p>
                                </section>
                                
                                <section class="group">
                                    <h3 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4 group-hover:translate-x-1 transition-transform">
                                        <span class="w-8 h-px bg-emerald-500/30"></span> 02. Fluxo de Tratamento
                                    </h3>
                                    <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">Seus dados nunca são vendidos. Eles circulam apenas entre a plataforma e os especialistas que você autorizar o vínculo. A criptografia é aplicada em todas as camadas de persistência.</p>
                                </section>
                                
                                <section class="group">
                                    <h3 class="flex items-center gap-4 text-xs font-black text-emerald-500 uppercase tracking-[0.4em] mb-4 group-hover:translate-x-1 transition-transform">
                                        <span class="w-8 h-px bg-emerald-500/30"></span> 03. Retenção & Exclusão
                                    </h3>
                                    <p class="text-sm text-zinc-400 font-medium leading-relaxed italic ml-12">Mantemos seus dados apenas pelo tempo necessário para cumprir o protocolo de performance ou enquanto sua conta estiver ativa. Você pode solicitar a exclusão total via Gestão LGPD.</p>
                                </section>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: Cookies -->
                    <div x-show="tab === 'cookies'" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="p-10 md:p-16">
                        <div class="space-y-12">
                            <div class="flex items-center gap-6 pb-8 border-b border-zinc-800/50">
                                <div class="w-16 h-16 bg-emerald-500/10 rounded-[1.5rem] flex items-center justify-center text-emerald-500">
                                    <i data-lucide="cookie" class="w-8 h-8"></i>
                                </div>
                                <h2 class="text-3xl font-black text-white uppercase italic tracking-tighter">Protocolo de <span class="text-emerald-500">Cookies</span></h2>
                            </div>
                            
                            <div class="space-y-8">
                                <p class="text-sm text-zinc-400 font-medium italic leading-relaxed">Utilizamos micro-arquivos de dados para estabilizar sua sessão e personalizar sua interface. Nenhum cookie de rastreamento invasivo é utilizado sem consentimento.</p>
                                
                                <div class="grid md:grid-cols-2 gap-6 mt-8">
                                    <div class="bg-zinc-950/50 border border-zinc-800 p-8 rounded-[2rem] space-y-4 group hover:border-emerald-500/30 transition-all duration-500">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="lock" class="w-4 h-4 text-emerald-500"></i>
                                            <h4 class="text-[10px] font-black text-white uppercase tracking-widest">Essenciais</h4>
                                        </div>
                                        <p class="text-[10px] text-zinc-600 font-medium leading-relaxed italic">Fundamentais para autenticação, segurança e integridade das rotas protegidas.</p>
                                    </div>
                                    <div class="bg-zinc-950/50 border border-zinc-800 p-8 rounded-[2rem] space-y-4 group hover:border-emerald-500/30 transition-all duration-500">
                                        <div class="flex items-center gap-3">
                                            <i data-lucide="bar-chart-3" class="w-4 h-4 text-emerald-500"></i>
                                            <h4 class="text-[10px] font-black text-white uppercase tracking-widest">Performance</h4>
                                        </div>
                                        <p class="text-[10px] text-zinc-600 font-medium leading-relaxed italic">Métricas de velocidade e usabilidade que nos ajudam a otimizar sua experiência de treino.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @auth
                    <!-- Tab: LGPD Management -->
                    <div x-show="tab === 'lgpd'" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0" class="p-10 md:p-16">
                        <div class="space-y-12">
                            <div class="flex items-center gap-6 pb-8 border-b border-zinc-800/50">
                                <div class="w-16 h-16 bg-white/10 rounded-[1.5rem] flex items-center justify-center text-white transform -rotate-3">
                                    <i data-lucide="fingerprint" class="w-8 h-8"></i>
                                </div>
                                <h2 class="text-3xl font-black text-white uppercase italic tracking-tighter">Gestão de <span class="text-emerald-500">Bio-Dados</span></h2>
                            </div>

                            <div class="grid md:grid-cols-2 gap-8">
                                <!-- Portability -->
                                <div class="bg-zinc-950/40 border border-zinc-800 p-10 rounded-[2.5rem] space-y-6 group hover:border-emerald-500/20 transition-all duration-500">
                                    <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 transform group-hover:scale-110 transition-transform">
                                        <i data-lucide="download"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <h3 class="text-lg font-black text-white italic uppercase tracking-tighter">Portabilidade</h3>
                                        <p class="text-[10px] text-zinc-600 font-medium leading-relaxed italic">Baixe seu histórico completo de bio-métricas e treinos em formato JSON legível.</p>
                                    </div>
                                    <a href="{{ route('privacy.download') }}" class="inline-flex w-full items-center justify-center gap-3 bg-zinc-900 border border-zinc-800 text-white py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-500 hover:text-zinc-950 hover:border-emerald-500 transition-all shadow-xl">
                                        <i data-lucide="download-cloud" class="w-3 h-3"></i>
                                        Baixar Meus Dados
                                    </a>
                                </div>

                                <!-- Deletion -->
                                <div class="bg-zinc-950/40 border border-zinc-800 p-10 rounded-[2.5rem] space-y-6 group hover:border-rose-500/20 transition-all duration-500">
                                    <div class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 transform group-hover:scale-110 transition-transform">
                                        <i data-lucide="trash-2"></i>
                                    </div>
                                    <div class="space-y-2">
                                        <h3 class="text-lg font-black text-white italic uppercase tracking-tighter">Esquecimento</h3>
                                        <p class="text-[10px] text-zinc-600 font-medium leading-relaxed italic">Inicie o protocolo de deleção definitiva da sua identidade digital e bio-logs.</p>
                                    </div>
                                    <button onclick="document.getElementById('deleteRequestModal').classList.remove('hidden')" class="w-full flex items-center justify-center gap-3 bg-zinc-900 border border-zinc-800 text-rose-500 py-4 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-500 hover:text-white hover:border-rose-500 transition-all shadow-xl">
                                        <i data-lucide="user-minus" class="w-3 h-3"></i>
                                        Solicitar Exclusão
                                    </button>
                                </div>
                            </div>

                            <div class="bg-emerald-500/5 border border-emerald-500/10 p-8 rounded-[2.5rem] mt-12 flex gap-6 items-start">
                                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-500 shrink-0">
                                    <i data-lucide="info" class="w-5 h-5"></i>
                                </div>
                                <div class="space-y-2">
                                    <h4 class="text-[10px] text-emerald-500 font-black uppercase tracking-[0.3em]">Compliance LGPD</h4>
                                    <p class="text-xs text-zinc-600 leading-relaxed font-medium italic">
                                        O NEX SHAPE opera sob conformidade rigorosa com a Lei Federal nº 13.709/2018. Sua privacidade não é apenas um requisito legal, é um pilar da nossa arquitetura.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endauth

                </div>
            </main>
        </div>
    </div>
</div>

<!-- Modal: Account Deletion Request -->
<div id="deleteRequestModal" class="fixed inset-0 z-[500] hidden items-center justify-center p-6 bg-zinc-950/90 backdrop-blur-md animate-fade-in">
    <div class="bg-zinc-900 border border-zinc-800 w-full max-w-md rounded-[3rem] p-10 shadow-4xl space-y-8 relative overflow-hidden">
        <div class="absolute -top-20 -right-20 w-40 h-40 bg-rose-500/10 blur-[60px] rounded-full pointer-events-none"></div>

        <div class="text-center space-y-4">
            <div class="w-20 h-20 bg-rose-500/10 rounded-[1.8rem] flex items-center justify-center mx-auto text-rose-500 mb-6 shadow-2xl transform -rotate-12">
                <i data-lucide="alert-triangle" class="w-10 h-10"></i>
            </div>
            <h3 class="text-3xl font-black text-white italic uppercase tracking-tighter">Protocolo de <span class="text-rose-500">Exclusão</span></h3>
            <p class="text-zinc-500 text-xs font-medium leading-relaxed italic px-4">Este processo é irreversível. Todas as métricas de evolução e conquistas serão permanentemente deletadas.</p>
        </div>

        <form action="{{ route('privacy.request-deletion') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-3">
                <label class="text-[9px] text-zinc-600 font-black uppercase tracking-widest ml-2">Motivo da Solicitação (Opcional)</label>
                <textarea name="reason" rows="3" placeholder="QUAL O MOTIVO DA SUA SAÍDA?" 
                        class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl p-6 text-white text-xs font-bold focus:border-rose-500/50 outline-none transition-all placeholder:text-zinc-800 italic shadow-inner"></textarea>
            </div>
            
            <div class="grid grid-cols-1 gap-3">
                <button type="submit" 
                        class="w-full py-5 bg-rose-600 text-white font-black text-[10px] uppercase tracking-[0.3em] rounded-2xl hover:bg-rose-500 transition-all shadow-xl shadow-rose-500/20 active:scale-95">
                    Confirmar Deleção Permanente
                </button>
                <button type="button" onclick="document.getElementById('deleteRequestModal').classList.add('hidden')" 
                        class="w-full py-5 bg-zinc-950 border border-zinc-800 text-zinc-600 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:text-white transition-all">
                    Cancelar Protocolo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>

<style>
    [x-cloak] { display: none !important; }
    
    /* Hide Global UI Elements */
    .site-header, .site-footer, .topbar, .app-container > aside { display: none !important; }
    
    /* Ensure page takes full space */
    #main.shell.main-content { 
        padding: 0 !important; 
        max-width: 100% !important; 
        margin: 0 !important; 
    }

    .animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    .animate-slide-in-left { animation: slideInLeft 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes slideInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
    
    .shadow-4xl { box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.5); }

    /* Custom Scrollbar for the content area */
    main::-webkit-scrollbar { width: 4px; }
    main::-webkit-scrollbar-track { background: transparent; }
    main::-webkit-scrollbar-thumb { background: #18181b; border-radius: 10px; }
    main::-webkit-scrollbar-thumb:hover { background: #10b981; }
</style>

<!-- Floating Back Button -->
<a href="{{ route('register') }}" 
   onclick="if(window.history.length > 1) { window.history.back(); return false; }"
   class="fixed top-8 left-8 z-[500] w-14 h-14 bg-zinc-900/80 backdrop-blur-2xl border border-zinc-800 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-emerald-500 hover:border-emerald-500/50 transition-all duration-500 group shadow-2xl hover:scale-110 active:scale-95"
   title="Voltar">
    <i data-lucide="arrow-left" class="w-6 h-6 group-hover:-translate-x-1 transition-transform"></i>
</a>
@endsection
