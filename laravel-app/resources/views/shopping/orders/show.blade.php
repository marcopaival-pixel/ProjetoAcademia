@extends('layouts.app')

@section('title', 'Pedido ' . $order->order_number . ' — Shopping Fitness')

@section('content')
<div class="max-w-[900px] mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('shopping.orders.index') }}" class="w-10 h-10 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center hover:border-emerald-500/40 transition-all text-zinc-400 hover:text-white">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">{{ $order->order_number }}</h1>
            <p class="text-xs text-zinc-500">{{ $order->created_at->format('d/m/Y \à\s H:i') }}</p>
        </div>
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
        <span class="px-3 py-1.5 text-[10px] font-black uppercase tracking-widest rounded-full border {{ $statusColors[$order->status] ?? 'text-zinc-400 bg-zinc-800 border-zinc-700' }}">
            {{ $order->statusLabel() }}
        </span>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Itens ───────────────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6">
                <h2 class="text-sm font-black text-white uppercase tracking-widest mb-5 flex items-center gap-2">
                    <i class="fas fa-box text-emerald-400 text-xs"></i>
                    Itens ({{ $order->items->count() }})
                </h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex gap-4 pb-4 border-b border-zinc-800 last:border-0 last:pb-0">
                        <div class="w-16 h-16 rounded-2xl overflow-hidden bg-zinc-800 flex-shrink-0">
                            @if($item->product?->primaryImage())
                                <img src="{{ $item->product->primaryImage()->url() }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center"><i class="fas fa-box text-zinc-600"></i></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-black text-white">{{ $item->product_name }}</p>
                            <p class="text-xs text-zinc-500 mt-0.5">{{ $item->quantity }}x R$ {{ number_format($item->unit_price, 2, ',', '.') }}</p>
                            <p class="text-[10px] font-bold uppercase tracking-widest mt-1
                                {{ $item->product_type === 'physical' ? 'text-blue-400' : ($item->product_type === 'digital' ? 'text-violet-400' : 'text-amber-400') }}">
                                {{ ['physical' => 'Físico', 'digital' => 'Digital', 'service' => 'Serviço'][$item->product_type] ?? '' }}
                            </p>
                            @if($item->isDownloadable() && $item->canDownload())
                            <a href="{{ route('shopping.orders.download', $item->download_token) }}" class="inline-flex items-center gap-1.5 mt-2 text-[10px] font-black text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-full hover:bg-emerald-500/20 transition-all uppercase tracking-widest">
                                <i class="fas fa-download"></i> Baixar arquivo
                            </a>
                            @endif
                        </div>
                        <p class="text-sm font-black text-white flex-shrink-0">R$ {{ number_format($item->total, 2, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Entrega --}}
            @if($order->shipping_address || $order->tracking_code)
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6">
                <h2 class="text-sm font-black text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-truck text-emerald-400 text-xs"></i>
                    Entrega
                </h2>
                @if($order->tracking_code)
                <p class="text-xs text-zinc-400 mb-2">Código de rastreio: <span class="font-black text-white font-mono">{{ $order->tracking_code }}</span></p>
                @endif
                @if($order->shipping_address)
                @php $addr = $order->shipping_address; @endphp
                <p class="text-xs text-zinc-400">
                    {{ $addr['street'] ?? '' }}, {{ $addr['number'] ?? '' }} — {{ $addr['city'] ?? '' }}/{{ $addr['state'] ?? '' }}
                    @if(isset($addr['cep'])) · CEP {{ $addr['cep'] }} @endif
                </p>
                @endif
            </div>
            @endif
        </div>

        {{-- ── Resumo financeiro + ações ───────────────────────────────── --}}
        <div class="space-y-4">
            <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 space-y-3">
                <h2 class="text-sm font-black text-white uppercase tracking-widest">Valores</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-zinc-400">
                        <span>Subtotal</span><span class="font-bold text-white">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between text-emerald-400">
                        <span>Desconto</span><span class="font-bold">− R$ {{ number_format($order->discount_amount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($order->shipping_amount > 0)
                    <div class="flex justify-between text-zinc-400">
                        <span>Frete</span><span class="font-bold text-white">R$ {{ number_format($order->shipping_amount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between border-t border-zinc-700 pt-3">
                        <span class="font-black text-white uppercase tracking-widest">Total</span>
                        <span class="font-black text-emerald-400 text-xl">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Pagamento --}}
            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 grid grid-cols-2 gap-3 text-center">
                <div>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Pagamento</p>
                    <p class="text-xs font-black text-white uppercase">{{ $order->payment_method ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Gateway</p>
                    <p class="text-xs font-black text-white uppercase">{{ $order->payment_gateway ?? '—' }}</p>
                </div>
            </div>

            {{-- Cancelar --}}
            @if($order->isCancellable())
            <form action="{{ route('shopping.orders.cancel', $order) }}" method="POST"
                  onsubmit="return confirm('Tem certeza que deseja cancelar este pedido?')">
                @csrf
                <button type="submit"
                    class="w-full py-3 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 hover:border-rose-500/40 text-rose-400 font-black rounded-2xl transition-all text-xs uppercase tracking-widest">
                    <i class="fas fa-times-circle mr-1.5"></i>Cancelar Pedido
                </button>
            </form>
            @endif
        </div>
    </div>

</div>
@endsection
