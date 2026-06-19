@extends('layouts.app')

@section('title', 'Agenda Comercial')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white uppercase italic">Minha <span class="text-emerald-500">Agenda</span></h1>
            <p class="text-zinc-500 text-sm mt-1">Controle de reuniões, demonstrações e follow-ups.</p>
        </div>
        <button class="bg-emerald-500 hover:bg-emerald-400 text-zinc-950 px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-colors">
            + Novo Compromisso
        </button>
    </div>

    <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2rem] overflow-hidden p-10 text-center">
        <i data-lucide="calendar" class="w-12 h-12 text-zinc-700 mx-auto mb-4"></i>
        <h3 class="text-lg font-bold text-white">Agenda livre</h3>
        <p class="text-zinc-500 text-sm mt-2">Nenhum compromisso agendado no momento.</p>
    </div>
</div>
@endsection
