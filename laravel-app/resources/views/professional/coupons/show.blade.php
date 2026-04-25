@extends('layouts.app')

@section('title', 'Detalhes do Cupom — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[800px] mx-auto px-6">
    <!-- Header -->
    <div class="space-y-3 pb-4 border-b border-white/5">
        <div class="flex items-center gap-3">
            <a href="{{ route('professional.coupons.index') }}" class="text-zinc-500 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Visão Detalhada</span>
        </div>
        <h1 class="text-4xl font-black tracking-tight text-white leading-tight">
            Gestão do <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Cupom</span>
        </h1>
    </div>

    <!-- Details Card -->
    <div class="bg-zinc-900/60 backdrop-blur-md border border-white/10 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>

        <div class="relative z-10 space-y-10">
            <!-- Ticket Design -->
            <div class="bg-zinc-950 border border-white/5 rounded-3xl p-8 flex flex-col items-center text-center space-y-4">
                <p class="text-zinc-500 font-black uppercase tracking-[0.3em] text-[10px]">Código de Desconto</p>
                <div class="py-4 px-8 bg-blue-600/10 border border-blue-500/30 rounded-2xl flex items-center gap-4 group">
                    <span class="text-3xl font-black text-white tracking-widest uppercase">
                        {{ $coupon->code ?: 'AGUARDANDO LIBERAÇÃO' }}
                    </span>
                    @if($coupon->code)
                    <button onclick="navigator.clipboard.writeText('{{ $coupon->code }}')" class="text-blue-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </button>
                    @endif
                </div>
                <p class="text-xs text-zinc-600 font-bold uppercase tracking-widest">
                    {{ $coupon->discount_type === 'percent' ? $coupon->discount_value.'% de Desconto' : 'R$ '.number_format($coupon->discount_value, 2, ',', '.').' de Desconto' }}
                </p>
            </div>

            <!-- Metadata Grid -->
            <div class="grid grid-cols-2 gap-10">
                <div class="space-y-1">
                    <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest">Paciente</p>
                    <p class="text-lg font-bold text-white">{{ $coupon->patient->name }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest">Expiração</p>
                    <p class="text-lg font-bold text-white">{{ $coupon->expiration_date->format('d/m/Y') }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest">Status Atual</p>
                    <p class="text-lg font-black uppercase tracking-tighter text-blue-400">{{ $coupon->status }}</p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] text-zinc-600 font-black uppercase tracking-widest">Uso</p>
                    <p class="text-lg font-bold text-white">{{ $coupon->used_count }} / {{ $coupon->max_uses }}</p>
                </div>
            </div>

            @if($coupon->admin_notes)
            <div class="bg-blue-500/5 border border-blue-500/10 p-6 rounded-2xl">
                <p class="text-[10px] text-blue-400 font-black uppercase tracking-widest mb-2">Mensagem do Administrador</p>
                <p class="text-sm text-zinc-400 font-medium italic">"{{ $coupon->admin_notes }}"</p>
            </div>
            @endif

            <div class="pt-6 border-t border-white/5">
                <p class="text-[10px] text-zinc-700 font-black uppercase tracking-[0.2em] mb-4">Histórico de Utilização</p>
                <div class="space-y-4">
                    @forelse($coupon->usages as $usage)
                    <div class="flex items-center justify-between p-4 bg-zinc-950 rounded-2xl border border-white/5">
                        <span class="text-sm font-bold text-zinc-300">{{ $usage->user->name }}</span>
                        <span class="text-[10px] text-zinc-600 font-black uppercase">{{ $usage->used_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-zinc-600 italic">Nenhum registro de uso encontrado.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
