@extends('layouts.app')

@section('title', 'Meus Chamados')

@section('content')
<div class="max-w-6xl mx-auto animate-fade-in space-y-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Canais de Suporte</h2>
            <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest mt-1">Estamos aqui para ajudar você a crescer</p>
        </div>
        <a href="{{ route('support.tickets.create') }}" class="px-6 py-3 bg-blue-600 rounded-2xl text-[10px] text-white font-black uppercase tracking-widest hover:bg-blue-500 transition-all flex items-center gap-2 shadow-lg shadow-blue-600/20">
            <i class="fas fa-plus"></i> Abrir Novo Chamado
        </a>
    </div>

    <!-- Active Tickets -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-white/[0.02] border-bottom border-white/5">
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest">Assunto do Chamado</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Prioridade</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Status Atual</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-center">Última Resposta</th>
                    <th class="px-8 py-6 text-[10px] font-black text-zinc-500 uppercase tracking-widest text-right">Ação</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-white/[0.01] transition-all group">
                    <td class="px-8 py-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-white">{{ $ticket->subject }}</span>
                            <span class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest mt-1">#{{ $ticket->id }} &bull; {{ $ticket->category ?? 'Individual' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6 text-center">
                        @php
                            $prioColors = ['Low' => 'zinc', 'Medium' => 'blue', 'High' => 'amber', 'Critical' => 'red'];
                            $prioColor = $prioColors[$ticket->priority] ?? 'zinc';
                        @endphp
                        <span class="px-3 py-1 bg-{{ $prioColor }}-500/10 border border-{{ $prioColor }}-500/20 text-{{ $prioColor }}-500 text-[8px] font-black uppercase rounded-lg">
                            {{ $ticket->priority }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-center">
                         @php
                            $statusColors = ['Open' => 'blue', 'In Progress' => 'amber', 'Resolved' => 'emerald', 'Closed' => 'zinc'];
                            $statusColor = $statusColors[$ticket->status] ?? 'zinc';
                        @endphp
                        <span class="px-3 py-1 bg-{{ $statusColor }}-500/10 border border-{{ $statusColor }}-500/20 text-{{ $statusColor }}-500 text-[8px] font-black uppercase rounded-lg">
                            {{ $ticket->status }}
                        </span>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="text-[9px] text-zinc-500 font-bold uppercase">{{ $ticket->updated_at->diffForHumans() }}</span>
                    </td>
                    <td class="px-8 py-6 text-right">
                        <a href="{{ route('support.tickets.show', $ticket) }}" class="inline-flex w-8 h-8 rounded-lg bg-zinc-800 border border-white/5 items-center justify-center text-zinc-400 hover:text-white transition-all opacity-0 group-hover:opacity-100">
                            <i class="fas fa-search text-[10px]"></i>
                        </a>
                    </td>
                </tr>
                @empty
                 <tr>
                    <td colspan="5" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-headphones text-4xl text-zinc-800 mb-6"></i>
                            <p class="text-zinc-600 font-bold uppercase tracking-widest text-[11px]">Você ainda não abriu nenhum chamado</p>
                            <p class="text-zinc-700 text-[9px] mt-2 font-medium">Se precisar de ajuda, clique no botão acima para abrir um ticket.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Help Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-gradient-to-br from-blue-600/10 to-transparent border border-white/10 rounded-[2.5rem] p-10 flex flex-col items-start gap-6 group hover:border-blue-500/30 transition-all">
            <div class="w-14 h-14 rounded-2xl bg-blue-500 flex items-center justify-center text-white shadow-xl shadow-blue-600/20 transition-transform group-hover:scale-110">
                <i class="fas fa-book text-2xl"></i>
            </div>
            <div class="space-y-2">
                <h4 class="text-xl font-black text-white px-1">Base de Conhecimento</h4>
                <p class="text-xs text-zinc-500 font-medium leading-relaxed px-1">Aprenda a utilizar todos os recursos do sistema com nossos tutoriais e manuais detalhados.</p>
            </div>
            <a href="#" class="px-8 py-3 bg-white/5 rounded-2xl text-[9px] text-white font-black uppercase tracking-widest hover:bg-white/10 transition-all">Acessar Documentação</a>
        </div>

        <div class="bg-gradient-to-br from-purple-600/10 to-transparent border border-white/10 rounded-[2.5rem] p-10 flex flex-col items-start gap-6 group hover:border-purple-500/30 transition-all text-right">
            <div class="w-14 h-14 rounded-2xl bg-purple-600 flex items-center justify-center text-white shadow-xl shadow-purple-600/20 transition-transform group-hover:scale-110 ml-auto">
                <i class="fas fa-video text-2xl"></i>
            </div>
            <div class="space-y-2 w-full text-right px-1">
                <h4 class="text-xl font-black text-white">Treinamentos</h4>
                <p class="text-xs text-zinc-500 font-medium leading-relaxed">Assista às nossas masterclasses e aprenda a elevar o nível da sua academia.</p>
            </div>
             <a href="#" class="px-8 py-3 bg-white/5 rounded-2xl text-[9px] text-white font-black uppercase tracking-widest hover:bg-white/10 transition-all ml-auto">Abrir Academia App</a>
        </div>
    </div>
</div>
@endsection
