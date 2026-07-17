@extends('layouts.admin')

@section('title', 'Dashboard Financeiro Global')

@section('content')
<div class="animate-fade-in space-y-6">
    
    <!-- Header -->
    <div class="mb-10 animate-fade-in flex flex-wrap items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="px-2.5 py-1 rounded bg-emerald-600/10 border border-emerald-500/20 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-emerald-400 text-[9px] font-black uppercase tracking-widest">Controle Financeiro em Tempo Real</span>
                </div>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter">
                Saúde <span class="text-emerald-500">Financeira</span>
            </h1>
        </div>

        <!-- Período Seletor -->
        <div class="flex bg-[#11141b]/80 p-1 rounded-xl border border-white/5">
            <a href="?period=week" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $period == 'week' ? 'bg-emerald-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">Semana</a>
            <a href="?period=month" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $period == 'month' ? 'bg-emerald-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">Mês</a>
            <a href="?period=year" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $period == 'year' ? 'bg-emerald-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">Ano</a>
            <a href="?period=all" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all {{ $period == 'all' ? 'bg-emerald-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white' }}">Tudo</a>
        </div>
    </div>

    <!-- Principais KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Receita Período -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-600/10 rounded-full blur-2xl group-hover:bg-emerald-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Receita no Período</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="text-3xl font-black text-white tracking-tight">R$ {{ number_format($metrics['period_revenue'], 2, ',', '.') }}</div>
            <div class="mt-2 flex items-center gap-2">
                <span class="text-[9px] text-zinc-600 font-bold uppercase">Total Acumulado: R$ {{ number_format($metrics['total_revenue'], 2, ',', '.') }}</span>
            </div>
        </div>

        <!-- Faturas -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-600/10 rounded-full blur-2xl group-hover:bg-blue-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Status de Cobrança</span>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <div class="text-3xl font-black text-white tracking-tight">{{ $metrics['paid_invoices'] }}</div>
                    <span class="text-[9px] text-emerald-400 font-bold uppercase">Pagas</span>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-black text-zinc-400 tracking-tight">{{ $metrics['pending_invoices'] }}</div>
                    <span class="text-[9px] text-amber-500 font-bold uppercase">Pendentes</span>
                </div>
            </div>
        </div>

        <!-- Inadimplência -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-600/10 rounded-full blur-2xl group-hover:bg-rose-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Risco e Inadimplência</span>
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center text-rose-500">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <div class="text-3xl font-black text-rose-500 tracking-tight">{{ $metrics['delinquent_users'] }}</div>
                    <span class="text-[9px] text-rose-400 font-bold uppercase">Atrasados</span>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-black text-zinc-400 tracking-tight">{{ $metrics['blocked_users'] }}</div>
                    <span class="text-[9px] text-zinc-500 font-bold uppercase">Bloqueados</span>
                </div>
            </div>
        </div>

        <!-- Créditos IA -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-600/10 rounded-full blur-2xl group-hover:bg-purple-600/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Economia de IA</span>
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center text-purple-500">
                    <i class="fas fa-brain"></i>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <div class="text-3xl font-black text-purple-400 tracking-tight">{{ number_format($metrics['ai_credits_sold'], 0, ',', '.') }}</div>
                    <span class="text-[9px] text-purple-500 font-bold uppercase">Vendidos</span>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-black text-zinc-400 tracking-tight">{{ number_format($metrics['ai_credits_used'], 0, ',', '.') }}</div>
                    <span class="text-[9px] text-zinc-500 font-bold uppercase">Consumidos</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos e Tabelas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Receita por Plano -->
        <div class="glass-card p-6 rounded-2xl">
            <h3 class="text-base font-black text-white tracking-tight mb-6">Receita por Plano</h3>
            <div class="space-y-4">
                @foreach($metrics['revenue_by_plan'] as $plan)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-zinc-400">{{ $plan->name }}</span>
                        <span class="text-xs font-black text-white">R$ {{ number_format($plan->revenue, 2, ',', '.') }}</span>
                    </div>
                    <div class="h-1.5 w-full bg-zinc-900 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $metrics['total_revenue'] > 0 ? ($plan->revenue / $metrics['total_revenue']) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Clínicas / Academias -->
        <div class="glass-card p-6 rounded-2xl lg:col-span-2">
            <h3 class="text-base font-black text-white tracking-tight mb-6">Top 10 Clínicas (Receita)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-white/5">
                            <th class="pb-3 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Clínica</th>
                            <th class="pb-3 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Faturamento</th>
                            <th class="pb-3 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Status</th>
                            <th class="pb-3 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($metrics['revenue_by_clinic'] as $clinic)
                        <tr class="group hover:bg-white/[0.02] transition-colors">
                            <td class="py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-900 flex items-center justify-center text-[10px] font-black text-zinc-500">
                                        {{ substr($clinic->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-black text-white">{{ $clinic->name }}</div>
                                        <div class="text-[9px] text-zinc-600 font-bold uppercase">{{ $clinic->city ?? 'N/D' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-right">
                                <span class="text-xs font-black text-emerald-500">R$ {{ number_format($clinic->revenue, 2, ',', '.') }}</span>
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-500 text-[8px] font-black uppercase">Ativa</span>
                            </td>
                            <td class="py-4 text-center">
                                <a href="#" class="text-zinc-600 hover:text-white transition-colors"><i class="fas fa-eye text-xs"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Exportação -->
    <div class="flex justify-center gap-4 py-10">
        <button class="px-8 py-3 bg-[#11141b] border border-white/5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-white hover:border-emerald-500/50 transition-all flex items-center gap-2">
            <i class="fas fa-file-pdf text-rose-500"></i> Exportar PDF
        </button>
        <button class="px-8 py-3 bg-[#11141b] border border-white/5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-white hover:border-emerald-500/50 transition-all flex items-center gap-2">
            <i class="fas fa-file-excel text-emerald-500"></i> Exportar Excel
        </button>
        <button class="px-8 py-3 bg-[#11141b] border border-white/5 rounded-xl text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:text-white hover:border-emerald-500/50 transition-all flex items-center gap-2">
            <i class="fas fa-file-csv text-blue-500"></i> Exportar CSV
        </button>
    </div>

</div>
@endsection
