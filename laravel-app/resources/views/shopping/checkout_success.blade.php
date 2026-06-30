@extends('layouts.app')

@section('title', 'Pedido Confirmado — Shopping Fitness')

@section('content')
<div class="max-w-[700px] mx-auto px-4 py-12">

    {{-- Ícone de sucesso animado --}}
    <div class="text-center mb-10">
        <div class="relative inline-flex items-center justify-center w-24 h-24 mb-6">
            <div class="absolute inset-0 bg-emerald-500/20 rounded-full animate-ping"></div>
            <div class="relative w-24 h-24 bg-emerald-500/10 border-2 border-emerald-500/30 rounded-full flex items-center justify-center">
                <i class="fas fa-check text-emerald-400 text-3xl"></i>
            </div>
        </div>
        <h1 class="text-3xl font-black text-white tracking-tight">Pedido Confirmado!</h1>
        <p class="text-zinc-400 mt-2">
            Obrigado, <span class="text-white font-bold">{{ Auth::user()->name }}</span>!
            Seu pedido <span class="text-emerald-400 font-black">{{ $order->order_number }}</span> foi registrado.
        </p>
    </div>

    {{-- Detalhes do pedido --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] p-7 mb-6 space-y-5">
        <h2 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2">
            <i class="fas fa-receipt text-emerald-400 text-xs"></i>
            Itens do Pedido
        </h2>

        <div class="space-y-4">
            @foreach($order->items as $item)
            <div class="flex gap-4 pb-4 border-b border-zinc-800 last:border-0 last:pb-0">
                <div class="w-14 h-14 rounded-2xl overflow-hidden bg-zinc-800 flex-shrink-0">
                    @if($item->product?->primaryImage())
                        <img src="{{ $item->product->primaryImage()->url() }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center"><i class="fas fa-box text-zinc-600"></i></div>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-black text-white">{{ $item->product_name }}</p>
                    <p class="text-xs text-zinc-500">{{ $item->quantity }}x R$ {{ number_format($item->unit_price, 2, ',', '.') }}</p>
                    {{-- Download (digital) --}}
                    @if($item->isDownloadable() && $item->canDownload())
                    <a href="#" class="inline-flex items-center gap-1.5 mt-2 text-[10px] font-black text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-full hover:bg-emerald-500/20 transition-all uppercase tracking-widest">
                        <i class="fas fa-download"></i> Download disponível
                    </a>
                    @endif
                </div>
                <p class="text-sm font-black text-white flex-shrink-0">R$ {{ number_format($item->total, 2, ',', '.') }}</p>
            </div>
            @endforeach
        </div>

        {{-- Totais --}}
        <div class="border-t border-zinc-700 pt-4 space-y-2 text-sm">
            <div class="flex justify-between text-zinc-400">
                <span>Subtotal</span>
                <span class="font-bold text-white">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
            </div>
            @if($order->discount_amount > 0)
            <div class="flex justify-between text-emerald-400">
                <span>Desconto</span>
                <span class="font-bold">− R$ {{ number_format($order->discount_amount, 2, ',', '.') }}</span>
            </div>
            @endif
            @if($order->shipping_amount > 0)
            <div class="flex justify-between text-zinc-400">
                <span>Frete</span>
                <span class="font-bold text-white">R$ {{ number_format($order->shipping_amount, 2, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between pt-3 border-t border-zinc-800">
                <span class="font-black text-white uppercase tracking-widest">Total</span>
                <span class="font-black text-emerald-400 text-xl">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Status e método --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 text-center">
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Status</p>
            <p class="text-sm font-black text-amber-400">{{ $order->statusLabel() }}</p>
        </div>
        <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 text-center">
            <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Pagamento</p>
            <p class="text-sm font-black text-white uppercase">{{ $order->payment_method ?? '—' }}</p>
        </div>
    </div>

    {{-- CTAs --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="{{ route('shopping.orders.show', $order) }}"
           class="flex-1 py-4 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 hover:border-emerald-500/40 text-emerald-400 font-black rounded-2xl transition-all text-center text-sm uppercase tracking-widest">
            <i class="fas fa-list-alt mr-2"></i>Ver Pedido
        </a>
        <a href="{{ route('shopping.index') }}"
           class="flex-1 py-4 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-white font-black rounded-2xl transition-all text-center text-sm uppercase tracking-widest">
            <i class="fas fa-store mr-2"></i>Continuar Comprando
        </a>
    </div>

</div>
@endsection
