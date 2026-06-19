@extends('layouts.app')

@section('title', 'Relatórios Comerciais')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white uppercase italic"><span class="text-emerald-500">Relatórios</span></h1>
            <p class="text-zinc-500 text-sm mt-1">Métricas avançadas e histórico de conversão.</p>
        </div>
    </div>

    <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2rem] overflow-hidden p-10 text-center">
        <i data-lucide="bar-chart-2" class="w-12 h-12 text-zinc-700 mx-auto mb-4"></i>
        <h3 class="text-lg font-bold text-white">Dados insuficientes</h3>
        <p class="text-zinc-500 text-sm mt-2">Não há dados comerciais suficientes para gerar relatórios.</p>
    </div>
</div>
@endsection
