@extends('layouts.app')

@section('title', 'Dashboard de IA')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tight">NexShape <span class="text-purple-500">IA</span></h1>
            <p class="text-zinc-500 font-medium mt-1">Gestão de créditos e histórico de inteligência artificial.</p>
        </div>
        <button onclick="window.dispatchEvent(new CustomEvent('open-ai-credits-modal'))" class="px-6 py-3 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-2xl transition-all shadow-lg shadow-purple-600/20 flex items-center gap-2 self-start">
            <i class="fas fa-plus-circle"></i>
            ADQUIRIR CRÉDITOS
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Balance -->
        <div class="bg-[#0b0e14] border border-white/5 p-6 rounded-[2rem] shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/5 blur-3xl group-hover:bg-purple-500/10 transition-all"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-1">Saldo Atual</span>
                <span class="text-3xl font-black text-white leading-none">{{ $user->ai_credits }}</span>
                <span class="text-[10px] font-bold text-purple-500 uppercase block mt-2">Créditos de IA</span>
            </div>
        </div>

        <!-- Today -->
        <div class="bg-[#0b0e14] border border-white/5 p-6 rounded-[2rem] shadow-sm relative overflow-hidden group">
            <div class="relative z-10">
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-1">Uso Hoje</span>
                <span class="text-3xl font-black text-white leading-none">{{ $usageToday }}</span>
                <span class="text-[10px] font-bold text-zinc-400 uppercase block mt-2">Créditos Consumidos</span>
            </div>
        </div>

        <!-- This Month -->
        <div class="bg-[#0b0e14] border border-white/5 p-6 rounded-[2rem] shadow-sm relative overflow-hidden group">
            <div class="relative z-10">
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-1">Uso no Mês</span>
                <span class="text-3xl font-black text-white leading-none">{{ $usageMonth }}</span>
                <span class="text-[10px] font-bold text-zinc-400 uppercase block mt-2">Créditos Totais</span>
            </div>
        </div>

        <!-- Lifetime -->
        <div class="bg-[#0b0e14] border border-white/5 p-6 rounded-[2rem] shadow-sm relative overflow-hidden group">
            <div class="relative z-10">
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-1">Uso Total</span>
                <span class="text-3xl font-black text-white leading-none">{{ $totalUsage }}</span>
                <span class="text-[10px] font-bold text-zinc-400 uppercase block mt-2">Desde o início</span>
            </div>
        </div>
    </div>

    <!-- Usage History -->
    <div class="bg-[#0b0e14] border border-white/5 rounded-[2.5rem] overflow-hidden shadow-sm">
        <div class="p-8 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-xl font-black text-white tracking-tight uppercase">Histórico de Uso</h3>
            <i class="fas fa-history text-zinc-700"></i>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-zinc-500 uppercase tracking-widest border-b border-white/5">
                        <th class="px-8 py-4">Ação</th>
                        <th class="px-8 py-4">Data/Hora</th>
                        <th class="px-8 py-4">Créditos</th>
                        <th class="px-8 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($history as $log)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-zinc-900 rounded-lg flex items-center justify-center text-purple-500 text-xs border border-white/5">
                                    @switch($log->action_type)
                                        @case('generate_diet') <i class="fas fa-utensils"></i> @break
                                        @case('generate_workout') <i class="fas fa-dumbbell"></i> @break
                                        @case('chat_response') <i class="fas fa-comments"></i> @break
                                        @case('analyze_body_photo') <i class="fas fa-camera"></i> @break
                                        @case('distribution') <i class="fas fa-share-alt"></i> @break
                                        @default <i class="fas fa-magic"></i>
                                    @endswitch
                                </div>
                                <div>
                                    <span class="text-sm font-bold text-white block">
                                        @switch($log->action_type)
                                            @case('generate_diet') Plano Alimentar @break
                                            @case('generate_workout') Treino Inteligente @break
                                            @case('chat_response') Resposta do NexBot @break
                                            @case('analyze_body_photo') Análise Corporal @break
                                            @case('distribution') Distribuição @break
                                            @default {{ ucfirst(str_replace('_', ' ', $log->action_type)) }}
                                        @endswitch
                                    </span>
                                    <span class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest">Ação registrada</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-sm font-medium text-zinc-400 block">{{ $log->created_at->format('d/m/Y') }}</span>
                            <span class="text-[10px] font-bold text-zinc-600 uppercase tracking-widest">{{ $log->created_at->format('H:i') }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-1.5">
                                <span class="text-sm font-black text-purple-500">-{{ $log->credits_consumed }}</span>
                                <i class="fas fa-magic text-[8px] text-purple-500/50"></i>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-2 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest rounded-md">Concluído</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-12 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center text-zinc-700">
                                    <i class="fas fa-ghost text-2xl"></i>
                                </div>
                                <p class="text-zinc-500 font-medium">Nenhum uso de IA registrado ainda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($history->hasPages())
        <div class="p-8 border-t border-white/5">
            {{ $history->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
