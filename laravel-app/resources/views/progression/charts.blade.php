@extends('layouts.app')

@section('title', 'Evolução de Cargas')

@section('content')
<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest block mb-1">Volume (30 dias)</span>
            <h4 class="text-2xl font-black text-white">{{ number_format($totalVolumeMonth, 0, ',', '.') }} <small class="text-zinc-500 text-xs">kg</small></h4>
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

            <!-- Muscle Distribution (Small Chart) -->
            <div class="bg-zinc-900/20 border border-white/5 rounded-3xl p-6">
                <h3 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest flex items-center gap-2 mb-6">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                    Foco por Grupo
                </h3>
                <div style="height: 180px;">
                    <canvas id="muscleChart"></canvas>
                </div>
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
@endsection
