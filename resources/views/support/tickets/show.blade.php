@extends('layouts.app')

@section('title', 'Acompanhar Chamado')

@section('content')
<div class="max-w-5xl mx-auto animate-fade-in space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('support.tickets.index') }}" class="w-10 h-10 rounded-full bg-zinc-900 border border-white/5 flex items-center justify-center text-zinc-400 hover:bg-white/10 hover:text-white transition-all shadow-xl">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-3xl font-black text-white tracking-tight">{{ $ticket->subject }}</h2>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-[9px] text-zinc-500 font-black uppercase tracking-widest">Protocolo #{{ $ticket->id }}</span>
                    <span class="w-1 h-1 rounded-full bg-zinc-800"></span>
                     @php
                        $statusColors = ['Open' => 'blue', 'In Progress' => 'amber', 'Resolved' => 'emerald', 'Closed' => 'zinc'];
                        $statusColor = $statusColors[$ticket->status] ?? 'zinc';
                    @endphp
                    <span class="text-[9px] text-{{ $statusColor }}-500 font-black uppercase tracking-widest">{{ $ticket->status }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline of Messages -->
    <div class="space-y-6">
        @foreach($ticket->messages as $message)
        <div class="flex {{ $message->is_admin_reply ? 'justify-start' : 'justify-end' }}">
            <div class="max-w-[85%] {{ $message->is_admin_reply ? 'bg-zinc-900/40 border-white/10 shadow-2xl' : 'bg-blue-600/10 border-blue-500/20' }} border rounded-[2.5rem] p-8">
                <div class="flex items-center gap-3 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($message->user->name) }}&background={{ $message->is_admin_reply ? '2563eb' : '18181b' }}&color=fff" class="w-6 h-6 rounded-lg">
                    <div>
                        <p class="text-[10px] font-black {{ $message->is_admin_reply ? 'text-blue-500' : 'text-zinc-300' }} uppercase tracking-widest">{{ $message->is_admin_reply ? 'Suporte Premium' : 'Você' }}</p>
                        <p class="text-[8px] text-zinc-600 font-bold uppercase">{{ $message->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <p class="text-sm text-zinc-300 leading-relaxed">{{ $message->message }}</p>
            </div>
        </div>
        @endforeach
    </div>

    @if($ticket->status != 'Closed')
    <!-- Reply Form -->
    <div class="bg-zinc-900/40 border border-white/5 rounded-[3rem] p-10 shadow-2xl">
        <form action="{{ route('support.tickets.reply', $ticket) }}" method="POST" class="space-y-6">
            @csrf
            <textarea name="message" rows="4" required class="w-full bg-zinc-950 border border-white/5 rounded-3xl px-6 py-5 text-zinc-300 text-sm outline-none focus:border-blue-500/50 resize-none transition-all shadow-inner" placeholder="Escreva sua resposta aqui..."></textarea>
            <div class="flex justify-end">
                <button type="submit" class="px-10 py-5 bg-white/5 border border-white/10 rounded-2xl text-[10px] text-white font-black uppercase tracking-widest hover:bg-white/10 transition-all flex items-center gap-3">
                    <i class="fas fa-reply"></i> Responder Suporte
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="p-10 bg-zinc-950/30 border border-dashed border-white/5 rounded-[3rem] text-center">
        <i class="fas fa-lock text-zinc-800 text-3xl mb-4"></i>
        <p class="text-xs text-zinc-600 font-black uppercase tracking-widest">Este chamado foi resolvido e encerrado</p>
    </div>
    @endif
</div>
@endsection
