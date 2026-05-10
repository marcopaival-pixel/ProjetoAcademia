@extends('layouts.app')

@section('title', 'Acesso Restrito — Academia Digital')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>

<div class="min-h-screen flex bg-zinc-950 font-['Outfit'] selection:bg-emerald-500/30 overflow-hidden">
    <!-- Lado Esquerdo: Formulário -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 sm:px-16 lg:px-24 py-8 relative z-10 bg-zinc-950">
        <!-- Ambient Background Glows -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[500px] h-[500px] bg-emerald-600/10 rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-[20%] -right-[10%] w-[400px] h-[400px] bg-blue-600/5 rounded-full blur-[100px]"></div>
        </div>

        <div class="max-w-md w-full mx-auto relative">
            <!-- Header -->
            <div class="mb-6 text-left animate-fade-in-up">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <i data-lucide="dumbbell" class="text-white w-6 h-6"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-white tracking-tighter uppercase">NEX<span class="text-emerald-500">SHAPE</span></span>
                </div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight mb-2 leading-tight">Transforme seu corpo com inteligência.</h1>
                <p class="text-zinc-500 text-base">Acesse sua plataforma personalizada de treino e nutrição.</p>
            </div>

            <!-- Status Messages -->
            @if (session('status'))
                <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm font-medium flex items-center gap-3 animate-shake">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-sm font-medium animate-shake">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-center gap-2">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-6 animate-fade-in-up" style="animation-delay: 0.1s">
                @csrf
                
                <div class="space-y-3 group">
                    <label for="email" class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em] ml-2 transition-colors group-focus-within:text-emerald-500 italic">Identificação Neural</label>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-700 transition-all group-focus-within:text-emerald-500 group-focus-within:border-emerald-500/30 group-focus-within:shadow-[0_0_15px_rgba(16,185,129,0.1)] shadow-inner">
                            <i data-lucide="mail" class="w-6 h-6"></i>
                        </div>
                        <div class="relative flex-1">
                            <input id="email" name="email" type="email" autocomplete="username" required value="{{ old('email') }}"
                                class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl px-6 py-3 text-white placeholder:text-zinc-800 outline-none focus:ring-1 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all shadow-inner font-bold text-sm"
                                placeholder="seu@email.com">
                        </div>
                    </div>
                </div>

                <div class="space-y-3 group">
                    <div class="flex items-center justify-between px-2">
                        <label for="password" class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em] transition-colors group-focus-within:text-emerald-500 italic">Chave de Acesso</label>
                        <a href="{{ route('password.request') }}" class="text-[10px] font-black text-emerald-500 hover:text-emerald-400 transition-colors uppercase tracking-widest">Esqueceu?</a>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-700 transition-all group-focus-within:text-emerald-500 group-focus-within:border-emerald-500/30 group-focus-within:shadow-[0_0_15px_rgba(16,185,129,0.1)] shadow-inner">
                            <i data-lucide="lock" class="w-6 h-6"></i>
                        </div>
                        <div class="relative flex-1">
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl px-6 py-3 pr-14 text-white placeholder:text-zinc-800 outline-none focus:ring-1 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all shadow-inner font-bold text-sm"
                                placeholder="••••••••••••">
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-700 hover:text-white transition-colors focus:outline-none">
                                <i data-lucide="eye" id="eyeIcon" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 px-1">
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="remember" class="sr-only peer">
                            <div class="w-5 h-5 bg-zinc-900 border border-zinc-800 rounded-md transition-all peer-checked:bg-emerald-500 peer-checked:border-emerald-500"></div>
                            <i data-lucide="check" class="absolute inset-0 m-auto w-3 h-3 text-white opacity-0 transition-opacity peer-checked:opacity-100"></i>
                        </div>
                        <span class="ml-2 text-sm text-zinc-500 font-medium group-hover:text-zinc-300 transition-colors">Lembrar meu acesso</span>
                    </label>
                </div>

                <button type="submit" id="submitBtn" class="w-full bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-extrabold py-3.5 rounded-2xl transition-all shadow-lg shadow-emerald-500/20 active:scale-[0.98] flex items-center justify-center gap-2 group">
                    <span id="btnText">ENTRAR NA PLATAFORMA</span>
                    <i data-lucide="arrow-right" class="w-5 h-5 transition-transform group-hover:translate-x-1"></i>
                    <div id="btnLoader" class="hidden animate-spin w-5 h-5 border-2 border-zinc-950 border-t-transparent rounded-full"></div>
                </button>
            </form>

            <!-- Social Login -->
            <div class="mt-6 animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="relative mb-4">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-zinc-800"></div></div>
                    <div class="relative flex justify-center text-xs uppercase font-bold tracking-widest">
                        <span class="bg-zinc-950 px-4 text-zinc-600">Acesso Rápido</span>
                    </div>
                </div>

                <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 px-4 py-3.5 bg-zinc-900 border border-zinc-800 rounded-2xl hover:bg-zinc-800 transition-all group active:scale-[0.98]">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-5 h-5" alt="Google">
                    <span class="text-sm font-bold text-zinc-400 group-hover:text-white transition-colors">Continuar com Google</span>
                </a>
            </div>

            <!-- Footer Link -->
            <div class="mt-8 text-center animate-fade-in-up" style="animation-delay: 0.3s">
                <p class="text-zinc-500 text-sm font-medium">
                    Ainda não faz parte? 
                    <a href="{{ route('register') }}" class="text-emerald-500 hover:text-emerald-400 font-bold ml-1 transition-colors">Comece agora &rarr;</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Lado Direito: Visual Premium -->
    <div class="hidden lg:block lg:w-1/2 relative overflow-hidden bg-zinc-950">
        <!-- Main Image with Overlay -->
        <div class="absolute inset-0">
            <img src="{{ asset('images/auth-bg-premium.png') }}" class="w-full h-full object-cover opacity-80 scale-105 animate-slow-zoom" alt="Gym Visual">
            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/20 to-transparent"></div>
            <div class="absolute inset-0 bg-emerald-500/5 mix-blend-overlay"></div>
        </div>

        <!-- Premium UI Overlay -->
        <div class="relative h-full flex flex-col justify-end p-6 xl:p-10">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 w-full items-end max-w-4xl mx-auto xl:mx-0">
                <!-- Card 1: Workout Plan -->
                <div class="bg-zinc-950/20 backdrop-blur-2xl border border-white/5 p-4 rounded-[1.8rem] animate-fade-in-up shadow-xl transition-all hover:bg-zinc-950/40 duration-500 group/card" style="animation-delay: 0.4s">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center border border-emerald-500/20">
                            <i data-lucide="clipboard-list" class="text-emerald-500 w-4 h-4"></i>
                        </div>
                        <span class="text-white font-bold text-xs tracking-tight opacity-70 group-hover/card:opacity-100 transition-opacity">Workout Plan</span>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <p class="text-white font-bold text-[11px] mb-2">Morning Strength</p>
                            <div class="flex flex-wrap gap-1">
                                <span class="text-[8px] text-zinc-400 bg-zinc-900/50 px-1.5 py-0.5 rounded-md">Squats</span>
                                <span class="text-[8px] text-zinc-400 bg-zinc-900/50 px-1.5 py-0.5 rounded-md">Press</span>
                            </div>
                        </div>
                        <div class="h-1 w-full bg-zinc-800/30 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.4)]" style="width: 75%"></div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Calorie Tracking -->
                <div class="bg-zinc-950/20 backdrop-blur-2xl border border-white/5 p-4 rounded-[1.8rem] animate-fade-in-up shadow-xl transition-all hover:bg-zinc-950/40 duration-500 group/card" style="animation-delay: 0.5s">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-orange-500/10 rounded-lg flex items-center justify-center border border-orange-500/20">
                            <i data-lucide="flame" class="text-orange-500 w-4 h-4"></i>
                        </div>
                        <span class="text-white font-bold text-xs tracking-tight opacity-70 group-hover/card:opacity-100 transition-opacity">Calories</span>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-end justify-between">
                            <p class="text-white font-bold text-[11px]">1.850 <span class="text-zinc-500 font-normal">kcal</span></p>
                            <div class="flex items-end gap-0.5 h-6">
                                <div class="w-1 bg-zinc-800 rounded-full h-1/2"></div>
                                <div class="w-1 bg-emerald-500 rounded-full h-full"></div>
                                <div class="w-1 bg-emerald-500 rounded-full h-3/4"></div>
                                <div class="w-1 bg-zinc-800 rounded-full h-1/3"></div>
                            </div>
                        </div>
                        <div class="h-1 w-full bg-zinc-800/30 rounded-full overflow-hidden">
                            <div class="h-full bg-orange-500 rounded-full" style="width: 82%"></div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Performance Chart -->
                <div class="bg-zinc-950/20 backdrop-blur-2xl border border-white/5 p-4 rounded-[1.8rem] animate-fade-in-up shadow-xl transition-all hover:bg-zinc-950/40 duration-500 group/card" style="animation-delay: 0.6s">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center border border-blue-500/20">
                            <i data-lucide="trending-up" class="text-blue-500 w-4 h-4"></i>
                        </div>
                        <span class="text-white font-bold text-xs tracking-tight opacity-70 group-hover/card:opacity-100 transition-opacity">Performance</span>
                    </div>
                    <div class="space-y-3">
                        <div class="relative h-10 w-full opacity-60 group-hover/card:opacity-100 transition-opacity">
                            <svg class="w-full h-full" viewBox="0 0 100 40" preserveAspectRatio="none">
                                <path d="M0 35 L20 30 L40 25 L60 15 L80 10 L100 8" fill="none" stroke="#10b981" stroke-width="2" />
                                <path d="M0 38 L20 32 L40 30 L60 20 L80 15 L100 5" fill="none" stroke="#3b82f6" stroke-width="2" opacity="0.4" />
                            </svg>
                        </div>
                        <div class="flex justify-between text-[7px] text-zinc-600 font-bold uppercase">
                            <span>Jan</span>
                            <span>Jun</span>
                            <span>Dec</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tagline footer on right side -->
            <div class="mt-8 flex items-center justify-between opacity-0 animate-fade-in pointer-events-none" style="animation-delay: 1.2s">
                <div class="flex gap-4 grayscale opacity-10">
                    <i data-lucide="shield-check" class="text-white w-4 h-4"></i>
                    <i data-lucide="zap" class="text-white w-4 h-4"></i>
                    <i data-lucide="award" class="text-white w-4 h-4"></i>
                </div>
                <div class="text-right flex flex-col items-end opacity-20">
                    <span class="text-white font-black text-sm tracking-tighter uppercase italic">NEXSHAPE</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes float {
        0%, 100% { transform: translateX(48px) translateY(0); }
        50% { transform: translateX(48px) translateY(-10px); }
    }
    @keyframes floatDelayed {
        0%, 100% { transform: translateX(-24px) translateY(0); }
        50% { transform: translateX(-24px) translateY(-10px); }
    }
    @keyframes slowZoom {
        0% { transform: scale(1.05); }
        100% { transform: scale(1.15); }
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-float { animation: float 6s ease-in-out infinite; }
    .animate-float-delayed { animation: floatDelayed 7s ease-in-out infinite; }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-shake { animation: shake 0.4s ease-in-out 2; }
    
    input:focus {
        background-color: #18181b !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();

        // Lógica de "Lembrar meu acesso" (E-mail)
        const emailInput = document.getElementById('email');
        const rememberCheckbox = document.querySelector('input[name="remember"]');
        const savedEmail = localStorage.getItem('nexshape_remember_email');

        if (savedEmail) {
            emailInput.value = savedEmail;
            rememberCheckbox.checked = true;
        }
    });

    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }

    const form = document.getElementById('loginForm');
    form.addEventListener('submit', (e) => {
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoader = document.getElementById('btnLoader');
        const arrow = btn.querySelector('[data-lucide="arrow-right"]');
        
        const emailInput = document.getElementById('email');
        const rememberCheckbox = document.querySelector('input[name="remember"]');

        // Persistir e-mail se solicitado
        if (rememberCheckbox.checked) {
            localStorage.setItem('nexshape_remember_email', emailInput.value);
        } else {
            localStorage.removeItem('nexshape_remember_email');
        }

        btnText.innerText = 'AUTENTICANDO...';
        btnLoader.classList.remove('hidden');
        arrow.classList.add('hidden');
        btn.classList.add('opacity-80', 'cursor-not-allowed');
    });
</script>
@endsection
