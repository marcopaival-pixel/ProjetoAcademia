@php
    $enabled = \App\Models\SystemSetting::isTrue('app_banner_enabled', false);
    if (!$enabled) return;

    $allowedRoles = json_decode(\App\Models\SystemSetting::get('app_banner_roles', '[]'), true);
    $user = auth()->user();
    
    // Se houver roles definidos, verifica se o usuário tem algum deles
    if (!empty($allowedRoles) && $user) {
        $userRoleIds = $user->roles()->pluck('roles.id')->toArray();
        $hasAccess = !empty(array_intersect($userRoleIds, $allowedRoles));
        if (!$hasAccess) return;
    } elseif (empty($allowedRoles)) {
        // Se não houver roles definidos, por segurança não exibe (ou exibe para todos? O usuário disse 'escolher em qual painel', então se não escolheu nenhum, melhor ocultar)
        return;
    }

    $title = \App\Models\SystemSetting::get('app_banner_title', '🚀 Em breve: Aplicativo Oficial do NexShape');
    $description = \App\Models\SystemSetting::get('app_banner_description', 'Tenha seus treinos, dieta, consultas, agenda, evolução e inteligência artificial na palma da sua mão.');
    $image = \App\Models\SystemSetting::get('app_banner_image', '');
    $launchDate = \App\Models\SystemSetting::get('app_banner_launch_date', '');
    $googlePlay = \App\Models\SystemSetting::get('app_banner_google_play_link', '#');
    $appleStore = \App\Models\SystemSetting::get('app_banner_apple_store_link', '#');
@endphp

<div x-data="{ 
    showBanner: !localStorage.getItem('hide_app_banner'),
    showModal: false,
    name: '{{ addslashes(auth()->user()?->name ?? "") }}',
    email: '{{ addslashes(auth()->user()?->email ?? "") }}',
    loading: false,
    success: false,
    track(event) {
        fetch('{{ route('api.marketing.app-banner.metric') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ event_type: event })
        });
    },
    dismiss() {
        this.showBanner = false;
        localStorage.setItem('hide_app_banner', 'true');
        this.track('banner_dismiss');
    },
    submitLead() {
        this.loading = true;
        fetch('{{ route('api.marketing.app-banner.lead') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ name: this.name, email: this.email })
        })
        .then(res => res.json())
        .then(data => {
            this.success = true;
            this.loading = false;
            setTimeout(() => { this.showModal = false; this.dismiss(); }, 3000);
        });
    }
}" 
x-show="showBanner"
x-init="track('view')"
x-transition:enter="transition ease-out duration-500"
x-transition:enter-start="opacity-0 transform -translate-y-4"
x-transition:enter-end="opacity-100 transform translate-y-0"
class="relative group mb-10 overflow-hidden rounded-[2.5rem] bg-zinc-950 p-[2px] shadow-[0_0_50px_-12px_rgba(16,185,129,0.3)] animate-fade-in-up"
style="animation-delay: 100ms">

    <!-- Animated Border Gradient -->
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 via-blue-500 to-emerald-500 opacity-20 group-hover:opacity-100 transition-opacity duration-1000 animate-spin-slow" style="border-radius: inherit; margin: -100%;"></div>

    <div class="relative w-full h-full bg-zinc-900 rounded-[2.4rem] overflow-hidden">
        <!-- Shimmer Effect -->
        <div class="absolute inset-0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-[1.5s] ease-in-out bg-gradient-to-r from-transparent via-white/5 to-transparent skew-x-[-20deg] pointer-events-none"></div>

        <!-- Floating Particles -->
        <div class="absolute top-10 left-1/4 w-1 h-1 bg-emerald-400 rounded-full animate-ping opacity-20"></div>
        <div class="absolute bottom-10 right-1/3 w-1.5 h-1.5 bg-blue-400 rounded-full animate-pulse opacity-10" style="animation-delay: 1s"></div>
        <div class="absolute top-1/2 right-1/4 w-1 h-1 bg-white rounded-full animate-bounce opacity-20" style="animation-delay: 1.5s"></div>

        <!-- Background Design -->
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 via-zinc-900 to-zinc-950 pointer-events-none"></div>
    @if($image)
        <img src="{{ $image }}" class="absolute inset-0 w-full h-full object-cover opacity-30 group-hover:scale-105 transition-transform duration-[5s]">
    @endif
    
    <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-[100px]"></div>

    <div class="relative z-10 flex flex-col lg:flex-row items-center gap-8 p-8 lg:p-10">
        <!-- Icon/Mockup Area -->
        <div class="relative w-24 h-24 lg:w-32 lg:h-32 flex items-center justify-center shrink-0">
            <div class="absolute inset-0 bg-emerald-500/20 rounded-3xl blur-2xl animate-pulse-slow"></div>
            <div class="relative w-full h-full bg-zinc-950/80 backdrop-blur-xl border border-emerald-500/30 rounded-3xl flex items-center justify-center shadow-inner">
                <i data-lucide="smartphone" class="w-10 h-10 lg:w-14 lg:h-14 text-emerald-400"></i>
                <div class="absolute -top-2 -right-2 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center text-zinc-950 shadow-lg">
                    <i data-lucide="zap" class="w-3 h-3 fill-current"></i>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="flex-1 text-center lg:text-left space-y-4">
            <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                <h3 class="text-2xl lg:text-3xl font-black text-white italic tracking-tighter uppercase leading-none">
                    {{ $title }}
                </h3>
                @if($launchDate)
                <div x-data="{ 
                    days: 0, hours: 0, mins: 0,
                    init() {
                        const target = new Date('{{ $launchDate }}').getTime();
                        setInterval(() => {
                            const now = new Date().getTime();
                            const diff = target - now;
                            if (diff > 0) {
                                this.days = Math.floor(diff / (1000 * 60 * 60 * 24));
                                this.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                                this.mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                            }
                        }, 1000);
                    }
                }" class="inline-flex items-center gap-3 px-4 py-1.5 bg-zinc-950/50 border border-white/5 rounded-full">
                    <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">Lançamento em:</span>
                    <span class="text-[11px] text-emerald-400 font-black tabular-nums">
                        <span x-text="days">0</span>d <span x-text="hours">0</span>h <span x-text="mins">0</span>m
                    </span>
                </div>
                @endif
            </div>
            <p class="text-zinc-400 text-sm lg:text-base font-medium leading-relaxed max-w-2xl">
                {{ $description }}
            </p>
            
            <div class="flex flex-wrap items-center justify-center lg:justify-start gap-6 pt-2">
                <div class="flex items-center gap-4 text-zinc-500">
                    <span class="text-[10px] font-black uppercase tracking-widest italic">Disponível em breve:</span>
                    <div class="flex items-center gap-3">
                        <i data-lucide="play-circle" class="w-5 h-5 hover:text-emerald-400 transition-colors cursor-pointer" @click="track('click_google_play')"></i>
                        <i data-lucide="apple" class="w-5 h-5 hover:text-white transition-colors cursor-pointer" @click="track('click_app_store')"></i>
                    </div>
                </div>
                <div class="h-4 w-px bg-zinc-800 hidden lg:block"></div>
                <div class="flex items-center gap-3">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" class="h-6 opacity-40 hover:opacity-100 transition-all cursor-pointer" @click="track('click_google_play')">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/3/3c/Download_on_the_App_Store_Badge.svg" class="h-6 opacity-40 hover:opacity-100 transition-all cursor-pointer" @click="track('click_app_store')">
                </div>
            </div>
        </div>

        <!-- Action Area -->
        <div class="shrink-0 flex flex-col items-center gap-4">
            <button @click="showModal = true; track('modal_open')" class="px-10 py-5 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-black rounded-2xl transition-all shadow-2xl shadow-emerald-500/20 text-xs uppercase tracking-[0.2em] group/btn animate-pulse hover:animate-none">
                Quero ser avisado
                <i data-lucide="arrow-right" class="inline-block w-4 h-4 ml-2 group-hover/btn:translate-x-1 transition-transform"></i>
            </button>
        </div>

        <!-- Close Button -->
        <button @click="dismiss()" class="absolute top-6 right-6 w-10 h-10 bg-zinc-950/50 hover:bg-zinc-800 text-zinc-500 hover:text-white rounded-full flex items-center justify-center transition-all border border-white/5">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Modal Lead Form -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="showModal = false" class="relative w-full max-w-lg bg-zinc-900 border border-emerald-500/20 rounded-[3rem] p-10 shadow-2xl animate-fade-in-up">
            <button @click="showModal = false" class="absolute top-8 right-8 text-zinc-600 hover:text-white transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>

            <div class="text-center space-y-6">
                <div class="w-20 h-20 bg-emerald-500/10 rounded-[2rem] flex items-center justify-center mx-auto border border-emerald-500/20 mb-8">
                    <i data-lucide="bell-ring" class="w-10 h-10 text-emerald-500"></i>
                </div>
                
                <h3 class="text-3xl font-black text-white italic tracking-tighter uppercase">Fique por dentro!</h3>
                <p class="text-zinc-400 text-sm font-medium">Cadastre-se abaixo e receba acesso antecipado e benefícios exclusivos no lançamento do App NexShape.</p>

                <div x-show="!success" class="space-y-4 pt-6">
                    <div class="space-y-2 text-left">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest px-4">Nome Completo</label>
                        <input type="text" x-model="name" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm outline-none focus:border-emerald-500/30 transition-all" placeholder="Seu nome">
                    </div>
                    <div class="space-y-2 text-left">
                        <label class="text-[10px] text-zinc-600 font-black uppercase tracking-widest px-4">E-mail de Contato</label>
                        <input type="email" x-model="email" class="w-full bg-zinc-950 border border-white/5 rounded-2xl px-6 py-4 text-white text-sm outline-none focus:border-emerald-500/30 transition-all" placeholder="seu@email.com">
                    </div>
                    
                    <button @click="submitLead()" :disabled="loading" class="w-full py-5 bg-emerald-600 hover:bg-emerald-500 text-zinc-950 font-black rounded-2xl transition-all shadow-2xl shadow-emerald-500/20 text-xs uppercase tracking-[0.2em] flex items-center justify-center gap-3">
                        <span x-show="!loading">Confirmar Interesse</span>
                        <span x-show="loading" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-zinc-950" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Processando...
                        </span>
                    </button>
                </div>

                <div x-show="success" class="pt-10 animate-fade-in">
                    <div class="w-16 h-16 bg-emerald-500 text-zinc-950 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl shadow-emerald-500/20">
                        <i data-lucide="check" class="w-8 h-8"></i>
                    </div>
                    <h4 class="text-xl font-black text-white uppercase tracking-tight">Sucesso!</h4>
                    <p class="text-zinc-500 text-xs mt-2">Você foi adicionado à nossa lista VIP. Em breve entraremos em contato.</p>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<style>
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin-slow {
        animation: spin-slow 8s linear infinite;
    }
    @keyframes pulse-slow {
        0%, 100% { opacity: 0.3; transform: scale(1); }
        50% { opacity: 0.6; transform: scale(1.1); }
    }
    .animate-pulse-slow {
        animation: pulse-slow 3s ease-in-out infinite;
    }
</style>
