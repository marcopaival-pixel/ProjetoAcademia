@extends('layouts.app', ['navCurrent' => 'messages'])

@section('title', 'Chat Direto')

@section('content')
<div class="space-y-8 animate-dashboard-entry" x-data="{ showNewModal: false }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                Central de Atendimento
            </h1>
            <p class="text-zinc-500 font-medium mt-1">Canal direto com nosso suporte técnico e financeiro.</p>
        </div>
        
        <button @click="showNewModal = true" class="bg-emerald-600 hover:bg-emerald-500 text-white font-black px-6 py-3 rounded-2xl transition-all shadow-lg shadow-emerald-600/20 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            NOVA CONVERSA
        </button>
    </div>

    <!-- Modal Nova Conversa -->
    <div x-show="showNewModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;">
        <div @click.away="showNewModal = false" class="bg-zinc-900 border border-white/10 rounded-[2.5rem] w-full max-w-md p-8 shadow-2xl">
            <h3 class="text-2xl font-black text-white mb-6">Como podemos ajudar?</h3>
            
            <div class="space-y-4">
                <form action="{{ route('messages.start') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tipo" value="SUPORTE">
                    <button type="submit" class="w-full p-6 bg-zinc-800/50 hover:bg-emerald-500/10 border border-white/5 hover:border-emerald-500/50 rounded-3xl transition-all group flex items-center gap-4 text-left">
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-black text-white">SUPORTE TÉCNICO</div>
                            <div class="text-xs text-zinc-500 font-medium">Dúvidas sobre o sistema e treinos.</div>
                        </div>
                    </button>
                </form>

                <form action="{{ route('messages.start') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tipo" value="FINANCEIRO">
                    <button type="submit" class="w-full p-6 bg-zinc-800/50 hover:bg-amber-500/10 border border-white/5 hover:border-amber-500/50 rounded-3xl transition-all group flex items-center gap-4 text-left">
                        <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-black text-white">FINANCEIRO</div>
                            <div class="text-xs text-zinc-500 font-medium">Pagamentos, planos e faturas.</div>
                        </div>
                    </button>
                </form>
            </div>

            <button @click="showNewModal = false" class="mt-8 w-full py-4 text-zinc-500 font-bold hover:text-white transition-colors">
                CANCELAR
            </button>
        </div>
    </div>

    <!-- Stats / Shortcuts -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php($unreadTotal = $conversations->filter(fn($c) => $c->messages->first() && !$c->messages->first()->is_read && $c->messages->first()->sender_id !== auth()->id())->count())
        <div class="bg-zinc-900/50 border border-white/5 backdrop-blur-md p-6 rounded-[2rem] flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
            </div>
            <div>
                <div class="text-[10px] font-black tracking-widest text-zinc-500 uppercase">Mensagens não lidas</div>
                <div class="text-2xl font-black text-white">{{ $unreadTotal }}</div>
            </div>
        </div>
    </div>

    <!-- Conversations List -->
    <div class="bg-zinc-900/50 border border-white/5 backdrop-blur-md rounded-[2.5rem] overflow-hidden">
        <div class="p-8 border-b border-white/5 flex items-center justify-between">
            <h2 class="text-xl font-black text-white">Atendimentos Recentes</h2>
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
                        <!-- Type Icon -->
                        <div class="relative">
                            @if($conv->tipo === 'SUPORTE')
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-emerald-600/20 group-hover:scale-105 transition-transform">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                </div>
                            @else
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-600 to-orange-600 flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-amber-600/20 group-hover:scale-105 transition-transform">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            @endif
                            
                            @if($isUnread)
                                <div class="absolute -top-1 -right-1 w-5 h-5 bg-blue-500 border-4 border-zinc-900 rounded-full animate-pulse"></div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-width-0">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-3">
                                    <h3 class="text-lg font-bold text-white group-hover:text-emerald-400 transition-colors truncate">
                                        Atendimento: {{ $conv->tipo }}
                                    </h3>
                                    <span class="px-2 py-0.5 bg-zinc-800 text-zinc-500 text-[9px] font-black rounded-md border border-white/5 uppercase">
                                        {{ $conv->status }}
                                    </span>
                                </div>
                                <span class="text-[11px] font-bold text-zinc-500 uppercase tracking-tighter">
                                    {{ $lastMsg ? $lastMsg->created_at->diffForHumans() : 'Aberto agora' }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium {{ $isUnread ? 'text-zinc-200' : 'text-zinc-500' }} truncate max-w-md">
                                    @if($lastMsg && $lastMsg->sender_id === auth()->id())
                                        <span class="text-emerald-500/50 mr-1">Você:</span>
                                    @endif
                                    {{ $lastMsg ? Str::limit($lastMsg->content, 80) : 'Nenhuma mensagem enviada.' }}
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
                    <h3 class="text-xl font-black text-white mb-2">Nenhuma conversa iniciada. Precisa de ajuda?</h3>
                    <p class="text-zinc-500 font-medium mb-8">Escolha um canal abaixo para falar com nossa equipe.</p>
                    
                    <div class="flex flex-wrap justify-center gap-4">
                        <form action="{{ route('messages.start') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipo" value="SUPORTE">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-8 py-3 rounded-2xl transition-all shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                                FALAR COM SUPORTE
                            </button>
                        </form>
                        <form action="{{ route('messages.start') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tipo" value="FINANCEIRO">
                            <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white font-bold px-8 py-3 rounded-2xl transition-all shadow-lg shadow-amber-600/20 flex items-center gap-2">
                                FALAR COM FINANCEIRO
                            </button>
                        </form>
                    </div>
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
