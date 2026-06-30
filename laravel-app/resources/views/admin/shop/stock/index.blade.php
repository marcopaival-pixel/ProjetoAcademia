@extends('layouts.admin')

@section('title', 'Alertas de Estoque')

@section('content')
<div class="space-y-10 animate-fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Estoque do Shopping</h2>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1">Produtos com estoque baixo ou zerado</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('admin.shop.stock.notify') }}" method="POST"
                onsubmit="return confirm('Enviar alerta de estoque a todos os administradores?');">
                @csrf
                <button type="submit" class="px-6 py-4 bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/30 text-amber-300 text-xs font-black uppercase tracking-widest rounded-2xl transition-all">
                    <i class="fas fa-bell mr-2"></i> Notificar admins
                </button>
            </form>
            <a href="{{ route('admin.shop.products.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Produtos
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-400"></i>
        <p class="text-emerald-400 text-sm font-bold">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 bg-rose-500/10 border border-rose-500/20 rounded-2xl flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-rose-400"></i>
        <p class="text-rose-400 text-sm font-bold">{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-zinc-900/60 border border-amber-500/20 p-5 rounded-2xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Estoque baixo</p>
            <p class="text-3xl font-black text-amber-400 mt-1">{{ $lowStock->count() }}</p>
        </div>
        <div class="bg-zinc-900/60 border border-red-500/20 p-5 rounded-2xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Sem estoque</p>
            <p class="text-3xl font-black text-red-400 mt-1">{{ $outOfStock->count() }}</p>
        </div>
    </div>

    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
        <h3 class="text-sm font-black text-white uppercase tracking-wider mb-5">Produtos abaixo do limite</h3>
        @if($lowStock->isEmpty())
            <p class="text-zinc-500 text-sm">Nenhum produto com estoque baixo.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[10px] text-zinc-500 font-black uppercase tracking-wider text-left">
                            <th class="pb-3">Produto</th>
                            <th class="pb-3">Categoria</th>
                            <th class="pb-3 text-right">Qtd</th>
                            <th class="pb-3 text-right">Alerta</th>
                            <th class="pb-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($lowStock as $product)
                        <tr>
                            <td class="py-3 text-white font-bold">{{ $product->name }}</td>
                            <td class="py-3 text-zinc-400">{{ $product->category?->name ?? '—' }}</td>
                            <td class="py-3 text-right font-black {{ $product->stock_quantity <= 0 ? 'text-red-400' : 'text-amber-400' }}">
                                {{ $product->stock_quantity }}
                            </td>
                            <td class="py-3 text-right text-zinc-500">{{ $product->stock_alert_threshold }}</td>
                            <td class="py-3 text-right">
                                <a href="{{ route('admin.shop.products.edit', $product) }}" class="text-xs font-black uppercase tracking-wider text-emerald-400 hover:text-emerald-300">
                                    Editar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
