@extends('layouts.admin')

@section('title', 'Pedidos Recebidos')

@section('content')
<div class="space-y-10 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Pedidos do Shopping</h2>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1">Acompanhe as vendas, pagamentos e envios</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.shop.points.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-coins text-emerald-400"></i> Pontos
            </a>
            <a href="{{ route('admin.shop.products.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Voltar aos Produtos
            </a>
        </div>
    </div>

    @isset($stats)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-zinc-900/60 border border-white/10 p-5 rounded-2xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Pendentes</p>
            <p class="text-2xl font-black text-white mt-1">{{ $stats['pending_count'] }}</p>
        </div>
        <div class="bg-zinc-900/60 border border-white/10 p-5 rounded-2xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Receita hoje</p>
            <p class="text-2xl font-black text-emerald-400 mt-1">R$ {{ number_format($stats['paid_today'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-zinc-900/60 border border-white/10 p-5 rounded-2xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Receita do mês</p>
            <p class="text-2xl font-black text-emerald-400 mt-1">R$ {{ number_format($stats['month_revenue'], 2, ',', '.') }}</p>
        </div>
    </div>
    @endisset

    <!-- Search & Filters -->
    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
        <form action="{{ route('admin.shop.orders.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-zinc-600 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por número do pedido ou nome do aluno..." 
                    class="w-full bg-zinc-950 border border-white/5 p-4 pl-14 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
            </div>
            <div class="flex gap-2">
                <select name="status" onchange="this.form.submit()" class="flex-1 bg-zinc-950 border border-white/5 p-4 rounded-2xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                    <option value="">Todos os status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Aguardando Pagamento</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pago</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Em Processamento</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Enviado</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Entregue</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Concluído</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.shop.orders.index') }}" class="px-5 bg-red-500/10 text-red-500 flex items-center justify-center rounded-2xl hover:bg-red-500/20 transition-all" title="Limpar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 rounded-[2.5rem] overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] text-zinc-500 font-black uppercase tracking-widest">
                        <th class="p-6">Pedido</th>
                        <th class="p-6">Aluno</th>
                        <th class="p-6">Data</th>
                        <th class="p-6">Itens</th>
                        <th class="p-6">Total</th>
                        <th class="p-6">Status</th>
                        <th class="p-6 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm text-zinc-300">
                    @forelse($orders as $order)
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
                    <tr class="hover:bg-white/[0.01] transition-colors">
                        <td class="p-6 font-bold text-white font-mono">{{ $order->order_number }}</td>
                        <td class="p-6">
                            <p class="font-bold text-white">{{ $order->user?->name ?? '—' }}</p>
                            <p class="text-xs text-zinc-500 mt-0.5">{{ $order->user?->email }}</p>
                        </td>
                        <td class="p-6 text-xs text-zinc-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-6 text-xs font-bold text-zinc-400">{{ $order->items->count() }} item(ns)</td>
                        <td class="p-6 font-black text-emerald-400">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                        <td class="p-6">
                            <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-widest rounded-full border {{ $statusColors[$order->status] ?? '' }}">
                                {{ $order->statusLabel() }}
                            </span>
                        </td>
                        <td class="p-6 text-right">
                            <a href="{{ route('admin.shop.orders.show', $order) }}" class="px-4 py-2 bg-zinc-950 border border-white/5 hover:border-emerald-500/30 text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all inline-block">
                                Detalhes
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-zinc-500">Nenhum pedido encontrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
        <div class="p-6 border-t border-white/5">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
