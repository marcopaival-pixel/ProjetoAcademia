@extends('layouts.app')

@section('title', 'Finalizar Compra — Shopping Fitness')

@section('content')
<div class="max-w-[1000px] mx-auto px-4 py-8">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-8">
        <a href="{{ route('shopping.cart.index') }}" class="w-10 h-10 rounded-2xl bg-zinc-800 border border-zinc-700 flex items-center justify-center hover:border-emerald-500/40 transition-all text-zinc-400 hover:text-white">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-black text-white tracking-tight">Finalizar Compra</h1>
            <p class="text-xs text-zinc-500">Revise seus dados antes de confirmar</p>
        </div>
    </div>

    @if(session('error'))
    <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-rose-400"></i>
        <p class="text-rose-400 text-sm font-bold">{{ session('error') }}</p>
    </div>
    @endif

    <form action="{{ route('shopping.checkout.process') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

            {{-- ── Formulário ──────────────────────────────────────────────── --}}
            <div class="lg:col-span-3 space-y-6">

                {{-- Método de pagamento --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6">
                    <h2 class="text-sm font-black text-white uppercase tracking-widest mb-5 flex items-center gap-2">
                        <i class="fas fa-credit-card text-emerald-400 text-xs"></i>
                        Forma de Pagamento
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach([
                            ['pix', 'fa-qrcode', 'Pix', 'Aprovação imediata'],
                            ['credit_card', 'fa-credit-card', 'Cartão de Crédito', 'Em até 12x'],
                            ['points', 'fa-coins', 'Pontos / Cashback', 'Use seu saldo'],
                        ] as [$val, $icon, $label, $sub])
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_method" value="{{ $val }}"
                                   {{ old('payment_method', 'pix') === $val ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="p-4 bg-zinc-800 border border-zinc-700 rounded-2xl text-center
                                peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 transition-all hover:border-zinc-500">
                                <i class="fas {{ $icon }} text-xl mb-2 text-zinc-400 peer-checked:text-emerald-400 block"></i>
                                <p class="text-sm font-black text-white">{{ $label }}</p>
                                <p class="text-[10px] text-zinc-500 mt-0.5">{{ $sub }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Entrega (apenas se houver itens físicos) --}}
                @if($summary['cart']->items->contains(fn($i) => $i->product?->isPhysical()))
                <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6" x-data="{ method: 'pickup' }">
                    <h2 class="text-sm font-black text-white uppercase tracking-widest mb-5 flex items-center gap-2">
                        <i class="fas fa-truck text-emerald-400 text-xs"></i>
                        Entrega
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
                        @foreach([
                            ['pickup', 'fa-store', 'Retirar na Academia', 'Gratuito'],
                            ['correios', 'fa-envelope', 'Correios', 'Calculado no envio'],
                            ['transportadora', 'fa-truck', 'Transportadora', 'Calculado no envio'],
                        ] as [$val, $icon, $label, $sub])
                        <label class="cursor-pointer" @click="method = '{{ $val }}'">
                            <input type="radio" name="shipping_method" value="{{ $val }}"
                                   {{ $val === 'pickup' ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="p-4 bg-zinc-800 border border-zinc-700 rounded-2xl text-center
                                peer-checked:border-emerald-500 peer-checked:bg-emerald-500/10 transition-all hover:border-zinc-500">
                                <i class="fas {{ $icon }} text-lg mb-2 text-zinc-400 block"></i>
                                <p class="text-xs font-black text-white">{{ $label }}</p>
                                <p class="text-[10px] text-zinc-500">{{ $sub }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    {{-- Endereço (somente se não for retirada) --}}
                    <div x-show="method !== 'pickup'" x-transition class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest block mb-1.5">CEP</label>
                                <input type="text" name="shipping_address[cep]" placeholder="00000-000"
                                    class="w-full bg-zinc-800 border border-zinc-700 text-white placeholder-zinc-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500/50 transition-colors">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest block mb-1.5">Rua</label>
                                <input type="text" name="shipping_address[street]"
                                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500/50 transition-colors">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest block mb-1.5">Número</label>
                                <input type="text" name="shipping_address[number]"
                                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500/50 transition-colors">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest block mb-1.5">Cidade</label>
                                <input type="text" name="shipping_address[city]"
                                    class="w-full bg-zinc-800 border border-zinc-700 text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500/50 transition-colors">
                            </div>
                            <div>
                                <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest block mb-1.5">Estado</label>
                                <input type="text" name="shipping_address[state]" maxlength="2" placeholder="SP"
                                    class="w-full bg-zinc-800 border border-zinc-700 text-white placeholder-zinc-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500/50 transition-colors uppercase">
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Observações --}}
                <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6">
                    <h2 class="text-sm font-black text-white uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="fas fa-comment text-emerald-400 text-xs"></i>
                        Observações (opcional)
                    </h2>
                    <textarea name="notes" rows="3" placeholder="Alguma observação para o pedido?"
                        class="w-full bg-zinc-800 border border-zinc-700 text-white placeholder-zinc-600 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-emerald-500/50 transition-colors resize-none"></textarea>
                </div>
            </div>

            {{-- ── Resumo lateral ──────────────────────────────────────────── --}}
            <div class="lg:col-span-2">
                <div class="sticky top-4 bg-zinc-900 border border-zinc-800 rounded-[2rem] p-6 space-y-5">
                    <h2 class="text-sm font-black text-white uppercase tracking-widest">Resumo do Pedido</h2>

                    {{-- Itens --}}
                    <div class="space-y-3 max-h-48 overflow-y-auto">
                        @foreach($summary['cart']->items as $item)
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-xl overflow-hidden bg-zinc-800 flex-shrink-0">
                                @if($item->product->primaryImage())
                                    <img src="{{ $item->product->primaryImage()->url() }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center"><i class="fas fa-box text-zinc-600 text-xs"></i></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold text-white truncate">{{ $item->product->name }}</p>
                                <p class="text-[10px] text-zinc-500">{{ $item->quantity }}x R$ {{ number_format($item->unit_price, 2, ',', '.') }}</p>
                            </div>
                            <p class="text-xs font-black text-white flex-shrink-0">R$ {{ number_format($item->lineTotal(), 2, ',', '.') }}</p>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-zinc-800 pt-4 space-y-2 text-sm">
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
                        <div class="flex justify-between border-t border-zinc-700 pt-3 mt-3">
                            <span class="font-black text-white uppercase tracking-widest">Total</span>
                            <span class="font-black text-emerald-400 text-xl">R$ {{ number_format($summary['total'], 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl transition-all active:scale-95 shadow-xl shadow-emerald-500/10 text-sm uppercase tracking-widest flex items-center justify-center gap-2">
                        <i class="fas fa-lock"></i>
                        Confirmar Pedido
                    </button>

                    <div class="flex items-center justify-center gap-2 text-[10px] text-zinc-600">
                        <i class="fas fa-shield-alt"></i>
                        <span>Pagamento seguro e criptografado</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
