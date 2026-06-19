@extends('layouts.app')

@section('title', 'Hub de Evolução — ' . $branding['clinic_name'])

@section('style')
<style>
    :root {
        --brand-primary: {{ $branding['primary_color'] }};
        --brand-accent: {{ $branding['accent_color'] }};
        --card-bg: rgba(20, 22, 28, 0.7);
        --glass-border: rgba(255, 255, 255, 0.08);
    }
    
    .glass-card {
        background: var(--card-bg);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid var(--glass-border);
    }

    .metric-delta.positive { color: #f87171; }
    .metric-delta.negative { color: #34d399; }
    .metric-delta.neutral { color: #94a3b8; }

    .apexcharts-canvas {
        margin: 0 auto;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-[#06080c] text-white pb-32">
    <div class="py-10 px-6 max-w-2xl mx-auto space-y-10">
        <!-- Header -->
        <header class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-1"></div> <!-- Spacer for alignment -->
                <div>
                    <h1 class="text-xl font-black tracking-tighter uppercase italic">Hub de Evolução</h1>
                    <p class="text-[9px] font-black text-zinc-500 uppercase tracking-widest">Acompanhe sua transformação</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="generateShareCard()" class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 hover:bg-emerald-500/20 transition-all" title="Compartilhar Resultados">
                    <i class="fas fa-share-alt"></i>
                </button>
            </div>
        </header>

        <!-- Share Card Modal (Hidden by default) -->
        <div id="shareModal" class="fixed inset-0 z-[9999] hidden items-center justify-center p-4 bg-black/90 backdrop-blur-md">
            <div class="w-full max-w-sm space-y-6">
                <!-- The Card to be Captured -->
                <div id="captureCard" class="bg-[#06080c] w-full aspect-[9/16] rounded-[3rem] p-8 border border-white/10 relative overflow-hidden flex flex-col justify-between">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 via-transparent to-emerald-600/20"></div>
                    
                    <div class="relative z-10 space-y-6 text-center">
                        <img src="{{ $branding['logo_url'] ?: asset('images/logo_Academia.png') }}" class="h-10 mx-auto opacity-80" alt="Logo">
                        
                        <div class="space-y-1 pt-4">
                            <h2 class="text-xs font-black uppercase tracking-[0.3em] text-zinc-500">Minha Evolução</h2>
                            <p class="text-2xl font-black text-white italic tracking-tighter">{{ $patient->name }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-8">
                            <div class="glass-card p-4 rounded-3xl space-y-1">
                                <span class="text-[8px] font-black uppercase text-zinc-500">Peso Atual</span>
                                <div class="text-xl font-black text-white italic">{{ number_format($latest?->weight_kg ?? 0, 1) }}<small class="text-[10px] not-italic ml-0.5">kg</small></div>
                            </div>
                            <div class="glass-card p-4 rounded-3xl space-y-1 border-emerald-500/30">
                                <span class="text-[8px] font-black uppercase text-zinc-500">Gordura Corporal</span>
                                <div class="text-xl font-black text-emerald-400 italic">{{ $latest?->bf_percent ?? 0 }}<small class="text-[10px] not-italic ml-0.5">%</small></div>
                            </div>
                        </div>

                        @if(count($evolutionPhotos) > 0)
                        @php($pair = reset($evolutionPhotos))
                        <div class="grid grid-cols-2 gap-2 pt-4">
                            <div class="rounded-2xl overflow-hidden aspect-square relative border border-white/5">
                                <img src="{{ route('secure-files.show', ['type' => 'evolution', 'id' => $pair['first']->id]) }}" class="w-full h-full object-cover grayscale opacity-50" alt="Antes">
                                <div class="absolute bottom-1 left-2 text-[6px] font-black uppercase tracking-widest text-zinc-500">Antes</div>
                            </div>
                            <div class="rounded-2xl overflow-hidden aspect-square relative border border-blue-500/30">
                                <img src="{{ route('secure-files.show', ['type' => 'evolution', 'id' => $pair['last']->id]) }}" class="w-full h-full object-cover" alt="Depois">
                                <div class="absolute bottom-1 right-2 text-[6px] font-black uppercase tracking-widest text-blue-400">Depois</div>
                            </div>
                        </div>
                        @endif

                        <div class="pt-8 flex justify-center">
                            <div class="px-6 py-3 bg-white/5 rounded-full border border-white/10 flex items-center gap-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[8px] font-black text-zinc-500 uppercase">Health Score</span>
                                    <span class="text-xs font-black text-blue-400">{{ $healthScore }}</span>
                                </div>
                                <div class="w-px h-3 bg-white/10"></div>
                                <span class="text-[8px] font-black text-white uppercase tracking-widest">NexShape Arena</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-10 text-center pb-4 opacity-30 text-[7px] font-black uppercase tracking-[0.4em]">NexShape Performance Elite</div>
                </div>

                <!-- Controls -->
                <div class="flex gap-4">
                    <button onclick="downloadCard()" class="flex-1 py-4 bg-emerald-500 text-zinc-950 font-black rounded-2xl hover:bg-emerald-400 transition-all flex items-center justify-center gap-2">
                        <i class="fas fa-download"></i>
                        BAIXAR IMAGEM
                    </button>
                    <button onclick="closeShareModal()" class="w-16 h-16 bg-white/10 text-white rounded-2xl flex items-center justify-center hover:bg-white/20">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Health Score Summary -->
        <div class="glass-card rounded-[2.5rem] p-6 flex items-center justify-between overflow-hidden relative group">
            <div class="absolute -right-4 -top-4 w-32 h-32 bg-brand-primary/10 blur-3xl rounded-full group-hover:bg-brand-primary/20 transition-all duration-700"></div>
            
            <div class="space-y-1 relative z-10">
                <span class="text-[8px] font-black text-zinc-500 uppercase tracking-[0.2em]">Health Score Global</span>
                <div class="flex items-baseline gap-1">
                    <span class="text-4xl font-black text-white italic tracking-tighter">{{ $healthScore }}</span>
                    <span class="text-xs font-bold text-zinc-500">/100</span>
                </div>
            </div>

            <div class="w-24 h-24 relative flex items-center justify-center relative z-10">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/5" />
                    <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" 
                        class="text-blue-500" 
                        stroke-dasharray="{{ 2 * pi() * 40 }}" 
                        stroke-dashoffset="{{ (1 - $healthScore / 100) * (2 * pi() * 40) }}" 
                        stroke-linecap="round" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-heartbeat text-blue-500/50 animate-pulse"></i>
                </div>
            </div>
        </div>

        <!-- Visual Transformation Gallery -->
        @if(count($evolutionPhotos) > 0)
        <div class="space-y-6">
            <div class="flex items-center justify-between px-2">
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-600">Transformação Visual</h2>
                <span class="text-[8px] font-bold text-blue-400 bg-blue-500/10 px-2 py-1 rounded-full uppercase tracking-widest">Antes & Depois</span>
            </div>

            <div class="grid grid-cols-1 gap-6">
                @foreach($evolutionPhotos as $type => $pair)
                <div class="glass-card rounded-[2.5rem] overflow-hidden p-4 space-y-4">
                    <div class="flex items-center justify-between px-2">
                        <span class="text-[9px] font-black uppercase tracking-widest text-zinc-400">Vista {{ ucfirst($type == 'front' ? 'Frontal' : ($type == 'side' ? 'Lateral' : 'Posterior')) }}</span>
                        <div class="flex gap-2">
                            <span class="text-[7px] font-bold text-zinc-500 uppercase">{{ $pair['first']->created_at->format('d/m/y') }}</span>
                            <span class="text-[7px] font-bold text-zinc-400 uppercase">vs</span>
                            <span class="text-[7px] font-bold text-white uppercase">{{ $pair['last']->created_at->format('d/m/y') }}</span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2 relative">
                        <div class="relative rounded-2xl overflow-hidden aspect-[3/4] bg-zinc-900">
                            <img src="{{ route('secure-files.show', ['type' => 'evolution', 'id' => $pair['first']->id]) }}" class="w-full h-full object-cover grayscale opacity-60" alt="Antes">
                            <div class="absolute bottom-2 left-2 px-2 py-1 bg-black/50 backdrop-blur-md rounded-lg text-[8px] font-black uppercase tracking-widest">Antes</div>
                        </div>
                        <div class="relative rounded-2xl overflow-hidden aspect-[3/4] bg-zinc-900 border border-blue-500/30">
                            <img src="{{ route('secure-files.show', ['type' => 'evolution', 'id' => $pair['last']->id]) }}" class="w-full h-full object-cover" alt="Depois">
                            <div class="absolute bottom-2 right-2 px-2 py-1 bg-blue-500/80 backdrop-blur-md rounded-lg text-[8px] font-black uppercase tracking-widest">Depois</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Anatomical Measurements Map -->
        @if($assessments->count() > 0)
        @php($latest = $assessments->first())
        <div class="space-y-6">
            <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-600 px-2">Mapa de Medidas Recentes</h2>
            
            <div class="glass-card rounded-[2.5rem] p-8 flex flex-col md:flex-row items-center gap-10">
                <div class="relative w-48 shrink-0">
                    <img src="{{ asset('images/body/' . ($gender == 'F' ? 'female' : 'male') . '_front.png') }}" class="w-full opacity-20 grayscale brightness-200" alt="Silhouette">
                    
                    <!-- Measurement Pins -->
                    <div class="absolute top-[25%] left-1/2 -translate-x-1/2 w-2 h-2 bg-blue-500 rounded-full shadow-[0_0_10px_rgba(59,130,246,0.8)] animate-pulse"></div> <!-- Peito -->
                    <div class="absolute top-[40%] left-1/2 -translate-x-1/2 w-2 h-2 bg-blue-500 rounded-full shadow-[0_0_10px_rgba(59,130,246,0.8)] animate-pulse"></div> <!-- Cintura -->
                    <div class="absolute top-[55%] left-1/2 -translate-x-1/2 w-2 h-2 bg-blue-500 rounded-full shadow-[0_0_10px_rgba(59,130,246,0.8)] animate-pulse"></div> <!-- Quadril -->
                </div>

                <div class="flex-1 grid grid-cols-2 gap-x-8 gap-y-6">
                    <div class="space-y-1">
                        <span class="text-[7px] font-black text-zinc-500 uppercase tracking-widest">Tórax</span>
                        <div class="text-lg font-black text-white italic tracking-tighter">{{ $latest?->chest ?? '--' }} <small class="text-[9px] font-bold text-zinc-600 not-italic">cm</small></div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[7px] font-black text-zinc-500 uppercase tracking-widest">Cintura</span>
                        <div class="text-lg font-black text-white italic tracking-tighter">{{ $latest?->waist ?? '--' }} <small class="text-[9px] font-bold text-zinc-600 not-italic">cm</small></div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[7px] font-black text-zinc-500 uppercase tracking-widest">Abdômen</span>
                        <div class="text-lg font-black text-white italic tracking-tighter">{{ $latest?->abdomen ?? '--' }} <small class="text-[9px] font-bold text-zinc-600 not-italic">cm</small></div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[7px] font-black text-zinc-500 uppercase tracking-widest">Quadril</span>
                        <div class="text-lg font-black text-white italic tracking-tighter">{{ $latest?->hips ?? '--' }} <small class="text-[9px] font-bold text-zinc-600 not-italic">cm</small></div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[7px] font-black text-zinc-500 uppercase tracking-widest">Bíceps Dir.</span>
                        <div class="text-lg font-black text-white italic tracking-tighter">{{ $latest?->bicep_r ?? '--' }} <small class="text-[9px] font-bold text-zinc-600 not-italic">cm</small></div>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[7px] font-black text-zinc-500 uppercase tracking-widest">Coxa Dir.</span>
                        <div class="text-lg font-black text-white italic tracking-tighter">{{ $latest?->thigh_r ?? '--' }} <small class="text-[9px] font-bold text-zinc-600 not-italic">cm</small></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Chart Section -->
        @if(count($chartData['dates']) > 1)
        <div class="glass-card rounded-[2.5rem] p-6 space-y-4">
            <div class="flex items-center justify-between px-2">
                <h2 class="text-xs font-black uppercase tracking-widest text-zinc-400">Tendência de Peso & BF</h2>
                <div class="flex gap-4">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span class="text-[8px] font-black uppercase text-zinc-500">Peso</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-[8px] font-black uppercase text-zinc-500">BF%</span>
                    </div>
                </div>
            </div>
            <div id="evolutionChart" class="w-full h-64"></div>
        </div>
        @endif

        <!-- List Section -->
        <div class="space-y-6">
            <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-600 px-2">Histórico de Avaliações</h2>
            
            @if($assessments->count() > 0)
                @foreach($assessments as $assessment)
                <div class="glass-card rounded-[2.5rem] overflow-hidden group hover:border-white/20 transition-all duration-500">
                    <div class="bg-white/5 px-6 py-4 flex items-center justify-between border-b border-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-zinc-900 flex items-center justify-center text-zinc-500">
                                <i class="fas fa-calendar-alt text-xs"></i>
                            </div>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">{{ $assessment->assessment_date->format('d \d\e M, Y') }}</span>
                        </div>
                        <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Por {{ $assessment->professional->name ?? 'Pro' }}</span>
                    </div>
                    
                    <div class="p-6 grid grid-cols-2 gap-6">
                        <!-- Peso -->
                        <div class="space-y-1">
                            <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Peso Atual</span>
                            <div class="flex items-center gap-2">
                                <div class="flex items-end gap-1">
                                    <span class="text-2xl font-black text-white">{{ number_format($assessment->weight_kg, 1) }}</span>
                                    <span class="text-[9px] font-bold text-zinc-600 mb-1.5">kg</span>
                                </div>
                                @if($assessment->delta_weight != 0)
                                    <div class="text-[10px] font-black flex items-center gap-0.5 {{ $assessment->delta_weight > 0 ? 'text-red-400' : 'text-emerald-400' }}">
                                        <i class="fas fa-caret-{{ $assessment->delta_weight > 0 ? 'up' : 'down' }}"></i>
                                        {{ abs(number_format($assessment->delta_weight, 1)) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- BF -->
                        <div class="space-y-1 text-right">
                            <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest">Gordura Corporal</span>
                            <div class="flex items-center justify-end gap-2">
                                @if($assessment->delta_bf != 0)
                                    <div class="text-[10px] font-black flex items-center gap-0.5 {{ $assessment->delta_bf > 0 ? 'text-red-400' : 'text-emerald-400' }}">
                                        <i class="fas fa-caret-{{ $assessment->delta_bf > 0 ? 'up' : 'down' }}"></i>
                                        {{ abs(number_format($assessment->delta_bf, 1)) }}%
                                    </div>
                                @endif
                                <div class="flex items-end gap-1">
                                    <span class="text-2xl font-black text-emerald-500">{{ $assessment->bf_percent }}</span>
                                    <span class="text-[9px] font-bold text-zinc-600 mb-1.5">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Medidas Grid -->
                        <div class="col-span-2 grid grid-cols-3 gap-2 py-4 border-y border-white/5">
                            <div class="text-center space-y-1">
                                <span class="block text-[7px] font-black text-zinc-500 uppercase tracking-widest">Cintura</span>
                                <span class="text-xs font-bold text-zinc-200">{{ $assessment->waist ?? '--' }} <small class="text-[8px] text-zinc-600">cm</small></span>
                            </div>
                            <div class="text-center space-y-1 border-x border-white/5">
                                <span class="block text-[7px] font-black text-zinc-500 uppercase tracking-widest">Quadril</span>
                                <span class="text-xs font-bold text-zinc-200">{{ $assessment->hips ?? '--' }} <small class="text-[8px] text-zinc-600">cm</small></span>
                            </div>
                            <div class="text-center space-y-1">
                                <span class="block text-[7px] font-black text-zinc-500 uppercase tracking-widest">Abdômen</span>
                                <span class="text-xs font-bold text-zinc-200">{{ $assessment->abdomen ?? '--' }} <small class="text-[8px] text-zinc-600">cm</small></span>
                            </div>
                        </div>

                        @if($assessment->notes)
                        <div class="col-span-2 space-y-2">
                            <span class="text-[8px] font-black text-zinc-500 uppercase tracking-widest block">Observações do Profissional</span>
                            <div class="relative">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500/30 rounded-full"></div>
                                <p class="text-[10px] text-zinc-400 leading-relaxed italic pl-4">
                                    "{{ $assessment->notes }}"
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <x-patient.empty-state 
                    icon="fas fa-weight" 
                    title="Sem Evolução" 
                    description="Sua jornada de transformação ainda não possui registros. Realize sua primeira avaliação física com o profissional para visualizar seu progresso."
                />
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script>
    function generateShareCard() {
        document.getElementById('shareModal').classList.remove('hidden');
        document.getElementById('shareModal').classList.add('flex');
    }

    function closeShareModal() {
        document.getElementById('shareModal').classList.add('hidden');
        document.getElementById('shareModal').classList.remove('flex');
    }

    function downloadCard() {
        const btn = event.currentTarget;
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> GERANDO...';
        btn.disabled = true;

        const card = document.getElementById('captureCard');
        
        html2canvas(card, {
            useCORS: true,
            scale: 3, // High quality
            backgroundColor: '#06080c'
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = `evolucao-{{ \Illuminate\Support\Str::slug($patient->name) }}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
            
            btn.innerHTML = originalContent;
            btn.disabled = false;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        @if(count($chartData['dates']) > 1)
        const options = {
            series: [{
                name: 'Peso (kg)',
                data: {!! json_encode($chartData['weight']) !!}
            }, {
                name: 'BF (%)',
                data: {!! json_encode($chartData['bf']) !!}
            }],
            chart: {
                height: 250,
                type: 'area',
                toolbar: { show: false },
                zoom: { enabled: false },
                background: 'transparent',
                foreColor: '#52525b'
            },
            colors: ['#3b82f6', '#10b981'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: {!! json_encode($chartData['dates']) !!},
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        fontSize: '9px',
                        fontWeight: 900
                    }
                }
            },
            yaxis: {
                show: false
            },
            grid: {
                show: true,
                borderColor: 'rgba(255, 255, 255, 0.03)',
                xaxis: { lines: { show: true } },
                yaxis: { lines: { show: false } }
            },
            legend: { show: false },
            tooltip: {
                theme: 'dark',
                x: { show: true },
                y: {
                    formatter: function(val) { return val.toFixed(1) }
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#evolutionChart"), options);
        chart.render();
        @endif
    });
</script>
@endpush
