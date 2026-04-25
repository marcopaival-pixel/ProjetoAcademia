@extends('layouts.app', ['navCurrent' => 'messages'])

@section('title', 'Chat Direto')

@section('content')
<div class="space-y-8 animate-dashboard-entry">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </div>
                Centro de Mensagens
            </h1>
            <p class="text-zinc-500 font-medium mt-1">Converse diretamente com treinadores e amigos.</p>
        </div>
        
        <a href="{{ route('messages.create') }}" class="bg-blue-600 hover:bg-blue-500 text-white font-black px-6 py-3 rounded-2xl transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            NOVA CONVERSA
        </a>
    </div>

    <!-- Stats / Shortcuts -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php($unreadTotal = $conversations->filter(fn($c) => $c->messages->first() && !$c->messages->first()->is_read && $c->messages->first()->sender_id !== auth()->id())->count())
        <div class="bg-zinc-900/50 border border-white/5 backdrop-blur-md p-6 rounded-[2rem] flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </div>
            <div>
                <div class="text-[10px] font-black tracking-widest text-zinc-500 uppercase">Não Lidas</div>
                <div class="text-2xl font-black text-white">{{ $unreadTotal }}</div>
            </div>
        </div>
    </div>

    <!-- Conversations List -->
    <div class="bg-zinc-900/50 border border-white/5 backdrop-blur-md rounded-[2.5rem] overflow-hidden">
        <div class="p-8 border-b border-white/5 flex items-center justify-between">
            <h2 class="text-xl font-black text-white">Conversas Ativas</h2>
            <div class="flex gap-2">
                <span class="px-3 py-1 bg-zinc-800 text-zinc-400 text-[10px] font-bold rounded-full border border-white/5">{{ $conversations->count() }} TOTAL</span>
            </div>
        </div>

        <div class="divide-y divide-white/5">
            @forelse($conversations as $conv)
                @php($otherUser = $conv->getOtherUser(auth()->id()))
                @php($lastMsg = $conv->messages->first())
                @php($isUnread = $lastMsg && !$lastMsg->is_read && $lastMsg->sender_id !== auth()->id())

                <a href="{{ route('messages.show', $conv) }}" class="group block p-6 hover:bg-white/[0.02] transition-all relative">
                    <div class="flex items-center gap-6">
                        <!-- Avatar -->
                        <div class="relative">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-blue-600/20 group-hover:scale-105 transition-transform">
                                {{ substr($otherUser->name, 0, 1) }}
                            </div>
                            @if($isUnread)
                                <div class="absolute -top-1 -right-1 w-5 h-5 bg-blue-500 border-4 border-zinc-900 rounded-full"></div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-width-0">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="text-lg font-bold text-white group-hover:text-blue-400 transition-colors truncate">
                                    {{ $otherUser->name }}
                                </h3>
                                <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-tighter">
                                    {{ $lastMsg ? $lastMsg->created_at->diffForHumans() : 'Nova conversa' }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium {{ $isUnread ? 'text-zinc-200' : 'text-zinc-500' }} truncate max-w-md">
                                    @if($lastMsg && $lastMsg->sender_id === auth()->id())
                                        <span class="text-blue-500/50 mr-1">Você:</span>
                                    @endif
                                    {{ $lastMsg ? Str::limit($lastMsg->content, 80) : 'Sem mensagens ainda.' }}
                                </p>
                                
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-20 text-center">
                    <div class="w-20 h-20 bg-zinc-800 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6 text-zinc-600">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-black text-white mb-2">Silêncio no rádio</h3>
                    <p class="text-zinc-500 font-medium mb-8">Nenhuma conversa iniciada ainda. Que tal dar o primeiro passo?</p>
                    <a href="{{ route('messages.create') }}" class="bg-zinc-800 hover:bg-zinc-700 text-white font-bold px-8 py-3 rounded-2xl transition-all border border-white/5">
                        INICIAR CONVERSA
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    /* Custom scrollbar para o chat list se necessário */
    ::-webkit-scrollbar {
        width: 6px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.05);
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(255,255,255,0.1);
    }
</style>
@endsection
