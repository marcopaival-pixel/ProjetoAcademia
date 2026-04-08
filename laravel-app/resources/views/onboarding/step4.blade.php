@extends('layouts.onboarding')

@section('title', 'Passo 4: Bio — NexShape')
@section('step_number', '04/08')
@section('back_route', route('onboarding.step3'))

@section('onboarding_content')
<div class="space-y-10 animate-fade-up">
    <header class="space-y-4">
        <h2 class="text-4xl font-black text-white tracking-tight leading-tight">Conte-nos sobre você</h2>
        <p class="text-zinc-400 text-base font-medium">Precisamos desses dados para calibrar o algoritmo de bioimpedância visual.</p>
    </header>

    <form action="{{ route('onboarding.step4.save') }}" method="POST" class="space-y-8">
        @csrf
        
        <div class="space-y-6">
            <!-- Gênero -->
            <div class="space-y-3">
                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Gênero Biológico</label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach(['Masculino', 'Feminino'] as $gender)
                        <label class="group cursor-pointer">
                            <input type="radio" name="gender" value="{{ $gender }}" class="peer sr-only" required>
                            <div class="py-4 px-6 bg-white/5 border-2 border-white/10 rounded-2xl text-center font-bold text-zinc-400 transition-all group-hover:bg-white/10 peer-checked:border-blue-500 peer-checked:bg-blue-500/10 peer-checked:text-white">
                                {{ $gender }}
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Data de Nascimento -->
            <div class="space-y-3">
                <label for="birth_date" class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Data de Nascimento</label>
                <div class="relative group">
                    <input type="date" name="birth_date" id="birth_date" required
                        class="w-full bg-white/5 border-b-2 border-white/10 py-4 px-0 text-xl font-bold text-white focus:outline-none focus:border-blue-500 transition-all appearance-none">
                    <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-500 group-focus-within:w-full transition-all duration-500"></div>
                </div>
            </div>

            <!-- País -->
            <div class="space-y-3">
                <label for="country" class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Onde você vive?</label>
                <div class="relative group">
                    <select name="country" id="country" required
                        class="w-full bg-white/5 border-b-2 border-white/10 py-4 px-0 text-xl font-bold text-white focus:outline-none focus:border-blue-500 transition-all appearance-none cursor-pointer">
                        <option value="Brasil" class="bg-[#0b0e14]">Brasil</option>
                        <option value="Portugal" class="bg-[#0b0e14]">Portugal</option>
                        <option value="Outro" class="bg-[#0b0e14]">Outro</option>
                    </select>
                    <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-emerald-500 group-focus-within:w-full transition-all duration-500"></div>
                    <div class="absolute right-0 top-1/2 -translate-y-1/2 pointer-events-none text-zinc-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-8">
            <button type="submit" class="w-full py-5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 transition-all shadow-[0_0_30px_rgba(37,99,235,0.3)] hover:shadow-[0_0_50px_rgba(37,99,235,0.5)] uppercase tracking-widest text-sm transform hover:-translate-y-1">
                Avançar
            </button>
        </div>
    </form>
</div>
@endsection
