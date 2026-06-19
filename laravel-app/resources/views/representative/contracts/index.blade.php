@extends('layouts.app')

@section('title', 'Contratos Fechados')

@section('content')
<div class="space-y-6 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white uppercase italic">Meus <span class="text-blue-500">Contratos</span></h1>
            <p class="text-zinc-500 text-sm mt-1">Histórico de contratos assinados e negociações concretizadas.</p>
        </div>
    </div>

    <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2rem] overflow-hidden p-10 text-center">
        <i data-lucide="file-signature" class="w-12 h-12 text-zinc-700 mx-auto mb-4"></i>
        <h3 class="text-lg font-bold text-white">Nenhum contrato assinado</h3>
        <p class="text-zinc-500 text-sm mt-2">Os contratos aprovados aparecerão aqui.</p>
    </div>
</div>
@endsection
