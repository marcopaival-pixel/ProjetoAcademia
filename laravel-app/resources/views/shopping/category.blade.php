@extends('layouts.app')

@section('title', $category->name . ' — Shopping Fitness')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 py-8">

    {{-- Header da categoria --}}
    <div class="relative overflow-hidden rounded-[2.5rem] bg-zinc-900 border border-zinc-800 p-8 mb-8 shadow-xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 blur-[80px] rounded-full pointer-events-none"></div>
        <nav class="flex items-center gap-2 text-xs text-zinc-500 mb-4">
            <a href="{{ route('shopping.index') }}" class="hover:text-emerald-400 transition-colors">Shopping</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            <span class="text-zinc-300 font-bold">{{ $category->name }}</span>
        </nav>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                <i class="fas fa-{{ $category->icon ?? 'tag' }} text-emerald-400"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">{{ $category->name }}</h1>
                <p class="text-zinc-500 text-sm">{{ $products->total() }} produto(s)</p>
            </div>
        </div>
        @if($category->description)
        <p class="text-zinc-400 text-sm mt-4 max-w-xl">{{ $category->description }}</p>
        @endif
    </div>

    @if($products->isEmpty())
    <div class="text-center py-20 bg-zinc-900 border border-zinc-800 rounded-[2rem]">
        <i class="fas fa-box-open text-zinc-600 text-4xl mb-4 block"></i>
        <h3 class="text-lg font-black text-white mb-2">Nenhum produto nesta categoria</h3>
        <p class="text-zinc-500 text-sm">Em breve novos produtos serão adicionados aqui.</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($products as $product)
            @include('shopping.partials.product-card', ['product' => $product, 'wishlistIds' => []])
        @endforeach
    </div>
    <div class="mt-8">{{ $products->links() }}</div>
    @endif

</div>
@endsection
