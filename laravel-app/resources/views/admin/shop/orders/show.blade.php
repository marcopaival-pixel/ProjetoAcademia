@extends('layouts.admin')

@section('title', 'Pedido ' . $order->order_number)

@section('content')
<div class="max-w-[1000px] mx-auto space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.shop.orders.index') }}" class="w-10 h-10 rounded-xl bg-zinc-800 border border-white/5 flex items-center justify-center text-zinc-400 hover:text-white transition-all">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Detalhes do Pedido</h2>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1">{{ $order->order_number }} · {{ $order->created_at->format('d/m/Y \à\s H:i') }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-400"></i>
        <p class="text-emerald-400 text-sm font-bold">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-400"></i>
        <p class="text-red-400 text-sm font-bold">{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Itens e Dados do Aluno (Coluna Esquerda) --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Dados do Comprador --}}
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-4">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-user text-emerald-400"></i> Aluno / Comprador
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Nome</p>
                        <p class="text-white font-bold">{{ $order->user?->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">E-mail</p>
                        <p class="text-white">{{ $order->user?->email ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Detalhe dos Itens --}}
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-5">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-box text-emerald-400"></i> Itens do Pedido
                </h3>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                    <div class="flex gap-4 pb-4 border-b border-white/5 last:border-0 last:pb-0">
                        <div class="w-14 h-14 rounded-xl overflow-hidden bg-zinc-950 border border-white/5 flex-shrink-0 flex items-center justify-center">
                            @if($item->product?->primaryImage())
                                <img src="{{ $item->product->primaryImage()->url() }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-box text-zinc-700 text-lg"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-white text-sm truncate">{{ $item->product_name }}</p>
                            <p class="text-xs text-zinc-500 mt-1">
                                Qtd: {{ $item->quantity }} · Valor Unitário: R$ {{ number_format($item->unit_price, 2, ',', '.') }}
                            </p>
                            <p class="text-[9px] font-black uppercase tracking-widest mt-1.5
                                {{ $item->product_type === 'physical' ? 'text-blue-400' : ($item->product_type === 'digital' ? 'text-violet-400' : 'text-amber-400') }}">
                                {{ ['physical'=>'Físico','digital'=>'Digital','service'=>'Serviço'][$item->product_type] ?? '' }}
                            </p>
                        </div>
                        <p class="font-black text-white text-sm">R$ {{ number_format($item->total, 2, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Endereço / Entrega --}}
            @if($order->shipping_address || $order->shipping_method)
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-4">
                <h3 class="text-sm font-black text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-truck text-emerald-400"></i> Informações de Entrega
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Método de Envio</p>
                        <p class="text-white font-bold uppercase">{{ ['pickup'=>'Retirada na Academia','correios'=>'Correios','transportadora'=>'Transportadora'][$order->shipping_method] ?? $order->shipping_method }}</p>
                    </div>
                    @if($order->shipping_address)
                    @php $addr = $order->shipping_address; @endphp
                    <div>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-wider">Endereço de Destino</p>
                        <p class="text-zinc-300 text-xs mt-1">
                            {{ $addr['street'] ?? '' }}, {{ $addr['number'] ?? '' }} — {{ $addr['city'] ?? '' }}/{{ $addr['state'] ?? '' }}
                            @if(isset($addr['cep'])) <br>CEP {{ $addr['cep'] }} @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Ações e Status (Coluna Direita) --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Seletor de Status --}}
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-5">
                <h3 class="text-sm font-black text-white uppercase tracking-wider">Atualizar Status</h3>
                
                <form action="{{ route('admin.shop.orders.status.update', $order) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Status do Pedido</label>
                        <select name="status" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Aguardando Pagamento</option>
                            <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Pago</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Em Processamento</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Enviado</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Entregue</option>
                            <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Concluído</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Reembolsado</option>
                        </select>
                    </div>

                    @if($order->shipping_method !== 'pickup')
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Código de Rastreamento</label>
                        <input type="text" name="tracking_code" value="{{ old('tracking_code', $order->tracking_code) }}" placeholder="Ex: BR123456789BR"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all font-mono">
                    </div>
                    @endif

                    <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black text-xs uppercase tracking-widest rounded-2xl transition-all shadow-xl shadow-emerald-500/10">
                        Salvar Status
                    </button>
                </form>
            </div>

            {{-- Resumo Financeiro --}}
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem] space-y-4">
                <h3 class="text-sm font-black text-white uppercase tracking-wider">Financeiro</h3>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-zinc-500 font-black uppercase tracking-wider">Status</span>
                    <span class="font-black text-white">{{ $order->statusLabel() }}</span>
                </div>
                @if($order->payment_method)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-zinc-500 font-black uppercase tracking-wider">Pagamento</span>
                    <span class="font-bold text-zinc-300 uppercase">{{ $order->payment_method }}</span>
                </div>
                @endif
                @if($order->payment_gateway)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-zinc-500 font-black uppercase tracking-wider">Gateway</span>
                    <span class="font-bold text-zinc-300">{{ $order->payment_gateway }}</span>
                </div>
                @endif
                @if($order->points_earned > 0)
                <div class="flex items-center justify-between text-xs">
                    <span class="text-zinc-500 font-black uppercase tracking-wider">Cashback</span>
                    <span class="font-bold text-emerald-400">+{{ number_format($order->points_earned, 0, ',', '.') }} pts</span>
                </div>
                @endif
                <div class="space-y-3 text-sm pt-2 border-t border-white/5">
                    <div class="flex justify-between text-zinc-500">
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
                    <div class="flex justify-between text-zinc-500">
                        <span>Frete</span>
                        <span class="font-bold text-white">R$ {{ number_format($order->shipping_amount, 2, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="border-t border-white/5 pt-3 mt-3 flex justify-between">
                        <span class="font-black text-white uppercase tracking-widest text-xs">Total Geral</span>
                        <span class="font-black text-emerald-400 text-lg">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            @if($order->isCancellable())
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-red-500/20 p-6 rounded-[2.5rem] space-y-4">
                <h3 class="text-sm font-black text-red-400 uppercase tracking-wider">
                    {{ $order->status === 'paid' ? 'Reembolsar Pedido' : 'Cancelar Pedido' }}
                </h3>
                <p class="text-xs text-zinc-500">
                    {{ $order->status === 'paid'
                        ? 'Estorna o pagamento (gateway ou pontos) e devolve o estoque.'
                        : 'Cancela o pedido pendente e libera o estoque reservado.' }}
                </p>
                <form action="{{ route('admin.shop.orders.refund', $order) }}" method="POST" class="space-y-4"
                    onsubmit="return confirm('Confirma {{ $order->status === 'paid' ? 'o reembolso' : 'o cancelamento' }} deste pedido?');">
                    @csrf
                    <div>
                        <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Motivo (opcional)</label>
                        <input type="text" name="reason" maxlength="500" placeholder="Ex: Solicitação do cliente"
                            class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-red-600 transition-all">
                    </div>
                    <button type="submit" class="w-full py-4 bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 text-red-400 font-black text-xs uppercase tracking-widest rounded-2xl transition-all">
                        {{ $order->status === 'paid' ? 'Reembolsar' : 'Cancelar Pedido' }}
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
