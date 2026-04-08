@extends('layouts.onboarding')

@section('title', 'Passo 6: Meta Semanal — NexShape')
@section('step_number', '06/08')
@section('back_route', route('onboarding.step5'))

@section('onboarding_content')
<div class="space-y-10 animate-fade-up">
    <header class="space-y-4">
        <h2 class="text-4xl font-black text-white tracking-tight leading-tight">Ritmo de Progresso</h2>
        <p class="text-zinc-400 text-base font-medium">Com que velocidade você deseja chegar ao seu objetivo?</p>
    </header>

    <form action="{{ route('onboarding.step6.save') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="space-y-4">
            @foreach([
                '0,25 kg por semana' => 'Recomendado para iniciantes.',
                '0,5 kg por semana' => 'Equilíbrio ideal entre saúde e velocidade.',
                '0,75 kg por semana' => 'Para quem tem foco rigoroso.',
                '1 kg por semana' => 'Apenas sob supervisão ou metas curtas.'
            ] as $goal => $desc)
                <label class="group relative cursor-pointer block">
                    <input type="radio" name="weekly_goal" value="{{ $goal }}" class="peer sr-only" required>
                    <div class="p-6 bg-white/5 border-2 border-white/10 rounded-2xl transition-all duration-300 group-hover:bg-white/10 peer-checked:border-blue-500 peer-checked:bg-blue-500/10">
                        <div class="flex flex-col">
                            <span class="text-lg font-bold text-white">{{ $goal }}</span>
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
