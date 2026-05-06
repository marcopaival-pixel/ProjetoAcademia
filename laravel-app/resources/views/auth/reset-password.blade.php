@extends('layouts.app')

@section('title', 'Nova Chave de Acesso — NexShape')

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
                        <i data-lucide="key-round" class="text-white w-6 h-6"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-white tracking-tighter uppercase">NEX<span class="text-emerald-500">SHAPE</span></span>
                </div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight mb-2 leading-tight">Nova Chave Neural</h1>
                <p class="text-zinc-500 text-lg">Defina sua nova credencial de acesso segura para retomar sua jornada de evolução.</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('password.update') }}" id="resetForm" class="space-y-6 animate-fade-in-up" style="animation-delay: 0.1s" novalidate onsubmit="return validateForm(this)">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- E-mail (readonly) -->
                <div class="space-y-3 group opacity-60">
                    <label for="email" class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em] ml-2 italic">Confirmação Neural</label>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-700">
                            <i data-lucide="mail" class="w-6 h-6"></i>
                        </div>
                        <input id="email" name="email" type="email" readonly value="{{ $email ?? old('email') }}"
                            class="flex-1 bg-transparent border-none text-zinc-400 font-bold text-sm outline-none cursor-default">
                    </div>
                </div>

                <div class="w-full h-px bg-zinc-800/50 my-6"></div>

                <!-- Nova Senha -->
                <div class="space-y-4 group">
                    <label for="password" class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em] ml-2 transition-colors group-focus-within:text-emerald-500 italic">Nova Chave Mestra</label>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-700 transition-all group-focus-within:text-emerald-500 group-focus-within:border-emerald-500/30 group-focus-within:shadow-[0_0_15px_rgba(16,185,129,0.1)] shadow-inner">
                            <i data-lucide="lock" class="w-6 h-6"></i>
                        </div>
                        <div class="relative flex-1">
                            <input id="password" name="password" type="password" required
                                class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl px-6 py-4 pr-14 text-white placeholder:text-zinc-800 outline-none focus:ring-1 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all shadow-inner font-bold text-sm"
                                placeholder="••••••••••••">
                            <button type="button" onclick="togglePass('password', 'eye1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-700 hover:text-white transition-colors focus:outline-none">
                                <i data-lucide="eye" id="eye1" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <p class="text-[9px] text-red-500 font-bold uppercase tracking-widest ml-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar Senha -->
                <div class="space-y-4 group">
                    <label for="password_confirmation" class="text-[10px] font-black text-zinc-600 uppercase tracking-[0.2em] ml-2 transition-colors group-focus-within:text-emerald-500 italic">Sincronização Final</label>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-700 transition-all group-focus-within:text-emerald-500 group-focus-within:border-emerald-500/30 group-focus-within:shadow-[0_0_15px_rgba(16,185,129,0.1)] shadow-inner">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                        <div class="relative flex-1">
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                class="w-full bg-zinc-900 border border-zinc-800 rounded-2xl px-6 py-4 pr-14 text-white placeholder:text-zinc-800 outline-none focus:ring-1 focus:ring-emerald-500/30 focus:border-emerald-500/50 transition-all shadow-inner font-bold text-sm"
                                placeholder="••••••••••••">
                            <button type="button" onclick="togglePass('password_confirmation', 'eye2')" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-700 hover:text-white transition-colors focus:outline-none">
                                <i data-lucide="eye" id="eye2" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" id="submitBtn" class="w-full bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-extrabold py-5 rounded-[2rem] transition-all shadow-xl shadow-emerald-500/10 active:scale-[0.98] flex items-center justify-center gap-3 group">
                        <span id="btnText">REDEFINIR AGORA</span>
                        <i data-lucide="refresh-cw" class="w-5 h-5 transition-transform group-hover:rotate-180 duration-500"></i>
                        <div id="btnLoader" class="hidden animate-spin w-5 h-5 border-2 border-zinc-950 border-t-transparent rounded-full"></div>
                    </button>
                </div>
            </form>

            <!-- Footer Links -->
            <div class="mt-12 text-left animate-fade-in-up" style="animation-delay: 0.2s">
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
        
        <img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&q=80&w=1470" 
             alt="Fitness Focus" 
             class="absolute inset-0 w-full h-full object-cover grayscale opacity-40 mix-blend-luminosity scale-110 animate-slow-zoom">

        <div class="absolute inset-0 z-20 flex flex-col justify-end p-20">
            <div class="max-w-md space-y-8 animate-fade-in-right">
                <div class="inline-flex items-center gap-3 px-4 py-2 bg-emerald-500/10 border border-emerald-500/20 rounded-full backdrop-blur-md">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Acesso Restaurado</span>
                </div>
                
                <h2 class="text-5xl font-black text-white tracking-tighter uppercase italic leading-[0.9]">Retome seu <br><span class="text-emerald-500">Protocolo.</span></h2>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-lg bg-zinc-900 border border-zinc-800 flex items-center justify-center text-emerald-500 shrink-0">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-white uppercase tracking-widest mb-1">Performance Sem Interrupções</h4>
                            <p class="text-xs text-zinc-500 font-medium italic">Sua jornada de evolução continua exatamente de onde você parou.</p>
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
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });

    function togglePass(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(eyeId);
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }

    function validateForm(form) {
        let isValid = true;
        const pass = form.querySelector('#password');
        const confirm = form.querySelector('#password_confirmation');
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoader = document.getElementById('btnLoader');
        const icon = btn.querySelector('[data-lucide="refresh-cw"]');

        if (pass.value.length < 8) {
            isValid = false;
            pass.parentElement.parentElement.classList.add('animate-shake');
            setTimeout(() => pass.parentElement.parentElement.classList.remove('animate-shake'), 400);
            
            window.dispatchEvent(new CustomEvent('toast', { 
                detail: { 
                    message: 'A senha deve ter no mínimo 8 caracteres.', 
                    type: 'error' 
                } 
            }));
            return false;
        }

        if (pass.value !== confirm.value) {
            isValid = false;
            confirm.parentElement.parentElement.classList.add('animate-shake');
            setTimeout(() => confirm.parentElement.parentElement.classList.remove('animate-shake'), 400);
            
            window.dispatchEvent(new CustomEvent('toast', { 
                detail: { 
                    message: 'As senhas não coincidem.', 
                    type: 'error' 
                } 
            }));
            return false;
        }

        if (isValid) {
            btnText.innerText = 'ATUALIZANDO...';
            btnLoader.classList.remove('hidden');
            icon.classList.add('hidden');
            btn.classList.add('opacity-80', 'cursor-not-allowed');
        }

        return isValid;
    }
</script>
@endpush
@endsection
