@extends('layouts.app')

@section('title', 'Recuperação de Acesso — NexShape')

@section('content')
<div class="min-h-screen flex items-start justify-center px-4 sm:px-6 lg:px-8 pt-10 sm:pt-16 lg:pt-20 pb-12 relative animate-fade-in overflow-hidden">
    <!-- Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-600/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 relative z-10">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-zinc-900/50 border border-white/10 mb-6 shadow-2xl backdrop-blur-xl">
                <i class="fas fa-shield-alt text-3xl text-blue-500"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Recuperar Senha</h2>
            <p class="mt-2 text-sm text-zinc-500 font-bold uppercase tracking-widest px-4 text-center">Informe seu e-mail e enviaremos um link para você criar uma nova senha.</p>
        </div>

        <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[2.5rem] shadow-2xl">
            @if (session('status'))
                <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-500 text-xs font-bold uppercase tracking-widest text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6" novalidate onsubmit="return validateForm(this)">
                @csrf
                <div class="space-y-2">
                    <label for="email" class="text-[10px] text-zinc-500 font-black uppercase tracking-widest ml-1">Seu E-mail Cadastrado</label>
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                        class="w-full bg-zinc-950/50 border border-white/5 rounded-2xl px-5 py-4 text-white text-sm outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all placeholder:text-zinc-700 @error('email') border-red-500/50 @enderror" 
                        placeholder="atleta@nexshape.com">
                    @error('email')
                        <p class="text-[10px] text-red-500 font-bold uppercase tracking-widest ml-1 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full py-5 bg-blue-600 hover:bg-blue-500 text-white font-black rounded-3xl transition-all active:scale-[0.98] shadow-2xl shadow-blue-600/20 uppercase tracking-[0.2em] text-[10px]">
                    Enviar Link de Recuperação
                </button>
            </form>
        </div>

        <div class="text-center mt-8">
            <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest">
                Lembrou a senha? 
                <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-400 ml-1">Voltar ao Login</a>
            </p>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
</style>
@endsection

@push('scripts')
<script>
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
                    message: 'Informe seu e-mail para receber o link de recuperação.', 
                    type: 'error' 
                } 
            }));
            return false;
        }
        return true;
    }
</script>
@endpush
