@extends('layouts.professional')

@section('title', 'Financeiro Pro — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up max-w-[1400px] mx-auto px-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-4 border-b border-zinc-900">
        <div class="flex items-center gap-6">
            <a href="{{ route('professional.reports.index') }}" class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-600 hover:text-emerald-500 hover:border-emerald-500/30 transition-all shadow-xl">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="w-14 h-14 rounded-2xl bg-amber-500 text-zinc-950 flex items-center justify-center shadow-lg shadow-amber-500/20">
                 <i class="fas fa-file-invoice-dollar text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">Financeiro <span class="text-amber-500">Pro</span></h1>
                <p class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.3em] mt-1">
                    Gestão de Receita • {{ $patientsLabel }} Vinculados
                </p>
            </div>
        </div>

        <a href="{{ route('professional.reports.export', ['type' => 'detailed_finance']) }}" 
           class="px-5 py-2.5 rounded-2xl bg-zinc-950 border border-zinc-800 text-[10px] font-black text-zinc-400 uppercase tracking-widest hover:text-amber-500 hover:border-amber-500/30 transition-all shadow-xl flex items-center gap-3 group">
            <i class="fas fa-file-csv text-sm group-hover:scale-110 transition-transform"></i>
            Exportar CSV
        </a>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 blur-[50px] rounded-full"></div>
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em] mb-4 block">Faturamento Estimado</span>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-white italic tracking-tighter tabular-nums">R$ {{ number_format($data['total_revenue_estimated'], 2, ',', '.') }}</span>
            </div>
            <p class="text-[9px] font-black text-zinc-700 mt-4 uppercase tracking-widest">Baseado em assinaturas ativas</p>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 blur-[50px] rounded-full"></div>
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em] mb-4 block">Assinaturas Ativas</span>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-black text-emerald-500 italic tracking-tighter tabular-nums">{{ $data['total_active_subscriptions'] }}</span>
            </div>
            <p class="text-[9px] font-black text-zinc-700 mt-4 uppercase tracking-widest">Fluxo de receita recorrente</p>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/5 blur-[50px] rounded-full"></div>
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em] mb-4 block">Aguardando Pagamento</span>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-black text-amber-500 italic tracking-tighter tabular-nums">{{ $data['total_pending'] }}</span>
            </div>
            <p class="text-[9px] font-black text-zinc-700 mt-4 uppercase tracking-widest">Pendências de renovação</p>
        </div>
    </div>

    <!-- Subscriptions List -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-[3.5rem] overflow-hidden shadow-2xl">
        <div class="p-10 border-b border-zinc-800 flex items-center justify-between">
            <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">Histórico de <span class="text-amber-500">Assinaturas</span></h3>
            <span class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">{{ $patientsLabel }} & Planos</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-zinc-950/50">
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest">{{ $patientLabel }}</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest">Plano</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest text-center">Status</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest text-right">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($data['subscriptions'] as $sub)
                    <tr class="hover:bg-amber-500/[0.02] transition-all group">
                        <td class="py-6 px-10">
                            <div class="text-sm font-black text-white">{{ $sub['user']['name'] ?? 'Usuário' }}</div>
                            <div class="text-[9px] text-zinc-600 font-bold uppercase tracking-tighter">{{ $sub['user']['email'] ?? '' }}</div>
                        </td>
                        <td class="py-6 px-10">
                            <span class="px-2.5 py-1 rounded bg-zinc-950 border border-white/5 text-[10px] font-black text-zinc-500 uppercase tracking-widest">
                                {{ $sub['plan']['name'] ?? 'N/D' }}
                            </span>
                        </td>
                        <td class="py-6 px-10 text-center">
                            @php
                                $status = $sub['status'] ?? 'pending';
                                $colors = [
                                    'active' => 'text-emerald-500 bg-emerald-500/10',
                                    'pending' => 'text-amber-500 bg-amber-500/10',
                                    'overdue' => 'text-rose-500 bg-rose-500/10',
                                ];
                                $colorClass = $colors[$status] ?? 'text-zinc-600 bg-zinc-800';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $colorClass }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="py-6 px-10 text-right">
                            <span class="text-sm font-black text-white tabular-nums">R$ {{ number_format($sub['plan']['price'] ?? 0, 2, ',', '.') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center text-zinc-600 italic">Nenhuma assinatura vinculada encontrada.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection



