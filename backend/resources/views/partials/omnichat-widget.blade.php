<div x-data="omniWidget()"
     class="fixed bottom-6 right-6 z-[110]"
     x-cloak>

    <!-- Chat Button -->
    <button @click="toggle()"
            class="w-16 h-16 bg-emerald-500 text-zinc-950 rounded-full shadow-2xl flex items-center justify-center transform hover:scale-110 active:scale-95 transition-all duration-300 relative group">
        <div class="absolute inset-0 bg-emerald-400 rounded-full animate-ping opacity-20 group-hover:opacity-40"></div>
        <i x-show="!open" data-lucide="message-circle" class="w-8 h-8 relative z-10"></i>
        <i x-show="open" data-lucide="x" class="w-8 h-8 relative z-10"></i>

        <span x-show="unreadCount > 0"
              class="absolute -top-1 -right-1 w-6 h-6 bg-rose-500 text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-zinc-950">
            <span x-text="unreadCount"></span>
        </span>
    </button>

    <!-- Chat Window -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 scale-90"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-90"
         class="absolute bottom-20 right-0 w-[380px] max-w-[calc(100vw-2rem)] h-[560px] max-h-[calc(100vh-7rem)] bg-zinc-900/95 border border-white/10 rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.5)] flex flex-col overflow-hidden backdrop-blur-xl">

        <!-- Header -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-zinc-950/20 rounded-2xl flex items-center justify-center">
                    <i data-lucide="bot" class="w-7 h-7 text-zinc-950/70"></i>
                </div>
                <div>
                    <h3 class="text-zinc-950 font-black text-lg leading-tight">NexBot IA</h3>
                    <p class="text-zinc-950/60 text-[10px] font-black uppercase tracking-widest">IA exclusiva para alunos premium</p>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar" id="omni-widget-messages">
            <div x-show="messages.length === 0 && !loading" class="space-y-4">
                <div class="flex justify-start">
                    <div class="bg-zinc-800 text-zinc-200 rounded-2xl rounded-bl-none px-4 py-3 max-w-[92%] text-sm font-medium border border-white/5">
                        <p>Ola! Sou o assistente do NexShape. Posso ajudar com treino, evolucao corporal, relatorios, pagamentos, upload de fotos ou uso da plataforma.</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <template x-for="shortcut in shortcuts" :key="shortcut.label">
                        <button type="button"
                                @click="handleShortcut(shortcut)"
                                class="min-h-[72px] text-left bg-zinc-950/70 border border-white/10 rounded-2xl px-3 py-3 text-xs text-zinc-200 hover:border-emerald-500/50 hover:bg-emerald-500/10 transition-all">
                            <span class="block font-black text-emerald-400 leading-tight break-words" x-text="shortcut.label"></span>
                            <span class="block text-[10px] text-zinc-500 mt-1 leading-snug" x-text="shortcut.hint"></span>
                        </button>
                    </template>
                </div>
            </div>

            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.sender_type === 'customer' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.sender_type === 'customer'
                        ? 'bg-emerald-500 text-zinc-950 rounded-2xl rounded-br-none px-4 py-2.5 max-w-[85%] text-sm font-medium'
                        : 'bg-zinc-800 text-zinc-200 rounded-2xl rounded-bl-none px-4 py-2.5 max-w-[85%] text-sm font-medium border border-white/5'">
                        <p x-text="msg.content"></p>
                        <span class="text-[9px] opacity-40 mt-1 block font-black uppercase tracking-widest" x-text="formatTime(msg.created_at)"></span>
                    </div>
                </div>
            </template>

            <div x-show="loading" class="flex justify-start">
                <div class="bg-zinc-800 rounded-2xl px-4 py-3 flex gap-1">
                    <span class="w-1.5 h-1.5 bg-zinc-600 rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-zinc-600 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                    <span class="w-1.5 h-1.5 bg-zinc-600 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                </div>
            </div>
        </div>

        <!-- Input -->
        <div class="p-4 bg-zinc-950/50 border-t border-white/5">
            <p class="px-1 pb-2 text-[10px] text-zinc-600 font-bold leading-snug">
                A IA do assistente e exclusiva para alunos premium. Atalhos e ajuda rapida continuam disponiveis.
            </p>
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <input type="text"
                       x-model="newMessage"
                       placeholder="Pergunte sobre treino, evolucao ou suporte..."
                       class="flex-1 bg-zinc-900 border border-white/5 rounded-2xl px-4 py-3 text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-emerald-500/50 transition-all">
                <button type="submit"
                        :disabled="!newMessage.trim() || loading"
                        class="w-12 h-12 bg-emerald-500 text-zinc-950 rounded-xl flex items-center justify-center hover:bg-emerald-400 transition-all active:scale-90 disabled:opacity-50 disabled:grayscale">
                    <i data-lucide="send" class="w-5 h-5"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function omniWidget() {
    return {
        open: false,
        messages: [],
        newMessage: '',
        loading: false,
        unreadCount: 0,
        isPremiumUser: Boolean(window.isPremiumUser),
        customerId: '{{ auth()->id() }}',
        customerName: '{{ auth()->user()->name }}',
        shortcuts: [
            {
                label: 'Meu treino',
                hint: 'Abrir planos ativos',
                href: '{{ route('patient.prescriptions') }}'
            },
            {
                label: 'Relatorio',
                hint: 'Evolucao corporal',
                href: '{{ route('patient.reports.index') }}'
            },
            {
                label: 'Upload foto',
                hint: 'Analise corporal',
                href: '{{ route('body-analysis.index') }}'
            },
            {
                label: 'Suporte',
                hint: 'Acesso e pagamentos',
                href: '{{ route('support.tickets.index') }}'
            }
        ],

        init() {
            this.$watch('open', value => {
                if (value) {
                    this.unreadCount = 0;
                    this.scrollToBottom();
                    if (window.lucide) window.lucide.createIcons();
                }
            });
        },

        toggle() {
            this.open = !this.open;
        },

        handleShortcut(shortcut) {
            if (shortcut.href) {
                window.location.href = shortcut.href;
                return;
            }

            this.newMessage = shortcut.prompt;
            this.sendMessage();
        },

        async sendMessage() {
            const content = this.newMessage.trim();
            if (!content || this.loading) return;

            this.newMessage = '';
            this.pushMessage('customer', content);

            const localAnswer = this.localAnswer(content);
            if (localAnswer) {
                this.pushMessage('bot', localAnswer);
                return;
            }

            if (!this.isPremiumUser) {
                this.pushMessage('bot', 'A consulta com IA do NexBot e exclusiva para alunos premium. Voce ainda pode usar os atalhos e a ajuda rapida; para liberar respostas inteligentes, acesse seu plano.');
                window.dispatchEvent(new CustomEvent('open-ai-credits-modal'));
                return;
            }

            this.loading = true;
            try {
                const res = await fetch('{{ route("chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ message: content })
                });
                const data = await res.json();

                if (data.ok) {
                    this.pushMessage('bot', data.message);
                } else {
                    this.pushMessage('bot', data.error || 'Nao consegui responder agora. Tente novamente em alguns segundos.');

                    if (data.code === 'chat_quota_exceeded' || data.code === 'plan_blocked') {
                        window.dispatchEvent(new CustomEvent('open-ai-credits-modal'));
                    }
                }
            } catch (err) {
                this.pushMessage('bot', 'Erro de conexao com o NexBot IA. Tente novamente em alguns segundos.');
            } finally {
                this.loading = false;
            }
        },

        pushMessage(senderType, content) {
            this.messages.push({
                id: Date.now() + Math.random(),
                sender_type: senderType,
                content: content,
                created_at: new Date().toISOString()
            });
            this.scrollToBottom();
        },

        localAnswer(content) {
            const text = content.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            if (text.includes('meu treino') || text.includes('treino ativo') || text.includes('plano de treino')) {
                return 'Seu treino fica em Prescricoes. Use o atalho "Meu treino" para abrir seus planos ativos.';
            }

            if (text.includes('relatorio') || text.includes('evolucao')) {
                return 'Seus relatorios ficam no hub de Relatorios. Use o atalho "Relatorio" para ver as opcoes disponiveis.';
            }

            if (text.includes('upload') || text.includes('foto') || text.includes('analise corporal')) {
                return 'Para enviar foto ou fazer analise corporal, use o atalho "Upload foto".';
            }

            if (text.includes('suporte') || text.includes('pagamento') || text.includes('acesso') || text.includes('ticket')) {
                return 'Para suporte, acesso ou pagamentos, use o atalho "Suporte" para abrir seus chamados.';
            }

            return null;
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const el = document.getElementById('omni-widget-messages');
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        formatTime(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }
    }
}
</script>
