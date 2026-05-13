<div x-data="{ 
        show: false, 
        email: '',
        loading: false,
        message: '',
        isError: false,
        resendSuccess: false,
        redirectUrl: '{{ route('login') }}'
    }"
    x-on:registration-success.window="
        console.log('DEBUG: Evento registration-success recebido no modal', $event.detail);
        email = $event.detail.email || '';
        message = $event.detail.message || '';
        redirectUrl = $event.detail.redirect || '{{ route('login') }}';
        show = true;
    "
    x-show="show"
    class="fixed inset-0 z-[600] flex items-center justify-center p-4 sm:p-6"
    style="display: none;"
    role="dialog"
    aria-modal="true"
    id="success-modal-container"
>
    <!-- Backdrop -->
    <div x-show="show" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="absolute inset-0 bg-zinc-950/90 backdrop-blur-xl"></div>

    <!-- Modal Content -->
    <div x-show="show"
         x-transition:enter="ease-out duration-500"
         x-transition:enter-start="opacity-0 scale-90 translate-y-8"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-8"
         class="relative w-full max-w-lg rounded-[3.5rem] border border-emerald-500/20 bg-zinc-900 shadow-[0_0_80px_rgba(16,185,129,0.15)] overflow-hidden"
    >
        <!-- Top Gradient Strip -->
        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-transparent via-emerald-500 to-transparent"></div>
        
        <!-- Ambient Background Glows -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-emerald-500/10 rounded-full blur-[80px] pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-blue-500/5 rounded-full blur-[80px] pointer-events-none"></div>

        <div class="p-10 md:p-14 relative z-10">
            <div class="flex flex-col items-center text-center space-y-8">
                <!-- Success Icon -->
                <div class="relative">
                    <div class="absolute inset-0 bg-emerald-500/20 rounded-[2.5rem] blur-2xl animate-pulse"></div>
                    <div class="relative flex h-28 w-28 items-center justify-center rounded-[2.2rem] bg-zinc-950 border border-emerald-500/30 text-emerald-500 shadow-2xl transform -rotate-6">
                        <i data-lucide="shield-check" class="w-12 h-12"></i>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="text-3xl md:text-4xl font-black tracking-tighter text-white uppercase italic leading-none">
                        Cadastro realizado <br>
                        <span class="text-emerald-500">com sucesso!</span>
                    </h2>
                    
                    <div class="space-y-4 text-sm leading-relaxed text-zinc-400 font-medium max-w-sm mx-auto">
                        <p>Seu cadastro foi concluído com sucesso. Enviamos um e-mail de confirmação para:</p>
                        <div class="px-4 py-2 bg-emerald-500/5 border border-emerald-500/10 rounded-xl">
                            <span class="text-emerald-500 font-black tracking-wider uppercase text-[11px]" x-text="email"></span>
                        </div>
                        <p>Por favor, acesse sua caixa de entrada e clique no link de confirmação para ativar sua conta.</p>
                        <p class="text-[11px] text-zinc-600 italic">
                            Se não encontrar o e-mail, verifique também as pastas <strong>Spam, Promoções ou Lixo Eletrônico</strong>.
                        </p>
                    </div>
                </div>

                <!-- Resend Message -->
                <template x-if="message">
                    <div class="w-full p-4 rounded-2xl border text-[10px] font-black uppercase tracking-widest animate-fade-in"
                         :class="isError ? 'bg-rose-500/10 border-rose-500/20 text-rose-500' : 'bg-emerald-500/10 border-emerald-500/20 text-emerald-500'">
                        <span x-text="message"></span>
                    </div>
                </template>

                <!-- Actions -->
                <div class="w-full space-y-4 pt-4">
                    <button type="button"
                            @click="window.location.href = redirectUrl"
                            class="group relative w-full overflow-hidden rounded-2xl bg-emerald-500 py-5 font-black uppercase tracking-[0.3em] text-zinc-950 transition-all hover:scale-[1.02] active:scale-95 shadow-2xl shadow-emerald-500/20">
                        <div class="relative flex items-center justify-center gap-3">
                            <span>OK, Entendi</span>
                            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                        </div>
                    </button>

                    <button type="button"
                            @click="
                                loading = true;
                                message = '';
                                fetch('{{ url('/confirmar-email/reenviar') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({ email: email })
                                })
                                .then(res => res.json())
                                .then(data => {
                                    loading = false;
                                    isError = !data.success;
                                    message = data.message || (data.success ? 'E-mail reenviado com sucesso!' : 'Erro ao reenviar.');
                                    if(data.success) resendSuccess = true;
                                })
                                .catch(() => {
                                    loading = false;
                                    isError = true;
                                    message = 'Erro na conexão. Tente novamente.';
                                });
                            "
                            :disabled="loading || resendSuccess"
                            class="w-full py-4 text-zinc-500 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed font-black text-[10px] uppercase tracking-[0.3em] transition-all flex items-center justify-center gap-2">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5" :class="loading ? 'animate-spin' : ''"></i>
                        <span x-text="loading ? 'Enviando...' : (resendSuccess ? 'E-mail Reenviado' : 'Reenviar E-mail')"></span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Bottom element -->
        <div class="bg-white/[0.02] py-4 px-10 flex justify-center border-t border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[9px] font-black uppercase tracking-[0.4em] text-zinc-700">NexShape Security Protocol Active</span>
            </div>
        </div>
    </div>
</div>
