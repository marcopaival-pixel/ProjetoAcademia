<div x-data="{ 
        show: false, 
        message: '',
        title: 'Ops! Algo deu errado'
    }"
    x-init="
        const sessError = {{ session('error') ? json_encode(session('error')) : 'null' }};
        if (sessError) {
            message = sessError;
            show = true;
        }
    "
    x-on:error-modal.window="
        message = $event.detail.message;
        title = $event.detail.title || 'Ops! Algo deu errado';
        show = true;
    "
    x-show="show"
    class="fixed inset-0 z-[500] flex items-center justify-center p-4 sm:p-6"
    style="display: none;"
    role="dialog"
    aria-modal="true"
>
    <!-- Backdrop -->
    <div x-show="show" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="absolute inset-0 bg-zinc-950/80 backdrop-blur-md" 
         x-on:click="show = false"></div>

    <!-- Modal Content -->
    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="relative w-full max-w-md rounded-[2.5rem] border border-red-500/20 bg-zinc-900 shadow-[0_0_50px_rgba(239,68,68,0.1)] overflow-hidden"
    >
        <!-- Top Gradient Strip -->
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-red-500/50 to-transparent"></div>

        <div class="p-10">
            <div class="flex flex-col items-center text-center space-y-6">
                <!-- Icon -->
                <div class="relative">
                    <div class="absolute inset-0 bg-red-500/20 rounded-3xl blur-xl animate-pulse"></div>
                    <div class="relative flex h-24 w-24 items-center justify-center rounded-[2rem] bg-red-500/10 text-red-500 ring-1 ring-red-500/30">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                </div>

                <div class="space-y-3">
                    <h2 class="text-2xl font-black tracking-tight text-white uppercase" x-text="title"></h2>
                    <p class="text-sm leading-relaxed text-zinc-400 font-bold" x-text="message"></p>
                </div>
            </div>

            <div class="mt-10">
                <button type="button"
                        x-on:click="show = false"
                        class="group relative w-full overflow-hidden rounded-2xl bg-zinc-800 p-px font-black uppercase tracking-[0.2em] text-white transition-all hover:scale-[1.02] active:scale-95 shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="relative flex items-center justify-center gap-2 rounded-[0.9rem] bg-zinc-900 py-4 px-6">
                        <span>Entendi</span>
                        <i class="fas fa-arrow-right text-[10px] text-zinc-600 group-hover:text-red-500 transition-colors"></i>
                    </div>
                </button>
            </div>
        </div>
        
        <!-- Bottom element -->
        <div class="bg-white/[0.02] py-3 px-10 flex justify-center border-t border-white/5">
            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-700">NexSense Intelligence Error Handling</span>
        </div>
    </div>
</div>
