@extends('layouts.app')

@section('title', 'Propostas Comerciais')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white uppercase italic">Propostas <span class="text-amber-500">Comerciais</span></h1>
            <p class="text-zinc-500 text-sm mt-1">Acompanhe as propostas enviadas aos seus leads.</p>
        </div>
        <a href="{{ route('representative.proposals.create') }}" class="bg-amber-500 hover:bg-amber-400 text-zinc-950 px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-colors inline-block">
            Nova Proposta
        </a>
    </div>

    <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2rem] overflow-hidden p-10 text-center">
        <i data-lucide="file-invoice-dollar" class="w-12 h-12 text-zinc-700 mx-auto mb-4"></i>
        <h3 class="text-lg font-bold text-white">Nenhuma proposta enviada</h3>
        <p class="text-zinc-500 text-sm mt-2">Crie propostas para formalizar suas vendas.</p>
    </div>
</div>
@endsection
