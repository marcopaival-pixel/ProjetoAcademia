@extends('layouts.admin')

@section('title', 'Gerenciar Cupons de Brindes e Descontos')

@section('content')
<div class="space-y-10 animate-fade-in">
    <!-- Header Admin -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Fila de Liberação de <span class="text-blue-500">Cupons</span></h1>
            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Análise e aprovação de solicitações de profissionais premium</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-zinc-900/40 border border-white/5 rounded-2xl">
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mr-2">Total Pendente</span>
                <span class="text-lg font-black text-blue-500">{{ $coupons->where('status', 'pending')->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Coupons Fila -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.2em] border-b border-white/5 bg-white/[0.02]">
                        <th class="px-8 py-5">Solicitante (Profissional)</th>
                        <th class="px-8 py-5">Paciente Beneficiado</th>
                        <th class="px-8 py-5">Proposta de Desconto</th>
                        <th class="px-8 py-5">Data Solicitação</th>
                        <th class="px-8 py-5 text-center">Status</th>
                        <th class="px-8 py-5 text-right">Decisão</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($coupons as $coupon)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500 border border-blue-500/20">
                                    <i class="fas fa-user-md text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-zinc-200">{{ $coupon->professional->name }}</p>
                                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">{{ $coupon->professional->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-chevron-right text-[10px] text-zinc-700"></i>
                                <span class="text-xs font-bold text-zinc-400">{{ $coupon->patient->name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div>
                                <p class="text-sm font-black text-white">
                                    {{ $coupon->discount_type === 'percent' ? $coupon->discount_value.'%' : 'R$ '.number_format($coupon->discount_value, 2, ',', '.') }}
                                </p>
                                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-tighter">{{ $coupon->discount_type === 'percent' ? 'Percentual' : 'Valor Fixo' }}</p>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">{{ $coupon->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @php
                                $statusStyles = [
                                    'pending' => ['bg' => 'bg-amber-500/10', 'text' => 'text-amber-500', 'border' => 'border-amber-500/10', 'label' => 'Aguardando'],
                                    'active' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-500', 'border' => 'border-emerald-500/10', 'label' => 'Aprovado'],
                                    'used' => ['bg' => 'bg-blue-500/10', 'text' => 'text-blue-500', 'border' => 'border-blue-500/10', 'label' => 'Utilizado'],
                                    'cancelled' => ['bg' => 'bg-red-500/10', 'text' => 'text-red-500', 'border' => 'border-red-500/10', 'label' => 'Rejeitado'],
                                ];
                                $style = $statusStyles[$coupon->status] ?? $statusStyles['pending'];
                            @endphp
                            <span class="px-3 py-1 rounded-lg {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }} text-[8px] font-black border uppercase tracking-widest">
                                {{ $style['label'] }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            @if($coupon->status === 'pending')
                            <div class="flex items-center justify-end gap-2">
                                <!-- Rejeitar Modal Trigger -->
                                <button onclick="openRejectModal({{ $coupon->id }})" class="w-8 h-8 rounded-lg bg-zinc-950 text-zinc-600 hover:text-red-500 border border-white/5 hover:border-red-500/30 transition-all flex items-center justify-center" title="Rejeitar Solicitação">
                                    <i class="fas fa-times"></i>
                                </button>
                                
                                <!-- Aprovar Direct -->
                                <form action="{{ route('admin.coupons.approve', $coupon) }}" method="POST"
                                      data-confirm-delete="true"
                                      data-confirm-title="Liberar Cupom"
                                      data-confirm-message="Deseja aprovar este cupom de {{ $coupon->discount_type === 'percent' ? $coupon->discount_value.'%' : 'R$ '.number_format($coupon->discount_value, 2, ',', '.') }}?">
                                    @csrf
                                    <button type="submit" class="h-8 px-4 rounded-lg bg-emerald-500 text-white text-[9px] font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20 hover:scale-105 active:scale-95 transition-all">
                                        Liberar Agora
                                    </button>
                                </form>
                            </div>
                            @else
                                <span class="text-[9px] text-zinc-700 font-black uppercase tracking-widest">Finalizado</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <i class="fas fa-ticket-alt text-4xl text-zinc-800"></i>
                                <p class="text-zinc-600 font-bold text-sm tracking-tight">Nenhuma solicitação pendente no momento.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($coupons->hasPages())
    <div class="py-4">
        {{ $coupons->links() }}
    </div>
    @endif
</div>

<!-- Reject Modal (Simple JS for Demo) -->
<div id="rejectModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6 bg-black/80 backdrop-blur-sm">
    <div class="bg-zinc-900 border border-white/10 p-8 rounded-[2rem] w-full max-w-md shadow-2xl">
        <h3 class="text-xl font-black text-white mb-2">Rejeitar Cupom</h3>
        <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mb-6">Informe o motivo da rejeição para o profissional</p>
        
        <form id="rejectForm" method="POST">
            @csrf
            <textarea name="admin_notes" required class="w-full h-32 bg-zinc-950 border border-white/5 rounded-2xl p-4 text-white text-sm focus:outline-none focus:border-red-500/50 transition-all placeholder:text-zinc-800" placeholder="Ex: O limite de cupons por mês para este plano foi excedido."></textarea>
            
            <div class="flex gap-4 mt-8">
                <button type="button" onclick="closeRejectModal()" class="flex-1 py-4 bg-zinc-800 text-zinc-400 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-zinc-700 transition-all">Cancelar</button>
                <button type="submit" class="flex-1 py-4 bg-red-600 text-white font-black text-[10px] uppercase tracking-widest rounded-2xl shadow-lg shadow-red-500/20 transition-all">Confirmar Rejeição</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal(id) {
        const modal = document.getElementById('rejectModal');
        const form = document.getElementById('rejectForm');
        form.action = `/admin/coupons/${id}/reject`;
        modal.classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endsection
