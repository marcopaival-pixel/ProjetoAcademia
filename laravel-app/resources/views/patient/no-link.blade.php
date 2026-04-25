@extends('layouts.app')

@section('title', 'Vínculo Necessário')

@section('content')
<div class="min-h-screen bg-[#06080c] flex items-center justify-center p-6 text-center">
    <div class="max-w-md space-y-8">
        <div class="w-24 h-24 bg-zinc-900 rounded-[2rem] flex items-center justify-center mx-auto border border-white/5 shadow-2xl">
            <i class="fas fa-link-slash text-zinc-700 text-3xl"></i>
        </div>
        
        <div class="space-y-4">
            <h1 class="text-3xl font-black text-white tracking-tighter italic">OPS! CADÊ O SEU PROFISSIONAL?</h1>
            <p class="text-zinc-500 font-medium leading-relaxed">
                Parece que você ainda não possui um vínculo ativo com um profissional de saúde ou performance.
            </p>
        </div>

        <div class="pt-6">
            <a href="{{ route('patient.professionals.search') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-white text-black font-black text-sm rounded-2xl hover:bg-zinc-200 transition-all uppercase tracking-widest italic">
                Encontrar Profissional <i class="fas fa-search text-[10px]"></i>
            </a>
        </div>
        
        <p class="text-[10px] text-zinc-700 font-black uppercase tracking-widest pt-10">NexShape Ecosystem — Performance Healthcare</p>
    </div>
</div>
@endsection
