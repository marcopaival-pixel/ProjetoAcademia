@extends('layouts.app')

@section('title', 'Recuperação de Acesso — NexShape')

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
                        <i data-lucide="shield-check" class="text-white w-6 h-6"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-white tracking-tighter uppercase">NEX<span class="text-emerald-500">SHAPE</span></span>
                </div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight mb-2 leading-tight">Esqueceu sua chave?</h1>
                <p class="text-zinc-500 text-lg">Sem problemas. Informe seu e-mail e iniciaremos o protocolo de restauração.</p>
            </div>

            <!-- Status Messages -->
            @if (session('status'))
                <div class="mb-8 p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-[2rem] text-emerald-400 text-sm font-medium flex items-center gap-4 animate-shake shadow-2xl">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center shrink-0">
                        <i data-lucide="mail-check" class="w-5 h-5"></i>
                    </div>
                    <p class="leading-tight">{{ session('status') }}</p>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('password.email') }}" id="resetForm" class="space-y-8 animate-fade-in-up" style="animation-delay: 0.1s" novalidate onsubmit="return validateForm(this)">
                @csrf
                
                <div class="space-y-3 group">
                    <label for="email" class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em] ml-2 transition-colors group-focus-within:text-emerald-500 italic">Identificação Neural</label>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-700 transition-all group-focus-within:text-emerald-500 group-focus-within:border-emerald-500/30 group-focus-within:shadow-[0_0_15px_rgba(16,185,129,0.1)] shadow-inner">
                            <i data-lucide="mail" class="w-6 h-6"></i>
                        </div>
                        <div class="relative flex-1">
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                                class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl px-6 py-4 text-white placeholder:text-zinc-800 outline-none focus:ring-1 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all shadow-inner font-bold text-sm @error('email') border-red-500/50 @enderror" 
                                placeholder="seu@email.com">
                            @error('email')
                                <div class="absolute -bottom-6 left-2 flex items-center gap-1 text-red-500 text-[9px] font-bold uppercase tracking-widest animate-fade-in">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="submitBtn" class="w-full bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-extrabold py-5 rounded-[2rem] transition-all shadow-xl shadow-emerald-500/10 active:scale-[0.98] flex items-center justify-center gap-3 group">
                        <span id="btnText">ENVIAR PROTOCOLO</span>
                        <i data-lucide="send" class="w-5 h-5 transition-transform group-hover:translate-x-1 group-hover:-translate-y-1"></i>
                        <div id="btnLoader" class="hidden animate-spin w-5 h-5 border-2 border-zinc-950 border-t-transparent rounded-full"></div>
                    </button>
                </div>
            </form>

            <!-- Footer Links -->
            <div class="mt-12 text-left space-y-6 animate-fade-in-up" style="animation-delay: 0.2s">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-[10px] font-black text-zinc-600 hover:text-white transition-all uppercase tracking-[0.2em] group">
                    <i data-lucide="arrow-left" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
                    Voltar ao Portal de Acesso
                </a>
            </div>
        </div>
    </div>

    <!-- Lado Direito: Imagem/Conteúdo Premium -->
    <div class="hidden lg:block lg:w-1/2 relative overflow-hidden bg-zinc-900">
        <div class="absolute inset-0 z-10 bg-gradient-to-r from-zinc-950 via-transparent to-transparent opacity-60"></div>
        <div class="absolute inset-0 z-10 bg-gradient-to-t from-zinc-950 via-transparent to-transparent opacity-40"></div>
        
        <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&q=80&w=1470" 
             alt="Fitness Performance" 
             class="absolute inset-0 w-full h-full object-cover grayscale opacity-40 mix-blend-luminosity scale-110 animate-slow-zoom">

        <div class="absolute inset-0 z-20 flex flex-col justify-end p-20">
            <div class="max-w-md space-y-8 animate-fade-in-right">
                <div class="inline-flex items-center gap-3 px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-full backdrop-blur-md">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Segurança NEX SHAPE</span>
                </div>
                
                <h2 class="text-5xl font-black text-white tracking-tighter uppercase italic leading-[0.9]">Proteja sua <br><span class="text-emerald-500">Evolução.</span></h2>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-lg bg-zinc-900 border border-zinc-800 flex items-center justify-center text-emerald-500 shrink-0">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Criptografia de Ponta</h4>
                            <p class="text-xs text-zinc-500 font-medium italic">Seus dados biológicos e treinos são protegidos por camadas neurais de segurança.</p>
                        </div>
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
    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(30px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slowZoom {
        from { transform: scale(1); }
        to { transform: scale(1.1); }
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-fade-in-right { animation: fadeInRight 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .animate-slow-zoom { animation: slowZoom 20s linear infinite alternate; }
    .animate-shake { animation: shake 0.4s ease-in-out 2; }
    .shadow-3xl { box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.5); }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });

    function validateForm(form) {
        let isValid = true;
        const email = form.querySelector('#email');
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoader = document.getElementById('btnLoader');
        const icon = btn.querySelector('[data-lucide="send"]');

        if (!email.value.trim() || !email.value.includes('@')) {
            isValid = false;
            email.parentElement.parentElement.classList.add('animate-shake');
            setTimeout(() => email.parentElement.parentElement.classList.remove('animate-shake'), 400);
            
            window.dispatchEvent(new CustomEvent('toast', { 
                detail: { 
                    message: 'Informe um e-mail válido para continuar.', 
                    type: 'error' 
                } 
            }));
            return false;
        }

        if (isValid) {
            btnText.innerText = 'ENVIANDO...';
            btnLoader.classList.remove('hidden');
            icon.classList.add('hidden');
            btn.classList.add('opacity-80', 'cursor-not-allowed');
        }

        return isValid;
    }
</script>
@endpush
@endsection
