@extends('layouts.app')

@section('title', 'Acesso Restrito — Academia Digital')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>

<div class="min-h-screen flex bg-zinc-950 font-['Outfit'] selection:bg-emerald-500/30 overflow-hidden">
    <!-- Lado Esquerdo: Formulário -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center px-8 sm:px-16 lg:px-24 py-12 relative z-10 bg-zinc-950">
        <!-- Ambient Background Glows -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[500px] h-[500px] bg-emerald-600/10 rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-[20%] -right-[10%] w-[400px] h-[400px] bg-blue-600/5 rounded-full blur-[100px]"></div>
        </div>

        <div class="max-w-md w-full mx-auto relative">
            <!-- Header -->
            <div class="mb-10 text-left animate-fade-in-up">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <i data-lucide="dumbbell" class="text-white w-6 h-6"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-white tracking-tighter uppercase">NEX<span class="text-emerald-500">SHAPE</span></span>
                </div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight mb-2 leading-tight">Transforme seu corpo com inteligência.</h1>
                <p class="text-zinc-500 text-lg">Acesse sua plataforma personalizada de treino e nutrição.</p>
            </div>

            <!-- Status Messages -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm font-medium flex items-center gap-3 animate-shake">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-sm font-medium animate-shake">
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
            <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-8 animate-fade-in-up" style="animation-delay: 0.1s">
                @csrf
                
                <div class="space-y-3 group">
                    <label for="email" class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em] ml-2 transition-colors group-focus-within:text-emerald-500 italic">Identificação Neural</label>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-700 transition-all group-focus-within:text-emerald-500 group-focus-within:border-emerald-500/30 group-focus-within:shadow-[0_0_15px_rgba(16,185,129,0.1)] shadow-inner">
                            <i data-lucide="mail" class="w-6 h-6"></i>
                        </div>
                        <div class="relative flex-1">
                            <input id="email" name="email" type="email" autocomplete="username" required value="{{ old('email') }}"
                                class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl px-6 py-4 text-white placeholder:text-zinc-800 outline-none focus:ring-1 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all shadow-inner font-bold text-sm"
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
                                class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl px-6 py-4 pr-14 text-white placeholder:text-zinc-800 outline-none focus:ring-1 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all shadow-inner font-bold text-sm"
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

                <button type="submit" id="submitBtn" class="w-full bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-extrabold py-4 rounded-2xl transition-all shadow-lg shadow-emerald-500/20 active:scale-[0.98] flex items-center justify-center gap-2 group">
                    <span id="btnText">ENTRAR NA PLATAFORMA</span>
                    <i data-lucide="arrow-right" class="w-5 h-5 transition-transform group-hover:translate-x-1"></i>
                    <div id="btnLoader" class="hidden animate-spin w-5 h-5 border-2 border-zinc-950 border-t-transparent rounded-full"></div>
                </button>
            </form>

            <!-- Social Login -->
            <div class="mt-8 animate-fade-in-up" style="animation-delay: 0.2s">
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-zinc-800"></div></div>
                    <div class="relative flex justify-center text-xs uppercase font-bold tracking-widest">
                        <span class="bg-zinc-950 px-4 text-zinc-600">Acesso Rápido</span>
                    </div>
                </div>

                <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 px-4 py-4 bg-zinc-900 border border-zinc-800 rounded-2xl hover:bg-zinc-800 transition-all group active:scale-[0.98]">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-5 h-5" alt="Google">
                    <span class="text-sm font-bold text-zinc-400 group-hover:text-white transition-colors">Continuar com Google</span>
                </a>
            </div>

            <!-- Footer Link -->
            <div class="mt-10 text-center animate-fade-in-up" style="animation-delay: 0.3s">
                <p class="text-zinc-500 text-sm font-medium">
                    Ainda não faz parte? 
                    <a href="{{ route('register') }}" class="text-emerald-500 hover:text-emerald-400 font-bold ml-1 transition-colors">Comece agora &rarr;</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Lado Direito: Visual Premium -->
    <div class="hidden lg:block lg:w-1/2 relative overflow-hidden bg-zinc-900">
        <!-- Main Image with Overlay -->
        <div class="absolute inset-0">
            <img src="{{ asset('images/auth-bg.png') }}" class="w-full h-full object-cover opacity-60 scale-105 animate-slow-zoom" alt="Gym Visual">
            <div class="absolute inset-0 bg-gradient-to-tr from-zinc-950 via-zinc-950/40 to-transparent"></div>
        </div>

        <!-- Floating UI Mockups -->
        <div class="relative h-full flex flex-col justify-end p-16">
            <div class="space-y-6 max-w-lg">
                <!-- Benefit Card 1 -->
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-[2rem] flex items-center gap-5 translate-x-12 animate-float shadow-2xl">
                    <div class="w-14 h-14 bg-emerald-500/20 rounded-2xl flex items-center justify-center border border-emerald-500/30">
                        <i data-lucide="activity" class="text-emerald-500 w-7 h-7"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-bold text-lg">Controle de Treinos</h4>
                        <p class="text-zinc-400 text-sm">Visualize sua evolução em tempo real com gráficos inteligentes.</p>
                    </div>
                </div>

                <!-- Benefit Card 2 -->
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 p-6 rounded-[2rem] flex items-center gap-5 -translate-x-6 animate-float-delayed shadow-2xl">
                    <div class="w-14 h-14 bg-blue-500/20 rounded-2xl flex items-center justify-center border border-blue-500/30">
                        <i data-lucide="apple" class="text-blue-500 w-7 h-7"></i>
                    </div>
                    <div>
                        <h4 class="text-white font-bold text-lg">Nutrição Avançada</h4>
                        <p class="text-zinc-400 text-sm">Planos alimentares adaptados ao seu metabolismo e objetivos.</p>
                    </div>
                </div>

                <!-- Tagline -->
                <div class="pt-8 opacity-0 animate-fade-in" style="animation-delay: 0.8s">
                    <p class="text-zinc-500 font-bold uppercase tracking-[0.3em] text-xs mb-4">Parceiros da sua saúde</p>
                    <div class="flex gap-6 grayscale opacity-50">
                        <i data-lucide="shield-check" class="text-white w-6 h-6"></i>
                        <i data-lucide="zap" class="text-white w-6 h-6"></i>
                        <i data-lucide="award" class="text-white w-6 h-6"></i>
                    </div>
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

        btnText.innerText = 'AUTENTICANDO...';
        btnLoader.classList.remove('hidden');
        arrow.classList.add('hidden');
        btn.classList.add('opacity-80', 'cursor-not-allowed');
    });
</script>
@endsection
