@extends('layouts.admin')

@section('title', 'NexCentral — Omnichannel Business')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/omnichannel.css') }}">
    <style>
        .omni-container { height: calc(100vh - 120px); display: grid; grid-template-columns: 350px 1fr 380px; gap: 0; background: #0b0e14; border: 1px border-white/5; border-radius: 2.5rem; overflow: hidden; }
        .omni-sidebar { background: rgba(15, 17, 26, 0.4); border-right: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; }
        .omni-sidebar-header { padding: 30px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .omni-conv-list { flex: 1; overflow-y: auto; padding: 15px; }
        .omni-conv-item { padding: 15px 20px; border-radius: 20px; cursor: pointer; transition: all 0.2s; display: flex; items-center gap: 15px; margin-bottom: 8px; border: 1px solid transparent; }
        .omni-conv-item:hover { background: rgba(255,255,255,0.03); }
        .omni-conv-item.active { background: rgba(59, 130, 246, 0.1); border-color: rgba(59,130,246,0.2); }
        
        .omni-chat-main { display: flex; flex-direction: column; background: #0b0e14; position: relative; }
        .omni-chat-header { padding: 25px 40px; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; }
        .omni-chat-messages { flex: 1; overflow-y: auto; padding: 40px; display: flex; flex-direction: column; gap: 20px; }
        .omni-msg { max-width: 70%; padding: 15px 22px; border-radius: 25px; font-size: 14px; line-height: 1.5; font-weight: 500; }
        .omni-msg-customer { align-self: flex-start; background: rgba(255,255,255,0.05); color: #e2e8f0; border-bottom-left-radius: 5px; }
        .omni-msg-agent { align-self: flex-end; background: #3b82f6; color: white; border-bottom-right-radius: 5px; box-shadow: 0 10px 20px -5px rgba(59,130,246,0.3); }
        .omni-msg-bot { align-self: center; background: rgba(99, 102, 241, 0.1); color: #818cf8; font-size: 11px; font-weight: 800; border-radius: 12px; text-transform: uppercase; letter-spacing: 0.1em; }

        .omni-chat-input { padding: 30px 40px; border-top: 1px solid rgba(255,255,255,0.05); display: flex; items: center; gap: 15px; background: rgba(255,255,255,0.01); }
        .omni-textarea { flex: 1; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 15px 25px; color: white; resize: none; outline: none; transition: border-color 0.2s; height: 56px; line-height: 24px; }
        .omni-textarea:focus { border-color: #3b82f6; }
        .omni-btn-send { width: 56px; height: 56px; background: #3b82f6; color: white; border-radius: 18px; display: flex; items-center justify-center transition: all 0.2s; box-shadow: 0 10px 20px -5px rgba(59,130,246,0.3); }
        .omni-btn-send:hover:not(:disabled) { transform: translateY(-2px); background: #2563eb; }

        .omni-right-panel { background: rgba(15, 17, 26, 0.4); border-left: 1px solid rgba(255,255,255,0.05); padding: 40px; }
        .customer-avatar { width: 100px; height: 100px; border-radius: 35px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); margin: 0 auto 25px; display: flex; items-center justify-center font-black text-4xl text-white shadow-2xl shadow-blue-500/20; }
        
        @media (max-width: 1200px) { .omni-right-panel { display: none; } .omni-container { grid-template-columns: 320px 1fr; } }
    </style>
@endpush

@section('content')
<div class="omni-container border border-white/5 shadow-2xl mb-10">
    <!-- List Column -->
    <div class="omni-sidebar">
        <div class="omni-sidebar-header">
            <h3 class="text-white font-bold text-xl tracking-tight">Central <span class="text-blue-500">Nex</span></h3>
            <div class="flex items-center gap-2 mt-4">
                <div id="api-status" class="w-2 h-2 rounded-full bg-gray-600"></div>
                <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest" id="status-text">Conectando Workspace...</span>
            </div>
        </div>
        
        <div class="omni-conv-list" id="conv-list">
            <div class="py-20 flex flex-col items-center justify-center opacity-20 grayscale">
                <i class="fas fa-spinner fa-spin text-3xl mb-4"></i>
                <span class="text-xs font-bold uppercase tracking-widest">Sincronizando...</span>
            </div>
        </div>
    </div>

    <!-- Chat Column -->
    <div class="omni-chat-main">
        <div class="omni-chat-header" id="chat-header">
            <div class="flex flex-col">
                <span class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">Aguardando Seleção</span>
                <h3 class="text-white font-bold text-lg">Workspace NexCentral</h3>
            </div>
            <div class="flex items-center gap-4">
                 <a href="{{ route('admin.omnichannel.bots') }}" class="p-3 bg-white/5 hover:bg-white/10 text-zinc-400 rounded-2xl transition-all border border-white/5">
                    <i class="fas fa-robot"></i>
                </a>
            </div>
        </div>

        <div class="omni-chat-messages" id="chat-messages">
            <div class="flex items-center justify-center h-full text-center flex-col gap-6 opacity-30">
                <div class="w-24 h-24 bg-zinc-900 rounded-[2.5rem] flex items-center justify-center text-blue-500 mb-2">
                    <i class="fas fa-comments text-4xl"></i>
                </div>
                <div>
                    <h4 class="text-white font-bold text-lg">Atendimento Multicanal</h4>
                    <p class="text-xs font-medium text-zinc-500 max-w-[250px] mx-auto mt-2 leading-relaxed">Centralize WhatsApp, Widget e direct em um único workspace profissional.</p>
                </div>
            </div>
        </div>

        <div class="omni-chat-input">
            <button class="w-12 h-12 bg-zinc-800 text-zinc-400 rounded-2xl hover:bg-zinc-700 transition-all flex items-center justify-center">
                <i class="fas fa-plus"></i>
            </button>
            <textarea class="omni-textarea" id="chat-textarea" placeholder="Clique em uma conversa para começar..." disabled></textarea>
            <button class="omni-btn-send" id="btn-send" disabled>
                <i class="fas fa-paper-plane text-lg"></i>
            </button>
        </div>
    </div>

    <!-- Info Column -->
    <div class="omni-right-panel" id="customer-panel">
        <div class="text-center">
            <div class="customer-avatar" id="c-avatar">?</div>
            <h3 class="text-white font-bold text-xl tracking-tight mb-1" id="c-name">Paciente</h3>
            <p class="text-blue-500 text-[10px] font-black uppercase tracking-[0.2em]" id="c-status">Offline</p>
        </div>

        <div class="mt-12 space-y-8">
            <div class="space-y-4">
                <h4 class="text-zinc-500 text-[10px] font-black uppercase tracking-widest border-b border-white/5 pb-2">Atalhos de Apoio IA</h4>
                <div class="grid grid-cols-1 gap-2">
                    <button class="w-full p-4 bg-white/5 border border-white/5 rounded-2xl text-left hover:bg-white/10 transition-all group">
                        <p class="text-zinc-300 text-xs font-bold leading-tight">Sugerir Dieta Padrão</p>
                        <p class="text-[9px] text-zinc-600 font-black uppercase mt-1">Preset Nutrição</p>
                    </button>
                    <button class="w-full p-4 bg-white/5 border border-white/5 rounded-2xl text-left hover:bg-white/10 transition-all group">
                        <p class="text-zinc-300 text-xs font-bold leading-tight">Motivar Fidelização</p>
                        <p class="text-[9px] text-zinc-600 font-black uppercase mt-1">NexBot Retenção</p>
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <h4 class="text-zinc-500 text-[10px] font-black uppercase tracking-widest border-b border-white/5 pb-2">Sessão Atual</h4>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-zinc-500">Canal:</span>
                    <span class="text-white font-bold" id="c-channel">Widget Web</span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-zinc-500">Tempo de Espera:</span>
                    <span class="text-emerald-400 font-bold">02:45m</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeConvId = null;

    async function loadConversations() {
        try {
            const apiStatus = document.getElementById('api-status');
            const res = await fetch('{{ route("omni.conversations") }}');
            if(!res.ok) throw new Error(`${res.status}`);

            const data = await res.json();
            const list = document.getElementById('conv-list');
            
            apiStatus.className = 'w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981]';
            document.getElementById('status-text').textContent = 'Workspace Sincronizado';

            list.innerHTML = '';
            if (data.data.length === 0) {
                list.innerHTML = '<div class="py-20 text-center text-zinc-600 text-[10px] font-black uppercase tracking-widest">Nenhuma conversa pendente</div>';
                return;
            }

            data.data.forEach(conv => {
                const item = document.createElement('div');
                item.className = `omni-conv-item ${activeConvId == conv.id ? 'active' : ''}`;
                item.innerHTML = `
                    <div class="w-12 h-12 rounded-2xl bg-zinc-900 border border-white/5 flex items-center justify-center font-black text-blue-500 shadow-xl">
                        ${(conv.customer_name || 'C')[0]}
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <div class="flex justify-between items-center mb-0.5">
                            <span class="text-white font-black text-sm truncate">${conv.customer_name || 'Cliente'}</span>
                            <span class="text-[8px] text-zinc-600 tabular-nums">12:45</span>
                        </div>
                        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest truncate">${conv.channel.type}</p>
                    </div>
                `;
                item.onclick = () => selectConversation(conv);
                list.appendChild(item);
            });
        } catch(err) {
            document.getElementById('api-status').className = 'w-2 h-2 rounded-full bg-rose-500 shadow-[0_0_8px_#f43f5e]';
            document.getElementById('status-text').textContent = 'Erro de Conexão';
        }
    }

    async function selectConversation(conv) {
        activeConvId = conv.id;
        document.getElementById('chat-header').innerHTML = `
            <div class="flex flex-col">
                <span class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">${conv.channel.type} • Online</span>
                <h3 class="text-white font-black text-xl">${conv.customer_name}</h3>
            </div>
        `;
        
        // Update Side Panel
        document.getElementById('c-name').textContent = conv.customer_name;
        document.getElementById('c-avatar').textContent = (conv.customer_name || 'C')[0];
        document.getElementById('c-channel').textContent = conv.channel.name || conv.channel.type;
        document.getElementById('c-status').textContent = 'Atendimento Ativo';
        document.getElementById('c-status').className = 'text-emerald-400 text-[10px] font-black uppercase tracking-[0.2em]';

        document.getElementById('chat-textarea').disabled = false;
        document.getElementById('btn-send').disabled = false;
        document.getElementById('chat-textarea').placeholder = "Escreva sua resposta profissional...";
        
        loadMessages(conv.id);
        loadConversations(); 
    }

    async function loadMessages(id) {
        try {
            const res = await fetch(`{{ url('admin/omnichannel/api/conversations') }}/${id}/messages`);
            const messages = await res.json();
            const container = document.getElementById('chat-messages');
            
            container.innerHTML = '';
            messages.forEach(msg => {
                const div = document.createElement('div');
                div.className = `omni-msg omni-msg-${msg.sender_type}`;
                div.textContent = msg.content;
                container.appendChild(div);
            });

            container.scrollTop = container.scrollHeight;
        } catch(err) {}
    }

    document.getElementById('btn-send').onclick = sendMessage;
    document.getElementById('chat-textarea').onkeydown = (e) => {
        if(e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    };

    async function sendMessage() {
        const val = document.getElementById('chat-textarea').value.trim();
        if(!val || !activeConvId) return;

        try {
            const res = await fetch(`{{ url('admin/omnichannel/api/conversations') }}/${activeConvId}/reply`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ content: val })
            });

            if(res.ok) {
                document.getElementById('chat-textarea').value = '';
                loadMessages(activeConvId);
            }
        } catch(err) {}
    }

    setInterval(() => {
        loadConversations();
        if(activeConvId) loadMessages(activeConvId);
    }, 2000);

    loadConversations();
</script>
@endpush
