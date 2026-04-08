@extends('layouts.onboarding')

@section('title', 'Passo 2: Objetivo — NexShape')
@section('step_number', '02/08')
@section('back_route', route('onboarding.step1'))

@section('onboarding_content')
<div class="space-y-8 animate-fade-up">
    <header class="space-y-4">
        <h2 class="text-4xl font-black text-white tracking-tight leading-tight">Qual é o seu objetivo?</h2>
        <p class="text-zinc-400 text-base font-medium">Isso nos ajuda a calcular suas necessidades calóricas exatas.</p>
    </header>

    <form action="{{ route('onboarding.step2.save') }}" method="POST" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 gap-4">
            @foreach(['Perder peso', 'Manter o peso', 'Ganhar massa muscular'] as $goal)
                <label class="group relative cursor-pointer">
                    <input type="radio" name="goal" value="{{ $goal }}" class="peer sr-only" required>
                    <div class="p-6 bg-white/5 border-2 border-white/10 rounded-2xl transition-all duration-300 group-hover:bg-white/10 peer-checked:border-blue-500 peer-checked:bg-blue-500/10">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold text-white">{{ $goal }}</span>
                            <div class="w-6 h-6 rounded-full border-2 border-white/20 peer-checked:border-blue-500 flex items-center justify-center">
                                <div class="w-2.5 h-2.5 bg-blue-500 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                            </div>
                        </div>
                    </div>
                </label>
            @endforeach
        </div>

        <div class="pt-10">
            <button type="submit" class="w-full py-5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 transition-all shadow-[0_0_30px_rgba(37,99,235,0.3)] hover:shadow-[0_0_50px_rgba(37,99,235,0.5)] uppercase tracking-widest text-sm transform hover:-translate-y-1">
                Continuar
            </button>
        </div>
    </form>
</div>
@endsection
