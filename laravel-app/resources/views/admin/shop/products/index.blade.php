@extends('layouts.admin')

@section('title', 'Gestão de Produtos')

@section('content')
<div class="space-y-10 animate-fade-in">
    <!-- Header & Action Bar -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Produtos do Shopping</h2>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.3em] mt-1">Gerencie a vitrine de produtos e o estoque</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.shop.products.create') }}" class="px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-3 shadow-xl shadow-emerald-600/20 group">
                <i class="fas fa-plus group-hover:rotate-90 transition-transform"></i> Novo Produto
            </a>
            <a href="{{ route('admin.shop.categories.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-tags text-emerald-400"></i> Categorias
            </a>
            <a href="{{ route('admin.shop.stock.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-boxes-stacked text-amber-400"></i> Estoque
            </a>
            <a href="{{ route('admin.shop.vendors.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-store text-emerald-400"></i> Parceiros
            </a>
            <a href="{{ route('admin.shop.suppliers.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-truck text-amber-400"></i> Fornecedores
            </a>
            <a href="{{ route('admin.shop.reports.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-chart-line text-emerald-400"></i> Relatório
            </a>
            <a href="{{ route('admin.shop.coupons.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-ticket-alt text-emerald-400"></i> Cupons
            </a>
            <a href="{{ route('admin.shop.orders.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-receipt text-emerald-400"></i> Pedidos
            </a>
            <a href="{{ route('admin.shop.points.index') }}" class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all flex items-center gap-2">
                <i class="fas fa-coins text-emerald-400"></i> Pontos
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-400"></i>
        <p class="text-emerald-400 text-sm font-bold">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Search & Filters -->
    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-6 rounded-[2.5rem]">
        <form action="{{ route('admin.shop.products.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-zinc-600 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou SKU..." 
                    class="w-full bg-zinc-950 border border-white/5 p-4 pl-14 rounded-2xl text-white text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
            </div>
            <div>
                <select name="type" onchange="this.form.submit()" class="w-full bg-zinc-950 border border-white/5 p-4 rounded-2xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                    <option value="">Todos os tipos</option>
                    <option value="physical" {{ request('type') === 'physical' ? 'selected' : '' }}>Físico</option>
                    <option value="digital" {{ request('type') === 'digital' ? 'selected' : '' }}>Digital</option>
                    <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Serviço</option>
                </select>
            </div>
            <div class="flex gap-2">
                <select name="status" onchange="this.form.submit()" class="flex-1 bg-zinc-950 border border-white/5 p-4 rounded-2xl text-zinc-400 text-sm outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                    <option value="">Todos os status</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                    <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arquivado</option>
                </select>
                @if(request()->anyFilled(['search', 'type', 'status']))
                    <a href="{{ route('admin.shop.products.index') }}" class="px-5 bg-red-500/10 text-red-500 flex items-center justify-center rounded-2xl hover:bg-red-500/20 transition-all" title="Limpar Filtros">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table List -->
    <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 rounded-[2.5rem] overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 text-[10px] text-zinc-500 font-black uppercase tracking-widest">
                        <th class="p-6">Produto</th>
                        <th class="p-6">Fornecedor</th>
                        <th class="p-6">Tipo</th>
                        <th class="p-6">Preço</th>
                        <th class="p-6">Estoque</th>
                        <th class="p-6">Status</th>
                        <th class="p-6 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-sm text-zinc-300">
                    @forelse($products as $product)
                    <tr class="hover:bg-white/[0.01] transition-colors">
                        <td class="p-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-zinc-950 border border-white/5 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                    @if($product->primaryImage())
                                        <img src="{{ $product->primaryImage()->url() }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-box text-zinc-700"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-white leading-tight">{{ $product->name }}</p>
                                    <p class="text-xs text-zinc-500 mt-1">Cat: {{ $product->category?->name ?? '—' }} | SKU: {{ $product->sku ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-6 text-xs text-zinc-400">
                            {{ $product->supplier?->name ?? '—' }}
                        </td>
                        <td class="p-6">
                            <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-widest rounded-full border 
                                {{ $product->type === 'physical' ? 'text-blue-400 bg-blue-500/10 border-blue-500/20' : 
                                   ($product->type === 'digital' ? 'text-violet-400 bg-violet-500/10 border-violet-500/20' : 
                                                                  'text-amber-400 bg-amber-500/10 border-amber-500/20') }}">
                                {{ ['physical' => 'Físico', 'digital' => 'Digital', 'service' => 'Serviço'][$product->type] ?? $product->type }}
                            </span>
                        </td>
                        <td class="p-6">
                            @if($product->isOnSale())
                                <p class="text-xs text-zinc-500 line-through">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                            @endif
                            <p class="font-black text-emerald-400">R$ {{ number_format($product->currentPrice(), 2, ',', '.') }}</p>
                        </td>
                        <td class="p-6">
                            @if($product->manage_stock)
                                <span class="font-bold {{ $product->stock_quantity <= ($product->stock_alert_threshold ?? 5) ? 'text-amber-400' : 'text-zinc-300' }}">
                                    {{ $product->stock_quantity }} un
                                </span>
                            @else
                                <span class="text-zinc-500">Ilimitado</span>
                            @endif
                        </td>
                        <td class="p-6">
                            <span class="px-2.5 py-1 text-[9px] font-black uppercase tracking-widest rounded-full border
                                {{ $product->status === 'published' ? 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' : 
                                   ($product->status === 'draft' ? 'text-amber-400 bg-amber-500/10 border-amber-500/20' : 
                                                                   'text-zinc-500 bg-zinc-800 border-zinc-700') }}">
                                {{ ['published' => 'Publicado', 'draft' => 'Rascunho', 'archived' => 'Arquivado'][$product->status] ?? $product->status }}
                            </span>
                        </td>
                        <td class="p-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.shop.products.edit', $product) }}" class="w-8 h-8 rounded-lg bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-400 hover:text-white hover:bg-emerald-600 transition-all" title="Editar">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('admin.shop.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-zinc-950 border border-white/5 flex items-center justify-center text-zinc-400 hover:text-rose-500 hover:bg-rose-500/10 transition-all" title="Excluir">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-zinc-500">Nenhum produto cadastrado.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="p-6 border-t border-white/5">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
