@extends('layouts.app')

@section('title', 'Autenticação — NexShape')

@section('content')
<div class="h-screen flex items-start justify-center px-4 sm:px-6 lg:px-8 pt-4 sm:pt-8 lg:pt-12 pb-8 relative animate-fade-in overflow-hidden">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 relative z-10">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-zinc-900/50 border border-white/10 mb-4 shadow-2xl backdrop-blur-xl">
                <i class="fas fa-fingerprint text-2xl text-blue-500"></i>
            </div>
            <h2 class="text-2xl font-black text-white tracking-tight">Bem-vindo de volta</h2>
            <p class="mt-1 text-[11px] text-zinc-500 font-bold uppercase tracking-widest">Acesse sua conta para continuar sua evolução.</p>
        </div>



        @if (session('status'))
            <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold text-center">
                {{ session('status') }}
            </div>
        @endif

        @error('email')
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-xs font-bold text-center">
                {{ $message }}
            </div>
        @enderror

        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-6 sm:p-8 rounded-[2rem] shadow-2xl">
            <form method="post" action="{{ route('login') }}" class="space-y-4" novalidate autocomplete="off" onsubmit="return validateForm(this)">
                @csrf
                <div class="space-y-2">
                    <label for="email" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Identidade (E-mail)</label>
                    <input id="email" name="email" type="email" autocomplete="off" required value="{{ old('email') }}"
                        class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-3 text-white text-xs outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" 
                        placeholder="atleta@nexshape.com">
                </div>
                
                <div class="space-y-2">
                    <label for="password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Chave de Acesso</label>
                    <div class="relative group">
                        <input id="password" name="password" type="password" autocomplete="new-password" required
                            class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-3 pr-12 text-white text-xs outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" 
                            placeholder="••••••••••••">
                        <button type="button" onclick="togglePass()" class="absolute right-4 top-1/2 -translate-y-1/2 text-blue-500 hover:text-blue-400 transition-colors focus:outline-none z-50 px-2" title="Mostrar/Ocultar senha">
                            <i class="fas fa-eye text-lg" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-end relative z-50">
                    <a href="{{ route('password.request') }}" class="text-[10px] text-blue-500 font-bold uppercase tracking-widest hover:text-blue-400 transition-colors">Esqueceu sua senha?</a>
                </div>

                <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-2xl transition-all active:scale-[0.98] shadow-2xl shadow-blue-600/20 uppercase tracking-[0.2em] text-[10px]">
                    Autenticar no Sistema
                </button>

                @if (session('unverified_email'))
                    <div class="pt-4 border-t border-white/5">
                        <p class="text-[11px] text-zinc-500 text-center mb-4 font-semibold">Precisa de um novo link?</p>
                        <form method="post" action="{{ route('verification.resend.guest') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                            <button type="submit" class="w-full py-4 bg-zinc-800 hover:bg-zinc-700 border border-white/10 text-white font-bold rounded-2xl transition-all text-[10px] uppercase tracking-widest">
                                Reenviar confirmação
                            </button>
                        </form>
                    </div>
                @endif
            </form>

            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/5"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-[#0b0e14] text-zinc-600 text-[10px] font-black uppercase tracking-widest rounded-full border border-white/5">Acesso Expresso</span>
                </div>
            </div>

            <div class="flex justify-center">
                <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-white/5 bg-zinc-950/50 rounded-2xl hover:bg-white/5 transition-all group">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-5" alt="Google">
                    <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest group-hover:text-white">Entrar com Google</span>
                </a>
            </div>
        </div>

        <div class="text-center mt-4">
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">
                Ainda não tem acesso? 
                <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-400 ml-1">Cadastre-se &rarr;</a>
            </p>
        </div>
    </div>
</div>

<script>
    function togglePass() {
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');
        if (!passwordInput || !eyeIcon) return;

        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        if (type === 'text') {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    function validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500/50');
            } else {
                field.classList.remove('border-red-500/50');
            }
        });

        if (!isValid) {
            window.dispatchEvent(new CustomEvent('toast', { 
                detail: { 
                    message: 'Preencha sua identidade e chave de acesso.', 
                    type: 'error' 
                } 
            }));
            return false;
        }
        return true;
    }
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; overflow: hidden !important; }
</style>
@endsection
