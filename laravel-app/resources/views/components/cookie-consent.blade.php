<!-- Componente de Consentimento de Cookies (Alpine.js + Tailwind CSS) -->
<div 
    x-data="cookieConsent()" 
    x-init="init()"
    x-cloak
    x-show="hasLoaded"
    class="relative z-[9999]"
>
    <!-- Overlay Backdrop para quando as preferências estão abertas -->
    <div 
        x-show="showPreferences && isVisible" 
        x-transition:enter="transition-opacity duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/40 backdrop-blur-sm"
    ></div>

    <!-- Container Principal -->
    <div 
        x-show="isVisible"
        x-transition:enter="transition-all duration-700 ease-out transform"
        x-transition:enter-start="translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition-all duration-500 ease-in transform"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0"
        class="fixed bottom-0 left-0 right-0 p-4 sm:p-6 md:p-8 pointer-events-none"
    >
        <div class="max-w-[1200px] mx-auto pointer-events-auto">
            <div 
                class="bg-zinc-950/80 backdrop-blur-2xl border border-zinc-800/60 shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.5)] rounded-2xl sm:rounded-3xl overflow-hidden transition-all duration-500 ease-[cubic-bezier(0.23,1,0.32,1)]"
                :class="showPreferences ? 'max-w-3xl mx-auto' : 'max-w-4xl mx-auto md:flex md:items-center md:justify-between'"
            >
                
                <!-- Visão Simplificada (Banner Inicial) -->
                <div x-show="!showPreferences" class="p-6 md:p-8 flex flex-col md:flex-row gap-6 md:gap-8 items-start md:items-center w-full">
                    <div class="flex-1 flex gap-5 items-start">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-900/80 border border-zinc-800 flex items-center justify-center shrink-0 shadow-inner">
                            <i data-lucide="shield-check" class="w-5 h-5 text-emerald-500"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white tracking-tight mb-2">Sua Privacidade</h3>
                            <p class="text-sm text-zinc-400 leading-relaxed font-medium">
                                Utilizamos cookies e tecnologias similares para otimizar sua experiência, analisar o tráfego e personalizar conteúdo. 
                                Ao continuar, você concorda com nossa 
                                <a href="{{ route('legal.privacy') }}" class="text-emerald-400 hover:text-emerald-300 underline underline-offset-2 transition-colors">Política de Privacidade</a>
                                e nossa
                                <a href="{{ route('legal.cookies') }}" class="text-emerald-400 hover:text-emerald-300 underline underline-offset-2 transition-colors">Política de Cookies</a>.
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto shrink-0">
                        <button 
                            @click="showPreferences = true"
                            class="px-5 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-300 text-sm font-semibold border border-zinc-800/50 hover:border-zinc-700 transition-all flex items-center justify-center gap-2 group"
                        >
                            <i data-lucide="settings" class="w-4 h-4"></i>
                            <span>Preferências</span>
                        </button>
                        <button 
                            @click="handleRejectAll()"
                            class="px-5 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-300 text-sm font-semibold border border-zinc-800/50 hover:border-zinc-700 transition-all"
                        >
                            Recusar
                        </button>
                        <button 
                            @click="handleAcceptAll()"
                            class="px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-zinc-950 text-sm font-bold shadow-[0_0_20px_-5px_rgba(16,185,129,0.4)] transition-all active:scale-[0.98]"
                        >
                            Aceitar Todos
                        </button>
                    </div>
                </div>

                <!-- Visão de Preferências Avançadas -->
                <div x-show="showPreferences" class="flex flex-col max-h-[80vh]" style="display: none;">
                    <div class="p-6 md:p-8 border-b border-zinc-800/60 flex items-start gap-5">
                        <div class="w-12 h-12 rounded-2xl bg-zinc-900/80 border border-zinc-800 flex items-center justify-center shrink-0">
                            <i data-lucide="settings" class="w-5 h-5 text-zinc-300"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white tracking-tight mb-2">Preferências de Cookies</h3>
                            <p class="text-sm text-zinc-400 leading-relaxed font-medium">
                                Gerencie como utilizamos seus dados. Cookies essenciais são necessários para o funcionamento básico da plataforma.
                            </p>
                        </div>
                    </div>

                    <div class="p-6 md:p-8 space-y-6 overflow-y-auto custom-scrollbar">
                        <!-- Essenciais -->
                        <div class="flex items-start gap-4 p-4 rounded-2xl border border-zinc-800/40 bg-zinc-900/30 hover:bg-zinc-900/50 transition-colors">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <h4 class="text-base font-semibold text-zinc-200">Essenciais (Estritamente Necessários)</h4>
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-zinc-800 text-zinc-400">Sempre Ativo</span>
                                </div>
                                <p class="text-sm text-zinc-500 font-medium leading-relaxed">Necessários para o funcionamento da plataforma, segurança, login e persistência de sessão. Não podem ser desativados.</p>
                            </div>
                            <button type="button" disabled class="relative inline-flex h-6 w-11 shrink-0 cursor-not-allowed rounded-full border-2 border-transparent bg-emerald-500 opacity-60">
                                <span class="translate-x-5 inline-block h-5 w-5 transform rounded-full bg-white shadow flex items-center justify-center">
                                    <i data-lucide="check" class="w-3 h-3 text-emerald-500"></i>
                                </span>
                            </button>
                        </div>

                        <!-- Analytics -->
                        <div class="flex items-start gap-4 p-4 rounded-2xl border border-zinc-800/40 bg-zinc-900/30 hover:bg-zinc-900/50 transition-colors">
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-zinc-200 mb-1.5">Analytics e Desempenho</h4>
                                <p class="text-sm text-zinc-500 font-medium leading-relaxed">Ajudam-nos a entender como os visitantes interagem com o site, coletando e relatando informações anonimamente.</p>
                            </div>
                            <button 
                                type="button" 
                                @click="preferences.analytics = !preferences.analytics"
                                :class="preferences.analytics ? 'bg-emerald-500' : 'bg-zinc-700'"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 hover:scale-105 active:scale-95"
                            >
                                <span 
                                    :class="preferences.analytics ? 'translate-x-5' : 'translate-x-0'"
                                    class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-300 ease-in-out flex items-center justify-center"
                                >
                                    <i x-show="preferences.analytics" data-lucide="check" class="w-3 h-3 text-emerald-500"></i>
                                </span>
                            </button>
                        </div>

                        <!-- Marketing -->
                        <div class="flex items-start gap-4 p-4 rounded-2xl border border-zinc-800/40 bg-zinc-900/30 hover:bg-zinc-900/50 transition-colors">
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-zinc-200 mb-1.5">Marketing e Publicidade</h4>
                                <p class="text-sm text-zinc-500 font-medium leading-relaxed">Utilizados para rastrear visitantes em diferentes sites para exibir anúncios relevantes e engajadores.</p>
                            </div>
                            <button 
                                type="button" 
                                @click="preferences.marketing = !preferences.marketing"
                                :class="preferences.marketing ? 'bg-emerald-500' : 'bg-zinc-700'"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 hover:scale-105 active:scale-95"
                            >
                                <span 
                                    :class="preferences.marketing ? 'translate-x-5' : 'translate-x-0'"
                                    class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-300 ease-in-out flex items-center justify-center"
                                >
                                    <i x-show="preferences.marketing" data-lucide="check" class="w-3 h-3 text-emerald-500"></i>
                                </span>
                            </button>
                        </div>

                        <!-- Preferências -->
                        <div class="flex items-start gap-4 p-4 rounded-2xl border border-zinc-800/40 bg-zinc-900/30 hover:bg-zinc-900/50 transition-colors">
                            <div class="flex-1">
                                <h4 class="text-base font-semibold text-zinc-200 mb-1.5">Preferências e Personalização</h4>
                                <p class="text-sm text-zinc-500 font-medium leading-relaxed">Permitem que o site lembre de informações que mudam a forma como o site se comporta ou se parece.</p>
                            </div>
                            <button 
                                type="button" 
                                @click="preferences.preferences = !preferences.preferences"
                                :class="preferences.preferences ? 'bg-emerald-500' : 'bg-zinc-700'"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 hover:scale-105 active:scale-95"
                            >
                                <span 
                                    :class="preferences.preferences ? 'translate-x-5' : 'translate-x-0'"
                                    class="inline-block h-5 w-5 transform rounded-full bg-white shadow transition duration-300 ease-in-out flex items-center justify-center"
                                >
                                    <i x-show="preferences.preferences" data-lucide="check" class="w-3 h-3 text-emerald-500"></i>
                                </span>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 md:p-8 border-t border-zinc-800/60 bg-zinc-900/20 flex flex-col sm:flex-row gap-3 justify-end">
                        <button 
                            @click="handleRejectAll()"
                            class="px-5 py-3 rounded-xl bg-transparent hover:bg-zinc-900 text-zinc-400 hover:text-zinc-300 text-sm font-semibold transition-all"
                        >
                            Recusar Todos
                        </button>
                        <button 
                            @click="handleAcceptAll()"
                            class="px-5 py-3 rounded-xl bg-zinc-900 hover:bg-zinc-800 text-zinc-300 text-sm font-semibold border border-zinc-800 hover:border-zinc-700 transition-all"
                        >
                            Aceitar Todos
                        </button>
                        <button 
                            @click="handleSavePreferences()"
                            class="px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-zinc-950 text-sm font-bold shadow-[0_0_20px_-5px_rgba(16,185,129,0.4)] transition-all active:scale-[0.98]"
                        >
                            Salvar Minhas Escolhas
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('cookieConsent', () => ({
            isVisible: false,
            showPreferences: false,
            hasLoaded: false,
            consentUrl: @json(route('legal.cookie-consent')),
            preferences: {
                essential: true,
                analytics: false,
                marketing: false,
                preferences: false
            },

            init() {
                setTimeout(() => {
                    this.hasLoaded = true;
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }, 500);

                try {
                    const stored = localStorage.getItem('nexshape_cookie_consent');
                    if (stored) {
                        this.preferences = JSON.parse(stored);
                        this.applyPreferences();
                    } else {
                        this.isVisible = true;
                    }
                } catch (e) {
                    this.isVisible = true;
                }
            },

            csrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.content || '';
            },

            async persistToServer(newPrefs) {
                try {
                    await fetch(this.consentUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken(),
                        },
                        body: JSON.stringify({
                            analytics: !!newPrefs.analytics,
                            marketing: !!newPrefs.marketing,
                            preferences: !!newPrefs.preferences,
                        }),
                    });
                } catch (e) {
                    console.error('Erro ao registrar consentimento no servidor', e);
                }
            },

            savePreferences(newPrefs) {
                try {
                    localStorage.setItem('nexshape_cookie_consent', JSON.stringify(newPrefs));
                    this.preferences = newPrefs;
                    this.isVisible = false;
                    this.showPreferences = false;
                    this.persistToServer(newPrefs);
                    this.applyPreferences();
                } catch (e) {
                    console.error('Erro ao salvar consentimento', e);
                }
            },

            handleAcceptAll() {
                this.savePreferences({
                    essential: true,
                    analytics: true,
                    marketing: true,
                    preferences: true
                });
            },

            handleRejectAll() {
                this.savePreferences({
                    essential: true,
                    analytics: false,
                    marketing: false,
                    preferences: false
                });
            },

            handleSavePreferences() {
                this.savePreferences(this.preferences);
            },

            applyPreferences() {
                window.__nexshapeCookieConsent = { ...this.preferences };
                window.dispatchEvent(new CustomEvent('cookie-consent-updated', {
                    detail: this.preferences
                }));
            }
        }));
    });
</script>
