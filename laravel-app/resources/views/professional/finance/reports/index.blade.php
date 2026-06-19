@extends('layouts.app')

@section('title', 'Relatórios Financeiros')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-400 text-[10px] font-black uppercase tracking-widest border border-indigo-500/20">Financeiro</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold italic">Relatórios</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Relatórios <span class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-400 to-purple-400">Financeiros</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Análise da saúde financeira do seu negócio e fluxo de caixa.</p>
        </div>
        
        <form method="GET" class="flex items-center gap-4 bg-zinc-900/60 p-2 rounded-2xl border border-white/5">
            <select name="month" class="bg-zinc-950/50 border border-white/5 rounded-xl p-3 text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500/50 outline-none">
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('month', now()->month) == $i ? 'selected' : '' }}>{{ strftime('%B', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
            <select name="year" class="bg-zinc-950/50 border border-white/5 rounded-xl p-3 text-white text-sm font-bold focus:ring-2 focus:ring-indigo-500/50 outline-none">
                @for($y = now()->year; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="px-6 py-3 bg-indigo-500 text-white font-bold rounded-xl hover:bg-indigo-400 transition-all shadow-lg">
                Analisar
            </button>
        </form>
    </div>

    <!-- Visão Geral -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-8 rounded-[2rem]">
            <h3 class="text-xs font-black text-zinc-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i data-lucide="trending-up" class="w-4 h-4 text-emerald-500"></i> Receitas Efetivadas
            </h3>
            <p class="text-4xl font-black text-emerald-400">R$ {{ number_format($monthlyRevenue, 2, ',', '.') }}</p>
        </div>
        
        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-8 rounded-[2rem]">
            <h3 class="text-xs font-black text-zinc-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                <i data-lucide="trending-down" class="w-4 h-4 text-rose-500"></i> Despesas Efetivadas
            </h3>
            <p class="text-4xl font-black text-rose-400">R$ {{ number_format($monthlyExpense, 2, ',', '.') }}</p>
        </div>

        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-8 rounded-[2rem] relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br {{ $netProfit >= 0 ? 'from-emerald-500/5 to-transparent' : 'from-rose-500/5 to-transparent' }}"></div>
            <div class="relative z-10">
                <h3 class="text-xs font-black text-zinc-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i data-lucide="dollar-sign" class="w-4 h-4 {{ $netProfit >= 0 ? 'text-emerald-500' : 'text-rose-500' }}"></i> Lucro Líquido
                </h3>
                <p class="text-4xl font-black {{ $netProfit >= 0 ? 'text-white' : 'text-rose-400' }}">R$ {{ number_format($netProfit, 2, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Despesas por Categoria -->
        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-8 rounded-[2rem]">
            <h2 class="text-xl font-black text-white mb-6">Composição de Despesas</h2>
            
            @if($expensesByCategory->isEmpty())
                <div class="text-center py-10 text-zinc-500 font-bold">Nenhuma despesa registrada neste período.</div>
            @else
                <div class="space-y-6">
                    @foreach($expensesByCategory as $expense)
                        <div>
                            <div class="flex justify-between text-sm font-bold mb-2">
                                <span class="text-white">{{ $expense['name'] }}</span>
                                <span class="text-rose-400">R$ {{ number_format($expense['total'], 2, ',', '.') }} ({{ $expense['percentage'] }}%)</span>
                            </div>
                            <div class="w-full bg-zinc-950 rounded-full h-3 overflow-hidden border border-white/5">
                                <div class="bg-rose-500 h-3 rounded-full" style="width: {{ $expense['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Fluxo de Caixa (Últimos 6 meses) -->
        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-8 rounded-[2rem]">
            <h2 class="text-xl font-black text-white mb-6">Fluxo de Caixa (Últimos 6 Meses)</h2>
            
            <div class="space-y-6">
                @foreach($historicalData as $data)
                    @php
                        $maxVal = max($data['revenue'], $data['expense']);
                        if($maxVal == 0) $maxVal = 1; // prevent div by zero
                        $revPct = ($data['revenue'] / $maxVal) * 100;
                        $expPct = ($data['expense'] / $maxVal) * 100;
                    @endphp
                    <div class="grid grid-cols-12 gap-4 items-center">
                        <div class="col-span-3 text-xs font-black text-zinc-400 uppercase tracking-widest text-right">
                            {{ $data['month_name'] }}
                        </div>
                        <div class="col-span-9 space-y-1">
                            <!-- Barra Receita -->
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-zinc-950 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $revPct }}%"></div>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-400 w-16 text-right">R$ {{ number_format($data['revenue'], 0, ',', '.') }}</span>
                            </div>
                            <!-- Barra Despesa -->
                            <div class="flex items-center gap-2">
                                <div class="w-full bg-zinc-950 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $expPct }}%"></div>
                                </div>
                                <span class="text-[10px] font-bold text-rose-400 w-16 text-right">R$ {{ number_format($data['expense'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8 pt-6 border-t border-white/5 flex items-center justify-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                    <span class="text-xs font-bold text-zinc-400">Receitas</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                    <span class="text-xs font-bold text-zinc-400">Despesas</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
