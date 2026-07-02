@extends('layouts.app')

@section('title', 'Faturamento e Planos — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1700px] mx-auto px-6">
    <!-- Header Strategy: Professional Glass Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Gestão de Recursos</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold italic">NexBilling v4.0</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Billing <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Control</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Gerencie seus planos, faturas e fluxos financeiros com transparência e segurança de nível bancário.</p>
        </div>
        
        <div class="flex items-center gap-4 bg-zinc-900/60 backdrop-blur-2xl p-6 rounded-[2rem] border border-white/10 shadow-2xl">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 shadow-lg shadow-emerald-500/5">
                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            </div>
            <div>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Status da Licença</p>
                <div class="flex items-center gap-2">
                    <p class="text-white font-black text-lg">{{ strtoupper($subscription['status']) }}</p>
                    <span class="text-zinc-600">•</span>
                    <p class="text-blue-400 font-bold">{{ $subscription['plan_name'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Planos (Upgrade/Downgrade) - Strategic Bento Layout -->
    <div class="space-y-8">
        <div class="flex items-center justify-between px-6">
            <h3 class="text-zinc-400 font-black text-xs uppercase tracking-[0.3em]">Níveis de Performance Disponíveis</h3>
            <span class="text-[10px] text-zinc-600 font-bold">ANUAL (ECONOMIZE 20%)</span>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            @foreach($plans as $plan)
            <div class="relative group">
                @if($plan['current'])
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-blue-600 text-white text-[9px] font-black px-5 py-1.5 rounded-full uppercase tracking-widest z-20 shadow-xl shadow-blue-500/30">ATIVO AGORA</div>
                @endif
                
                <div class="h-full bg-zinc-900/60 backdrop-blur-2xl border {{ $plan['current'] ? 'border-blue-500/40 shadow-[0_30px_60px_-15px_rgba(59,130,246,0.15)]' : 'border-white/10' }} p-12 rounded-[4rem] flex flex-col transition-all duration-500 hover:scale-[1.02] hover:border-blue-500/20 shadow-2xl overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
                    
                    <div class="relative z-10 mb-10 text-center md:text-left">
                        <h4 class="text-zinc-500 font-black uppercase text-[10px] tracking-[0.2em] mb-4">{{ $plan['name'] }}</h4>
                        <div class="flex items-baseline justify-center md:justify-start gap-2">
                            <span class="text-5xl font-black text-white tracking-tighter">R$ {{ $plan['price'] }}</span>
                            <span class="text-zinc-600 font-bold uppercase text-[10px]">/mês</span>
                        </div>
                    </div>

                    <ul class="space-y-6 mb-12 flex-1 relative z-10">
                        @foreach($plan['features'] as $feature)
                        <li class="flex items-center gap-4 text-zinc-400 text-sm font-bold group/feat">
                            <div class="w-6 h-6 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500 border border-blue-500/20 transition-all group-hover/feat:bg-blue-500 group-hover/feat:text-white">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="group-hover/feat:text-white transition-colors">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>

                    @if($plan['current'])
                        <button class="relative z-10 w-full py-5 rounded-3xl font-black uppercase text-[10px] tracking-widest bg-zinc-800 text-zinc-600 cursor-default border border-white/5">
                            Mantenha Atual
                        </button>
                    @else
                        <a href="{{ route('professional.billing.upgrade', ['plan_id' => $plan['id']]) }}" class="relative z-10 block text-center w-full py-5 rounded-3xl font-black transition-all uppercase text-[10px] tracking-widest bg-white text-zinc-900 hover:bg-blue-400 hover:text-white shadow-2xl">
                            Migrar Plano
                        </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Grid Inferior (Pagamento + Faturas) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Método de Pagamento - Glass Card -->
        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 p-10 rounded-[3.5rem] space-y-8 shadow-2xl">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-black text-white tracking-tight uppercase text-xs tracking-widest">Ativos Transacionais</h3>
                <button class="p-2 bg-zinc-800 rounded-xl text-zinc-500 hover:text-white transition-all"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg></button>
            </div>
            
            <div class="bg-zinc-950/50 p-8 rounded-[2.5rem] border border-white/10 flex items-center justify-between shadow-inner group transition-all hover:border-blue-500/20">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-10 bg-gradient-to-br from-zinc-800 to-zinc-900 rounded-xl flex items-center justify-center border border-white/10 shadow-xl transition-transform group-hover:scale-110">
                        <span class="text-[10px] font-black text-white tracking-widest italic">{{ strtoupper($subscription['card_brand']) }}</span>
                    </div>
                    <div>
                        <p class="text-white font-black text-lg tracking-widest">•••• •••• •••• {{ $subscription['card_last4'] }}</p>
                        <p class="text-zinc-600 text-[10px] uppercase font-black tracking-widest mt-1 italic">Válido até 12/28</p>
                    </div>
                </div>
                <button class="w-12 h-12 bg-zinc-900 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-blue-400 transition-all border border-white/10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
            </div>

            <div class="p-6 bg-blue-500/5 border border-blue-500/10 rounded-3xl backdrop-blur-xl">
                <div class="flex items-center gap-4">
                     <div class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></div>
                     <p class="text-xs text-zinc-400 font-medium leading-relaxed uppercase tracking-tighter">Próximo débito de <span class="text-white font-black">{{ $subscription['amount'] }}</span> programado para <span class="text-white font-black">{{ date('d/m/Y', strtotime($subscription['next_billing'])) }}</span>.</p>
                </div>
            </div>
        </div>

        <!-- Histórico de Faturas - Seamless Table -->
        <div class="bg-zinc-900/60 backdrop-blur-2xl border border-white/10 rounded-[3.5rem] overflow-hidden flex flex-col shadow-2xl">
            <h3 class="text-xl font-black text-white p-10 pb-6 uppercase text-xs tracking-widest">Registry Log</h3>
            <div class="overflow-x-auto flex-1 px-4">
                <table class="w-full text-left text-sm border-separate border-spacing-y-2">
                    <thead class="text-zinc-600 text-[9px] font-black uppercase tracking-[0.3em]">
                        <tr>
                            <th class="px-8 py-4">Auth Token</th>
                            <th class="px-8 py-4">Timestamp</th>
                            <th class="px-8 py-4 text-right">Value</th>
                        </tr>
                    </thead>
                    <tbody class="text-xs">
                        @foreach($invoices as $inv)
                        <tr class="group cursor-pointer">
                            <td class="px-8 py-5 bg-zinc-950/30 first:rounded-l-[1.5rem] last:rounded-r-[1.5rem] border-y border-white/0 group-hover:border-blue-500/20 group-hover:bg-zinc-900/50 transition-all">
                                <span class="text-white font-black tracking-widest">{{ $inv['id'] }}</span>
                            </td>
                            <td class="px-8 py-5 bg-zinc-950/30 border-y border-white/0 group-hover:border-blue-500/20 group-hover:bg-zinc-900/50 transition-all">
                                <span class="text-zinc-500 font-bold">{{ date('d M, Y', strtotime($inv['date'])) }}</span>
                            </td>
                            <td class="px-8 py-5 bg-zinc-950/30 last:rounded-r-[1.5rem] border-y border-white/0 group-hover:border-blue-500/20 group-hover:bg-zinc-900/50 text-right transition-all">
                                <span class="text-emerald-400 font-black tracking-tighter">{{ $inv['amount'] }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-8 text-center mt-auto">
                <a href="#" class="inline-flex items-center gap-2 group text-zinc-500 text-[10px] font-black hover:text-blue-400 transition-all uppercase tracking-[0.2em]">
                    Ver Ledger Completo 
                    <svg class="w-3 h-3 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
</div>
@endsection



