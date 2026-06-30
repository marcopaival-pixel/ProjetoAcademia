@extends('layouts.app')

@section('title', 'Carrinho — Shopping Fitness')

@section('content')
<div class="max-w-[1100px] mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('shopping.index') }}" class="w-10 h-10 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center hover:border-emerald-500/40 transition-all text-zinc-400 hover:text-white">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Meu Carrinho</h1>
                <p class="text-xs text-zinc-500">{{ $summary['cart']->totalItems() }} {{ $summary['cart']->totalItems() === 1 ? 'item' : 'itens' }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-400"></i>
        <p class="text-emerald-400 text-sm font-bold">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-rose-400"></i>
        <p class="text-rose-400 text-sm font-bold">{{ session('error') }}</p>
    </div>
    @endif

    @if($summary['cart']->isEmpty())
    {{-- Carrinho vazio --}}
    <div class="text-center py-24 bg-zinc-900 border border-zinc-800 rounded-[2.5rem]">
        <div class="w-20 h-20 rounded-3xl bg-zinc-800 border border-zinc-700 flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-shopping-cart text-zinc-500 text-2xl"></i>
        </div>
        <h3 class="text-xl font-black text-white mb-2">Carrinho vazio</h3>
        <p class="text-zinc-500 text-sm mb-6">Adicione produtos para continuar.</p>
        <a href="{{ route('shopping.index') }}"
           class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl transition-all active:scale-95 text-sm uppercase tracking-widest">
            <i class="fas fa-store"></i> Ver Produtos
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ── Lista de itens ──────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">
            @foreach($summary['cart']->items as $item)
            <div class="group bg-zinc-900 border border-zinc-800/50 hover:border-zinc-700 rounded-[2rem] p-5 transition-all">
                <div class="flex gap-4">
                    {{-- Imagem --}}
                    <div class="flex-shrink-0 w-20 h-20 rounded-2xl overflow-hidden bg-zinc-800 border border-zinc-700">
                        @if($item->product->primaryImage())
                            <img src="{{ $item->product->primaryImage()->url() }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-box text-zinc-600 text-xl"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">{{ $item->product->category?->name }}</p>
                                <h3 class="text-sm font-black text-white truncate">{{ $item->product->name }}</h3>
                                <p class="text-sm text-emerald-400 font-black mt-1">
                                    R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                                </p>
                            </div>

                            {{-- Remover --}}
                            <form action="{{ route('shopping.cart.remove', $item->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-xl bg-zinc-800 hover:bg-rose-500/10 border border-zinc-700 hover:border-rose-500/40 flex items-center justify-center transition-all text-zinc-500 hover:text-rose-400">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </form>
                        </div>

                        {{-- Quantidade + subtotal --}}
                        <div class="flex items-center justify-between mt-3">
                            <form action="{{ route('shopping.cart.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                                @csrf @method('PATCH')
                                <div class="flex items-center bg-zinc-800 border border-zinc-700 rounded-xl overflow-hidden">
                                    <button type="submit" name="quantity" value="{{ max(0, $item->quantity - 1) }}"
                                        class="w-8 h-8 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-700 transition-all text-xs">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="w-8 text-center text-white font-black text-sm">{{ $item->quantity }}</span>
                                    <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}"
                                        class="w-8 h-8 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-zinc-700 transition-all text-xs">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </form>

                            <p class="text-sm font-black text-white">
                                R$ {{ number_format($item->lineTotal(), 2, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ── Resumo e cupom ──────────────────────────────────────────────── --}}
        <div class="space-y-4">

            {{-- Cupom --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6">
                <h3 class="text-sm font-black text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-ticket-alt text-emerald-400 text-xs"></i>
                    Cupom de Desconto
                </h3>

                @if($summary['coupon'])
                <div class="flex items-center justify-between p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl mb-3">
                    <div>
                        <p class="text-emerald-400 font-black text-sm">{{ $summary['coupon']->code }}</p>
                        <p class="text-xs text-zinc-400">− R$ {{ number_format($summary['discount'], 2, ',', '.') }}</p>
                    </div>
                    <form action="{{ route('shopping.cart.coupon.remove') }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-zinc-500 hover:text-rose-400 transition-colors">
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </form>
                </div>
                @else
                <form action="{{ route('shopping.cart.coupon.apply') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="code" placeholder="Código do cupom"
                        class="flex-1 bg-zinc-800 border border-zinc-700 text-white placeholder-zinc-600 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-emerald-500/50 uppercase font-bold tracking-widest transition-colors">
                    <button type="submit"
                        class="px-4 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 font-black rounded-xl transition-all text-xs uppercase tracking-widest">
                        Aplicar
                    </button>
                </form>
                @endif
            </div>

            {{-- Resumo de valores --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 space-y-4">
                <h3 class="text-sm font-black text-white uppercase tracking-widest">Resumo</h3>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-zinc-400">
                        <span>Subtotal</span>
                        <span class="font-bold text-white">R$ {{ number_format($summary['subtotal'], 2, ',', '.') }}</span>
                    </div>

                    @if($summary['discount'] > 0)
                    <div class="flex justify-between text-emerald-400">
                        <span>Desconto</span>
                        <span class="font-bold">− R$ {{ number_format($summary['discount'], 2, ',', '.') }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between text-zinc-400">
                        <span>Frete</span>
                        <span class="font-bold {{ $summary['shipping'] == 0 ? 'text-emerald-400' : 'text-white' }}">
                            {{ $summary['shipping'] == 0 ? 'Grátis' : 'R$ ' . number_format($summary['shipping'], 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="border-t border-zinc-700 pt-3 flex justify-between">
                        <span class="font-black text-white uppercase tracking-widest">Total</span>
                        <span class="font-black text-emerald-400 text-xl">R$ {{ number_format($summary['total'], 2, ',', '.') }}</span>
                    </div>
                </div>

                <a href="{{ route('shopping.checkout.index') }}"
                   class="block w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl transition-all active:scale-95 shadow-xl shadow-emerald-500/10 text-center text-sm uppercase tracking-widest">
                    <i class="fas fa-lock mr-2"></i>
                    Finalizar Compra
                </a>

                <a href="{{ route('shopping.index') }}" class="block text-center text-xs text-zinc-500 hover:text-emerald-400 transition-colors font-bold">
                    ← Continuar comprando
                </a>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
