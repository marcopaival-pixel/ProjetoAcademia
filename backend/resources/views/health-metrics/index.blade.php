@extends('layouts.app')

@section('title', 'Saude e Recuperacao')

@section('content')
<div class="min-h-screen bg-[#080a0f] text-white p-6 md:p-12">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-12">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center border border-emerald-500/20">
                        <i data-lucide="heart" class="w-6 h-6 text-emerald-500"></i>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black tracking-tighter uppercase italic">
                        Saude e <span class="text-emerald-500">Recuperacao</span>
                    </h1>
                </div>
                <p class="text-zinc-500 font-medium uppercase tracking-[0.3em] text-xs">Sinais vitais, sono e prontidao para treino</p>
            </div>

            <div class="flex items-center gap-4">
                <button onclick="window.openMetricModal()" class="px-8 py-4 bg-emerald-500 hover:bg-emerald-400 text-zinc-950 font-black rounded-2xl transition-all shadow-xl shadow-emerald-500/20 active:scale-95 text-xs uppercase tracking-widest flex items-center gap-3">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Registrar Sinais Vitais
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Stats Grid -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $cards = [
                        [
                            'label' => 'HRV (Variabilidade)',
                            'value' => $latest['hrv']?->value,
                            'unit' => 'ms',
                            'icon' => 'activity',
                            'color' => 'emerald',
                            'desc' => 'Equilibrio do sistema nervoso'
                        ],
                        [
                            'label' => 'Sono Total',
                            'value' => $latest['sleep']?->value,
                            'unit' => 'hrs',
                            'icon' => 'moon',
                            'color' => 'blue',
                            'desc' => 'Tempo registrado de sono'
                        ],
                        [
                            'label' => 'Indice de Recuperacao',
                            'value' => $latest['recovery']?->value,
                            'unit' => '%',
                            'icon' => 'zap',
                            'color' => 'amber',
                            'desc' => 'Prontidao para esforco'
                        ]
                    ];
                @endphp

                @foreach($cards as $card)
                <div class="bg-zinc-900/50 border border-white/5 rounded-[2.5rem] p-8 relative overflow-hidden group hover:border-{{ $card['color'] }}-500/30 transition-all">
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-{{ $card['color'] }}-500/5 rounded-full blur-3xl group-hover:bg-{{ $card['color'] }}-500/10 transition-all"></div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <div class="w-12 h-12 bg-{{ $card['color'] }}-500/10 rounded-2xl flex items-center justify-center border border-{{ $card['color'] }}-500/20 text-{{ $card['color'] }}-500">
                            <i data-lucide="{{ $card['icon'] }}" class="w-6 h-6"></i>
                        </div>
                        <span class="text-[8px] font-black text-zinc-600 uppercase tracking-[0.3em] italic">Ultimo registro</span>
                    </div>

                    <div class="space-y-1">
                        @if($card['value'] !== null)
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black text-white tracking-tighter">{{ $card['value'] }}</span>
                                <span class="text-xs font-black text-zinc-500 uppercase">{{ $card['unit'] }}</span>
                            </div>
                        @else
                            <button type="button" onclick="window.openMetricModal()" class="text-left group/empty">
                                <span class="block text-2xl font-black text-zinc-500 tracking-tighter">Sem dados</span>
                                <span class="mt-1 inline-flex text-[9px] font-black text-{{ $card['color'] }}-500 uppercase tracking-widest group-hover/empty:text-white transition-colors">Fazer primeiro registro</span>
                            </button>
                        @endif
                        <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">{{ $card['label'] }}</p>
                        <p class="text-[8px] text-zinc-600 uppercase tracking-widest">{{ $card['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Trend Chart -->
            <div class="bg-zinc-900/50 border border-white/5 rounded-[2.5rem] p-10 relative overflow-hidden">
                <div class="flex items-center justify-between mb-10">
                    <div>
                        <h3 class="text-xl font-black text-white tracking-tighter uppercase italic">Tendencia de Recuperacao</h3>
                        <p class="text-[8px] text-zinc-500 uppercase tracking-[0.3em] font-black mt-1">Ultimos 30 dias</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shadow-lg shadow-emerald-500/20"></span>
                            <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Recuperacao</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500 shadow-lg shadow-blue-500/20"></span>
                            <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">HRV</span>
                        </div>
                    </div>
                </div>

                @if($recordDays > 0)
                    <div id="healthChart" class="h-80"></div>
                @else
                    <div class="h-80 flex flex-col items-center justify-center text-center border border-dashed border-white/10 rounded-[2rem] bg-zinc-950/30">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-500 mb-5">
                            <i data-lucide="line-chart" class="w-7 h-7"></i>
                        </div>
                        <h4 class="text-lg font-black text-white uppercase italic tracking-tight">Sua tendencia aparecera aqui</h4>
                        <p class="mt-2 max-w-md text-sm text-zinc-500 leading-relaxed">Registre seus sinais por alguns dias para acompanhar a evolucao da recuperacao.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar / IA Insights -->
        <div class="lg:col-span-4 space-y-8">
            <!-- IA Coach Card -->
            <div class="{{ $hasEnoughDataForAi ? 'bg-gradient-to-br from-emerald-500 to-emerald-700 text-zinc-950 shadow-emerald-500/20' : 'bg-zinc-900/50 border border-white/5 text-white' }} rounded-[2.5rem] p-10 relative overflow-hidden group shadow-2xl">
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
                
                <div class="relative space-y-8">
                    <div class="w-14 h-14 {{ $hasEnoughDataForAi ? 'bg-zinc-950 text-emerald-500' : 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' }} rounded-3xl flex items-center justify-center shadow-2xl">
                        <i data-lucide="sparkles" class="w-8 h-8 fill-current"></i>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-2xl font-black tracking-tighter uppercase italic leading-none">Analise NexBot</h3>
                        <p class="text-xs font-bold leading-relaxed {{ $hasEnoughDataForAi ? 'opacity-90' : 'text-zinc-400' }}">
                            @if(!$hasEnoughDataForAi)
                                Analise ainda indisponivel. Registre pelo menos 3 dias de informacoes para gerar uma avaliacao confiavel.
                            @elseif($latest['recovery'] && $latest['recovery']->value < 60)
                                Sua recuperacao recente esta abaixo da media. Priorize sono, hidratacao e reduza a intensidade do treino planejado.
                            @elseif($latest['hrv'] && $latest['hrv']->value > 70)
                                Seus sinais indicam boa prontidao. Se a percepcao de esforco tambem estiver positiva, hoje pode comportar maior intensidade.
                            @else
                                Seus registros ja permitem uma leitura inicial. Consulte a analise completa para cruzar sono, HRV e recuperacao.
                            @endif
                        </p>
                    </div>

                    <a href="{{ route('chat.page') }}" class="inline-flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.2em] border-b-2 {{ $hasEnoughDataForAi ? 'border-zinc-950' : 'border-emerald-500 text-emerald-500' }} pb-1">
                        {{ $hasEnoughDataForAi ? 'Ver analise completa' : 'Conversar com NexBot' }}
                        <i data-lucide="arrow-right" class="w-3 h-3"></i>
                    </a>
                </div>
            </div>

            <!-- Integration Status -->
            <div class="bg-zinc-900/50 border border-white/5 rounded-[2.5rem] p-10 space-y-8">
                <div><h3 class="text-[10px] font-black text-white uppercase tracking-[0.3em] italic">Conectar dispositivo</h3><p class="mt-2 text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Integracoes em desenvolvimento</p></div>
                
                <div class="space-y-4">
                    @foreach([
                        ['name' => 'Garmin Connect', 'status' => 'Em Breve', 'icon' => 'smartphone'],
                        ['name' => 'Apple Health', 'status' => 'Em Breve', 'icon' => 'watch'],
                        ['name' => 'Health Connect', 'status' => 'Em Breve', 'icon' => 'activity']
                    ] as $app)
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-zinc-950 border border-white/5 group opacity-50">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-zinc-900 flex items-center justify-center text-zinc-500">
                                <i data-lucide="{{ $app['icon'] }}" class="w-5 h-5"></i>
                            </div>
                            <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">{{ $app['name'] }}</span>
                        </div>
                        <span class="text-[8px] font-black text-zinc-600 uppercase tracking-widest">{{ $app['status'] }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="pt-4">
                    <p class="text-[9px] text-zinc-600 font-medium leading-relaxed uppercase tracking-widest">
                        As integracoes automaticas estao em homologacao. Enquanto isso, registre seus sinais manualmente para manter seu historico de recuperacao.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Registro -->
<div id="metricModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-6 bg-zinc-950/60 backdrop-blur-md animate-fade-in" style="display: none;">
    <div class="bg-zinc-900 border border-white/10 w-full max-w-lg rounded-[3rem] p-1 shadow-2xl animate-fade-in-up">
        <div class="bg-zinc-950/50 rounded-[2.9rem] p-10 md:p-12 space-y-8">
            <div class="flex items-center justify-between">
                <h3 class="text-2xl font-black text-white tracking-tighter uppercase italic">Registrar Sinais Vitais</h3>
                <button onclick="window.closeMetricModal()" class="text-zinc-500 hover:text-white">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form action="{{ route('health-metrics.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Tipo de sinal</label>
                    <select name="type" required class="w-full h-16 bg-zinc-900 border border-white/10 rounded-2xl px-6 text-white text-sm focus:border-emerald-500 outline-none transition-all appearance-none">
                        <option value="hrv">Variabilidade cardiaca - HRV (ms)</option>
                        <option value="sleep_hours">Sono Total (Horas)</option>
                        <option value="recovery_score">Indice de Recuperacao (%)</option>
                        <option value="resting_hr">Frequencia de repouso (bpm)</option>
                        <option value="spo2">Saturacao de oxigenio (SpO2)</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Valor medido</label>
                    <input type="number" step="0.1" name="value" required placeholder="0.0" class="w-full h-16 bg-zinc-900 border border-white/10 rounded-2xl px-6 text-white text-sm focus:border-emerald-500 outline-none transition-all">
                </div>

                <input type="hidden" name="recorded_at" value="{{ now()->format('Y-m-d H:i:s') }}">
                <input type="hidden" name="source" value="Manual Entry">

                <button type="submit" class="w-full py-6 bg-emerald-500 text-zinc-950 font-black rounded-3xl text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/20 active:scale-95 transition-all">
                    Salvar registro
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const history = @json($history);
        
        // Preparar dados para o grafico
        const dates = [];
        const hrvData = [];
        const recoveryData = [];

        // Consolidar datas unicas dos ultimos 30 dias
        const allEntries = [...(history.hrv || []), ...(history.recovery_score || [])]
            .sort((a, b) => new Date(a.recorded_at) - new Date(b.recorded_at));
        
        const uniqueDates = [...new Set(allEntries.map(e => e.recorded_at.split(' ')[0]))];
        
        uniqueDates.forEach(date => {
            dates.push(new Date(date).toLocaleDateString('pt-BR', { day: '2-digit', month: 'short' }));
            
            const hrvMatch = (history.hrv || []).find(e => e.recorded_at.startsWith(date));
            hrvData.push(hrvMatch ? hrvMatch.value : null);
            
            const recoveryMatch = (history.recovery_score || []).find(e => e.recorded_at.startsWith(date));
            recoveryData.push(recoveryMatch ? recoveryMatch.value : null);
        });

        const options = {
            series: [{
                name: 'Recuperacao (%)',
                data: recoveryData
            }, {
                name: 'HRV (ms)',
                data: hrvData
            }],
            chart: {
                height: 320,
                type: 'area',
                background: 'transparent',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#10b981', '#3b82f6'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            grid: {
                borderColor: 'rgba(255,255,255,0.05)',
                strokeDashArray: 4,
                padding: { left: 20, right: 20 }
            },
            xaxis: {
                categories: dates,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: { colors: '#71717a', fontSize: '9px', fontWeight: 700 }
                }
            },
            yaxis: {
                labels: {
                    style: { colors: '#71717a', fontSize: '9px', fontWeight: 700 }
                }
            },
            legend: { show: false },
            tooltip: {
                theme: 'dark',
                x: { show: true },
                y: { title: { formatter: (val) => val + ':' } }
            }
        };

        const chart = new ApexCharts(document.querySelector("#healthChart"), options);
        chart.render();

        window.openMetricModal = function() {
            document.getElementById('metricModal').style.display = 'flex';
        };

        window.closeMetricModal = function() {
            document.getElementById('metricModal').style.display = 'none';
        };
    });
</script>
@endpush
