<div x-data="omniWidget()" 
     class="fixed bottom-6 right-6 z-[110]"
     x-cloak>
    
    <!-- Chat Button -->
    <button @click="toggle()" 
            class="w-16 h-16 bg-emerald-500 text-zinc-950 rounded-full shadow-2xl flex items-center justify-center transform hover:scale-110 active:scale-95 transition-all duration-300 relative group">
        <div class="absolute inset-0 bg-emerald-400 rounded-full animate-ping opacity-20 group-hover:opacity-40"></div>
        <i x-show="!open" data-lucide="message-circle" class="w-8 h-8 relative z-10"></i>
        <i x-show="open" data-lucide="x" class="w-8 h-8 relative z-10"></i>
        
        <!-- Unread Badge -->
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
         class="absolute bottom-20 right-0 w-[380px] h-[550px] bg-zinc-900/95 border border-white/10 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.5)] flex flex-col overflow-hidden backdrop-blur-xl">
        
        <!-- Header -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-zinc-950/20 rounded-2xl flex items-center justify-center">
                    <i data-lucide="bot" class="w-7 h-7 text-zinc-950/70"></i>
                </div>
                <div>
                    <h3 class="text-zinc-950 font-black text-lg leading-tight">NexBot Support</h3>
                    <p class="text-zinc-950/60 text-[10px] font-black uppercase tracking-widest">Sempre Online • IA Ativa</p>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar" id="omni-widget-messages">
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
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <input type="text" 
                       x-model="newMessage" 
                       placeholder="Digite sua dúvida..." 
                       class="flex-1 bg-zinc-900 border border-white/5 rounded-2xl px-4 py-3 text-sm text-white placeholder-zinc-600 focus:outline-none focus:border-emerald-500/50 transition-all">
                <button type="submit" 
                        :disabled="!newMessage.trim()"
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
        conversationId: localStorage.getItem('omni_conv_id'),
        customerId: '{{ auth()->id() }}',
        customerName: '{{ auth()->user()->name }}',

        init() {
            if (this.conversationId) {
                this.loadMessages();
            }
            
            // Polling
            setInterval(() => {
                if (this.open && this.conversationId) {
                    this.loadMessages(true);
                }
            }, 3000);

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

        async loadMessages(silent = false) {
            if (!this.conversationId) return;
            try {
                const res = await fetch(`/admin/omnichannel/api/conversations/${this.conversationId}/messages`);
                const data = await res.json();
                
                if (data.length > this.messages.length && !this.open) {
                    this.unreadCount += (data.length - this.messages.length);
                }
                
                this.messages = data;
                if (!silent) this.scrollToBottom();
            } catch (err) {}
        },

        async sendMessage() {
            const content = this.newMessage.trim();
            if (!content) return;
            
            this.newMessage = '';
            
            // Se não tiver conversa, cria via Webhook
            if (!this.conversationId) {
                await this.startConversation(content);
            } else {
                // Simula envio imediato na UI
                const tempId = Date.now();
                this.messages.push({
                    id: tempId,
                    sender_type: 'customer',
                    content: content,
                    created_at: new Date().toISOString()
                });
                this.scrollToBottom();

                try {
                    await fetch('/omnichannel/webhook', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Omni-Secret': '{{ config("projeto.omni_webhook_secret") }}'
                        },
                        body: JSON.stringify({
                            company_slug: 'nexshape',
                            channel_type: 'widget',
                            customer_id: this.customerId,
                            customer_name: this.customerName,
                            content: content
                        })
                    });
                    this.loadMessages(true);
                } catch (err) {}
            }
        },

        async startConversation(content) {
            this.loading = true;
            try {
                const res = await fetch('/omnichannel/webhook', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Omni-Secret': '{{ config("projeto.omni_webhook_secret") }}'
                    },
                    body: JSON.stringify({
                        company_slug: 'nexshape',
                        channel_type: 'widget',
                        customer_id: this.customerId,
                        customer_name: this.customerName,
                        content: content
                    })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    // Busca a conversa criada para pegar o ID
                    const convRes = await fetch('/admin/omnichannel/api/conversations');
                    const convData = await convRes.json();
                    const myConv = convData.data.find(c => c.customer_external_id == this.customerId);
                    if (myConv) {
                        this.conversationId = myConv.id;
                        localStorage.setItem('omni_conv_id', this.conversationId);
                        this.loadMessages();
                    }
                }
            } catch (err) {} finally {
                this.loading = false;
            }
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
