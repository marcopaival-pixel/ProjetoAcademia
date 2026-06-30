@extends('layouts.app')

@section('title', 'Shopping Fitness')

@section('content')
<div class="max-w-[1400px] mx-auto px-4 py-8" x-data="shopStore()">

    {{-- ── Hero da Loja ─────────────────────────────────────────────────────── --}}
    <div class="relative overflow-hidden rounded-[3rem] bg-zinc-900 border border-zinc-800 p-8 mb-8 shadow-2xl">
        <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/5 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-violet-500/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-emerald-400 text-sm"></i>
                    </div>
                    <span class="text-xs font-black text-emerald-500 uppercase tracking-widest">Marketplace Fitness</span>
                </div>
                <h1 class="text-4xl font-black text-white tracking-tighter">
                    Shopping <span class="text-emerald-400">Fitness</span>
                </h1>
                <p class="text-zinc-400 font-medium max-w-lg">
                    Produtos, suplementos, serviços e conteúdo digital selecionados para o seu treino.
                </p>
            </div>

            {{-- Barra de busca --}}
            <form action="{{ route('shopping.search') }}" method="GET" class="flex gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-80">
                    <input type="text" name="q" placeholder="Buscar produtos..."
                        class="w-full bg-zinc-800/60 border border-zinc-700 text-white placeholder-zinc-500 rounded-2xl px-5 py-3 pr-12 focus:outline-none focus:border-emerald-500/50 transition-colors text-sm">
                    <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-emerald-400 transition-colors">
                        <i class="fas fa-search text-sm"></i>
                    </button>
                </div>
            </form>

            {{-- Mini carrinho --}}
            <a href="{{ route('shopping.points.index') }}"
               class="flex items-center gap-2 px-4 py-3 bg-zinc-800/60 hover:bg-zinc-800 border border-zinc-700 hover:border-emerald-500/30 rounded-2xl transition-all text-zinc-300 hover:text-white text-sm font-bold">
                <i class="fas fa-coins text-emerald-400"></i> Pontos
            </a>
            <a href="{{ route('shopping.cart.index') }}"
               class="relative flex items-center gap-3 px-6 py-3 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-500/40 rounded-2xl transition-all group">
                <i class="fas fa-shopping-cart text-emerald-400 group-hover:scale-110 transition-transform"></i>
                <span class="text-white font-bold text-sm">Carrinho</span>
                @if(($cartSummary['cart']->items->count() ?? 0) > 0)
                    <span class="absolute -top-2 -right-2 w-5 h-5 bg-emerald-500 text-zinc-950 text-xs font-black rounded-full flex items-center justify-center">
                        {{ $cartSummary['cart']->totalItems() }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    {{-- ── Categorias ──────────────────────────────────────────────────────── --}}
    @if($categories->isNotEmpty())
    <div class="mb-8">
        <h2 class="text-lg font-black text-white uppercase tracking-widest mb-4">Categorias</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('shopping.index') }}"
               class="px-5 py-2.5 bg-emerald-500 text-zinc-950 font-black rounded-2xl text-xs uppercase tracking-widest transition-all hover:bg-emerald-400 active:scale-95">
                Todos
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('shopping.category', $cat->slug) }}"
               class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-emerald-500/30 text-zinc-300 hover:text-white font-bold rounded-2xl text-xs uppercase tracking-widest transition-all active:scale-95 flex items-center gap-2">
                <i class="fas fa-{{ $cat->icon ?? 'tag' }} text-emerald-400 text-[10px]"></i>
                {{ $cat->name }}
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Recomendados para você ─────────────────────────────────────────── --}}
    @if(isset($recommended) && $recommended->isNotEmpty())
    <div class="mb-10">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-1 h-6 bg-amber-400 rounded-full"></div>
                <h2 class="text-xl font-black text-white uppercase tracking-tight">Recomendados para você</h2>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($recommended as $product)
                @include('shopping.partials.product-card', ['product' => $product, 'wishlistIds' => $wishlistIds])
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Destaques ─────────────────────────────────────────────────────── --}}
    @if($featured->isNotEmpty())
    <div class="mb-10">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-1 h-6 bg-emerald-400 rounded-full"></div>
                <h2 class="text-xl font-black text-white uppercase tracking-tight">Em Destaque</h2>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($featured as $product)
                @include('shopping.partials.product-card', ['product' => $product, 'wishlistIds' => $wishlistIds])
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Lançamentos ──────────────────────────────────────────────────── --}}
    @if($recent->isNotEmpty())
    <div>
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-1 h-6 bg-violet-400 rounded-full"></div>
                <h2 class="text-xl font-black text-white uppercase tracking-tight">Lançamentos</h2>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($recent as $product)
                @include('shopping.partials.product-card', ['product' => $product, 'wishlistIds' => $wishlistIds])
            @endforeach
        </div>
    </div>
    @endif

    {{-- Estado vazio --}}
    @if($featured->isEmpty() && $recent->isEmpty())
    <div class="text-center py-24">
        <div class="w-20 h-20 rounded-3xl bg-zinc-800 border border-zinc-700 flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-store text-zinc-500 text-2xl"></i>
        </div>
        <h3 class="text-xl font-black text-white mb-2">Loja em breve</h3>
        <p class="text-zinc-500 text-sm">Os primeiros produtos estão sendo cadastrados.</p>
    </div>
    @endif

</div>

@push('scripts')
<script>
function shopStore() {
    return {
        toggleWishlist(productId, btn) {
            fetch(`/shopping/desejos/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(r => r.json())
            .then(data => {
                const icon = btn.querySelector('i');
                if (data.in_wishlist) {
                    icon.classList.remove('far'); icon.classList.add('fas');
                    btn.classList.add('text-rose-400');
                } else {
                    icon.classList.remove('fas'); icon.classList.add('far');
                    btn.classList.remove('text-rose-400');
                }
            });
        }
    }
}
</script>
@endpush
@endsection
