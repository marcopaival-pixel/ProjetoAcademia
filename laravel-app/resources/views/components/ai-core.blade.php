@props(['size' => 'md'])

@php
    $dimensions = [
        'sm' => 'w-32 h-32',
        'md' => 'w-64 h-64',
        'lg' => 'w-96 h-96',
    ][$size] ?? 'w-64 h-64';

    $imgSize = [
        'sm' => 'w-24 h-24',
        'md' => 'w-48 h-48',
        'lg' => 'w-72 h-72',
    ][$size] ?? 'w-48 h-48';

    $viewBox = '0 0 200 200';
    $radius = 75;
@endphp

<div {{ $attributes->merge(['class' => 'relative flex items-center justify-center ' . $dimensions]) }}>
    <!-- O Core (Imagem Gerada) -->
    <div class="relative z-10 animate-pulse-slow">
        <img src="{{ asset('images/ai-core.png') }}" alt="NexShape AI Core" class="{{ $imgSize }} rounded-full object-cover">
        
        <!-- Glow Effect Backdrop -->
        <div class="absolute inset-0 bg-emerald-500/20 blur-3xl -z-10 rounded-full animate-glow"></div>
    </div>
    
    <!-- Texto em Órbita (SVG) -->
    <svg class="absolute inset-0 w-full h-full animate-spin-slow pointer-events-none" viewBox="{{ $viewBox }}">
        <defs>
            <path id="orbitPath-{{ $size }}" d="M 100, 100 m -{{ $radius }}, 0 a {{ $radius }},{{ $radius }} 0 1,1 {{ $radius * 2 }},0 a {{ $radius }},{{ $radius }} 0 1,1 -{{ $radius * 2 }},0" />
        </defs>
        <text class="text-[8px] font-black fill-emerald-500/80 uppercase tracking-[0.35em] drop-shadow-[0_0_8px_rgba(16,185,129,0.5)]">
            <textPath href="#orbitPath-{{ $size }}" startOffset="0%">
                NEXSHAPE AI • OTIMIZANDO PERFORMANCE • NEXSHAPE AI • OTIMIZANDO PERFORMANCE •
            </textPath>
        </text>
    </svg>

    <style>
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes glow {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.1); }
        }
        @keyframes pulse-slow {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.02); }
        }
        .animate-spin-slow {
            animation: spin-slow 20s linear infinite;
        }
        .animate-glow {
            animation: glow 4s ease-in-out infinite;
        }
        .animate-pulse-slow {
            animation: pulse-slow 6s ease-in-out infinite;
        }
    </style>
</div>
