@extends('layouts.app')

@section('title', 'NexBot AI Coach — Performance Elite')

@section('content')
@if(auth()->user()->hasPremiumAccess())
<div class="px-6 py-10 mx-auto max-w-6xl animate-fade-in-up" x-data="nexBot()">
    <!-- High Performance Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-8 pb-4 border-b border-zinc-900">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 shadow-inner">Rede Neural Ativa</span>
                <span class="text-zinc-700">•</span>
                <span class="text-zinc-500 text-xs font-black italic uppercase tracking-tighter text-emerald-500/50">Performance Coach</span>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter flex items-center gap-4 italic uppercase">
                <span class="text-emerald-500">NEX</span>BOT <span class="text-zinc-700 not-italic font-light tracking-widest">AI</span>
            </h1>
            <p class="text-zinc-500 font-medium text-sm">Sincronizado com o seu ecossistema de Bio-Performance em tempo real.</p>
        </div>

        <div class="flex items-center gap-6 bg-zinc-900/50 border border-zinc-800 p-6 rounded-[2.5rem] shadow-2xl backdrop-blur-md">
             <div class="text-right">
                <p class="text-[9px] font-black text-emerald-500 uppercase tracking-[0.2em] mb-1">Status Sinc</p>
                <div class="flex items-center gap-2 justify-end">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    <span class="text-xs text-white font-black uppercase tracking-widest italic">Otimizado</span>
                </div>
             </div>
             <div class="w-[1px] h-10 bg-zinc-800"></div>
             <button @click="deleteModalOpen = true" class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 text-zinc-600 hover:text-rose-500 hover:border-rose-500/30 transition-all flex items-center justify-center shadow-inner group">
                <i data-lucide="trash-2" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
             </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-10 h-[75vh]">
        <!-- Dashboard Lateral de Contexto -->
        <div class="hidden lg:flex flex-col gap-6">
            <div class="p-8 bg-zinc-900 border border-zinc-800 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-10 -top-10 w-24 h-24 bg-emerald-500/5 blur-3xl rounded-full"></div>
                <p class="text-[9px] font-black text-zinc-600 uppercase tracking-[0.3em] mb-6">Sessão Bio-Data</p>
                <div class="space-y-4">
                    <div class="flex justify-between items-center group/item">
                        <span class="text-[10px] text-zinc-500 font-black uppercase group-hover:text-zinc-300 transition-colors">Objetivo</span>
                        <span class="text-[10px] text-white font-black uppercase tracking-widest italic text-emerald-500">{{ auth()->user()->profile->goal ?? 'Geral' }}</span>
                    </div>
                    <div class="flex justify-between items-center group/item">
                        <span class="text-[10px] text-zinc-500 font-black uppercase group-hover:text-zinc-300 transition-colors">Metabolismo</span>
                        <span class="text-[10px] text-emerald-500 font-black uppercase tracking-widest italic">Monitorado</span>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-emerald-500/5 border border-emerald-500/20 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
                <div class="absolute -left-10 -bottom-10 w-24 h-24 bg-emerald-500/10 blur-3xl rounded-full"></div>
                <div class="flex items-center gap-3 mb-4">
                    <i data-lucide="sparkles" class="w-4 h-4 text-emerald-500"></i>
                    <p class="text-[9px] font-black text-emerald-500 uppercase tracking-[0.3em]">NexPoints Elite</p>
                </div>
                <p class="text-[11px] text-zinc-500 leading-relaxed italic font-medium">"O NexBot analisa o volume de treino da semana para calibrar suas sugestões de recuperação."</p>
            </div>
        </div>

        <!-- Janela de Chat Principal -->
        <div class="lg:col-span-3 flex flex-col bg-zinc-950 border border-zinc-800 rounded-[3.5rem] overflow-hidden shadow-3xl relative">
            <!-- Quota Bar -->
            <div id="chatQuotaBar" class="bg-emerald-500/10 border-b border-emerald-500/10 py-3 px-8 text-[9px] font-black text-emerald-500 text-center tracking-[0.4em] uppercase shadow-lg shadow-emerald-500/5" x-show="quota">
                ACESSO ELITE ATIVO — PROCESSAMENTO PRIORITÁRIO
            </div>

            <!-- Container de Mensagens -->
            <div class="flex-1 overflow-y-auto p-8 md:p-12 space-y-10 custom-scrollbar" id="messagesContainer">
                <!-- Mensagem de Boas-vindas -->
                <div class="flex gap-6 animate-fade-in">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-zinc-950 flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/20">
                        <i data-lucide="bot" class="w-6 h-6"></i>
                    </div>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] rounded-tl-none p-8 text-zinc-300 text-sm leading-relaxed max-w-[90%] shadow-inner relative">
                        <p class="font-medium">👋 Olá, <span class="text-white font-black">{{ explode(' ', auth()->user()->name)[0] }}</span>. Sou o <span class="text-emerald-500 font-black">NexBot</span>, seu núcleo de inteligência para alta performance.</p>
                        <p class="mt-4 opacity-70">Seus dados biométricos e registros de treino do dia já foram processados. Como posso otimizar sua evolução agora?</p>
                    </div>
                </div>

                <template x-for="msg in messages" :key="msg.id">
                    <div :class="msg.role === 'user' ? 'flex flex-row-reverse gap-6 animate-fade-in' : 'flex gap-6 animate-fade-in'">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-2xl transition-transform hover:scale-110"
                             :class="msg.role === 'user' ? 'bg-zinc-900 border border-zinc-800 text-zinc-500' : 'bg-emerald-500 text-zinc-950 shadow-emerald-500/20'">
                            <i :data-lucide="msg.role === 'user' ? 'user' : 'bot'" class="w-6 h-6"></i>
                        </div>
                        <div class="p-8 text-sm leading-relaxed max-w-[90%] shadow-2xl relative"
                             :class="msg.role === 'user' 
                                ? 'bg-emerald-500 text-zinc-950 rounded-[2.5rem] rounded-tr-none font-black' 
                                : 'bg-zinc-900 border border-zinc-800 text-zinc-300 rounded-[2.5rem] rounded-tl-none shadow-inner'">
                            <span x-html="formatMessage(msg.message)" class="block"></span>

                            <!-- Action Preview Card -->
                            <template x-if="msg.action">
                                <div class="mt-6 p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-3xl space-y-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                            <i data-lucide="zap" class="w-4 h-4 text-emerald-500"></i>
                                        </div>
                                        <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Ação Sugerida: <span x-text="msg.action.acao"></span></p>
                                    </div>
                                    <button 
                                        @click="executeAgentAction(msg)" 
                                        class="w-full py-3 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl transition-all shadow-lg shadow-emerald-500/10 active:scale-95"
                                        :disabled="msg.executed"
                                    >
                                        <span x-text="msg.executed ? 'EXECUTADO' : 'CONFIRMAR OPERAÇÃO'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Indicador de Digitação -->
                <div class="flex gap-6" x-show="loading">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-zinc-950 flex items-center justify-center shrink-0 shadow-lg">
                        <i data-lucide="bot" class="w-6 h-6"></i>
                    </div>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-[2.5rem] rounded-tl-none p-8 flex items-center gap-3 shadow-inner">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce shadow-[0_0_8px_rgba(16,185,129,0.5)]" style="animation-delay: 0.2s"></span>
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce shadow-[0_0_8px_rgba(16,185,129,0.5)]" style="animation-delay: 0.4s"></span>
                    </div>
                </div>
            </div>

            <!-- Sugestões de Perguntas -->
            <div class="px-12 pb-6 flex flex-wrap gap-3">
                <template x-for="suggestion in ['Consumo de calorias hoje?', 'Análise do último treino', 'Sugestão de refeição pós-treino']">
                    <button @click="suggestQuestion(suggestion)" class="px-6 py-2.5 bg-zinc-900 hover:bg-emerald-500/10 border border-zinc-800 hover:border-emerald-500/30 rounded-full text-[10px] text-zinc-600 hover:text-emerald-400 transition-all font-black uppercase tracking-widest shadow-inner">
                        <span x-text="suggestion"></span>
                    </button>
                </template>
            </div>

            <!-- Input de Chat Otimizado -->
            <div class="p-10 md:p-12 pt-0">
                <form @submit.prevent="sendMessage()" class="relative group">
                    <input 
                        type="text" 
                        x-model="input" 
                        placeholder="Comando para o Coach..." 
                        class="w-full bg-zinc-900 border border-zinc-800 rounded-[2.5rem] pl-10 pr-20 py-6 text-white text-sm font-black uppercase tracking-widest placeholder:text-zinc-800 focus:border-emerald-500/50 focus:ring-1 focus:ring-emerald-500/20 transition-all outline-none shadow-inner"
                        :disabled="loading"
                        autocomplete="off"
                    >
                    <button type="submit" 
                            class="absolute right-3 top-3 w-14 h-14 rounded-[1.5rem] bg-emerald-500 hover:bg-emerald-400 text-zinc-950 flex items-center justify-center transition-all shadow-xl shadow-emerald-500/20 active:scale-95 disabled:opacity-30 disabled:grayscale"
                            :disabled="loading || !input.trim()">
                        <i data-lucide="send" class="w-6 h-6"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div x-show="deleteModalOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-zinc-950/90 backdrop-blur-xl">
        <div @click.away="deleteModalOpen = false" 
             class="bg-zinc-900 border border-zinc-800 w-full max-w-md rounded-[3.5rem] p-12 shadow-3xl relative overflow-hidden">
            
            <div class="absolute -top-12 -right-12 w-40 h-40 bg-rose-500/5 blur-3xl rounded-full"></div>
            
            <div class="relative z-10 text-center space-y-8">
                <div class="w-24 h-24 bg-rose-500/10 rounded-[2rem] flex items-center justify-center mx-auto border border-rose-500/20 shadow-2xl shadow-rose-500/5">
                    <i data-lucide="trash-2" class="w-10 h-10 text-rose-500"></i>
                </div>
                
                <div class="space-y-3">
                    <h3 class="text-3xl font-black text-white tracking-tighter uppercase italic">Limpar Memória?</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed font-medium">
                        Esta ação irá purgar permanentemente todo o histórico de processamento do NexBot. Esta operação é irreversível.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 gap-4 pt-4">
                    <button @click="clearHistory()" 
                            class="px-8 py-5 bg-rose-500 hover:bg-rose-400 text-zinc-950 font-black rounded-3xl transition-all uppercase tracking-widest text-xs shadow-xl shadow-rose-500/20">
                        CONFIRMAR PURGA
                    </button>
                    <button @click="deleteModalOpen = false" 
                            class="px-8 py-4 bg-zinc-950 border border-zinc-800 text-zinc-600 hover:text-white font-black rounded-3xl transition-all uppercase tracking-widest text-[10px]">
                        CANCELAR
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.1); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.2); }
    
    body { 
        background-color: #080a0f;
        background-image:
            radial-gradient(at 0% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%),
            radial-gradient(at 100% 0%, rgba(16, 185, 129, 0.05) 0, transparent 40%);
        background-attachment: fixed;
    }

    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
function nexBot() {
    return {
        input: '',
        messages: [],
        loading: false,
        quota: null,
        deleteModalOpen: false,

        async init() {
            this.loadHistory();
            this.$nextTick(() => { lucide.createIcons(); });
        },

        async loadHistory() {
            try {
                const r = await fetch('{{ route("chat.history") }}');
                const d = await r.json();
                if (d.ok) {
                    this.messages = d.messages;
                    this.quota = d.chat_quota;
                    this.scrollToBottom();
                    this.$nextTick(() => { lucide.createIcons(); });
                }
            } catch (e) {}
        },

        suggestQuestion(q) {
            this.input = q;
            this.sendMessage();
        },

        async sendMessage() {
            const text = this.input.trim();
            if (!text || this.loading) return;

            this.input = '';
            const tempId = Date.now();
            this.messages.push({ id: tempId, role: 'user', message: text });
            this.loading = true;
            this.scrollToBottom();
            this.$nextTick(() => { lucide.createIcons(); });

            try {
                const r = await fetch('{{ route("chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: text })
                });
                
                const d = await r.json();
                if (d.ok) {
                    this.messages.push({ 
                        id: Date.now() + 1, 
                        role: 'assistant', 
                        message: d.message,
                        action: d.action,
                        executed: false
                    });
                    this.quota = d.chat_quota;
                } else {
                    this.messages.push({ id: Date.now() + 1, role: 'assistant', message: '⚠️ ' + (d.error || 'Falha na resposta neural.') });
                }
            } catch (e) {
                this.messages.push({ id: Date.now() + 1, role: 'assistant', message: '❌ Erro de conexão com o Coach.' });
            } finally {
                this.loading = false;
                this.scrollToBottom();
                this.$nextTick(() => { lucide.createIcons(); });
            }
        },

        async clearHistory() {
            try {
                await fetch('{{ route("chat.clear") }}', { 
                    method: 'POST', 
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } 
                });
                this.messages = [];
                this.deleteModalOpen = false;
            } catch (e) {}
        },

        async executeAgentAction(msg) {
            if (msg.executed) return;
            
            try {
                const r = await fetch('{{ route("chat.execute-action") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ action: msg.action })
                });
                
                const d = await r.json();
                if (d.ok) {
                    msg.executed = true;
                    this.messages.push({ 
                        id: Date.now() + 2, 
                        role: 'assistant', 
                        message: '✅ ' + d.message 
                    });
                } else {
                    alert('Erro ao executar ação: ' + d.error);
                }
            } catch (e) {
                alert('Erro de conexão ao executar ação.');
            } finally {
                this.scrollToBottom();
                this.$nextTick(() => { lucide.createIcons(); });
            }
        },

        scrollToBottom() {
            setTimeout(() => {
                const c = document.getElementById('messagesContainer');
                if (c) c.scrollTop = c.scrollHeight;
            }, 100);
        },

        formatMessage(text) {
            if (!text) return '';
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong class="text-emerald-500 font-black">$1</strong>')
                .replace(/\n/g, '<br>')
                .replace(/\|/g, '&nbsp;'); 
        }
    }
}
</script>
@else
<div class="max-w-4xl mx-auto py-24 px-6 text-center animate-fade-in-up">
    <div class="mb-12 inline-flex items-center justify-center w-28 h-28 rounded-[2.5rem] bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 shadow-3xl shadow-emerald-500/20 transform -rotate-12">
        <i data-lucide="bot" class="w-12 h-12"></i>
    </div>
    <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter mb-6 leading-tight uppercase italic">Conheça o <span class="text-emerald-500">NexBot AI</span></h1>
    <p class="text-xl text-zinc-500 mb-12 max-w-2xl mx-auto font-medium leading-relaxed">Eleve sua evolução ao estado da arte com o assistente neural exclusivo para membros <span class="text-emerald-500 font-black italic uppercase">Performance Elite</span>.</p>
    <a href="{{ route('plano') }}" class="px-12 py-6 bg-emerald-500 text-zinc-950 rounded-3xl font-black uppercase tracking-[0.2em] text-sm shadow-2xl shadow-emerald-500/20 hover:bg-emerald-400 transition-all active:scale-95">Ativar NexBot Coach</a>
</div>
@endif
@endsection
