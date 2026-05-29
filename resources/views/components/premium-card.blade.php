@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'iconColor' => 'emerald', // emerald, blue, amber, red
    'animate' => false
])

@php
    $iconColors = [
        'emerald' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
        'blue' => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
        'amber' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
        'red' => 'bg-red-500/10 text-red-500 border-red-500/20'
    ];
@endphp

<div {{ $attributes->merge(['class' => 'bg-zinc-900 border border-zinc-800 rounded-[2rem] p-8 shadow-2xl relative overflow-hidden group ' . ($animate ? 'animate-fade-in-up' : '')]) }}>
    <!-- Header -->
    @if($title || $icon)
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                @if($icon)
                    <div class="w-12 h-12 {{ $iconColors[$iconColor] }} rounded-xl flex items-center justify-center border shadow-lg shadow-black/20 transition-transform group-hover:scale-110">
                        <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
                    </div>
                @endif
                <div>
                    @if($title)
                        <h3 class="text-xl font-bold text-white tracking-tight">{{ $title }}</h3>
                    @endif
                    @if($subtitle)
                        <p class="text-zinc-500 text-sm font-medium">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
            
            @if(isset($action))
                <div>{{ $action }}</div>
            @endif
        </div>
    @endif

    <!-- Content -->
    <div class="relative z-10">
        {{ $slot }}
    </div>

    <!-- Decoration -->
    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-{{ $iconColor }}-500/5 rounded-full blur-[60px] pointer-events-none group-hover:bg-{{ $iconColor }}-500/10 transition-colors"></div>
</div>
