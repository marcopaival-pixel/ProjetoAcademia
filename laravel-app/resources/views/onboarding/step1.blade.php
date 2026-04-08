@extends('layouts.app')

@extends('layouts.onboarding')

@section('title', 'Passo 1: Nome — NexShape')
@section('step_number', '01/08')
@section('back_route', route('onboarding.welcome'))

@section('onboarding_content')
<div class="space-y-8 animate-fade-up">
    <header class="space-y-4">
        <h2 class="text-4xl font-black text-white tracking-tight leading-tight">Como podemos te chamar?</h2>
        <p class="text-zinc-400 text-base font-medium">Isso nos ajuda a personalizar sua experiência.</p>
    </header>

    <form action="{{ route('onboarding.step1.save') }}" method="POST" class="space-y-10">
        @csrf
        <div class="group relative">
            <input type="text" name="name" id="name" required placeholder="Seu nome ou apelido"
                value="{{ old('name', Session::get('onboarding_data.name')) }}"
                class="w-full bg-white/5 border-b-2 border-white/10 py-6 px-0 text-3xl font-bold text-white placeholder-zinc-700 focus:outline-none focus:border-blue-500 transition-all duration-300">
            <div class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-500 to-emerald-500 group-focus-within:w-full transition-all duration-500"></div>
        </div>

        <div class="pt-6">
            <button type="submit" class="w-full py-5 bg-blue-600 text-white font-black rounded-2xl hover:bg-blue-700 transition-all shadow-[0_0_30px_rgba(37,99,235,0.3)] hover:shadow-[0_0_50px_rgba(37,99,235,0.5)] uppercase tracking-widest text-sm transform hover:-translate-y-1">
                Próximo Passo
            </button>
        </div>
    </form>
</div>
@endsection
