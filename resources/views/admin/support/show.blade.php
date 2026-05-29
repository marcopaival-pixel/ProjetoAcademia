@extends('layouts.admin')

@section('title', 'Atender Chamado')

@section('content')
<div class="max-w-6xl mx-auto animate-fade-in space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.support.index') }}" class="w-10 h-10 rounded-full bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-400 hover:bg-white/10 hover:text-white transition-all shadow-xl">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-3xl font-black text-white tracking-tight">{{ $ticket->subject }}</h2>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">#{{ $ticket->id }}</span>
                    <span class="w-1 h-1 rounded-full bg-zinc-800"></span>
                    <span class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest">{{ $ticket->user->name }}</span>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <form action="{{ route('admin.support.update-status', $ticket) }}" method="POST" id="status-form" class="flex items-center gap-3">
                @csrf
                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest">Status:</p>
                <select name="status" onchange="document.getElementById('status-form').submit()" class="bg-zinc-900 border border-white/5 rounded-xl px-4 py-2 text-[10px] text-white font-black uppercase tracking-widest outline-none focus:border-blue-500/50">
                    <option value="Open" {{ $ticket->status == 'Open' ? 'selected' : '' }}>Aberto</option>
                    <option value="In Progress" {{ $ticket->status == 'In Progress' ? 'selected' : '' }}>Em Atendimento</option>
                    <option value="Resolved" {{ $ticket->status == 'Resolved' ? 'selected' : '' }}>Resolvido</option>
                    <option value="Closed" {{ $ticket->status == 'Closed' ? 'selected' : '' }}>Fechado</option>
                </select>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Messages Flow -->
        <div class="lg:col-span-3 space-y-8">
            <div class="space-y-6">
                @foreach($ticket->messages as $message)
                <div class="flex {{ $message->is_admin_reply ? 'justify-end' : 'justify-start' }} animate-fade-in-up">
                    <div class="max-w-[80%] {{ $message->is_admin_reply ? 'bg-blue-600/10 border-blue-500/20' : 'bg-zinc-900/40 border-white/5' }} border rounded-[2rem] p-6 shadow-2xl">
                        <div class="flex items-center gap-3 mb-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($message->user->name) }}&background={{ $message->is_admin_reply ? '2563eb' : '18181b' }}&color=fff" class="w-6 h-6 rounded-lg">
                            <div>
                                <p class="text-[10px] font-black {{ $message->is_admin_reply ? 'text-blue-400' : 'text-zinc-300' }} uppercase tracking-widest">{{ $message->is_admin_reply ? 'Equipe de Suporte' : $message->user->name }}</p>
                                <p class="text-[8px] text-zinc-600 font-bold uppercase">{{ $message->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-zinc-300 leading-relaxed">{{ $message->message }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            @if($ticket->status != 'Closed')
            <!-- Reply Box -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8 shadow-2xl">
                <form action="{{ route('admin.support.reply', $ticket) }}" method="POST" class="space-y-4">
                    @csrf
                    <textarea name="message" rows="5" required class="w-full bg-zinc-950 border border-white/5 rounded-[2rem] px-6 py-5 text-zinc-300 text-sm outline-none focus:border-blue-500/50 resize-none transition-all" placeholder="Escreva aqui a resposta para o cliente..."></textarea>
                    <div class="flex justify-between items-center px-2">
                        <p class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest leading-loose max-w-xs">Ao responder, o status do chamado será alterado para <span class="text-blue-500">Em Atendimento</span>.</p>
                        <button type="submit" class="px-10 py-4 bg-blue-600 rounded-2xl text-[10px] text-white font-black uppercase tracking-widest hover:bg-blue-500 transition-all shadow-xl shadow-blue-600/20">
                            Enviar Resposta
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="text-center py-10 bg-zinc-900/20 border border-dashed border-white/5 rounded-[2.5rem]">
                <p class="text-zinc-600 font-black uppercase tracking-widest text-[10px]">Este chamado já foi encerrado.</p>
            </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-8">
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] p-8 shadow-xl">
                 <h4 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mb-6">Info do Cliente</h4>
                 <div class="flex flex-col items-center text-center mb-8 pb-8 border-b border-white/5">
                    <div class="w-20 h-20 rounded-[1.5rem] bg-zinc-950 flex items-center justify-center text-3xl text-zinc-500 border border-white/5 mb-4 shadow-inner">
                        {{ substr($ticket->user->name, 0, 1) }}
                    </div>
                    <h5 class="text-lg font-black text-white px-2">{{ $ticket->user->name }}</h5>
                    <p class="text-[10px] text-zinc-600 font-bold uppercase mt-1">Membro desde {{ $ticket->user->created_at->format('M Y') }}</p>
                 </div>
                 
                 <div class="space-y-4">
                    <div class="flex justify-between text-[10px]">
                        <span class="text-zinc-600 font-black uppercase tracking-widest">Email</span>
                        <span class="text-zinc-300 font-bold truncate max-w-[140px]">{{ $ticket->user->email }}</span>
                    </div>
                    <div class="flex justify-between text-[10px]">
                        <span class="text-zinc-600 font-black uppercase tracking-widest">Plano</span>
                        <span class="text-blue-500 font-black uppercase italic">{{ $ticket->user->is_premium ? 'Premium' : 'Iniciante' }}</span>
                    </div>
                     <div class="flex justify-between text-[10px]">
                        <span class="text-zinc-600 font-black uppercase tracking-widest">Prioridade</span>
                        <span class="text-{{ ['Low'=>'zinc', 'Medium'=>'blue', 'High'=>'amber', 'Critical'=>'red'][$ticket->priority] }}-500 font-black uppercase">{{ $ticket->priority }}</span>
                    </div>
                 </div>
            </div>

            <div class="bg-gradient-to-br from-blue-600/10 to-transparent border border-blue-500/20 rounded-[2.5rem] p-8 shadow-xl">
                <i class="fas fa-lightbulb text-blue-500 mb-4 text-xl"></i>
                <h4 class="text-[10px] text-white font-black uppercase tracking-widest mb-3">Dica de Atendimento</h4>
                <p class="text-[10px] text-zinc-500 font-medium leading-relaxed italic">Respostas rápidas e gentis aumentam o NPS em até 40%. Tente resolver o problema no primeiro contato.</p>
            </div>
        </div>
    </div>
</div>
@endsection
