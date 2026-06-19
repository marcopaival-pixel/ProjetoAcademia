@extends('layouts.app')

@section('title', 'Dashboard Financeiro')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">Financeiro</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold italic">Módulo Profissional</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Dashboard <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-teal-400">Financeiro</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Visão geral das suas receitas, despesas e fluxo de caixa.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('professional.finance.entries.create') }}" class="px-6 py-3 bg-emerald-500 text-zinc-950 font-bold rounded-xl hover:bg-emerald-400 transition-all shadow-lg flex items-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i> Novo Lançamento
            </a>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Receitas -->
        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-6 rounded-[2rem] relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                <i data-lucide="trending-up" class="w-16 h-16 text-emerald-500"></i>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 border border-emerald-500/20">
                    <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black text-zinc-400 uppercase tracking-widest">Receitas do Mês</h3>
                </div>
            </div>
            <div>
                <p class="text-3xl font-black text-white">R$ {{ number_format($revenueMonth, 2, ',', '.') }}</p>
            </div>
        </div>

        <!-- Despesas -->
        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-6 rounded-[2rem] relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                <i data-lucide="trending-down" class="w-16 h-16 text-rose-500"></i>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500 border border-rose-500/20">
                    <i data-lucide="arrow-down-right" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black text-zinc-400 uppercase tracking-widest">Despesas do Mês</h3>
                </div>
            </div>
            <div>
                <p class="text-3xl font-black text-white">R$ {{ number_format($expenseMonth, 2, ',', '.') }}</p>
            </div>
        </div>

        <!-- Lucro -->
        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-6 rounded-[2rem] relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                <i data-lucide="dollar-sign" class="w-16 h-16 text-blue-500"></i>
            </div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 border border-blue-500/20">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-xs font-black text-zinc-400 uppercase tracking-widest">Lucro Líquido</h3>
                </div>
            </div>
            <div>
                <p class="text-3xl font-black {{ $netProfit >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">R$ {{ number_format($netProfit, 2, ',', '.') }}</p>
            </div>
        </div>

        <!-- Alertas -->
        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-6 rounded-[2rem] relative overflow-hidden group flex flex-col justify-center">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500 border border-amber-500/20">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                    </div>
                    <span class="text-sm font-bold text-zinc-300">Pendentes</span>
                </div>
                <span class="text-xl font-black text-white">{{ $unpaidEntriesCount }}</span>
            </div>
            <div class="h-px bg-white/5 my-4"></div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-rose-500/10 flex items-center justify-center text-rose-500 border border-rose-500/20">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    </div>
                    <span class="text-sm font-bold text-zinc-300">Atrasadas</span>
                </div>
                <span class="text-xl font-black text-rose-500">{{ $overdueEntriesCount }}</span>
            </div>
        </div>
    </div>

    <!-- Mais funcionalidades em breve -->
    <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-10 rounded-[3rem] text-center">
        <i data-lucide="construction" class="w-16 h-16 text-zinc-600 mx-auto mb-4"></i>
        <h2 class="text-2xl font-black text-white mb-2">Gráficos e Detalhes</h2>
        <p class="text-zinc-500">Em breve você verá gráficos detalhados do seu fluxo de caixa aqui.</p>
    </div>
</div>
@endsection
