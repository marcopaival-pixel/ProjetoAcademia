@extends('layouts.app')

@section('title', 'Gestão de Créditos de IA — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up mx-auto px-4 md:px-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 pb-4 border-b border-zinc-900">
        <div class="space-y-2">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.1)]">Inteligência Artificial Ativa</span>
                <span class="text-zinc-700">•</span>
                <span class="text-zinc-500 text-xs font-bold">{{ now()->translatedFormat('d \d\e F, Y') }}</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Gestão de <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 to-emerald-600">Créditos IA</span>
            </h1>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('ai-credits.index') }}" class="group relative px-8 py-4 bg-emerald-600 text-zinc-950 font-black rounded-2xl overflow-hidden transition-all hover:pr-12 active:scale-95 shadow-lg shadow-emerald-500/20">
                <span class="relative z-10 uppercase text-xs tracking-widest">Adquirir Créditos</span>
                <i data-lucide="plus" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 opacity-0 group-hover:opacity-100 transition-all"></i>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Balance -->
        <div class="bg-zinc-900 border border-emerald-500/10 p-8 rounded-[2.5rem] relative overflow-hidden shadow-2xl group">
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-emerald-500/5 blur-3xl group-hover:scale-150 transition-transform"></div>
            <div class="relative z-10">
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Saldo Total</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-5xl font-black text-white tabular-nums">{{ $wallet->balance }}</span>
                    <span class="text-xs font-bold text-zinc-600 uppercase tracking-widest">Créditos</span>
                </div>
                <div class="mt-6 flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                    <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Disponíveis para uso</span>
                </div>
            </div>
        </div>

        <!-- Monthly Allowance -->
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] relative overflow-hidden shadow-2xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Incluso no Plano</p>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-white tabular-nums">{{ $wallet->monthly_allowance }}</span>
                <span class="text-xs font-bold text-zinc-600 uppercase tracking-widest">/mês</span>
            </div>
            <p class="mt-6 text-[10px] text-zinc-500 font-bold uppercase tracking-tighter">Renova em: {{ $wallet->renewal_date?->format('d/m/Y') ?: 'Manual' }}</p>
        </div>

        <!-- Extras -->
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] relative overflow-hidden shadow-2xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Créditos Extras</p>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-emerald-400 tabular-nums">{{ $wallet->extra_credits }}</span>
            </div>
            <p class="mt-6 text-[10px] text-zinc-500 font-bold uppercase tracking-tighter">Adquiridos separadamente</p>
        </div>

        <!-- Usage Month -->
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] relative overflow-hidden shadow-2xl">
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-2">Consumo no Mês</p>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-white tabular-nums">{{ $usageMonth }}</span>
            </div>
            <p class="mt-6 text-[10px] text-zinc-500 font-bold uppercase tracking-tighter">Total utilizado em {{ now()->translatedFormat('F') }}</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Feature Costs Table -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-zinc-900 border border-zinc-800 rounded-[3rem] p-10 shadow-2xl">
                <h3 class="text-xl font-black text-white uppercase tracking-tighter mb-8 flex items-center gap-3">
                    <i data-lucide="calculator" class="w-6 h-6 text-emerald-500"></i>
                    Tabela de Custos
                </h3>
                <div class="space-y-4">
                    @foreach($featureCosts as $cost)
                    <div class="group flex items-center justify-between p-4 bg-zinc-950 rounded-2xl border border-white/5 hover:border-emerald-500/20 transition-all">
                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest group-hover:text-white transition-colors">{{ $cost->feature_name }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-white tabular-nums">{{ $cost->credits_required }}</span>
                            <i data-lucide="zap" class="w-3 h-3 text-emerald-500/50"></i>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="mt-10 p-6 bg-emerald-500/5 border border-emerald-500/10 rounded-2xl">
                    <p class="text-[9px] text-emerald-500 font-black uppercase tracking-[0.2em] mb-2">Economia Ativa</p>
                    <p class="text-[10px] text-zinc-400 font-medium leading-relaxed italic">"Respostas idênticas consultadas no mesmo dia não consomem novos créditos através do nosso cache neural."</p>
                </div>
            </div>
        </div>

        <!-- Usage History -->
        <div class="lg:col-span-2 bg-zinc-900 border border-zinc-800 rounded-[3rem] overflow-hidden shadow-2xl flex flex-col">
            <div class="p-10 border-b border-zinc-800 flex items-center justify-between">
                <h3 class="text-2xl font-black text-white uppercase tracking-tighter">Histórico de Transações</h3>
                <i data-lucide="history" class="w-7 h-7 text-zinc-700"></i>
            </div>
            
            <div class="flex-grow overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-zinc-500 uppercase tracking-widest border-b border-zinc-800/50">
                            <th class="px-10 py-6 italic">Ação / Motivo</th>
                            <th class="px-10 py-6 italic">Data e Hora</th>
                            <th class="px-10 py-6 italic">Créditos</th>
                            <th class="px-10 py-6 italic">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/30">
                        @forelse($history as $tx)
                        <tr class="group hover:bg-white/[0.01] transition-all">
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-zinc-950 border border-zinc-800 rounded-xl flex items-center justify-center transition-all group-hover:border-emerald-500/30">
                                        @switch($tx->type)
                                            @case('usage') <i data-lucide="zap" class="w-5 h-5 text-emerald-500"></i> @break
                                            @case('purchase') <i data-lucide="shopping-cart" class="w-5 h-5 text-emerald-500"></i> @break
                                            @case('monthly') <i data-lucide="calendar" class="w-5 h-5 text-blue-500"></i> @break
                                            @case('bonus') <i data-lucide="gift" class="w-5 h-5 text-amber-500"></i> @break
                                            @case('refund') <i data-lucide="refresh-ccw" class="w-5 h-5 text-red-500"></i> @break
                                            @default <i data-lucide="info" class="w-5 h-5 text-zinc-500"></i>
                                        @endswitch
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-white leading-tight uppercase tracking-tight">{{ $tx->description }}</p>
                                        <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">{{ strtoupper($tx->type) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <p class="text-sm font-bold text-zinc-400">{{ $tx->created_at->format('d/m/Y') }}</p>
                                <p class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">{{ $tx->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-2">
                                    <span class="text-lg font-black tabular-nums {{ $tx->credits > 0 ? 'text-emerald-500' : 'text-zinc-500' }}">
                                        {{ $tx->credits > 0 ? '+' : '' }}{{ $tx->credits }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-10 py-6">
                                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[9px] font-black uppercase tracking-widest rounded-lg border border-emerald-500/10">Processado</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-10 py-20 text-center">
                                <div class="flex flex-col items-center gap-6">
                                    <div class="w-20 h-20 bg-zinc-950 rounded-[2rem] border border-dashed border-zinc-800 flex items-center justify-center text-zinc-800">
                                        <i data-lucide="ghost" class="w-10 h-10"></i>
                                    </div>
                                    <p class="text-zinc-600 font-bold italic text-sm">Nenhuma transação registrada no histórico.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($history->hasPages())
            <div class="p-10 border-t border-zinc-800 bg-zinc-950/20">
                {{ $history->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endpush

<style>
    body {
        background-color: #080a0f;
        background-image: 
            radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.08) 0, transparent 40%),
            radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.08) 0, transparent 40%);
        background-attachment: fixed;
    }
</style>
@endsection
