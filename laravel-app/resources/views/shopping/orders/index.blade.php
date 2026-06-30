@extends('layouts.app')

@section('title', 'Meus Pedidos — Shopping Fitness')

@section('content')
<div class="max-w-[900px] mx-auto px-4 py-8">

    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('shopping.index') }}" class="w-10 h-10 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center hover:border-emerald-500/40 transition-all text-zinc-400 hover:text-white">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-white tracking-tight">Meus Pedidos</h1>
                <p class="text-xs text-zinc-500">{{ $orders->total() }} pedido(s) encontrado(s)</p>
            </div>
        </div>
    </div>

    @if($orders->isEmpty())
    <div class="text-center py-20 bg-zinc-900 border border-zinc-800 rounded-[2.5rem]">
        <div class="w-16 h-16 rounded-3xl bg-zinc-800 border border-zinc-700 flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-box-open text-zinc-500 text-xl"></i>
        </div>
        <h3 class="text-lg font-black text-white mb-2">Nenhum pedido ainda</h3>
        <p class="text-zinc-500 text-sm mb-6">Seus pedidos aparecerão aqui após a primeira compra.</p>
        <a href="{{ route('shopping.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl transition-all text-sm uppercase tracking-widest">
            <i class="fas fa-store"></i> Ir ao Shopping
        </a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($orders as $order)
        @php
            $statusColors = [
                'pending'    => 'text-amber-400 bg-amber-500/10 border-amber-500/20',
                'paid'       => 'text-blue-400 bg-blue-500/10 border-blue-500/20',
                'processing' => 'text-blue-400 bg-blue-500/10 border-blue-500/20',
                'shipped'    => 'text-violet-400 bg-violet-500/10 border-violet-500/20',
                'delivered'  => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                'completed'  => 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20',
                'cancelled'  => 'text-rose-400 bg-rose-500/10 border-rose-500/20',
                'refunded'   => 'text-zinc-400 bg-zinc-500/10 border-zinc-500/20',
            ];
        @endphp
        <div class="bg-zinc-900 border border-zinc-800/50 hover:border-zinc-700 rounded-[2rem] p-5 transition-all">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-3">
                        <p class="font-black text-white text-sm">{{ $order->order_number }}</p>
                        <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-widest rounded-full border {{ $statusColors[$order->status] ?? 'text-zinc-400 bg-zinc-800 border-zinc-700' }}">
                            {{ $order->statusLabel() }}
                        </span>
                    </div>
                    <p class="text-xs text-zinc-500">
                        {{ $order->created_at->format('d/m/Y \à\s H:i') }}
                        · {{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'itens' }}
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <p class="text-lg font-black text-emerald-400">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                    <a href="{{ route('shopping.orders.show', $order) }}"
                       class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 hover:border-emerald-500/30 text-white font-black rounded-xl transition-all text-xs uppercase tracking-widest">
                        Detalhes
                    </a>
                </div>
            </div>

            {{-- Preview de itens --}}
            <div class="flex gap-2 mt-4">
                @foreach($order->items->take(4) as $item)
                <div class="w-10 h-10 rounded-xl overflow-hidden bg-zinc-800 border border-zinc-700 flex-shrink-0"
                     title="{{ $item->product_name }}">
                    @if($item->product?->primaryImage())
                        <img src="{{ $item->product->primaryImage()->url() }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center"><i class="fas fa-box text-zinc-600 text-[8px]"></i></div>
                    @endif
                </div>
                @endforeach
                @if($order->items->count() > 4)
                <div class="w-10 h-10 rounded-xl bg-zinc-800 border border-zinc-700 flex items-center justify-center flex-shrink-0">
                    <span class="text-[10px] font-black text-zinc-400">+{{ $order->items->count() - 4 }}</span>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8">{{ $orders->links() }}</div>
    @endif

</div>
@endsection
