@extends('layouts.admin')

@section('title', 'Gestão de Propostas')

@section('content')
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Propostas Comerciais</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Gerencie ofertas enviadas aos leads</p>
        </div>
        <a href="{{ route('admin.proposals.create') }}" class="px-6 py-3 bg-blue-600 rounded-2xl text-[10px] text-white font-black uppercase tracking-widest hover:bg-blue-500 transition-all flex items-center gap-2 shadow-lg shadow-blue-600/20">
            <i class="fas fa-plus"></i> Nova Proposta
        </a>
    </div>

    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/[0.02] border-bottom border-white/5">
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Lead / Empresa</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Plano / Valor</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Validade</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Status</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($proposals as $proposal)
                <tr class="hover:bg-white/[0.01] transition-all group">
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-white">{{ $proposal->lead->nome }}</span>
                            <span class="text-[10px] text-zinc-500 font-bold uppercase">{{ $proposal->lead->empresa ?? 'Pessoa Física' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-blue-400">{{ $proposal->plan->name }}</span>
                            <span class="text-xs font-black text-white">R$ {{ number_format($proposal->valor - $proposal->desconto, 2, ',', '.') }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="text-xs font-bold text-zinc-400">
                            {{ $proposal->validade->format('d/m/Y') }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-center">
                        @php
                            $colors = [
                                'Pendente' => 'zinc',
                                'Enviada' => 'blue',
                                'Aprovada' => 'emerald',
                                'Rejeitada' => 'red'
                            ];
                            $color = $colors[$proposal->status] ?? 'zinc';
                        @endphp
                        <span class="px-3 py-1 bg-{{$color}}-500/10 border border-{{$color}}-500/20 text-{{$color}}-500 text-[8px] font-black uppercase rounded-lg">
                            {{ $proposal->status }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.proposals.show', $proposal) }}" class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white transition-all">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('admin.proposals.edit', $proposal) }}" class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-zinc-400 hover:text-white transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-20 text-center">
                         <div class="flex flex-col items-center">
                            <i class="fas fa-file-invoice text-3xl text-zinc-800 mb-4"></i>
                            <p class="text-zinc-600 font-bold uppercase tracking-widest text-[10px]">Nenhuma proposta gerada ainda</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($proposals->hasPages())
        <div class="px-8 py-6 bg-white/[0.01] border-t border-white/5">
            {{ $proposals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
