@extends('layouts.app')

@section('title', 'Nova Chave de Acesso — NexShape')

@section('content')
<div class="min-h-screen flex items-start justify-center px-4 sm:px-6 lg:px-8 pt-10 sm:pt-16 lg:pt-20 pb-12 relative animate-fade-in overflow-hidden">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 relative z-10">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-zinc-900/50 border border-white/10 mb-6 shadow-2xl backdrop-blur-xl">
                <i class="fas fa-key text-3xl text-blue-500"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Nova Senha</h2>
            <p class="mt-2 text-sm text-zinc-500 font-bold uppercase tracking-widest">Defina sua nova chave de acesso segura.</p>
        </div>

        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[2.5rem] shadow-2xl">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-6" novalidate onsubmit="return validateForm(this)">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="space-y-2">
                    <label for="email" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">E-mail de Confirmação</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ $email ?? old('email') }}"
                        class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700 @error('email') border-red-500/50 @enderror" 
                        placeholder="atleta@nexshape.com">
                    @error('email')
                        <p class="text-[10px] text-red-500 font-bold uppercase tracking-widest ml-1 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2 border-t border-white/5 pt-6">
                    <label for="password" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Nova Senha</label>
                    <div class="relative group">
                        <input id="password" name="password" type="password" required
                            class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 pr-12 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" 
                            placeholder="Mínimo 8 caracteres">
                        <button type="button" onclick="togglePass('password', 'eye1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-blue-500 transition-colors focus:outline-none px-2">
                            <i class="fas fa-eye text-lg" id="eye1"></i>
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Confirmar Nova Senha</label>
                    <div class="relative group">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 pr-12 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700" 
                            placeholder="Repita a senha">
                        <button type="button" onclick="togglePass('password_confirmation', 'eye2')" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-blue-500 transition-colors focus:outline-none px-2">
                            <i class="fas fa-eye text-lg" id="eye2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-3xl transition-all active:scale-[0.98] shadow-2xl shadow-blue-600/20 uppercase tracking-[0.2em] text-[10px]">
                    Redefinir Senha agora
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePass(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(eyeId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
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
                    message: 'Preencha todos os campos para redefinir sua senha.', 
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
    body { background-color: #0b0e14; }
</style>
@endsection
