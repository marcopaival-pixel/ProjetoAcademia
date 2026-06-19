@extends('layouts.app')

@section('title', 'Meus Cupons — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-dashboard-entry max-w-[1600px] mx-auto px-6">
    <!-- Glass Header -->
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 pb-4 border-b border-white/5">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest border border-blue-500/20">Gestão de Benefícios</span>
                <span class="text-zinc-600">•</span>
                <span class="text-zinc-400 text-xs font-bold">{{ now()->translatedFormat('d \d\e F, Y') }}</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight">
                Cupons de <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-indigo-400">Desconto</span>
            </h1>
            <p class="text-zinc-500 font-medium max-w-xl">Gerencie cupons exclusivos para seus {{ mb_strtolower($patientsLabel) }}. Solicite novos descontos e acompanhe o status de liberação.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('professional.coupons.create') }}" class="group relative px-8 py-4 bg-white text-zinc-900 font-black rounded-2xl overflow-hidden transition-all hover:scale-105 active:scale-95 shadow-[0_20px_40px_-10px_rgba(255,255,255,0.2)]">
                <span class="relative z-10 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                    SOLICITAR NOVO CUPOM
                </span>
            </a>
        </div>
    </div>

    <!-- Coupons Table -->
    <div class="bg-zinc-900/60 backdrop-blur-md border border-white/10 rounded-[3.5rem] overflow-hidden shadow-2xl">
        <div class="p-10 border-b border-white/5 flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-black text-white">Histórico de Solicitações</h3>
                <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Status de análise e utilização</p>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.2em] border-b border-white/5">
                        <th class="px-10 py-6">Nome / Código</th>
                        <th class="px-10 py-6">{{ $patientLabel }}</th>
                        <th class="px-10 py-6">Desconto</th>
                        <th class="px-10 py-6">Validade</th>
                        <th class="px-10 py-6">Status</th>
                        <th class="px-10 py-6 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($coupons as $coupon)
                    <tr class="hover:bg-white/5 transition-colors group">
                        <td class="px-10 py-8">
                            <div>
                                <p class="text-white font-black text-lg group-hover:text-blue-400 transition-colors uppercase">{{ $coupon->name }}</p>
                                @if($coupon->code)
                                    <span class="px-2 py-0.5 bg-zinc-800 text-blue-400 text-[10px] font-black rounded border border-white/5 tracking-wider">{{ $coupon->code }}</span>
                                @else
                                    <span class="text-zinc-600 text-[10px] font-bold italic tracking-wider">AGUARDANDO GERAÇÃO</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-[10px] text-zinc-400 font-black">
                                    {{ strtoupper(substr($coupon->patient->name, 0, 2)) }}
                                </div>
                                <span class="text-zinc-300 font-bold text-sm">{{ $coupon->patient->name }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex flex-col">
                                <span class="text-white font-black">
                                    {{ $coupon->discount_type === 'percent' ? $coupon->discount_value.'%' : 'R$ '.number_format($coupon->discount_value, 2, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-tighter">{{ $coupon->discount_type === 'percent' ? 'Percentual' : 'Valor Fixo' }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <span class="text-zinc-400 font-medium text-sm">{{ $coupon->expiration_date->format('d/m/Y') }}</span>
                        </td>
                        <td class="px-10 py-8">
                            @php
                                $statusStyles = [
                                    'pending' => ['bg' => 'bg-amber-500/10', 'text' => 'text-amber-400', 'border' => 'border-amber-500/20', 'label' => 'PENDENTE'],
                                    'active' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'border' => 'border-emerald-500/20', 'label' => 'ATIVO'],
                                    'used' => ['bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'border' => 'border-blue-500/20', 'label' => 'UTILIZADO'],
                                    'expired' => ['bg' => 'bg-zinc-500/10', 'text' => 'text-zinc-500', 'border' => 'border-white/5', 'label' => 'EXPIRADO'],
                                    'cancelled' => ['bg' => 'bg-red-500/10', 'text' => 'text-red-400', 'border' => 'border-red-500/20', 'label' => 'CANCELADO'],
                                ];
                                $style = $statusStyles[$coupon->status] ?? $statusStyles['pending'];
                            @endphp
                            <span class="px-3 py-1 rounded-full {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }} text-[10px] font-black border uppercase tracking-widest">
                                {{ $style['label'] }}
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <a href="{{ route('professional.coupons.show', $coupon) }}" class="inline-block p-3 bg-zinc-800 rounded-2xl hover:bg-white hover:text-zinc-900 transition-all border border-white/5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542-7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-10 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-white/5 rounded-full flex items-center justify-center text-zinc-600">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-16 0m16 0v10l-8 4-8-4V7m16 0l-8 4-8-4"></path></svg>
                                </div>
                                <p class="text-zinc-500 font-bold">Nenhum cupom solicitado até o momento.</p>
                                <a href="{{ route('professional.coupons.create') }}" class="text-blue-400 font-black uppercase text-[10px] tracking-widest hover:underline">Solicitar meu primeiro cupom</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($coupons->hasPages())
        <div class="p-10 border-t border-white/5">
            {{ $coupons->links() }}
        </div>
        @endif
    </div>
</div>
@endsection



