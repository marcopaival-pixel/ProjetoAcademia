@extends('layouts.onboarding')

@section('title', 'Passo 7: Conta — NexShape')
@section('step_number', '07/08')
@section('back_route', route('onboarding.step6'))

@section('onboarding_content')
<div class="space-y-10 animate-fade-up">
    <header class="space-y-4">
        <h2 class="text-4xl font-black text-white tracking-tight leading-tight">Quase lá! Crie sua conta.</h2>
        <p class="text-zinc-400 text-base font-medium">Salve seu progresso e acesse seu plano de qualquer lugar.</p>
    </header>

    <form action="{{ route('onboarding.step7.save') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="space-y-8">
            <!-- Email -->
            <div class="space-y-3">
                <label for="email" class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">E-mail</label>
                <div class="relative group">
                    <input type="email" name="email" id="email" required placeholder="seu@email.com"
                        class="w-full bg-white/5 border-b-2 border-white/10 py-4 px-0 text-xl font-bold text-white focus:outline-none focus:border-blue-500 transition-all">
                    <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-500 group-focus-within:w-full transition-all duration-500"></div>
                </div>
            </div>

            <!-- Senha -->
            <div class="space-y-3">
                <label for="password" class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Escolha uma Senha Forte</label>
                <div class="relative group">
                    <input type="password" name="password" id="password" required placeholder="••••••••"
                        class="w-full bg-white/5 border-b-2 border-white/10 py-4 px-0 text-xl font-bold text-white focus:outline-none focus:border-emerald-500 transition-all">
                    <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-500 group-focus-within:w-full transition-all duration-500"></div>
                </div>
            </div>
        </div>

        <div class="pt-8">
            <button type="submit" class="w-full py-5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 transition-all shadow-[0_0_30px_rgba(37,99,235,0.3)] hover:shadow-[0_0_50px_rgba(37,99,235,0.5)] uppercase tracking-widest text-sm transform hover:-translate-y-1">
                Criar minha Conta
            </button>
        </div>
    </form>
</div>
@endsection
