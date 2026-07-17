@extends('layouts.app', ['navCurrent' => 'body-analysis'])

@section('title', 'Comparacao de Evolucao | Cyber-Fit')

@section('content')
@php
    $metrics1 = is_array($analysis_1->metrics) ? $analysis_1->metrics : (json_decode($analysis_1->metrics, true) ?? []);
    $metrics2 = is_array($analysis_2->metrics) ? $analysis_2->metrics : (json_decode($analysis_2->metrics, true) ?? []);

    $summary1 = is_array($analysis_1->ai_summary) ? $analysis_1->ai_summary : (json_decode($analysis_1->ai_summary, true) ?? []);
    $summary2 = is_array($analysis_2->ai_summary) ? $analysis_2->ai_summary : (json_decode($analysis_2->ai_summary, true) ?? []);

    $metricCards = [
        [
            'label' => 'Score de Postura',
            'key' => 'posture_score',
            'suffix' => '%',
            'higher_is_better' => true,
            'hint' => 'Quanto maior, melhor o alinhamento visual estimado.',
        ],
        [
            'label' => 'Assimetria dos Ombros',
            'key' => 'asymmetry_shoulders',
            'suffix' => '',
            'higher_is_better' => false,
            'hint' => 'Valores menores indicam ombros mais nivelados na foto.',
        ],
        [
            'label' => 'Assimetria do Quadril',
            'key' => 'asymmetry_hips',
            'suffix' => '',
            'higher_is_better' => false,
            'hint' => 'Valores menores indicam quadril mais nivelado na foto.',
        ],
        [
            'label' => 'Cabeca a Frente',
            'key' => 'head_forward_score',
            'suffix' => '',
            'higher_is_better' => false,
            'hint' => 'Usado principalmente na vista lateral; menor tende a ser melhor.',
        ],
        [
            'label' => 'Confianca dos Pontos',
            'key' => 'landmark_confidence',
            'suffix' => '',
            'higher_is_better' => true,
            'hint' => 'Quanto maior, mais confiavel foi a deteccao dos pontos corporais.',
        ],
    ];

    $statusFor = function (float $diff, bool $higherIsBetter): array {
        $improved = $higherIsBetter ? $diff > 0 : $diff < 0;
        $worse = $higherIsBetter ? $diff < 0 : $diff > 0;

        return [
            'color' => $improved ? 'text-emerald-500' : ($worse ? 'text-red-500' : 'text-zinc-500'),
            'icon' => $improved ? 'fa-arrow-up' : ($worse ? 'fa-arrow-down' : 'fa-minus'),
            'label' => $improved ? 'Melhorou' : ($worse ? 'Piorou' : 'Estavel'),
        ];
    };
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-24 animate-fade-in">
    <div class="flex items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black text-white uppercase tracking-tighter italic">
                Comparativo de <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-blue-400">Evolucao</span>
            </h1>
            <p class="text-zinc-400 mt-2">Leitura lado a lado com metricas de postura, assimetria e qualidade da deteccao.</p>
        </div>
        <a href="{{ route('body-analysis.index') }}" class="btn btn-outline-info">
            <i class="fas fa-arrow-left me-2"></i> Voltar ao Hub
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        @foreach([[$analysis_1, 'Data Base', 'border-white/5'], [$analysis_2, 'Progresso', 'border-emerald-500/20']] as [$analysis, $label, $border])
            <div class="card glass p-4 relative overflow-hidden group">
                <div class="absolute top-4 left-4 z-10 bg-zinc-900/80 backdrop-blur border border-white/10 px-3 py-1 rounded-full">
                    <span class="text-xs font-bold text-zinc-300">{{ $label }}:</span>
                    <span class="text-sm font-black text-white ml-1">{{ $analysis->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="aspect-[3/4] bg-zinc-950 rounded-2xl overflow-hidden flex items-center justify-center border {{ $border }}">
                    <img src="{{ Storage::url($analysis->photo_path) }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-700" alt="Analise corporal {{ $analysis->created_at->format('d/m/Y') }}">
                </div>
            </div>
        @endforeach
    </div>

    <div class="card glass p-6 border-l-4 border-emerald-500">
        <h3 class="text-xl font-black text-white uppercase tracking-wider mb-6 flex items-center gap-2">
            <i class="fas fa-microchip text-emerald-500"></i> Evolucao das Metricas
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($metricCards as $card)
                @php
                    $first = floatval($metrics1[$card['key']] ?? 0);
                    $second = floatval($metrics2[$card['key']] ?? 0);
                    $diff = $second - $first;
                    $status = $statusFor($diff, $card['higher_is_better']);
                @endphp
                <div class="bg-zinc-900/50 rounded-xl p-5 border border-white/5 relative overflow-hidden">
                    <div class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-1">{{ $card['label'] }}</div>
                    <div class="flex items-end justify-between gap-4">
                        <div class="text-2xl font-black text-white">
                            {{ number_format($first, 1) }}{{ $card['suffix'] }}
                            <span class="text-zinc-600 font-light mx-2">-></span>
                            {{ number_format($second, 1) }}{{ $card['suffix'] }}
                        </div>
                        <div class="{{ $status['color'] }} font-black text-sm flex items-center gap-1">
                            <i class="fas {{ $status['icon'] }}"></i>
                            {{ $status['label'] }}
                        </div>
                    </div>
                    <p class="text-xs text-zinc-500 mt-3">{{ $card['hint'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        @foreach([[$summary1, 'Primeira analise'], [$summary2, 'Analise recente']] as [$summary, $label])
            <div class="card glass p-5">
                <h3 class="text-sm font-black text-white uppercase tracking-widest mb-3">{{ $label }}</h3>
                <p class="text-sm text-zinc-300">{{ $summary['summary'] ?? 'Resumo indisponivel.' }}</p>
                @if(!empty($summary['attention_points']))
                    <ul class="mt-4 space-y-2 text-xs text-zinc-400">
                        @foreach($summary['attention_points'] as $point)
                            <li class="flex gap-2"><span class="text-emerald-400">&bull;</span><span>{{ $point }}</span></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
