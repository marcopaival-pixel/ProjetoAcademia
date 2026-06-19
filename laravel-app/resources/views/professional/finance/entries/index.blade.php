@extends('layouts.app')

@section('title', 'Lançamentos Financeiros')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Financeiro</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold italic">Lançamentos</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Controle de <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Lançamentos</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Gerencie suas entradas e saídas de caixa.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('professional.finance.entries.create') }}" class="px-6 py-3 bg-blue-500 text-zinc-950 font-bold rounded-xl hover:bg-blue-400 transition-all shadow-lg flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Novo Lançamento
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-6 rounded-3xl font-bold flex items-center gap-4 animate-bounce-subtle">
            <i data-lucide="check-circle" class="w-6 h-6"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtros -->
    <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-6 rounded-[2rem] shadow-2xl">
        <form action="{{ route('professional.finance.entries.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2 block mb-2">Mês do Vencimento</label>
                <select name="month" class="w-full bg-zinc-950/50 border border-white/5 rounded-xl p-3 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    <option value="">Todos os meses</option>
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ strftime('%B', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2 block mb-2">Tipo</label>
                <select name="type" class="w-full bg-zinc-950/50 border border-white/5 rounded-xl p-3 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    <option value="revenue" {{ request('type') === 'revenue' ? 'selected' : '' }}>Receitas</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Despesas</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase tracking-widest text-zinc-500 px-2 block mb-2">Status</label>
                <select name="status" class="w-full bg-zinc-950/50 border border-white/5 rounded-xl p-3 text-white text-sm font-bold focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    <option value="">Todos</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendente</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Pago/Recebido</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-3 bg-zinc-800 text-white font-bold rounded-xl hover:bg-zinc-700 transition-all">Filtrar</button>
                <a href="{{ route('professional.finance.entries.index') }}" class="p-3 bg-zinc-800/50 text-zinc-400 font-bold rounded-xl hover:bg-zinc-700 hover:text-white transition-all"><i data-lucide="x" class="w-5 h-5"></i></a>
            </div>
        </form>
    </div>

    <!-- Tabela -->
    <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 rounded-[2rem] overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-zinc-950/50">
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-zinc-500">Data Venc.</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-zinc-500">Descrição</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-zinc-500">Categoria</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-zinc-500">Valor (R$)</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-zinc-500">Status</th>
                        <th class="p-6 text-[10px] font-black uppercase tracking-widest text-zinc-500 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($entries as $entry)
                        <tr class="hover:bg-zinc-800/20 transition-colors group">
                            <td class="p-6">
                                <div class="text-sm font-bold text-white">{{ \Carbon\Carbon::parse($entry->due_date)->format('d/m/Y') }}</div>
                                @if($entry->status === 'paid' && $entry->payment_date)
                                    <div class="text-[10px] text-zinc-500 font-bold uppercase mt-1">Pago em {{ \Carbon\Carbon::parse($entry->payment_date)->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="p-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $entry->type === 'revenue' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500' }}">
                                        <i data-lucide="{{ $entry->type === 'revenue' ? 'trending-up' : 'trending-down' }}" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-white">{{ $entry->description }}</div>
                                        @if($entry->notes)
                                            <div class="text-[10px] text-zinc-500 font-medium mt-0.5 truncate max-w-[200px]">{{ $entry->notes }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="p-6">
                                <span class="px-3 py-1 rounded-full bg-zinc-800 text-zinc-300 text-[10px] font-black uppercase tracking-widest">
                                    {{ $entry->category ? $entry->category->name : 'Sem Categoria' }}
                                </span>
                            </td>
                            <td class="p-6">
                                <span class="text-sm font-black {{ $entry->type === 'revenue' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $entry->type === 'revenue' ? '+' : '-' }} R$ {{ number_format($entry->amount, 2, ',', '.') }}
                                </span>
                            </td>
                            <td class="p-6">
                                @if($entry->status === 'paid')
                                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 text-[10px] font-black uppercase tracking-widest flex items-center gap-1 w-max">
                                        <i data-lucide="check" class="w-3 h-3"></i> Pago
                                    </span>
                                @elseif($entry->status === 'pending')
                                    @if(\Carbon\Carbon::parse($entry->due_date)->isPast())
                                        <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-500 border border-rose-500/20 text-[10px] font-black uppercase tracking-widest flex items-center gap-1 w-max">
                                            <i data-lucide="alert-circle" class="w-3 h-3"></i> Atrasado
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-500 border border-amber-500/20 text-[10px] font-black uppercase tracking-widest flex items-center gap-1 w-max">
                                            <i data-lucide="clock" class="w-3 h-3"></i> Pendente
                                        </span>
                                    @endif
                                @else
                                    <span class="px-3 py-1 rounded-full bg-zinc-500/10 text-zinc-400 border border-zinc-500/20 text-[10px] font-black uppercase tracking-widest flex items-center gap-1 w-max">
                                        <i data-lucide="x" class="w-3 h-3"></i> Cancelado
                                    </span>
                                @endif
                            </td>
                            <td class="p-6 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('professional.finance.entries.edit', $entry) }}" class="p-2 rounded-lg text-blue-400 hover:bg-blue-500/10 transition-all">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('professional.finance.entries.destroy', $entry) }}" method="POST" onsubmit="return confirm('Excluir este lançamento?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg text-rose-400 hover:bg-rose-500/10 transition-all">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-zinc-500 font-bold">
                                Nenhum lançamento encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($entries->hasPages())
            <div class="p-6 border-t border-white/5 bg-zinc-950/50">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
