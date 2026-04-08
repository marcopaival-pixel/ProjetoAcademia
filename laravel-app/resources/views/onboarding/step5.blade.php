@extends('layouts.onboarding')

@section('title', 'Passo 5: Medidas — NexShape')
@section('step_number', '05/08')
@section('back_route', route('onboarding.step4'))

@section('onboarding_content')
<div class="space-y-10 animate-fade-up">
    <header class="space-y-4">
        <h2 class="text-4xl font-black text-white tracking-tight leading-tight">Suas Medidas</h2>
        <p class="text-zinc-400 text-base font-medium">Estes dados são a base para o nosso cálculo metabólico.</p>
    </header>

    <form action="{{ route('onboarding.step5.save') }}" method="POST" class="space-y-12">
        @csrf
        
        <div class="space-y-10">
            <!-- Altura -->
            <div class="space-y-3">
                <label for="height" class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Qual sua altura? (cm)</label>
                <div class="relative group">
                    <input type="number" name="height" id="height" step="1" required placeholder="175"
                        class="w-full bg-white/5 border-b-2 border-white/10 py-4 px-0 text-4xl font-black text-white focus:outline-none focus:border-blue-500 transition-all appearance-none">
                    <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-500 group-focus-within:w-full transition-all duration-500"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <!-- Peso Atual -->
                <div class="space-y-3">
                    <label for="weight" class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Peso Atual (kg)</label>
                    <div class="relative group">
                        <input type="number" name="weight" id="weight" step="0.1" required placeholder="70.0"
                            class="w-full bg-white/5 border-b-2 border-white/10 py-4 px-0 text-3xl font-black text-white focus:outline-none focus:border-emerald-500 transition-all appearance-none">
                        <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-500 group-focus-within:w-full transition-all duration-500"></div>
                    </div>
                </div>

                <!-- Peso Desejado -->
                <div class="space-y-3">
                    <label for="target_weight" class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Peso Desejado</label>
                    <div class="relative group">
                        <input type="number" name="target_weight" id="target_weight" step="0.1" required placeholder="65.0"
                            class="w-full bg-white/5 border-b-2 border-white/10 py-4 px-0 text-3xl font-black text-white focus:outline-none focus:border-blue-400 transition-all appearance-none">
                        <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-400 group-focus-within:w-full transition-all duration-500"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full py-5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 transition-all shadow-[0_0_30px_rgba(37,99,235,0.3)] hover:shadow-[0_0_50px_rgba(37,99,235,0.5)] uppercase tracking-widest text-sm transform hover:-translate-y-1">
                Calcular Plano
            </button>
        </div>
    </form>
</div>
@endsection
