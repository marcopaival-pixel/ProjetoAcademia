@extends('layouts.app')

@section('title', 'Lista de Desejos — Shopping Fitness')

@section('content')
<div class="max-w-[1100px] mx-auto px-4 py-8">

    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('shopping.index') }}" class="w-10 h-10 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center hover:border-emerald-500/40 transition-all text-zinc-400 hover:text-white">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Lista de Desejos</h1>
            <p class="text-xs text-zinc-500">{{ $items->count() }} {{ $items->count() === 1 ? 'produto' : 'produtos' }} salvos</p>
        </div>
    </div>

    @if($items->isEmpty())
    <div class="text-center py-20 bg-zinc-900 border border-zinc-800 rounded-[2.5rem]">
        <div class="w-16 h-16 rounded-3xl bg-zinc-800 border border-zinc-700 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-heart text-zinc-500 text-xl"></i>
        </div>
        <h3 class="text-lg font-black text-white mb-2">Nenhum produto salvo</h3>
        <p class="text-zinc-500 text-sm mb-6">Clique no ❤ em qualquer produto para salvá-lo aqui.</p>
        <a href="{{ route('shopping.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl transition-all text-sm uppercase tracking-widest">
            <i class="fas fa-store"></i> Ver Produtos
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($items as $wishlistItem)
            @include('shopping.partials.product-card', [
                'product' => $wishlistItem->product,
                'wishlistIds' => [$wishlistItem->product_id]
            ])
        @endforeach
    </div>
    @endif

</div>
@endsection
