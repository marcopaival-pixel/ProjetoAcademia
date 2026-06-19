@extends('layouts.app')

@section('title', 'Painel do Representante')

@section('content')
<div class="space-y-10 animate-fade-in">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">
                Portal do <span class="text-emerald-500">Representante</span>
            </h1>
            <p class="text-zinc-500 font-medium mt-1">Gerencie suas indicações e acompanhe suas comissões em tempo real.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="p-4 bg-zinc-900/50 border border-zinc-800 rounded-3xl flex items-center gap-4">
                <div class="w-10 h-10 bg-emerald-500/10 text-emerald-500 rounded-xl flex items-center justify-center">
                    <i data-lucide="link" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">Seu Link de Indicação</p>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-white truncate max-w-[200px]">{{ $referralLink }}</span>
                        <button onclick="copyToClipboard('{{ $referralLink }}')" class="text-emerald-500 hover:text-emerald-400 transition-colors">
                            <i data-lucide="copy" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </div>

            <a href="{{ route('representative.simulator.index') }}" class="p-4 bg-emerald-600 hover:bg-emerald-500 text-white border border-emerald-500 rounded-3xl flex items-center gap-3 transition-colors shadow-lg shadow-emerald-500/20">
                <i data-lucide="calculator" class="w-5 h-5"></i>
                <span class="text-[10px] font-black uppercase tracking-widest">Simulador de Vendas</span>
            </a>
        </div>
    </div>

    {{-- Painel de Clínicas e Vendas --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-zinc-900/40 p-5 rounded-2xl border border-zinc-800">
            <p class="text-[10px] text-zinc-500 uppercase tracking-widest font-black mb-1">Total de Clínicas Vendidas</p>
            <h4 class="text-2xl font-black text-white">{{ $clinicsCount }}</h4>
        </div>
        <div class="bg-zinc-900/40 p-5 rounded-2xl border border-zinc-800">
            <p class="text-[10px] text-zinc-500 uppercase tracking-widest font-black mb-1">Clínicas Ativas</p>
            <h4 class="text-2xl font-black text-emerald-500">{{ $activeClinicsCount }}</h4>
        </div>
        <div class="bg-zinc-900/40 p-5 rounded-2xl border border-zinc-800">
            <p class="text-[10px] text-zinc-500 uppercase tracking-widest font-black mb-1">Clínicas Inadimplentes</p>
            <h4 class="text-2xl font-black text-red-500">{{ $defaultingClinicsCount }}</h4>
        </div>
        <div class="bg-zinc-900/40 p-5 rounded-2xl border border-zinc-800">
            <p class="text-[10px] text-zinc-500 uppercase tracking-widest font-black mb-1">Vendas do Mês</p>
            <h4 class="text-2xl font-black text-blue-500">{{ $salesThisMonth }}</h4>
        </div>
    </div>

    {{-- Stats Grid Financeiro --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Vendido --}}
        <div class="bg-zinc-900/50 border border-zinc-800 p-8 rounded-[2.5rem] relative overflow-hidden group hover:border-emerald-500/30 transition-all duration-500">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl group-hover:bg-emerald-500/10 transition-all"></div>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-2">Total Vendido</p>
            <h3 class="text-3xl font-black text-white tracking-tighter">R$ {{ number_format($totalSoldValue, 2, ',', '.') }}</h3>
            <div class="mt-4 flex items-center gap-2 text-emerald-500 text-[10px] font-bold uppercase tracking-widest">
                <i data-lucide="trending-up" class="w-3 h-3"></i>
                Meta: R$ {{ number_format($monthlyGoal, 2, ',', '.') }}
            </div>
        </div>

        {{-- Pendente (Garantia/Liberação) --}}
        <div class="bg-zinc-900/50 border border-zinc-800 p-8 rounded-[2.5rem] relative overflow-hidden group hover:border-amber-500/30 transition-all duration-500">
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-2">Comissão Prevista</p>
            <h3 class="text-3xl font-black text-white tracking-tighter">R$ {{ number_format($pendingAmount, 2, ',', '.') }}</h3>
            <div class="mt-4 flex items-center gap-2 text-amber-500 text-[10px] font-bold uppercase tracking-widest">
                <i data-lucide="clock" class="w-3 h-3"></i>
                Aguardando Pagamento da Clínica
            </div>
        </div>

        {{-- Disponível / Próximo Pagamento --}}
        <div class="bg-emerald-500 p-8 rounded-[2.5rem] relative overflow-hidden group shadow-xl shadow-emerald-500/10 hover:shadow-emerald-500/20 transition-all duration-500">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <p class="text-[10px] font-black text-zinc-950/50 uppercase tracking-[0.2em] mb-2">Próximo Pagamento</p>
            <h3 class="text-3xl font-black text-zinc-950 tracking-tighter">R$ {{ number_format($nextPaymentValue, 2, ',', '.') }}</h3>
            <a href="{{ route('representative.withdraw.form') }}" class="mt-4 inline-flex items-center gap-2 bg-zinc-950 text-white px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-transform">
                <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                Solicitar Saque
            </a>
        </div>

        {{-- Pago --}}
        <div class="bg-zinc-900/50 border border-zinc-800 p-8 rounded-[2.5rem] relative overflow-hidden group hover:border-blue-500/30 transition-all duration-500">
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em] mb-2">Comissão Paga</p>
            <h3 class="text-3xl font-black text-white tracking-tighter">R$ {{ number_format($paidAmount, 2, ',', '.') }}</h3>
            <div class="mt-4 flex items-center gap-2 text-blue-500 text-[10px] font-bold uppercase tracking-widest">
                <i data-lucide="check-circle" class="w-3 h-3"></i>
                Já transferido
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        {{-- Últimas Comissões --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-black text-white uppercase tracking-[0.3em] italic">Últimas <span class="text-emerald-500">Comissões</span></h2>
                <a href="{{ route('representative.commissions') }}" class="text-[10px] font-black text-zinc-600 hover:text-emerald-500 uppercase tracking-widest transition-colors">Ver Tudo</a>
            </div>

            <div class="bg-zinc-900/30 border border-zinc-900 rounded-[2rem] overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-900/50">
                            <th class="px-6 py-4 text-[9px] font-black text-zinc-600 uppercase tracking-widest">Usuário / Plano</th>
                            <th class="px-6 py-4 text-[9px] font-black text-zinc-600 uppercase tracking-widest">Valor</th>
                            <th class="px-6 py-4 text-[9px] font-black text-zinc-600 uppercase tracking-widest">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-900/30">
                        @forelse($latestCommissions as $commission)
                        <tr class="hover:bg-zinc-900/20 transition-colors">
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-white">{{ $commission->user->name }}</span>
                                    <span class="text-[10px] text-zinc-600 uppercase font-bold tracking-tighter">{{ $commission->subscription?->plan?->name ?? 'Venda Direta' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-sm font-black text-emerald-500">R$ {{ number_format($commission->commission_amount, 2, ',', '.') }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest 
                                    {{ $commission->status === 'PENDENTE' ? 'bg-amber-500/10 text-amber-500' : 
                                       ($commission->status === 'DISPONIVEL' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-blue-500/10 text-blue-500') }}">
                                    {{ $commission->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-zinc-600 font-medium italic">Nenhuma comissão registrada ainda.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Próximas Liberações --}}
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-black text-white uppercase tracking-[0.3em] italic">Próximas <span class="text-amber-500">Liberações</span></h2>
                <a href="{{ route('representative.commissions') }}" class="text-[10px] font-black text-zinc-600 hover:text-amber-500 uppercase tracking-widest transition-colors">Ver Tudo</a>
            </div>

            <div class="grid grid-cols-1 gap-4">
                @forelse($upcomingReleases as $release)
                <div class="bg-zinc-900/30 border border-zinc-900 p-5 rounded-3xl flex items-center justify-between hover:bg-zinc-900/50 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-zinc-950 rounded-2xl border border-zinc-800 flex items-center justify-center text-zinc-700 group-hover:text-amber-500 group-hover:border-amber-500/20 transition-all">
                            <i data-lucide="clock" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white group-hover:text-amber-400 transition-colors">{{ $release->user->name ?? 'Cliente' }}</h4>
                            <p class="text-[10px] text-zinc-600 font-bold uppercase tracking-widest italic">Criado em {{ $release->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-sm font-black text-amber-500">R$ {{ number_format($release->commission_amount, 2, ',', '.') }}</span>
                    </div>
                </div>
                @empty
                <div class="bg-zinc-900/30 border border-zinc-900 p-10 rounded-[2rem] text-center text-zinc-600 font-medium italic">
                    Nenhuma comissão pendente no momento.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            window.dispatchEvent(new CustomEvent('toast', { 
                detail: { message: 'Link de indicação copiado com sucesso!', type: 'success' } 
            }));
        }).catch(err => {
            window.dispatchEvent(new CustomEvent('toast', { 
                detail: { message: 'Erro ao copiar o link.', type: 'error' } 
            }));
            console.error('Erro ao copiar', err);
        });
    }
</script>
@endpush
@endsection
