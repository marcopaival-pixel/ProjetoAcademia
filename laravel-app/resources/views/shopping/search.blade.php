@extends('layouts.app')

@section('title', 'Busca: ' . $query . ' — Shopping Fitness')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 py-8">

    {{-- Header da busca --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('shopping.index') }}" class="w-10 h-10 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center hover:border-emerald-500/40 transition-all text-zinc-400 hover:text-white">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div class="flex-1">
            <h1 class="text-2xl font-black text-white tracking-tight">
                @if($query) Resultados para "<span class="text-emerald-400">{{ $query }}</span>"
                @else Todos os Produtos @endif
            </h1>
            <p class="text-xs text-zinc-500">{{ $products->total() }} produto(s) encontrado(s)</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

        {{-- ── Filtros laterais ─────────────────────────────────────────── --}}
        <div class="space-y-4">
            <form action="{{ route('shopping.search') }}" method="GET" id="filter-form">
                <input type="hidden" name="q" value="{{ $query }}">

                {{-- Ordenação --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest mb-3">Ordem</h3>
                    <select name="ordem" onchange="document.getElementById('filter-form').submit()"
                            class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-emerald-500/50">
                        <option value="recent" {{ $sort === 'recent' ? 'selected' : '' }}>Recomendados</option>
                        <option value="price_asc" {{ $sort === 'price_asc' ? 'selected' : '' }}>Menor preço</option>
                        <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Maior preço</option>
                    </select>
                </div>

                {{-- Preço --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest mb-3">Preço (R$)</h3>
                    <div class="flex gap-2 items-center">
                        <input type="number" name="preco_min" value="{{ $minPrice }}" placeholder="Mín"
                               class="w-full bg-zinc-800 border border-zinc-700 text-white placeholder-zinc-500 rounded-xl px-3 py-2 text-xs focus:outline-none">
                        <span class="text-zinc-500 text-xs">até</span>
                        <input type="number" name="preco_max" value="{{ $maxPrice }}" placeholder="Máx"
                               class="w-full bg-zinc-800 border border-zinc-700 text-white placeholder-zinc-500 rounded-xl px-3 py-2 text-xs focus:outline-none">
                    </div>
                    <button type="submit" class="w-full mt-3 py-2 bg-zinc-800 hover:bg-zinc-700 border border-zinc-750 text-white rounded-xl text-xs font-bold transition-colors">
                        Filtrar
                    </button>
                </div>

                {{-- Tipo --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest mb-3">Tipo</h3>
                    <div class="space-y-2">
                        @foreach(['' => 'Todos', 'physical' => 'Físico', 'digital' => 'Digital', 'service' => 'Serviço'] as $val => $label)
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                             <input type="radio" name="tipo" value="{{ $val }}"
                                   {{ $type === $val ? 'checked' : '' }}
                                   onchange="document.getElementById('filter-form').submit()"
                                   class="w-4 h-4 accent-emerald-500">
                            <span class="text-sm text-zinc-400 group-hover:text-white transition-colors">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Categoria --}}
                @if($categories->isNotEmpty())
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4">
                    <h3 class="text-xs font-black text-white uppercase tracking-widest mb-3">Categoria</h3>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" name="categoria" value=""
                                   {{ !$catId ? 'checked' : '' }}
                                   onchange="document.getElementById('filter-form').submit()"
                                   class="w-4 h-4 accent-emerald-500">
                            <span class="text-sm text-zinc-400 group-hover:text-white transition-colors">Todas</span>
                        </label>
                        @foreach($categories as $cat)
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" name="categoria" value="{{ $cat->id }}"
                                   {{ $catId == $cat->id ? 'checked' : '' }}
                                   onchange="document.getElementById('filter-form').submit()"
                                   class="w-4 h-4 accent-emerald-500">
                            <span class="text-sm text-zinc-400 group-hover:text-white transition-colors">{{ $cat->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif
            </form>
        </div>

        {{-- ── Grid de produtos ─────────────────────────────────────────── --}}
        <div class="lg:col-span-3">
            @if($products->isEmpty())
            <div class="text-center py-20 bg-zinc-900 border border-zinc-800 rounded-[2rem]">
                <i class="fas fa-search text-zinc-600 text-4xl mb-4 block"></i>
                <h3 class="text-lg font-black text-white mb-2">Nenhum resultado</h3>
                <p class="text-zinc-500 text-sm">Tente outros termos ou remova os filtros.</p>
            </div>
            @else
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                @foreach($products as $product)
                    @include('shopping.partials.product-card', ['product' => $product, 'wishlistIds' => []])
                @endforeach
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
            @endif
        </div>
    </div>

</div>
@endsection
