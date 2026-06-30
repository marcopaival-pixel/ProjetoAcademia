@extends('layouts.admin')

@section('title', 'Relatório de Vendas')

@section('content')
<div class="space-y-10 animate-fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Relatório do Shopping</h2>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1">Vendas, receita e comissões de parceiros</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.shop.orders.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-receipt text-emerald-400"></i> Pedidos
            </a>
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

    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
        <form action="{{ route('admin.shop.reports.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">De</label>
                <input type="date" name="from" value="{{ $from->toDateString() }}"
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
            </div>
            <div>
                <label class="text-[10px] text-zinc-500 font-black uppercase tracking-wider block mb-2">Até</label>
                <input type="date" name="to" value="{{ $to->toDateString() }}"
                    class="w-full bg-zinc-950 border border-white/5 p-4 rounded-xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600">
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black text-xs uppercase tracking-widest rounded-2xl transition-all">
                    Atualizar Relatório
                </button>
            </div>
        </form>
        <div class="flex flex-wrap gap-3 mt-4">
            <a href="{{ route('admin.shop.reports.export', ['from' => $from->toDateString(), 'to' => $to->toDateString(), 'type' => 'summary']) }}"
                class="px-5 py-3 bg-zinc-800 hover:bg-zinc-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all flex items-center gap-2">
                <i class="fas fa-file-csv text-emerald-400"></i> Exportar resumo CSV
            </a>
            <a href="{{ route('admin.shop.reports.export', ['from' => $from->toDateString(), 'to' => $to->toDateString(), 'type' => 'orders']) }}"
                class="px-5 py-3 bg-zinc-800 hover:bg-zinc-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all flex items-center gap-2">
                <i class="fas fa-file-csv text-emerald-400"></i> Exportar pedidos CSV
            </a>
        </div>
        <p class="text-[10px] text-zinc-600 mt-3 uppercase tracking-wider">Período: {{ $report['from'] }} — {{ $report['to'] }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-zinc-900/60 border border-white/10 p-5 rounded-2xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Pedidos pagos</p>
            <p class="text-3xl font-black text-white mt-1">{{ $report['order_count'] }}</p>
        </div>
        <div class="bg-zinc-900/60 border border-white/10 p-5 rounded-2xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Receita bruta</p>
            <p class="text-3xl font-black text-emerald-400 mt-1">R$ {{ number_format($report['gross_revenue'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-zinc-900/60 border border-white/10 p-5 rounded-2xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Descontos</p>
            <p class="text-3xl font-black text-amber-400 mt-1">R$ {{ number_format($report['discount_total'], 2, ',', '.') }}</p>
        </div>
        <div class="bg-zinc-900/60 border border-white/10 p-5 rounded-2xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-wider">Comissões pendentes</p>
            <p class="text-3xl font-black text-violet-400 mt-1">R$ {{ number_format($report['pending_commissions'], 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
            <h3 class="text-sm font-black text-white uppercase tracking-wider mb-5">Comissões por parceiro</h3>
            @if($report['by_vendor']->isEmpty())
                <p class="text-zinc-500 text-sm">Nenhuma comissão no período.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-[10px] text-zinc-500 font-black uppercase tracking-wider text-left">
                                <th class="pb-3">Parceiro</th>
                                <th class="pb-3 text-right">Total</th>
                                <th class="pb-3 text-right">Pendente</th>
                                <th class="pb-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($report['by_vendor'] as $row)
                            <tr>
                                <td class="py-3 text-white font-bold">{{ $row['vendor_name'] }}</td>
                                <td class="py-3 text-right text-emerald-400 font-black">R$ {{ number_format($row['commission_total'], 2, ',', '.') }}</td>
                                <td class="py-3 text-right text-violet-400 font-bold">R$ {{ number_format($row['commission_pending'], 2, ',', '.') }}</td>
                                <td class="py-3 text-right">
                                    @if($row['commission_pending'] > 0)
                                    <form action="{{ route('admin.shop.reports.commissions.pay') }}" method="POST" class="inline"
                                        onsubmit="return confirm('Liquidar comissões pendentes deste parceiro?');">
                                        @csrf
                                        <input type="hidden" name="vendor_id" value="{{ $row['vendor_id'] }}">
                                        <input type="hidden" name="until" value="{{ $to->toDateString() }}">
                                        <input type="hidden" name="from" value="{{ $from->toDateString() }}">
                                        <button type="submit" class="text-[10px] font-black uppercase tracking-wider text-emerald-400 hover:text-emerald-300">
                                            Liquidar
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
            <h3 class="text-sm font-black text-white uppercase tracking-wider mb-5">Top produtos</h3>
            @if($report['top_products']->isEmpty())
                <p class="text-zinc-500 text-sm">Sem vendas no período.</p>
            @else
                <div class="space-y-3">
                    @foreach($report['top_products'] as $product)
                    <div class="flex items-center justify-between py-3 border-b border-white/5 last:border-0">
                        <div>
                            <p class="text-white text-sm font-bold">{{ $product->product_name }}</p>
                            <p class="text-[10px] text-zinc-500 mt-1">{{ (int) $product->units_sold }} un.</p>
                        </div>
                        <span class="font-black text-emerald-400 text-sm">R$ {{ number_format((float) $product->revenue, 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if($report['by_status']->isNotEmpty())
    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
        <h3 class="text-sm font-black text-white uppercase tracking-wider mb-5">Pedidos por status (criados no período)</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($report['by_status'] as $status => $total)
            <span class="px-4 py-2 bg-zinc-950 border border-white/5 rounded-xl text-xs font-black uppercase tracking-wider text-zinc-300">
                {{ $status }}: {{ $total }}
            </span>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
