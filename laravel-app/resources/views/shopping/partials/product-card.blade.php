{{-- Partial: card de produto reutilizável --}}
@php
    $inWish = in_array($product->id, $wishlistIds ?? []);
    $price  = $product->currentPrice();
    $typeLabels = ['physical' => 'Físico', 'digital' => 'Digital', 'service' => 'Serviço'];
    $typeColors = ['physical' => 'text-blue-400 bg-blue-500/10 border-blue-500/20',
                   'digital'  => 'text-violet-400 bg-violet-500/10 border-violet-500/20',
                   'service'  => 'text-amber-400 bg-amber-500/10 border-amber-500/20'];
@endphp

<div class="group relative bg-zinc-900 border border-zinc-800/50 hover:border-emerald-500/30 rounded-[2rem] overflow-hidden transition-all duration-300 shadow-xl hover:shadow-2xl hover:shadow-emerald-500/5 hover:-translate-y-1">

    {{-- Imagem --}}
    <a href="{{ route('shopping.product.show', $product->slug) }}" class="block relative overflow-hidden aspect-square bg-zinc-800">
        @if($product->primaryImage())
            <img src="{{ $product->primaryImage()->url() }}" alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <i class="fas fa-box text-zinc-600 text-4xl"></i>
            </div>
        @endif

        {{-- Badge destaque --}}
        @if($product->is_featured)
        <div class="absolute top-3 left-3 px-3 py-1 bg-emerald-500 text-zinc-950 text-[10px] font-black uppercase tracking-widest rounded-full">
            Destaque
        </div>
        @endif

        {{-- Badge promoção --}}
        @if($product->isOnSale())
        <div class="absolute top-3 right-3 px-3 py-1 bg-rose-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full">
            Oferta
        </div>
        @endif

        {{-- Badge tipo --}}
        <div class="absolute bottom-3 left-3">
            <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-widest rounded-full border {{ $typeColors[$product->type] ?? '' }}">
                {{ $typeLabels[$product->type] ?? $product->type }}
            </span>
        </div>

        {{-- Wishlist button --}}
        <button
            onclick="shopStore().toggleWishlist({{ $product->id }}, this)"
            class="absolute bottom-3 right-3 w-8 h-8 rounded-xl bg-zinc-900/80 backdrop-blur border border-zinc-700 flex items-center justify-center transition-all hover:border-rose-500/50 {{ $inWish ? 'text-rose-400' : 'text-zinc-400' }}">
            <i class="{{ $inWish ? 'fas' : 'far' }} fa-heart text-xs"></i>
        </button>
    </a>

    {{-- Info --}}
    <div class="p-4 space-y-3">
        <div class="space-y-1">
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest truncate">
                {{ $product->category?->name ?? '—' }}
            </p>
            <h3 class="text-sm font-black text-white leading-tight line-clamp-2 group-hover:text-emerald-400 transition-colors">
                {{ $product->name }}
            </h3>
        </div>

        {{-- Preço --}}
        <div class="flex items-end justify-between gap-2">
            <div>
                @if($product->isOnSale())
                    <p class="text-xs text-zinc-600 line-through font-medium">
                        R$ {{ number_format($product->price, 2, ',', '.') }}
                    </p>
                @endif
                <p class="text-lg font-black text-emerald-400 leading-tight">
                    R$ {{ number_format($price, 2, ',', '.') }}
                </p>
            </div>

            {{-- Botão adicionar --}}
            @if($product->isInStock())
            <form action="{{ route('shopping.cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit"
                    class="w-9 h-9 rounded-xl bg-emerald-500/10 hover:bg-emerald-500 border border-emerald-500/30 hover:border-emerald-500 text-emerald-400 hover:text-zinc-950 flex items-center justify-center transition-all active:scale-95 group/btn">
                    <i class="fas fa-plus text-xs group-hover/btn:rotate-90 transition-transform duration-200"></i>
                </button>
            </form>
            @else
            <span class="text-[10px] font-black text-rose-400 uppercase tracking-wide px-3 py-2 bg-rose-500/10 border border-rose-500/20 rounded-xl">
                Esgotado
            </span>
            @endif
        </div>

        {{-- Sem estoque warning --}}
        @if($product->isPhysical() && $product->hasLowStock())
        <div class="flex items-center gap-2 text-amber-400 text-[10px] font-bold uppercase tracking-wide">
            <i class="fas fa-exclamation-triangle text-[8px]"></i>
            Últimas unidades
        </div>
        @endif
    </div>
</div>
