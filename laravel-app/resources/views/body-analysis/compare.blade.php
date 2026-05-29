@extends('layouts.app', ['navCurrent' => 'body-analysis'])

@section('title', 'Comparação de Evolução | Cyber-Fit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-24 animate-fade-in">
    
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-black text-white uppercase tracking-tighter italic">
                Comparativo de <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-blue-400">Evolução</span>
            </h1>
            <p class="text-zinc-400 mt-2">Visão anatômica lado a lado e análise de progressão por IA.</p>
        </div>
        <a href="{{ route('body-analysis.index') }}" class="btn btn-outline-info">
            <i class="fas fa-arrow-left me-2"></i> Voltar ao Hub
        </a>
    </div>

    <!-- Comparação Visual -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <!-- Foto 1 (Antiga) -->
        <div class="card glass p-4 relative overflow-hidden group">
            <div class="absolute top-4 left-4 z-10 bg-zinc-900/80 backdrop-blur border border-white/10 px-3 py-1 rounded-full">
                <span class="text-xs font-bold text-zinc-300">Data Base:</span>
                <span class="text-sm font-black text-white ml-1">{{ $analysis_1->created_at->format('d/m/Y') }}</span>
            </div>
            
            <div class="aspect-[3/4] bg-zinc-950 rounded-2xl overflow-hidden flex items-center justify-center border border-white/5">
                <img src="{{ Storage::url($analysis_1->photo_path) }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-700">
            </div>
        </div>

        <!-- Foto 2 (Recente) -->
        <div class="card glass p-4 relative overflow-hidden group">
            <div class="absolute top-4 left-4 z-10 bg-zinc-900/80 backdrop-blur border border-emerald-500/30 px-3 py-1 rounded-full shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                <span class="text-xs font-bold text-emerald-400">Progresso:</span>
                <span class="text-sm font-black text-white ml-1">{{ $analysis_2->created_at->format('d/m/Y') }}</span>
            </div>
            
            <div class="aspect-[3/4] bg-zinc-950 rounded-2xl overflow-hidden flex items-center justify-center border border-emerald-500/20">
                <img src="{{ Storage::url($analysis_2->photo_path) }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-700">
            </div>
        </div>
    </div>

    <!-- Comparativo de Métricas -->
    <div class="card glass p-6 border-l-4 border-emerald-500">
        <h3 class="text-xl font-black text-white uppercase tracking-wider mb-6 flex items-center gap-2">
            <i class="fas fa-microchip text-emerald-500"></i> Evolução das Métricas
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @php
                // Prevenção de nulos caso os dados da IA estejam ausentes
                $metrics1 = is_array($analysis_1->metrics) ? $analysis_1->metrics : (json_decode($analysis_1->metrics, true) ?? []);
                $metrics2 = is_array($analysis_2->metrics) ? $analysis_2->metrics : (json_decode($analysis_2->metrics, true) ?? []);
                
                // Simetria
                $sym1 = floatval($metrics1['posture_score'] ?? 0);
                $sym2 = floatval($metrics2['posture_score'] ?? 0);
                $symDiff = $sym2 - $sym1;
                $symColor = $symDiff > 0 ? 'text-emerald-500' : ($symDiff < 0 ? 'text-red-500' : 'text-zinc-500');
                $symIcon = $symDiff > 0 ? 'fa-arrow-up' : ($symDiff < 0 ? 'fa-arrow-down' : 'fa-minus');
                
                // Assimetria Ombros (Menor é melhor)
                $asym1 = floatval($metrics1['asymmetry_shoulders'] ?? 0);
                $asym2 = floatval($metrics2['asymmetry_shoulders'] ?? 0);
                $asymDiff = $asym1 - $asym2; // Invertido pois menor é melhor
                $asymColor = $asymDiff > 0 ? 'text-emerald-500' : ($asymDiff < 0 ? 'text-red-500' : 'text-zinc-500');
                $asymIcon = $asymDiff > 0 ? 'fa-arrow-up' : ($asymDiff < 0 ? 'fa-arrow-down' : 'fa-minus');
            @endphp

            <!-- Card Postura / Score Geral -->
            <div class="bg-zinc-900/50 rounded-xl p-5 border border-white/5 relative overflow-hidden">
                <div class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-1">Score de Postura</div>
                <div class="flex items-end justify-between">
                    <div>
                        <div class="text-3xl font-black text-white">{{ number_format($sym1, 1) }}% <span class="text-zinc-600 font-light mx-2">➔</span> {{ number_format($sym2, 1) }}%</div>
                    </div>
                    <div class="{{ $symColor }} font-black text-lg flex items-center gap-1">
                        <i class="fas {{ $symIcon }}"></i>
                        {{ $symDiff > 0 ? '+' : '' }}{{ number_format($symDiff, 1) }}%
                    </div>
                </div>
                <!-- Barra visual do progresso -->
                <div class="w-full bg-zinc-800 h-1.5 mt-4 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-blue-500 h-full rounded-full transition-all duration-1000" style="width: {{ $sym2 }}%"></div>
                </div>
            </div>

            <!-- Card Assimetria -->
            <div class="bg-zinc-900/50 rounded-xl p-5 border border-white/5 relative overflow-hidden">
                <div class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-1">Nível de Assimetria (Ombros)</div>
                <div class="flex items-end justify-between">
                    <div>
                        <div class="text-3xl font-black text-white">{{ number_format($asym1, 1) }} <span class="text-zinc-600 font-light mx-2">➔</span> {{ number_format($asym2, 1) }}</div>
                    </div>
                    <div class="{{ $asymColor }} font-black text-lg flex items-center gap-1">
                        <i class="fas {{ $asymIcon }}"></i>
                        {{ $asymDiff > 0 ? 'Melhorou' : ($asymDiff < 0 ? 'Piorou' : 'Estável') }}
                    </div>
                </div>
                <p class="text-xs text-zinc-500 mt-2">* Valores menores indicam melhor simetria bilateral.</p>
            </div>

        </div>
    </div>
</div>
@endsection
