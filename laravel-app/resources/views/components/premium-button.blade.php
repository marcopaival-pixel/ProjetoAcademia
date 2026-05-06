@props([
    'variant' => 'primary', // primary, secondary, ghost, danger
    'size' => 'md', // sm, md, lg
    'icon' => null,
    'type' => 'button',
    'href' => null,
    'loading' => false
])

@php
    $baseClasses = "inline-flex items-center justify-center font-extrabold transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed gap-2 group";
    
    $variants = [
        'primary' => 'bg-emerald-500 hover:bg-emerald-400 text-zinc-950 shadow-lg shadow-emerald-500/20',
        'secondary' => 'bg-zinc-800 hover:bg-zinc-700 text-white border border-zinc-700',
        'ghost' => 'bg-transparent hover:bg-zinc-800 text-zinc-400 hover:text-white',
        'danger' => 'bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white border border-red-500/20',
        'accent' => 'bg-blue-500 hover:bg-blue-400 text-white shadow-lg shadow-blue-500/20'
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-xs rounded-xl',
        'md' => 'px-6 py-4 text-sm rounded-2xl',
        'lg' => 'px-8 py-5 text-base rounded-[1.5rem]'
    ];

    $classes = "{$baseClasses} {$variants[$variant]} {$sizes[$size]}";
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon) <i data-lucide="{{ $icon }}" class="w-5 h-5"></i> @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)
            <div class="animate-spin w-5 h-5 border-2 border-current border-t-transparent rounded-full"></div>
        @else
            @if($icon) <i data-lucide="{{ $icon }}" class="w-5 h-5 transition-transform group-hover:translate-x-1"></i> @endif
        @endif
        {{ $slot }}
    </button>
@endif
