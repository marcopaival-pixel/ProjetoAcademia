@extends('layouts.app', ['navCurrent' => 'messages'])

@section('title', 'Conversa com ' . $conversation->getOtherUser(auth()->id())->name)

@section('content')
<div class="max-w-4xl mx-auto h-[calc(100vh-12rem)] flex flex-col animate-dashboard-entry">
    <!-- Chat Header -->
    <div class="bg-zinc-900/50 border border-white/5 backdrop-blur-md p-4 rounded-t-[2rem] flex items-center justify-between mb-1">
        <div class="flex items-center gap-4">
            <a href="{{ route('messages.index') }}" class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center text-zinc-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white font-black">
                    {{ substr($conversation->getOtherUser(auth()->id())->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-white font-black leading-tight">{{ $conversation->getOtherUser(auth()->id())->name }}</h2>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.5)]"></div>
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Online</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex gap-2">
            @php($otherUser = $conversation->getOtherUser(auth()->id()))
            <form action="{{ route('user.block', $otherUser) }}" method="POST">
                @csrf
                <button type="submit" 
                        class="p-2 {{ auth()->user()->isBlocking($otherUser) ? 'text-red-500' : 'text-zinc-500' }} hover:text-white transition-colors" 
                        title="{{ auth()->user()->isBlocking($otherUser) ? 'Desbloquear Usuário' : 'Bloquear Usuário' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </button>
            </form>

            <button onclick="toggleSelectionMode()" id="btnShowSelection" class="p-2 text-zinc-500 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Selection Toolbar (hidden by default) -->
    <div id="selectionToolbar" class="hidden bg-red-500/10 border border-red-500/20 backdrop-blur-md p-3 mb-1 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <input type="checkbox" id="masterCheckbox" onclick="toggleAllMessages(this)" class="rounded bg-zinc-800 border-white/10 text-red-500 focus:ring-red-500/20">
            <label for="masterCheckbox" class="text-xs font-black text-red-400 uppercase tracking-widest cursor-pointer">Selecionar Tudo</label>
            <span id="selectedCount" class="text-[10px] font-bold text-zinc-500 bg-white/5 px-2 py-0.5 rounded-full">0 selecionadas</span>
        </div>
        <button onclick="document.getElementById('bulkDeleteForm').submit()" class="bg-red-600 hover:bg-red-500 text-white text-[10px] font-black px-4 py-1.5 rounded-lg transition-all uppercase tracking-widest">
            Excluir Permanente
        </button>
    </div>

    <!-- Messages Container -->
    <div class="flex-1 bg-zinc-900/30 border-x border-white/5 overflow-y-auto px-6 py-8 space-y-6" id="messageList">
        <form id="bulkDeleteForm" action="{{ route('messages.bulk-delete') }}" method="POST">
            @csrf
            <div class="flex flex-col gap-6">
                @foreach($messages as $msg)
                    @php($isMine = $msg->sender_id === auth()->id())
                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} items-end gap-3 group">
                        @if(!$isMine)
                            <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-xs font-black text-zinc-500">
                                {{ substr($msg->sender->name, 0, 1) }}
                            </div>
                        @endif

                        <div class="max-w-[70%] flex flex-col {{ $isMine ? 'items-end' : 'items-start' }} gap-1">
                            <div class="flex items-center gap-3">
                                <div class="message-selection hidden transform transition-all">
                                    <input type="checkbox" name="ids[]" value="{{ $msg->id }}" onclick="updateSelectedCount()" class="rounded-full bg-zinc-800 border-white/10 text-blue-500 focus:ring-blue-500/20">
                                </div>
                                <div class="px-5 py-3 rounded-2xl transition-all shadow-sm
                                    {{ $isMine 
                                        ? 'bg-blue-600 text-white rounded-br-none' 
                                        : 'bg-zinc-800 text-zinc-200 rounded-bl-none border border-white/5' }}">
                                    <p class="text-sm font-medium leading-relaxed">{{ $msg->content }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 px-1">
                                <span class="text-[9px] font-bold text-zinc-600 uppercase">{{ $msg->created_at->format('H:i') }}</span>
                                @if($isMine)
                                    <svg class="w-3 h-3 {{ $msg->is_read ? 'text-blue-400' : 'text-zinc-700' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    </div>

    <!-- Message Input -->
    <div class="bg-zinc-900/50 border border-white/5 backdrop-blur-md p-4 rounded-b-[2rem] mt-1 text-center">
        @if(auth()->user()->isBlocking($otherUser))
            <p class="text-red-500 font-bold text-sm py-2">Você bloqueou este utilizador. Desbloqueie para enviar mensagens.</p>
        @elseif(auth()->user()->isBlockedBy($otherUser))
            <p class="text-zinc-500 font-medium text-sm py-2">Este utilizador bloqueou você. Você não pode enviar mensagens.</p>
        @elseif(!$canReply)
            <div class="py-4 px-6 bg-amber-500/10 border border-amber-500/20 rounded-2xl flex flex-col items-center gap-3">
                <i class="fas fa-lock text-amber-500 text-xl"></i>
                <div>
                    <p class="text-amber-500 font-bold text-sm">Comunicação Restrita</p>
                    <p class="text-zinc-400 text-xs mt-1">Você precisa estar em um grupo comum com este usuário para conversar.</p>
                </div>
                <a href="{{ route('groups.index') }}" class="mt-2 text-[10px] font-black uppercase tracking-widest bg-amber-500 text-zinc-950 px-4 py-2 rounded-lg hover:bg-amber-400 transition-all">Ver Grupos</a>
            </div>
        @else
            <form action="{{ route('messages.store', $conversation) }}" method="POST" class="flex items-end gap-3">
                @csrf
                <div class="flex-1 bg-white/5 rounded-2xl border border-white/10 focus-within:border-blue-500/50 transition-all px-4 py-2 flex items-center">
                    <textarea name="content" 
                            rows="1" 
                            placeholder="Digite sua mensagem aqui..." 
                            required
                            class="w-full bg-transparent border-none focus:ring-0 text-white placeholder-zinc-600 text-sm py-2 resize-none"
                            oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'></textarea>
                </div>
                <button type="submit" class="w-12 h-12 bg-blue-600 hover:bg-blue-500 text-white rounded-2xl flex items-center justify-center transition-all shadow-lg shadow-blue-600/20 group">
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                </button>
            </form>
        @endif
    </div>
</div>

<script>
    let selectionMode = false;

    function toggleSelectionMode() {
        selectionMode = !selectionMode;
        const toolbar = document.getElementById('selectionToolbar');
        const selections = document.querySelectorAll('.message-selection');
        const list = document.getElementById('messageList');

        toolbar.classList.toggle('hidden', !selectionMode);
        selections.forEach(el => el.classList.toggle('hidden', !selectionMode));
        
        if (!selectionMode) {
            document.getElementById('masterCheckbox').checked = false;
            toggleAllMessages({checked: false});
        }
    }

    function toggleAllMessages(master) {
        const checkboxes = document.querySelectorAll('input[name="ids[]"]');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checked = document.querySelectorAll('input[name="ids[]"]:checked').length;
        document.getElementById('selectedCount').textContent = `${checked} selecionadas`;
    }

    // Auto-scroll para o fim
    const list = document.getElementById('messageList');
    list.scrollTop = list.scrollHeight;
</script>

<style>
    /* Custom Scrollbar */
    #messageList::-webkit-scrollbar {
        width: 4px;
    }
    #messageList::-webkit-scrollbar-track {
        background: transparent;
    }
    #messageList::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.05);
        border-radius: 10px;
    }
    #messageList::-webkit-scrollbar-thumb:hover {
        background: rgba(255,255,255,0.1);
    }
</style>
@endsection
