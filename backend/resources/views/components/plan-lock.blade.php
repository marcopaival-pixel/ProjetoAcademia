<div {{ $attributes->merge(['class' => 'relative group overflow-hidden bg-zinc-900/40 backdrop-blur-3xl border border-amber-500/20 p-8 rounded-[2.5rem] shadow-2xl transition-all hover:border-amber-500/40']) }}>
    {{-- Efeito de Glow Premium --}}
    <div class="absolute -top-24 -right-24 w-64 h-64 bg-amber-500/5 blur-[100px] rounded-full transition-all group-hover:bg-amber-500/10"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 rounded-[1.5rem] bg-amber-500/10 flex items-center justify-center text-amber-500 border border-amber-500/20 shadow-lg shadow-amber-500/5">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="space-y-1">
                <h4 class="text-xl font-black text-white tracking-tight">Recurso <span class="text-amber-500">Premium</span></h4>
                <p class="text-xs text-zinc-500 font-bold uppercase tracking-widest leading-relaxed max-w-md">
                    {{ $slot->isEmpty() ? 'Esta funcionalidade exclusiva faz parte do plano PRO. Evolua sua performance com IA e dados avançados.' : $slot }}
                </p>
            </div>
        </div>
        
        <div class="shrink-0">
            <a href="{{ route('plano') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-amber-500 text-zinc-950 font-black rounded-2xl hover:bg-amber-400 hover:scale-105 transition-all shadow-xl shadow-amber-500/20 text-[10px] uppercase tracking-[0.2em]">
                <i class="fas fa-crown text-[8px]"></i>
                Liberação Imediata
            </a>
        </div>
    </div>
</div>
