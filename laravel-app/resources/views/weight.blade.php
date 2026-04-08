@extends('layouts.app')

@section('title', 'Bio-Metric Tracking — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-fade-in max-w-[1400px] mx-auto px-6">
    <!-- Header Strategy -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div class="space-y-3">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[10px] font-black uppercase tracking-widest rounded-lg border border-blue-500/20">Controle Ponderal</span>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter italic">Bio-Stats</h1>
            <p class="text-zinc-500 font-medium max-w-xl">Mantenha o registro diário para análise de tendências metabólicas e ajuste fino de suas metas.</p>
        </div>
        
        <div class="flex gap-4 p-2 bg-zinc-900/60 rounded-[2rem] border border-white/5 backdrop-blur-xl">
             <div class="w-16 h-16 rounded-[1.5rem] bg-zinc-950 flex flex-col items-center justify-center">
                <span class="text-blue-500 font-black text-xl">H2O</span>
                <span class="text-[8px] text-zinc-600 font-black uppercase">Sync</span>
             </div>
             <div class="w-16 h-16 rounded-[1.5rem] bg-blue-600/10 flex flex-col items-center justify-center text-blue-500">
                <i class="fas fa-weight text-2xl"></i>
             </div>
        </div>
    </div>

    @if (!empty($notice))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold animate-fade-in flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            {{ $notice }}
        </div>
    @endif
    
    @if (!empty($error))
        <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-xs font-bold animate-fade-in flex items-center gap-3">
            <i class="fas fa-exclamation-triangle"></i>
            {{ $error }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
        
        <!-- Evolution Chart (Left 8) -->
        <div class="lg:col-span-12 xl:col-span-8 space-y-10">
            @if ($weightChartHtml !== '')
                <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 p-10 rounded-[3.5rem] shadow-2xl overflow-hidden group">
                    <header class="flex items-center justify-between mb-12">
                        <div>
                            <h3 class="text-2xl font-black text-white tracking-tight">Evolução Cronológica</h3>
                            <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-1">Série histórica de pesagens</p>
                        </div>
                        <div class="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center text-zinc-600">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </header>
                    <div class="relative min-h-[300px] w-full group-hover:scale-[1.01] transition-transform duration-700">
                        {!! $weightChartHtml !!}
                    </div>
                </div>
            @else
                <div class="bg-zinc-900/20 border-2 border-dashed border-white/5 p-20 rounded-[3.5rem] text-center filter grayscale opacity-60">
                    <i class="fas fa-chart-area text-5xl text-zinc-700 mb-6"></i>
                    <h3 class="text-white font-black text-xl">Gráfico Indisponível</h3>
                    <p class="text-zinc-600 text-sm mt-2">Registre pelo menos dois dias para visualizar a linha de evolução.</p>
                </div>
            @endif

            <!-- Table Logs -->
            <div class="bg-zinc-900/40 border border-white/5 rounded-[3rem] overflow-hidden">
                <div class="p-8 border-b border-white/5 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-black text-white leading-none">Últimos Registros</h3>
                        <p class="text-[10px] text-zinc-500 font-black uppercase tracking-widest mt-2">Log de amostragem bio-métrica</p>
                    </div>
                    <i class="fas fa-history text-zinc-700"></i>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.2em] border-b border-white/5">
                                <th class="px-10 py-6">Data de Amostra</th>
                                <th class="px-10 py-6 text-right">Massa Corporal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach ($rows as $r)
                                <tr class="hover:bg-white/[0.02] transition-colors">
                                    <td class="px-10 py-6 font-bold text-sm text-zinc-300">{{ \Carbon\Carbon::parse($r->weighed_at)->translatedFormat('d/m/Y') }}</td>
                                    <td class="px-10 py-6 text-right">
                                        <span class="text-white font-black text-lg">{{ number_format((float)$r->weight_kg, 1, ',', '.') }}</span>
                                        <span class="text-zinc-600 text-[10px] font-black uppercase ml-1">kg</span>
                                    </td>
                                </tr>
                            @endforeach
                            @if($rows->isEmpty())
                                <tr><td colspan="2" class="px-10 py-12 text-center text-zinc-600 italic">Nenhum dado registrado ainda.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form Module (Right 4) -->
        <div class="lg:col-span-12 xl:col-span-4 space-y-8 sticky top-32">
            <div class="bg-zinc-900/60 backdrop-blur-3xl border border-white/10 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-600/10 rounded-full blur-3xl"></div>
                
                <header class="mb-12 relative z-10">
                    <h3 class="text-2xl font-black text-white">Nova Pesagem</h3>
                    <p class="text-zinc-500 text-xs font-medium mt-1">Recomendado: Em jejum, logo ao acordar.</p>
                </header>

                <form method="post" action="{{ route('weight') }}" class="space-y-8 relative z-10">
                    @csrf
                    <div class="space-y-6">
                        <div class="space-y-3">
                            <label class="block text-[10px] text-zinc-600 font-bold uppercase tracking-[0.2em]">Data da Medição</label>
                            <input type="date" name="weighed_at" value="{{ old('weighed_at', $today) }}" class="w-full bg-zinc-950 border border-white/10 p-4 rounded-2xl text-white font-bold outline-none focus:ring-2 focus:ring-blue-600 transition-all">
                        </div>

                        <div class="space-y-3">
                            <label class="block text-[10px] text-zinc-600 font-bold uppercase tracking-[0.2em]">Peso Atual (kg)</label>
                            <div class="relative">
                                <input type="number" name="weight_kg" step="0.1" value="{{ old('weight_kg') }}" placeholder="00,0" class="w-full bg-zinc-950 border border-white/10 p-6 rounded-[1.5rem] text-3xl font-black text-white outline-none focus:ring-4 focus:ring-blue-600/20 transition-all text-center placeholder:text-zinc-800 tabular-nums">
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 text-zinc-600 font-black text-sm">KG</div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-5 bg-white text-zinc-900 font-black rounded-[1.5rem] hover:bg-blue-400 hover:text-white transition-all active:scale-[0.98] shadow-2xl">
                        Registrar Amostra
                    </button>
                    
                    <p class="text-[9px] text-zinc-600 text-center font-bold uppercase tracking-widest leading-relaxed">Nota: Se já houver um registro para hoje, ele será atualizado.</p>
                </form>
            </div>

            <!-- Profile Info Tip -->
            @php
                $user = auth()->user();
                $bmi = null;
                if ($user->profile && $user->profile->height_cm && $rows->first()) {
                    $heightM = $user->profile->height_cm / 100;
                    $bmi = $rows->first()->weight_kg / ($heightM * $heightM);
                }
            @endphp
            @if($bmi)
            <div class="bg-zinc-900/40 border border-white/5 p-8 rounded-[2.5rem] flex items-center justify-between">
                <div>
                    <h4 class="text-[10px] text-zinc-500 font-black uppercase tracking-widest">IMC Estimado</h4>
                    <p class="text-2xl font-black text-white mt-1">{{ number_format($bmi, 1) }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20">
                    <i class="fas fa-info-circle"></i>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    body { background-color: #0b0e14; }
    /* Estilização para o SVG do gráfico gerado pelo backend */
    svg { max-width: 100% !important; height: auto !important; }
    polyline { stroke-width: 3 !important; }
    circle { r: 5 !important; stroke-width: 3 !important; }
</style>
@endsection
