@props([
    'icon' => 'fas fa-folder-open',
    'title' => 'Nada encontrado',
    'description' => 'Não existem registros nesta categoria.',
    'actionText' => null,
    'actionUrl' => null
])

<div class="glass-card p-12 rounded-[3.5rem] text-center border-dashed border-white/5 bg-transparent flex flex-col items-center animate-fade-in">
    <div class="w-20 h-20 bg-zinc-900/50 rounded-[2rem] flex items-center justify-center text-zinc-800 mb-6 shadow-inner border border-white/5 relative group">
        <div class="absolute inset-0 bg-blue-500/5 blur-xl rounded-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <i class="{{ $icon }} text-3xl relative z-10"></i>
    </div>
    
    <h5 class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.3em] mb-3">{{ $title }}</h5>
    
    <p class="text-zinc-700 text-[9px] font-bold px-10 leading-relaxed uppercase tracking-widest">
        {{ $description }}
    </p>

    @if($actionText && $actionUrl)
    <a href="{{ $actionUrl }}" class="mt-8 px-8 py-3 bg-white/5 border border-white/10 rounded-xl text-[8px] font-black uppercase tracking-[0.2em] text-zinc-400 hover:text-white hover:bg-white/10 transition-all">
        {{ $actionText }}
    </a>
    @endif
</div>
