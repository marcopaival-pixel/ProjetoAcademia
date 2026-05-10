<div x-data="{ 
        show: false, 
        message: '', 
        type: 'success',
        timer: null
    }"
    x-init="
        const successMsg = {{ session('success') ? json_encode(session('success')) : 'null' }};
        const errorMsg = {{ session('error') ? json_encode(session('error')) : 'null' }};
        const validationError = {{ isset($errors) && $errors->count() > 0 ? json_encode($errors->first()) : 'null' }};
        
        if (successMsg) {
            setTimeout(() => { show = true; message = successMsg; type = 'success'; timer = setTimeout(() => show = false, 8000); }, 100);
        } else if (errorMsg) {
            setTimeout(() => { show = true; message = errorMsg; type = 'error'; timer = setTimeout(() => show = false, 8000); }, 100);
        } else if (validationError) {
            setTimeout(() => { show = true; message = validationError; type = 'error'; timer = setTimeout(() => show = false, 8000); }, 100);
        }
    "
    x-on:notify.window="
        message = $event.detail.message; 
        type = $event.detail.type || 'success'; 
        show = true; 
        if(timer) clearTimeout(timer); 
        timer = setTimeout(() => show = false, 8000);
    "
    x-on:toast.window="
        message = $event.detail.message; 
        type = $event.detail.type || 'success'; 
        show = true; 
        if(timer) clearTimeout(timer); 
        timer = setTimeout(() => show = false, 8000);
    "
    x-show="show"
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed bottom-5 right-5 z-[100000] max-w-sm w-full bg-zinc-900 border border-white/10 rounded-2xl shadow-2xl p-4 backdrop-blur-xl pointer-events-auto"
    style="display: none;"
>
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" 
             :class="type === 'success' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'">
            <template x-if="type === 'success'">
                <i class="fas fa-check-circle text-lg"></i>
            </template>
            <template x-if="type === 'error'">
                <i class="fas fa-exclamation-triangle text-lg"></i>
            </template>
        </div>
        
        <div class="flex-1 min-w-0">
            <p class="text-sm font-black text-white uppercase tracking-widest leading-none mb-1" x-text="type === 'success' ? 'Sucesso' : 'Atenção'"></p>
            <p class="text-xs font-bold text-zinc-400 truncate" x-text="message"></p>
        </div>

        <button x-on:click="show = false" class="text-zinc-500 hover:text-white transition-colors p-1">
            <i class="fas fa-times text-xs"></i>
        </button>
    </div>

    <div class="absolute bottom-0 left-0 h-1 bg-current opacity-20 transition-all rounded-full" 
         :class="type === 'success' ? 'text-emerald-500' : 'text-red-500'"
         :style="'width: ' + (show ? '100%' : '0%')"></div>
</div>
