@extends('layouts.app')

@section('title', 'NexBot AI Coach')

@section('content')
@if(auth()->user()->hasPremiumAccess())
<div class="px-4 py-8 mx-auto max-w-5xl animate-fade-in" x-data="nexBot()">
    <!-- Header de Alta Performance -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-6">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tighter flex items-center gap-3 italic">
                <span class="text-blue-500">NEX</span>BOT <span class="text-zinc-600 not-italic font-light">AI COACH</span>
            </h1>
            <p class="text-zinc-500 mt-1 font-medium text-sm uppercase tracking-widest">Sincronizado com a sua Bio-Performance</p>
        </div>
        <div class="flex items-center gap-4">
             <div class="flex flex-col items-end">
                <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Status do Sistema</span>
                <span class="text-xs text-emerald-500 font-bold flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Otimizado
                </span>
             </div>
             <button @click="deleteModalOpen = true" class="w-10 h-10 rounded-xl bg-zinc-900 border border-zinc-800 text-zinc-500 hover:text-red-400 hover:border-red-500/30 transition-all flex items-center justify-center">
                <i class="fa-solid fa-trash-can text-sm"></i>
             </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 h-[70vh]">
        <!-- Dashboard Lateral de Contexto -->
        <div class="hidden lg:flex flex-col gap-4">
            <div class="p-5 bg-zinc-900/50 border border-zinc-800 rounded-3xl">
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-3">Sessão Atual</p>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-zinc-400">Objetivo</span>
                        <span class="text-xs text-white font-bold">{{ auth()->user()->profile->goal ?? 'Geral' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-zinc-400">Consumo Calórico</span>
                        <span class="text-xs text-blue-400 font-bold">Monitorado IA</span>
                    </div>
                </div>
            </div>

            <div class="p-5 bg-gradient-to-br from-blue-600/10 to-indigo-600/10 border border-blue-500/20 rounded-3xl">
                <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-3">NexPoints Inteligentes</p>
                <p class="text-[11px] text-zinc-400 leading-relaxed italic">"O NexBot analisa seus treinos recentes para ajustar as sugestões de macros em tempo real."</p>
            </div>
        </div>

        <!-- Janela de Chat Principal -->
        <div class="lg:col-span-3 flex flex-col bg-zinc-950 border border-zinc-800 rounded-[2.5rem] overflow-hidden shadow-2xl relative">
            <!-- Quota Bar -->
            <div id="chatQuotaBar" class="bg-blue-600/5 border-b border-blue-500/10 py-2 px-6 text-[10px] font-medium text-blue-400 text-center tracking-widest uppercase" x-show="quota">
                <i class="fas fa-crown mr-1"></i> Acesso Premium Ativo — Consultas Ilimitadas
            </div>

            <!-- Container de Mensagens -->
            <div class="flex-1 overflow-y-auto p-6 md:p-10 space-y-8 custom-scrollbar" id="messagesContainer">
                <!-- Mensagem de Boas-vindas -->
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white shrink-0 shadow-lg shadow-blue-600/20">
                        <i class="fa-solid fa-robot text-sm"></i>
                    </div>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] rounded-tl-none p-6 text-zinc-200 text-sm leading-relaxed max-w-[85%]">
                        👋 Olá, **{{ explode(' ', auth()->user()->name)[0] }}**. Sou o **NexBot**, seu Coach especializado em alta performance. <br><br>
                        Já carreguei seus dados de treino e nutrição do dia. Como posso otimizar seus resultados agora?
                    </div>
                </div>

                <template x-for="msg in messages" :key="msg.id">
                    <div :class="msg.role === 'user' ? 'flex flex-row-reverse gap-4' : 'flex gap-4'">
                        <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white shrink-0 shadow-lg"
                             :class="msg.role === 'user' ? 'bg-zinc-800' : 'bg-blue-600 shadow-blue-600/20'">
                            <i :class="msg.role === 'user' ? 'fa-solid fa-user text-xs' : 'fa-solid fa-robot text-sm'"></i>
                        </div>
                        <div class="p-6 text-sm leading-relaxed max-w-[85%]"
                             :class="msg.role === 'user' 
                                ? 'bg-blue-600 text-white rounded-[2rem] rounded-tr-none' 
                                : 'bg-zinc-900 border border-zinc-800 text-zinc-200 rounded-[2rem] rounded-tl-none'">
                            <span x-html="formatMessage(msg.message)"></span>
                        </div>
                    </div>
                </template>

                <!-- Indicador de Digitação -->
                <div class="flex gap-4" x-show="loading">
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center text-white shrink-0">
                        <i class="fa-solid fa-robot text-sm"></i>
                    </div>
                    <div class="bg-zinc-900 border border-zinc-800 rounded-[2rem] rounded-tl-none p-6 text-zinc-200 text-sm flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce"></span>
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                    </div>
                </div>
            </div>

            <!-- Sugestões de Perguntas -->
            <div class="px-8 pb-4 flex flex-wrap gap-2">
                <button @click="suggestQuestion('Como foi meu consumo de calorias hoje?')" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-full text-[11px] text-zinc-400 hover:text-white transition-all font-bold">
                    🔥 Consumo de hoje?
                </button>
                <button @click="suggestQuestion('Análise meu último treino')" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-full text-[11px] text-zinc-400 hover:text-white transition-all font-bold">
                    🏋️ Análise de treino
                </button>
                <button @click="suggestQuestion('O que devo jantar para bater minha meta de proteína?')" class="px-4 py-2 bg-zinc-900 hover:bg-zinc-800 border border-zinc-800 rounded-full text-[11px] text-zinc-400 hover:text-white transition-all font-bold">
                    🥗 Sugestão Jantar
                </button>
            </div>

            <!-- Input de Chat Otimizado -->
            <div class="p-6 md:p-8 pt-0">
                <form @submit.prevent="sendMessage()" class="relative">
                    <input 
                        type="text" 
                        x-model="input" 
                        placeholder="Comando para o Coach..." 
                        class="w-full bg-zinc-900 border border-zinc-800 rounded-[2rem] pl-8 pr-16 py-5 text-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all outline-none"
                        :disabled="loading"
                        autocomplete="off"
                    >
                    <button type="submit" 
                            class="absolute right-3 top-3 w-12 h-12 rounded-2xl bg-blue-600 hover:bg-blue-500 text-white flex items-center justify-center transition-all shadow-xl shadow-blue-600/20"
                            :disabled="loading || !input.trim()">
                        <i class="fa-solid fa-paper-plane text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão Premium -->
    <div x-show="deleteModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         x-cloak>
        <div @click.away="deleteModalOpen = false" 
             class="bg-zinc-900 border border-zinc-800 w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl relative overflow-hidden">
            
            <!-- Background Blur Decor -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-red-600/10 blur-3xl rounded-full"></div>
            
            <div class="relative z-10 text-center space-y-6">
                <div class="w-20 h-20 bg-red-600/10 rounded-full flex items-center justify-center mx-auto border border-red-600/20 shadow-lg shadow-red-600/5">
                    <i class="fa-solid fa-trash-can text-3xl text-red-500"></i>
                </div>
                
                <div class="space-y-2">
                    <h3 class="text-2xl font-black text-white tracking-tight">Limpar Histórico?</h3>
                    <p class="text-zinc-500 text-sm leading-relaxed">
                        Esta ação irá remover permanentemente todas as suas conversas com o NexBot. Esta operação não pode ser desfeita.
                    </p>
                </div>
                
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button @click="deleteModalOpen = false" 
                            class="px-6 py-4 bg-zinc-800 hover:bg-zinc-700 text-white font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] border border-zinc-700">
                        Cancelar
                    </button>
                    <button @click="clearHistory()" 
                            class="px-6 py-4 bg-red-600 hover:bg-red-500 text-white font-black rounded-2xl transition-all uppercase tracking-widest text-[10px] shadow-xl shadow-red-600/20">
                        Confirmar
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
.custom-scrollbar::-webkit-scrollbar-thumb { background: #27272a; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #3f3f46; }

/* Markdown-like styling for AI responses */
.message-bubble table { border-collapse: collapse; width: 100%; margin: 1rem 0; border: 1px solid #3f3f46; }
.message-bubble th, .message-bubble td { padding: 8px 12px; border: 1px solid #3f3f46; font-size: 11px; }
.message-bubble th { background: #27272a; color: #60a5fa; }
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
        },

        async loadHistory() {
            try {
                const r = await fetch('{{ route("chat.history") }}');
                const d = await r.json();
                if (d.ok) {
                    this.messages = d.messages;
                    this.quota = d.chat_quota;
                    this.scrollToBottom();
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
                    this.messages.push({ id: Date.now() + 1, role: 'assistant', message: d.message });
                    this.quota = d.chat_quota;
                } else {
                    this.messages.push({ id: Date.now() + 1, role: 'assistant', message: '⚠️ ' + (d.error || 'Falha na resposta.') });
                }
            } catch (e) {
                this.messages.push({ id: Date.now() + 1, role: 'assistant', message: '❌ Erro de conexão com o Coach.' });
            } finally {
                this.loading = false;
                this.scrollToBottom();
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

        scrollToBottom() {
            setTimeout(() => {
                const c = document.getElementById('messagesContainer');
                c.scrollTop = c.scrollHeight;
            }, 100);
        },

        formatMessage(text) {
            if (!text) return '';
            // Basic Markdown Parser for SaaS Premium Look
            return text
                .replace(/\*\*(.*?)\*\*/g, '<strong class="text-blue-400">$1</strong>')
                .replace(/\n/g, '<br>')
                .replace(/\|/g, '&nbsp;'); // Basic table placeholder
        }
    }
}
</script>
@else
{{-- LAYOUT PAYWALL PREMIUM (Manter o anterior que já era bom) --}}
<div class="max-w-4xl mx-auto py-12 px-4 text-center">
    <!-- ... (O paywall já é profissional) ... -->
    <div class="mb-8 inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-blue-600/10 border border-blue-500/20 text-blue-500 shadow-2xl shadow-blue-500/20">
        <i class="fas fa-robot text-4xl"></i>
    </div>
    <h1 class="text-4xl md:text-6xl font-black text-white tracking-tighter mb-4 leading-tight">Conhece o <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">NexBot AI</span></h1>
    <p class="text-xl text-zinc-400 mb-12 max-w-2xl mx-auto font-medium">Leva os teus resultados para o próximo nível com o assistente inteligente exclusivo para membros Premium.</p>
    <a href="{{ route('plano') }}" class="px-10 py-5 bg-blue-600 text-white rounded-2xl font-black uppercase tracking-widest text-sm shadow-2xl shadow-blue-600/30">Assinar NexShape Premium</a>
</div>
@endif
@endsection
