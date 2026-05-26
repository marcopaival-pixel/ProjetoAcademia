@extends('layouts.app')

@section('title', 'NexShape Elite — Relatório de Performance')

@section('style')
<style>
    :root {
        --elite-card: rgba(20, 24, 34, 0.85);
        --elite-border: rgba(16, 185, 129, 0.2);
        --elite-accent: #10b981;
    }
    
    .elite-card {
        background: var(--elite-card);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--elite-border);
        border-radius: 1.5rem;
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.5);
    }
    
    .score-bar-bg {
        background: rgba(255,255,255,0.05);
        border-radius: 999px;
        overflow: hidden;
        height: 6px;
        width: 100%;
    }
    
    .score-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #059669, #10b981);
        border-radius: 999px;
        transition: width 1s ease-in-out;
    }

    /* Print Styles */
    .print-only {
        display: none;
    }
    
    @media print {
        body * { visibility: hidden; }
        #print-section, #print-section * { visibility: visible; }
        
        #print-section {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .no-print { display: none !important; visibility: hidden !important; }
        
        body, html {
            background: #fff !important;
            color: #000 !important;
        }

        .elite-card {
            background: transparent !important;
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            page-break-inside: avoid;
            margin-bottom: 20px !important;
        }
        
        .score-bar-bg { background: #eee !important; }
        .score-bar-fill { background: #10b981 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        
        h1, h2, h3, h4, p, span, div { color: #000 !important; }
        .text-zinc-400, .text-zinc-500 { color: #555 !important; }
        .text-emerald-500 { color: #059669 !important; }
        
        .grid { display: block !important; }
        .grid > div { margin-bottom: 20px; }
        
        .print-only { display: block !important; }
        
        @page { margin: 1.5cm; }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="py-10 space-y-8 animate-fade-in-up max-w-[1200px] mx-auto px-6 relative z-10 pb-32">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-6 border-b border-zinc-900">
        <div class="space-y-2">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20 shadow-inner shadow-emerald-500/5">Elite Performance AI</span>
                <span class="text-zinc-700">•</span>
                <span class="text-zinc-500 text-xs font-black italic uppercase tracking-tighter">SaaS Enterprise</span>
            </div>
            <h1 class="text-5xl font-black tracking-tight text-white leading-tight uppercase">Dashboard <span class="text-emerald-500">Inteligente</span></h1>
            <p class="text-zinc-500 font-medium">Análise avançada de biometria e comportamento desportivo.</p>
        </div>
        
        <div class="flex flex-col items-end gap-4 no-print">
            <div class="flex items-center gap-4">
                <button onclick="printReport()" class="px-8 py-5 bg-emerald-500 text-zinc-950 font-black text-xs rounded-2xl hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/10 flex items-center gap-3 uppercase tracking-widest">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span class="hidden md:inline">Exportar PDF</span>
                </button>
                <a href="{{ route('evolution.index') }}" class="px-8 py-5 bg-zinc-900 border border-zinc-800 text-zinc-400 font-black text-xs rounded-2xl hover:text-white hover:border-zinc-700 transition-all flex items-center gap-3 uppercase tracking-widest">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Voltar
                </a>
            </div>
            
            <div class="hidden md:flex items-center gap-3 bg-zinc-900/40 p-2.5 rounded-2xl border border-zinc-800/50 backdrop-blur-sm">
                <div class="bg-white p-1 rounded-xl">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=60x60&data={{ urlencode(url()->current()) }}" alt="QR Code" class="w-[42px] h-[42px] object-cover mix-blend-multiply">
                </div>
                <div class="pr-3 text-right">
                    <p class="text-[9px] text-zinc-400 uppercase tracking-widest font-black leading-tight">Digitalizar QR</p>
                    <p class="text-xs text-emerald-500 font-bold leading-tight">Versão Mobile</p>
                </div>
            </div>
        </div>
    </div>

    <div id="print-section" class="space-y-6">
        <!-- Print Header -->
        <div class="print-only mb-8 pb-6" style="border-bottom: 4px solid #10b981;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h1 style="font-size: 28pt; font-weight: 900; text-transform: uppercase; margin: 0; color: #000; letter-spacing: -1px;">NEX<span style="color: #10b981;">SHAPE</span> ELITE</h1>
                    <p style="font-size: 10pt; font-weight: bold; color: #666; text-transform: uppercase; letter-spacing: 3px; margin-top: 5px;">Relatório de Performance AI</p>
                    <div style="margin-top: 25px;">
                        <p style="margin: 0; font-size: 12pt; color: #000;"><strong>Atleta:</strong> {{ $user->name }}</p>
                        <p style="margin: 0; font-size: 10pt; color: #666;"><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(url()->current()) }}" alt="QR Code" style="width: 120px; height: 120px; border: 2px solid #000; padding: 4px; border-radius: 8px;">
                </div>
            </div>
        </div>

        @if(isset($reportData['diagnostico']))
            <!-- Insight & General Score -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Insight Premium -->
                <div class="elite-card p-8 lg:col-span-2 relative overflow-hidden flex flex-col justify-center">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl -mr-20 -mt-20"></div>
                    <div class="flex items-center gap-3 mb-4 relative z-10">
                        <i data-lucide="brain-circuit" class="w-6 h-6 text-emerald-500"></i>
                        <h2 class="text-sm font-black text-white uppercase tracking-widest">Insight Inteligente NexShape</h2>
                    </div>
                    <p class="text-zinc-300 text-lg leading-relaxed relative z-10">
                        {{ $reportData['insight_premium'] ?? $reportData['diagnostico'] }}
                    </p>
                </div>
                
                <!-- Performance Geral -->
                <div class="elite-card p-8 flex flex-col items-center justify-center text-center relative">
                    <svg class="absolute inset-0 w-full h-full opacity-10" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="2" stroke-dasharray="4 4" />
                    </svg>
                    <h2 class="text-xs font-black text-zinc-400 uppercase tracking-widest mb-2 relative z-10">Performance Geral</h2>
                    <div class="text-6xl font-black text-white relative z-10 tracking-tighter">
                        {{ $reportData['scores']['performance_geral'] ?? 0 }}<span class="text-2xl text-emerald-500">%</span>
                    </div>
                    <p class="text-xs text-zinc-500 mt-3 uppercase tracking-widest relative z-10 font-bold">Status do Atleta</p>
                </div>
            </div>

            <!-- Radar & Detalhes -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Gráfico de Radar -->
                <div class="elite-card p-8 flex flex-col">
                    <div class="flex items-center gap-3 mb-6">
                        <i data-lucide="radar" class="w-5 h-5 text-emerald-500"></i>
                        <h2 class="text-xs font-black text-white uppercase tracking-widest">Mapeamento Biométrico</h2>
                    </div>
                    <div class="flex-1 flex items-center justify-center min-h-[300px]">
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>

                <!-- Scores Individuais -->
                <div class="elite-card p-8">
                    <div class="flex items-center gap-3 mb-8">
                        <i data-lucide="activity" class="w-5 h-5 text-emerald-500"></i>
                        <h2 class="text-xs font-black text-white uppercase tracking-widest">Métricas de Disciplina</h2>
                    </div>
                    
                    <div class="space-y-6">
                        @php
                            $metricas = [
                                'Disciplina' => $reportData['scores']['disciplina'] ?? 0,
                                'Consistência' => $reportData['scores']['consistencia'] ?? 0,
                                'Recuperação' => $reportData['scores']['recuperacao'] ?? 0,
                                'Intensidade' => $reportData['scores']['intensidade'] ?? 0,
                                'Condicionamento' => $reportData['scores']['condicionamento'] ?? 0,
                            ];
                        @endphp

                        @foreach($metricas as $nome => $valor)
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-zinc-300 uppercase tracking-wider">{{ $nome }}</span>
                                <span class="text-xs font-black text-emerald-500">{{ $valor }}%</span>
                            </div>
                            <div class="score-bar-bg">
                                <div class="score-bar-fill" style="width: {{ $valor }}%;"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tendências -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($reportData['tendencias'] ?? [] as $tendencia)
                <div class="elite-card p-6 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-1">{{ $tendencia['area'] }}</p>
                        <p class="text-sm font-bold text-zinc-200">{{ $tendencia['status'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-zinc-800/50 flex items-center justify-center text-emerald-500 font-bold text-xl border border-white/5">
                        {{ $tendencia['icon'] }}
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Análises Detalhadas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="elite-card p-8">
                    <h3 class="text-xs font-black text-emerald-500 uppercase tracking-widest mb-4 flex items-center gap-2"><i data-lucide="target" class="w-4 h-4"></i> Estratégia da Semana</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">{{ $reportData['estrategia_semana'] ?? '' }}</p>
                </div>
                <div class="elite-card p-8">
                    <h3 class="text-xs font-black text-emerald-500 uppercase tracking-widest mb-4 flex items-center gap-2"><i data-lucide="dumbbell" class="w-4 h-4"></i> Ajustes de Treino</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">{{ $reportData['ajustes_treino'] ?? '' }}</p>
                </div>
                <div class="elite-card p-8">
                    <h3 class="text-xs font-black text-emerald-500 uppercase tracking-widest mb-4 flex items-center gap-2"><i data-lucide="battery-charging" class="w-4 h-4"></i> Recuperação e Energia</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">{{ $reportData['recuperacao_energia'] ?? '' }}</p>
                </div>
                <div class="elite-card p-8">
                    <h3 class="text-xs font-black text-emerald-500 uppercase tracking-widest mb-4 flex items-center gap-2"><i data-lucide="flame" class="w-4 h-4"></i> Nutrição e Metabolismo</h3>
                    <p class="text-zinc-400 text-sm leading-relaxed">{{ $reportData['alimentacao_metabolismo'] ?? '' }}</p>
                </div>
            </div>

            <!-- Próximos Passos -->
            <div class="elite-card p-8 border-l-4 border-l-emerald-500">
                <h3 class="text-xs font-black text-white uppercase tracking-widest mb-6">Próximos Passos Estratégicos</h3>
                <ul class="space-y-3">
                    @foreach($reportData['proximos_passos'] ?? [] as $passo)
                    <li class="flex items-start gap-3">
                        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500 shrink-0"></i>
                        <span class="text-sm text-zinc-300">{{ $passo }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            
            <!-- Veredito -->
            <div class="text-center py-8">
                <p class="text-xl md:text-2xl font-black text-white italic uppercase tracking-tighter">"{{ $reportData['veredito'] ?? 'Mantenha a disciplina. Os resultados virão.' }}"</p>
                <div class="w-16 h-1 bg-emerald-500 mx-auto mt-6 rounded-full"></div>
            </div>

        @else
            <div class="elite-card p-12 text-center">
                <i data-lucide="alert-triangle" class="w-12 h-12 text-yellow-500 mx-auto mb-4"></i>
                <h2 class="text-xl font-bold text-white mb-2">Formato Inválido</h2>
                <p class="text-zinc-400">Ocorreu um problema ao descodificar os dados inteligentes. Por favor, limpe a cache ou gere o relatório novamente.</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        document.getElementById('print-section').children[0].style.display = 'none';

        @if(isset($reportData['scores']))
        const ctx = document.getElementById('radarChart').getContext('2d');
        
        // Cores Premium Dark Mode para o Radar
        Chart.defaults.color = '#71717a';
        Chart.defaults.font.family = "'Outfit', sans-serif";
        
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Disciplina', 'Consistência', 'Recuperação', 'Intensidade', 'Condicionamento'],
                datasets: [{
                    label: 'Score Atual',
                    data: [
                        {{ $reportData['scores']['disciplina'] ?? 0 }},
                        {{ $reportData['scores']['consistencia'] ?? 0 }},
                        {{ $reportData['scores']['recuperacao'] ?? 0 }},
                        {{ $reportData['scores']['intensidade'] ?? 0 }},
                        {{ $reportData['scores']['condicionamento'] ?? 0 }}
                    ],
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: '#10b981',
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#10b981',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { color: 'rgba(255,255,255,0.05)' },
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        pointLabels: {
                            font: { size: 10, weight: 'bold' },
                            color: '#a1a1aa'
                        },
                        ticks: {
                            display: false,
                            min: 0,
                            max: 100
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
        @endif
    });

    function printReport() {
        // Converter o Canvas para Imagem para que seja impresso no PDF
        const canvas = document.getElementById('radarChart');
        let canvasHtml = '';
        if (canvas) {
            const dataUrl = canvas.toDataURL('image/png');
            // Salvar o canvas original
            canvasHtml = canvas.outerHTML;
            // Substituir temporariamente por imagem
            canvas.outerHTML = '<img id="radarChartPrint" src="' + dataUrl + '" style="width: 100%; max-width: 300px; margin: 0 auto; display: block;" />';
        }

        const printContents = document.getElementById('print-section').innerHTML;
        const originalContents = document.body.innerHTML;
        
        document.body.innerHTML = '<div style="background: white; color: black; padding: 20px;">' + printContents + '</div>';
        
        // Esconder gráfico se falhou na conversão (fallback)
        const radarPrint = document.getElementById('radarChartPrint');
        if(radarPrint) { radarPrint.style.filter = "invert(1)"; } // Para ficar com as linhas pretas no papel branco
        
        document.body.children[0].children[0].style.display = 'block';

        window.print();
        
        document.body.innerHTML = originalContents;
        lucide.createIcons();
        window.location.reload();
    }
</script>
@endpush
@endsection
