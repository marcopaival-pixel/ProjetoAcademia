@extends('layouts.app')

@section('title', $product->name . ' — Shopping Fitness')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 py-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-zinc-500 mb-8">
        <a href="{{ route('shopping.index') }}" class="hover:text-emerald-400 transition-colors">Shopping</a>
        <i class="fas fa-chevron-right text-[8px]"></i>
        @if($product->category)
            <a href="{{ route('shopping.category', $product->category->slug) }}" class="hover:text-emerald-400 transition-colors">{{ $product->category->name }}</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
        @endif
        <span class="text-zinc-300 font-bold truncate max-w-[200px]">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">

        {{-- ── Galeria ─────────────────────────────────────────────────────── --}}
        <div class="space-y-4" x-data="{ activeImg: '{{ $product->primaryImage()?->url() ?? '' }}' }">
            <div class="relative overflow-hidden rounded-[2.5rem] bg-zinc-900 border border-zinc-800 aspect-square">
                @if($product->images->isNotEmpty())
                    <img :src="activeImg" alt="{{ $product->name }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <i class="fas fa-box text-zinc-600 text-6xl"></i>
                    </div>
                @endif

                {{-- Badges --}}
                <div class="absolute top-4 left-4 flex flex-col gap-2">
                    @if($product->is_featured)
                    <span class="px-3 py-1.5 bg-emerald-500 text-zinc-950 text-[10px] font-black uppercase tracking-widest rounded-full">Destaque</span>
                    @endif
                    @if($product->isOnSale())
                    <span class="px-3 py-1.5 bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full">Oferta</span>
                    @endif
                </div>
            </div>

            {{-- Thumbnails --}}
            @if($product->images->count() > 1)
            <div class="flex gap-3 overflow-x-auto pb-1">
                @foreach($product->images as $img)
                <button @click="activeImg = '{{ $img->url() }}'"
                    class="flex-shrink-0 w-16 h-16 rounded-2xl overflow-hidden border-2 transition-all"
                    :class="activeImg === '{{ $img->url() }}' ? 'border-emerald-500' : 'border-zinc-700 hover:border-zinc-500'">
                    <img src="{{ $img->url() }}" alt="" class="w-full h-full object-cover">
                </button>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Info do produto ──────────────────────────────────────────────── --}}
        <div class="space-y-6" x-data="{ qty: 1 }">

            {{-- Categoria + tipo --}}
            <div class="flex items-center gap-3 flex-wrap">
                @if($product->category)
                <a href="{{ route('shopping.category', $product->category->slug) }}"
                   class="text-xs font-bold text-emerald-400 uppercase tracking-widest hover:underline">
                    {{ $product->category->name }}
                </a>
                @endif
                @php
                    $typeMap = ['physical' => ['Físico','blue'], 'digital' => ['Digital','violet'], 'service' => ['Serviço','amber']];
                    [$typeLabel, $typeColor] = $typeMap[$product->type] ?? ['—','zinc'];
                @endphp
                <span class="px-3 py-1 text-[10px] font-black uppercase tracking-widest rounded-full
                    bg-{{ $typeColor }}-500/10 text-{{ $typeColor }}-400 border border-{{ $typeColor }}-500/20">
                    {{ $typeLabel }}
                </span>
            </div>

            {{-- Nome --}}
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight leading-tight">{{ $product->name }}</h1>
                @if($product->sku)
                <p class="text-xs text-zinc-600 mt-1 font-mono">SKU: {{ $product->sku }}</p>
                @endif
            </div>

            {{-- Preço --}}
            <div class="p-5 bg-zinc-800/50 border border-zinc-700/50 rounded-2xl space-y-1">
                @if($product->isOnSale())
                <p class="text-sm text-zinc-500 line-through font-medium">
                    R$ {{ number_format($product->price, 2, ',', '.') }}
                </p>
                @endif
                <p class="text-4xl font-black text-emerald-400">
                    R$ {{ number_format($product->currentPrice(), 2, ',', '.') }}
                </p>
                @if($product->isOnSale())
                @php $disc = round((1 - $product->currentPrice() / $product->price) * 100); @endphp
                <p class="text-xs text-rose-400 font-bold">{{ $disc }}% de desconto</p>
                @endif
            </div>

            {{-- Estoque --}}
            @if($product->isPhysical())
            <div class="flex items-center gap-2 text-sm">
                @if($product->isInStock())
                    <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                    <span class="text-emerald-400 font-bold">Em estoque</span>
                    @if($product->hasLowStock())
                        <span class="text-amber-400 text-xs font-bold">(Últimas {{ $product->stock_quantity }} unidades)</span>
                    @endif
                @else
                    <div class="w-2 h-2 rounded-full bg-rose-500"></div>
                    <span class="text-rose-400 font-bold">Esgotado</span>
                @endif
            </div>
            @endif

            {{-- Descrição curta --}}
            @if($product->short_description)
            <p class="text-zinc-400 text-sm leading-relaxed">{{ $product->short_description }}</p>
            @endif

            {{-- Ações --}}
            @if($product->isInStock())
            <form action="{{ route('shopping.cart.add') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                @if($product->isPhysical())
                <div class="flex items-center gap-4">
                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Quantidade</label>
                    <div class="flex items-center gap-2 bg-zinc-800 border border-zinc-700 rounded-2xl overflow-hidden">
                        <button type="button" @click="qty = Math.max(1, qty - 1)"
                            class="w-10 h-10 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-700 transition-all">
                            <i class="fas fa-minus text-xs"></i>
                        </button>
                        <span x-text="qty" class="w-8 text-center text-white font-black text-sm"></span>
                        <input type="hidden" name="quantity" :value="qty">
                        <button type="button" @click="qty = Math.min(99, qty + 1)"
                            class="w-10 h-10 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-700 transition-all">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                </div>
                @endif

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl transition-all active:scale-95 shadow-xl shadow-emerald-500/10 flex items-center justify-center gap-3 uppercase tracking-widest text-sm">
                        <i class="fas fa-cart-plus"></i>
                        Adicionar ao Carrinho
                    </button>

                    <button type="button" onclick="shopStore().toggleWishlist({{ $product->id }}, this)"
                        class="w-14 h-14 rounded-2xl bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-rose-500/40 flex items-center justify-center transition-all active:scale-95 {{ $inWishlist ? 'text-rose-400' : 'text-zinc-400' }}">
                        <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart"></i>
                    </button>
                </div>
            </form>
            @else
            <div class="py-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-center">
                <p class="text-rose-400 font-black uppercase tracking-widest text-sm">Produto Esgotado</p>
                <p class="text-zinc-500 text-xs mt-1">Adicione à wishlist para ser notificado quando voltar ao estoque.</p>
            </div>
            @endif

            {{-- Informações do vendedor --}}
            @if($product->vendor && $product->vendor->slug !== 'academia-propria')
            <div class="flex items-center gap-3 p-4 bg-zinc-800/40 border border-zinc-700/50 rounded-2xl">
                <div class="w-8 h-8 rounded-xl bg-zinc-700 flex items-center justify-center">
                    <i class="fas fa-store text-zinc-400 text-xs"></i>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 font-bold uppercase tracking-wide">Vendido por</p>
                    <p class="text-sm text-white font-bold">{{ $product->vendor->name }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ── Descrição completa ─────────────────────────────────────────── --}}
    @if($product->description)
    <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-8 mb-10">
        <h2 class="text-lg font-black text-white uppercase tracking-tight mb-4 flex items-center gap-3">
            <div class="w-1 h-5 bg-emerald-400 rounded-full"></div>
            Descrição
        </h2>
        <div class="text-zinc-400 text-sm leading-relaxed prose prose-invert max-w-none">
            {!! nl2br(e($product->description)) !!}
        </div>
    </div>
    @endif

    {{-- ── Produtos relacionados ─────────────────────────────────────── --}}
    @if($related->isNotEmpty())
    <div>
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1 h-6 bg-violet-400 rounded-full"></div>
            <h2 class="text-xl font-black text-white uppercase tracking-tight">Você também pode gostar</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($related as $rel)
                @include('shopping.partials.product-card', ['product' => $rel, 'wishlistIds' => [$inWishlist ? $product->id : -1]])
            @endforeach
        </div>
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
