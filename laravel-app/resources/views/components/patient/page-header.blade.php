@props([
    'title',
    'subtitle' => null,
    'backUrl' => null,
    'icon' => null
])

<header class="flex items-center gap-4 animate-fade-in mb-10">
    @if($backUrl)
    <a href="{{ $backUrl }}" class="w-12 h-12 rounded-2xl glass-card flex items-center justify-center text-zinc-400 hover:text-white hover:border-white/20 transition-all">
        <i class="fas fa-chevron-left"></i>
    </a>
    @endif
    
    <div>
        <h1 class="text-2xl font-black tracking-tighter uppercase italic text-white">
            @if($icon) <i class="{{ $icon }} mr-2 text-[var(--brand-primary, #3b82f6)]"></i> @endif
            {{ $title }}
        </h1>
        @if($subtitle)
            <p class="text-[9px] font-black text-zinc-500 uppercase tracking-[0.3em]">{{ $subtitle }}</p>
        @endif
    </div>
</header>
