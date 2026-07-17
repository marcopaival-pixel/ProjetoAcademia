@extends('layouts.app')

@section('title', 'Evolução de Cargas')

@section('content')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if($isPremium)
<div class="py-8 space-y-8 animate-fade-in max-w-[1400px] mx-auto px-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white">Evolução de Desempenho</h1>
            <p class="text-zinc-500 text-sm mt-1">Análise avançada de força e volume de treino.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('progression.plans.index') }}" class="px-5 py-2.5 bg-zinc-900 text-zinc-300 font-bold text-xs rounded-xl border border-white/5 hover:text-white transition-all">
                <i class="fas fa-arrow-left me-2"></i>Meus Treinos
            </a>
            @if($exerciseId)
                <button onclick="window.print()" class="px-5 py-2.5 bg-zinc-800 text-white font-black text-xs rounded-xl hover:bg-zinc-700 transition-all">
                    <i class="fas fa-print me-2"></i>Exportar PDF
                </button>
            @endif
        </div>
    </div>

    <!-- Global Stats Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-6">
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest block mb-1">Tonelagem (30 dias)</span>
            <h4 class="text-2xl font-black text-white">
                @if($isPremium)
                    {{ number_format($totalVolumeMonth, 0, ',', '.') }}
                @else
                    {{ number_format($totalVolumeMonth / 1000, 1, ',', '.') }}<small class="text-zinc-500 text-xs">t</small>
                @endif
                <small class="text-zinc-500 text-xs">kg</small>
            </h4>
        </div>
        <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-6">
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest block mb-1">Treinos (30 dias)</span>
            <h4 class="text-2xl font-black text-blue-400">{{ $sessionCountMonth }} <small class="text-zinc-500 text-xs">sessões</small></h4>
        </div>
        <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-6">
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest block mb-1">Recorde Pessoal (PR)</span>
            <h4 class="text-2xl font-black text-amber-500">{{ $personalRecordValue > 0 ? round($personalRecordValue, 1) : '--' }} <small class="text-zinc-500 text-xs">kg</small></h4>
        </div>
        <div class="bg-zinc-900/40 border border-white/5 rounded-2xl p-6">
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest block mb-1">Ganho de Força</span>
            <h4 class="text-2xl font-black {{ $strengthGainPercent >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                {{ $strengthGainPercent > 0 ? '+' : '' }}{{ round($strengthGainPercent, 1) }}%
            </h4>
        </div>
    </div>

    <!-- Composição Corporal (Sempre visível se houver dados) -->
    @if(count($compositionData) > 0)
    <div class="bg-zinc-900/20 border border-white/5 rounded-[2.5rem] p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-xl font-black text-white">Evolução de Composição Corporal</h3>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Peso (kg) vs Gordura (%)</p>
            </div>
            <div class="flex items-center gap-4 text-[10px] font-black uppercase">
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> <span class="text-zinc-400">Peso</span></div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500"></span> <span class="text-zinc-400">Gordura %</span></div>
            </div>
        </div>
        <div style="height: 300px;">
            <canvas id="compositionChart"></canvas>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar: Exercise List -->
        <div class="space-y-6">
            <div class="bg-zinc-900/40 border border-white/5 rounded-3xl p-6">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    Meus Exercícios
                </h3>
                <div class="flex flex-col gap-1 max-h-[500px] overflow-y-auto custom-scrollbar">
                    @foreach($userExercises as $ex)
                        <a href="{{ route('progression.charts', ['exercise_id' => $ex->id, 'range' => $range]) }}" 
                           class="flex items-center justify-between p-3 rounded-xl transition-all {{ $exerciseId == $ex->id ? 'bg-blue-600 text-white' : 'text-zinc-400 hover:bg-white/5' }}">
                            <div class="flex flex-col overflow-hidden">
                                <span class="text-xs font-bold truncate">{{ $ex->name }}</span>
                                <span class="text-[9px] opacity-60 uppercase">{{ $ex->muscle_group }}</span>
                            </div>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </a>
                    @endforeach
                    @if($userExercises->isEmpty())
                        <p class="text-[10px] text-zinc-600 italic p-4">Nenhum treino registrado ainda.</p>
                    @endif
                </div>
            </div>

            <!-- Muscle Distribution (Premium: Volume / Free: Count) -->
            <div class="bg-zinc-900/20 border border-white/5 rounded-3xl p-6 relative group overflow-hidden">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center justify-between mb-6">
                    <span class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                        Domínio Muscular
                    </span>
                    @if($isPremium)
                        <span class="text-blue-400 text-[8px] italic tracking-tighter">Volume (kg)</span>
                    @else
                        <span class="text-zinc-600 text-[8px] italic tracking-tighter">Qtd. Exercícios</span>
                    @endif
                </h3>
                <div style="height: 180px;">
                    <canvas id="muscleChart"></canvas>
                </div>
                @if(!$isPremium)
                <div class="absolute inset-0 bg-zinc-950/20 backdrop-blur-[1px] opacity-0 group-hover:opacity-100 transition-all flex flex-col items-center justify-center p-4 text-center cursor-pointer" onclick="document.getElementById('premiumModal').style.display = 'flex'">
                    <i class="fas fa-lock text-amber-500 text-xs mb-1"></i>
                    <p class="text-[8px] text-white font-black uppercase tracking-widest">Liberar Análise de Volume kg</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Main Content: Charts -->
        <div class="lg:col-span-3 space-y-8">
            @if($exerciseId && count($chartData) > 0)
                <!-- Exercise Header & Range -->
                <div class="bg-zinc-900/20 border border-white/5 rounded-3xl p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                        <div>
                            <h2 class="text-xl font-black text-white">{{ $userExercises->where('id', $exerciseId)->first()->name }}</h2>
                            <p class="text-zinc-500 text-[10px] uppercase font-bold tracking-widest mt-1">Evolução de 1-RM Estimado</p>
                        </div>
                        <div class="flex bg-zinc-950 p-1 rounded-xl border border-white/5">
                            @foreach([30 => '30D', 60 => '60D', 90 => '90D', 365 => '1A', 1000 => 'Total'] as $val => $label)
                                <a href="{{ route('progression.charts', ['exercise_id' => $exerciseId, 'range' => $val]) }}" 
                                   class="px-4 py-1.5 rounded-lg text-[10px] font-black transition-all {{ $range == $val ? 'bg-blue-600 text-white' : 'text-zinc-500 hover:text-white' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    
                    <div style="height: 350px;">
                        <canvas id="strengthChart"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Volume Chart -->
                    <div class="bg-zinc-900/20 border border-white/5 rounded-3xl p-6">
                        <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-8">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Volume de Treino
                        </h3>
                        <div style="height: 200px;">
                            <canvas id="volumeChart"></canvas>
                        </div>
                    </div>

                    <!-- RPE Trend Chart -->
                    <div class="bg-zinc-900/20 border border-white/5 rounded-3xl p-6">
                        <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-8">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Frequência de Esforço (RPE)
                        </h3>
                        <div style="height: 200px;">
                            <canvas id="rpeChart"></canvas>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-zinc-900/20 border border-white/5 rounded-3xl p-20 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 rounded-full bg-zinc-800 flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-zinc-600 text-3xl"></i>
                    </div>
                    <h3 class="text-white font-black">Nenhum Exercício Selecionado</h3>
                    <p class="text-zinc-500 text-sm mt-1">Selecione um exercício na lista lateral para analisar sua evolução.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    body { background-color: #0c0f16; }
    .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.05); border-radius: 10px; }
</style>

@if(count($chartData) > 0)
<script>
    const chartData = @json($chartData);
    const labels = chartData.map(d => d.date);

    // Common Scale Config
    const scaleConfig = {
        y: { grid: { color: 'rgba(255, 255, 255, 0.02)' }, ticks: { color: '#52525b', font: { size: 9 } } },
        x: { grid: { display: false }, ticks: { color: '#52525b', font: { size: 9 } } }
    };

    // Strength Chart
    new Chart(document.getElementById('strengthChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: '1-RM (kg)',
                data: chartData.map(d => d.one_rm),
                borderColor: '#3b82f6',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                backgroundColor: 'rgba(59, 130, 246, 0.05)',
                pointRadius: 4,
                pointBackgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: scaleConfig
        }
    });

    // Volume Chart
    new Chart(document.getElementById('volumeChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: chartData.map(d => d.volume),
                backgroundColor: 'rgba(16, 185, 129, 0.4)',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: scaleConfig
        }
    });

    // RPE Chart
    new Chart(document.getElementById('rpeChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                data: chartData.map(d => d.rpe),
                borderColor: '#f43f5e',
                borderWidth: 2,
                pointRadius: 2,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { min: 0, max: 10, ticks: { stepSize: 2, color: '#52525b', font: { size: 9 } }, grid: { color: 'rgba(255, 255, 255, 0.02)' } },
                x: { grid: { display: false }, ticks: { color: '#52525b', font: { size: 9 } } }
            }
        }
    });
</script>
@endif

<script>
    // Composition Chart (Weight vs BF)
    const compData = @json($compositionData);
    if (compData.length > 0) {
        new Chart(document.getElementById('compositionChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: compData.map(d => d.date),
                datasets: [
                    {
                        label: 'Peso (kg)',
                        data: compData.map(d => d.weight),
                        borderColor: '#10b981',
                        borderWidth: 4,
                        tension: 0.4,
                        yAxisID: 'y',
                        pointRadius: 5,
                        pointBackgroundColor: '#10b981'
                    },
                    {
                        label: 'Gordura (%)',
                        data: compData.map(d => d.bf),
                        borderColor: '#3b82f6',
                        borderWidth: 4,
                        tension: 0.4,
                        yAxisID: 'y1',
                        pointRadius: 5,
                        pointBackgroundColor: '#3b82f6'
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        type: 'linear', display: true, position: 'left',
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#10b981', font: { weight: 'bold' } }
                    },
                    y1: {
                        type: 'linear', display: true, position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { color: '#3b82f6', font: { weight: 'bold' } }
                    },
                    x: { grid: { display: false }, ticks: { color: '#52525b' } }
                }
            }
        });
    }

    // Muscle Distribution Chart
    const muscleData = @json($muscleDistribution);
    new Chart(document.getElementById('muscleChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(muscleData),
            datasets: [{
                data: Object.values(muscleData),
                backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f43f5e', '#f59e0b', '#10b981'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });
</script>
@else
{{-- LAYOUT PAYWALL CHARTS --}}
<div class="max-w-4xl mx-auto py-20 px-4 text-center">
    <div class="mb-10 inline-flex items-center justify-center w-24 h-24 rounded-[3rem] bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 shadow-2xl shadow-indigo-500/20">
        <i class="fas fa-chart-area text-4xl"></i>
    </div>
    
    <h1 class="text-4xl md:text-6xl font-black text-white tracking-tighter mb-6 leading-tight">
        Visualiza a tua <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">Evolução</span>
    </h1>
    
    <p class="text-xl text-zinc-400 mb-12 max-w-2xl mx-auto font-medium leading-relaxed">
        Não deixes o teu progresso ao acaso. Desbloqueia a análise avançada de dados para entender exatamente onde estás a ganhar força.
    </p>

    <div class="grid md:grid-cols-3 gap-6 mb-16 text-left">
        <div class="p-6 rounded-3xl bg-zinc-900/50 border border-white/5 hover:border-indigo-500/30 transition-all">
            <div class="w-10 h-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center mb-4">
                <i class="fas fa-signal"></i>
            </div>
            <h3 class="text-white font-bold mb-2 text-xs uppercase tracking-wider">Volume de Carga</h3>
            <p class="text-[10px] text-zinc-500 leading-relaxed">Gráficos detalhados de tonelagem semanal para garantir que estás em sobrecarga progressiva.</p>
        </div>
        
        <div class="p-6 rounded-3xl bg-zinc-900/50 border border-white/5 hover:border-indigo-500/30 transition-all">
            <div class="w-10 h-10 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center mb-4">
                <i class="fas fa-percentage"></i>
            </div>
            <h3 class="text-white font-bold mb-2 text-xs uppercase tracking-wider">Gordura vs Músculo</h3>
            <p class="text-[10px] text-zinc-500 leading-relaxed">Correlação direta entre o teu peso na balança e a tua composição corporal (%) ao longo do tempo.</p>
        </div>

        <div class="p-6 rounded-3xl bg-zinc-900/50 border border-white/5 hover:border-indigo-500/30 transition-all">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center mb-4">
                <i class="fas fa-medal"></i>
            </div>
            <h3 class="text-white font-bold mb-2 text-xs uppercase tracking-wider">PR Tracking</h3>
            <p class="text-[10px] text-zinc-500 leading-relaxed">Histórico completo de recordes pessoais (1RM) para celebrar cada pequena grande vitória.</p>
        </div>
    </div>

    <div class="bg-gradient-to-b from-blue-600 to-indigo-700 p-[1px] rounded-2xl inline-block shadow-2xl shadow-blue-600/20">
        <a href="{{ route('plano') }}" class="flex items-center gap-3 px-10 py-5 bg-[#0b0e14] rounded-[15px] text-white hover:bg-transparent transition-all group">
            <i class="fas fa-crown text-amber-500"></i>
            <span class="font-black uppercase tracking-[0.2em] text-sm">Libertar Gráficos NexCharts</span>
            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>
    
    <p class="mt-8 text-zinc-600 text-[10px] uppercase font-bold tracking-widest italic font-black">A inteligência que faltava ao teu treino</p>
</div>
@endif
@endsection
