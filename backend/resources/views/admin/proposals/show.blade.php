@extends('layouts.admin')

@section('title', 'Visualizar Proposta')

@section('content')
<div class="max-w-5xl mx-auto animate-fade-in space-y-8">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.proposals.index') }}" class="w-10 h-10 rounded-full bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-400 hover:bg-white/10 hover:text-white transition-all">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-3xl font-black text-white tracking-tight">Detalhes da Proposta</h2>
                <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Ref: {{ $proposal->token }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.proposals.edit', $proposal) }}" class="px-6 py-3 bg-zinc-900 border border-white/5 rounded-2xl text-[10px] text-zinc-400 font-black uppercase tracking-widest hover:bg-zinc-800 transition-all flex items-center gap-2">
                <i class="fas fa-edit"></i> Editar
            </a>
            @if($proposal->status == 'Pendente')
            <form action="{{ route('admin.proposals.send', $proposal) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-3 bg-blue-600 rounded-2xl text-[10px] text-white font-black uppercase tracking-widest hover:bg-blue-500 transition-all flex items-center gap-2 shadow-lg shadow-blue-600/20">
                    <i class="fas fa-paper-plane"></i> Marcar como Enviada
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8">
                <div class="flex items-center justify-between mb-8 pb-8 border-b border-white/5">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 rounded-2xl bg-zinc-950 flex items-center justify-center text-2xl text-blue-500 border border-white/5">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white">{{ $proposal->plan->name }}</h3>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">Plano Selecionado</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-black text-white">R$ {{ number_format($proposal->valor - $proposal->desconto, 2, ',', '.') }}</p>
                        @if($proposal->desconto > 0)
                            <p class="text-[10px] text-emerald-500 font-black uppercase tracking-widest mt-1">Com desconto de R$ {{ number_format($proposal->desconto, 2, ',', '.') }}</p>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                    <div>
                        <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest mb-1">Status Atual</p>
                        @php
                            $colors = ['Pendente' => 'zinc', 'Enviada' => 'blue', 'Aprovada' => 'emerald', 'Rejeitada' => 'red'];
                            $color = $colors[$proposal->status] ?? 'zinc';
                        @endphp
                        <span class="px-3 py-1 bg-{{$color}}-500/10 border border-{{$color}}-500/20 text-{{$color}}-500 text-[9px] font-black uppercase rounded-lg">
                            {{ $proposal->status }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest mb-1">Validade</p>
                        <p class="text-sm font-bold text-white">{{ $proposal->validade->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest mb-1">Criada em</p>
                        <p class="text-sm font-bold text-white">{{ $proposal->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>

                <div class="mt-10 pt-10 border-t border-white/5">
                    <p class="text-[9px] text-zinc-500 font-black uppercase tracking-widest mb-3">Observações e Condições</p>
                    <div class="bg-zinc-950/50 border border-white/5 rounded-2xl p-6 text-sm text-zinc-400 leading-relaxed italic">
                        {{ $proposal->observacoes ?? 'Nenhuma condição especial informada.' }}
                    </div>
                </div>
            </div>

            <!-- Share Link Container -->
            <div class="bg-gradient-to-br from-blue-600/10 to-purple-600/[0.02] border border-blue-500/20 rounded-[2.5rem] p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-600/30">
                        <i class="fas fa-link"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-white uppercase tracking-widest">Link de Aprovação do Cliente</h4>
                        <p class="text-[10px] text-blue-400 font-bold uppercase tracking-widest">Envie este link para o lead revisar e aceitar</p>
                    </div>
                </div>
                
                <div class="flex flex-col md:flex-row gap-4">
                    <input type="text" readonly value="{{ route('public.proposal.show', $proposal->token) }}" class="flex-1 bg-zinc-950 border border-white/10 rounded-2xl px-6 py-4 text-sm text-zinc-300 font-mono outline-none">
                    <button onclick="copyToClipboard('{{ route('public.proposal.show', $proposal->token) }}')" class="px-8 py-4 bg-zinc-900 border border-white/5 rounded-2xl text-[10px] text-white font-black uppercase tracking-widest hover:bg-zinc-800 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-copy"></i> Copiar Link
                    </button>
                </div>
                
                <div class="mt-6 flex justify-center">
                    <a href="{{ route('public.proposal.show', $proposal->token) }}" target="_blank" class="text-[9px] font-black text-zinc-600 uppercase tracking-widest hover:text-white transition-colors">Visualizar como cliente &nearrow;</a>
                </div>
            </div>
        </div>

        <!-- Lead Sidebar -->
        <div class="space-y-8">
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-6">Informações do Lead</h3>
                <div class="flex flex-col items-center text-center mb-8">
                    <div class="w-20 h-20 rounded-[1.5rem] bg-zinc-950 flex items-center justify-center text-3xl text-zinc-500 border border-white/5 mb-4 shadow-inner">
                        {{ substr($proposal->lead->nome, 0, 1) }}
                    </div>
                    <h4 class="text-lg font-black text-white leading-tight">{{ $proposal->lead->nome }}</h4>
                    <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">{{ $proposal->lead->empresa ?? 'Pessoa Física' }}</p>
                </div>
                
                <div class="space-y-4 pt-6 border-t border-white/5">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-zinc-500 font-bold uppercase tracking-widest text-[8px]">WhatsApp</span>
                        <span class="text-zinc-300 font-bold">{{ $proposal->lead->telefone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-zinc-500 font-bold uppercase tracking-widest text-[8px]">E-mail</span>
                        <span class="text-zinc-300 font-bold max-w-[120px] truncate">{{ $proposal->lead->email ?? 'N/A' }}</span>
                    </div>
                </div>
                
                <a href="{{ route('admin.leads.show', $proposal->lead) }}" class="mt-8 block w-full py-4 bg-zinc-950 border border-white/5 rounded-2xl text-[9px] text-center text-zinc-500 font-black uppercase tracking-widest hover:border-white/10 hover:text-white transition-all">Ver Perfil Completo</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Link copiado!');
        });
    }
</script>
@endpush
@endsection
