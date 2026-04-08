@extends('layouts.onboarding')

@section('title', 'Passo 3: Atividade — NexShape')
@section('step_number', '03/08')
@section('back_route', route('onboarding.step2'))

@section('onboarding_content')
<div class="space-y-8 animate-fade-up">
    <header class="space-y-4">
        <h2 class="text-4xl font-black text-white tracking-tight leading-tight">Qual seu nível de atividade?</h2>
        <p class="text-zinc-400 text-base font-medium">Seja honesto. Isso é crucial para o cálculo do seu TDEE.</p>
    </header>

    <form action="{{ route('onboarding.step3.save') }}" method="POST" class="space-y-4">
        @csrf
        <div class="space-y-3">
            @foreach([
                'Não muito ativo' => 'Trabalho sentado, pouco exercício.',
                'Levemente ativo' => 'Exercício leve 1-3 dias/semana.',
                'Ativo' => 'Exercício moderado 3-5 dias/semana.',
                'Bastante ativo' => 'Exercício intenso 6-7 dias/semana.'
            ] as $level => $desc)
                <label class="group relative cursor-pointer block">
                    <input type="radio" name="activity_level" value="{{ $level }}" class="peer sr-only" required>
                    <div class="p-5 bg-white/5 border-2 border-white/10 rounded-2xl transition-all duration-300 group-hover:bg-white/10 peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10">
                        <div class="flex flex-col">
                            <span class="text-lg font-bold text-white">{{ $level }}</span>
                            <span class="text-sm text-zinc-500">{{ $desc }}</span>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="pt-8">
            <button type="submit" class="w-full py-5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 transition-all shadow-[0_0_30px_rgba(37,99,235,0.3)] hover:shadow-[0_0_50px_rgba(37,99,235,0.5)] uppercase tracking-widest text-sm transform hover:-translate-y-1">
                Avançar
            </button>
        </div>
    </form>
</div>
@endsection
