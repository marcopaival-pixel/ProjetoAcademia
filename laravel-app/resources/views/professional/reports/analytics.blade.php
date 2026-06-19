@extends('layouts.professional')

@section('title', 'Analytics Pro — NexShape')

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up max-w-[1400px] mx-auto px-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-4 border-b border-zinc-900">
        <div class="flex items-center gap-6">
            <a href="{{ route('professional.reports.index') }}" class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-600 hover:text-emerald-500 hover:border-emerald-500/30 transition-all shadow-xl">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="w-14 h-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-lg shadow-emerald-600/20">
                 <i class="fas fa-chart-bar text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">Analytics <span class="text-emerald-500">Pro</span></h1>
                <p class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.3em] mt-1">
                    Performance Coletiva • {{ $data['total_students'] }} {{ $patientsLabel }} Monitorados
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('professional.reports.export', ['type' => 'complete_analytics', 'range' => $range, 'format' => 'csv']) }}" 
               class="px-5 py-2.5 rounded-2xl bg-zinc-950 border border-zinc-800 text-[10px] font-black text-zinc-400 uppercase tracking-widest hover:text-emerald-500 hover:border-emerald-500/30 transition-all shadow-xl flex items-center gap-3 group">
                <i class="fas fa-file-csv text-sm group-hover:scale-110 transition-transform"></i>
                CSV
            </a>
            <a href="{{ route('professional.reports.export', ['type' => 'complete_analytics', 'range' => $range, 'format' => 'pdf']) }}" 
               class="px-5 py-2.5 rounded-2xl bg-zinc-950 border border-zinc-800 text-[10px] font-black text-zinc-400 uppercase tracking-widest hover:text-emerald-500 hover:border-emerald-500/30 transition-all shadow-xl flex items-center gap-3 group">
                <i class="fas fa-file-pdf text-sm group-hover:scale-110 transition-transform"></i>
                PDF
            </a>
            <div class="flex bg-zinc-950 p-1.5 rounded-2xl border border-zinc-800 shadow-inner">
                @foreach([7 => '7D', 14 => '14D', 30 => '30D', 90 => '90D'] as $val => $label)
                    <a href="?range={{ $val }}" 
                       class="px-5 py-2 rounded-xl text-[10px] font-black transition-all uppercase tracking-widest {{ $range == $val ? 'bg-emerald-500 text-zinc-950 shadow-xl' : 'text-zinc-600 hover:text-white' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 blur-[50px] rounded-full"></div>
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em] mb-4 block">Aderência Nutricional</span>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-black text-white italic tracking-tighter tabular-nums">{{ $data['avg_adherence_food'] }}%</span>
            </div>
            <div class="w-full bg-zinc-950 h-1.5 rounded-full mt-6 overflow-hidden border border-zinc-800">
                <div class="h-full bg-emerald-500" style="width: {{ $data['avg_adherence_food'] }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 blur-[50px] rounded-full"></div>
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em] mb-4 block">Assiduidade Treino</span>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-black text-white italic tracking-tighter tabular-nums">{{ $data['avg_adherence_training'] }}%</span>
            </div>
            <div class="w-full bg-zinc-950 h-1.5 rounded-full mt-6 overflow-hidden border border-zinc-800">
                <div class="h-full bg-emerald-500" style="width: {{ $data['avg_adherence_training'] }}%"></div>
            </div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 blur-[50px] rounded-full"></div>
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em] mb-4 block">Total de Treinos</span>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-black text-white italic tracking-tighter tabular-nums">{{ $data['total_workouts'] }}</span>
            </div>
            <p class="text-[9px] font-black text-zinc-700 mt-4 uppercase tracking-widest">Sessões registradas no período</p>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 blur-[50px] rounded-full"></div>
            <span class="text-[9px] text-zinc-600 font-black uppercase tracking-[0.2em] mb-4 block">{{ $patientsLabel }} Ativos</span>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-black text-emerald-500 italic tracking-tighter tabular-nums">{{ $data['active_students'] }}</span>
            </div>
            <p class="text-[9px] font-black text-zinc-700 mt-4 uppercase tracking-widest">{{ $patientsLabel }} com registros ativos</p>
        </div>
    </div>

    <!-- Detailed List -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-[3.5rem] overflow-hidden shadow-2xl">
        <div class="p-10 border-b border-zinc-800 flex items-center justify-between">
            <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">Ranking de <span class="text-emerald-500">Engajamento</span></h3>
            <span class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">Ordenado por Assiduidade</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-zinc-950/50">
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest">{{ $patientLabel }}</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest text-center">Treinos</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest text-center">Dias Nutri</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest">Aderência GERAL</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @foreach($data['students_data'] as $student)
                    <tr class="hover:bg-emerald-500/[0.02] transition-all group">
                        <td class="py-6 px-10">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-700 overflow-hidden shadow-inner">
                                    @if($student['avatar'])
                                        <img src="{{ asset('storage/' . $student['avatar']) }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fas fa-user text-xs"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-black text-white">{{ $student['name'] }}</div>
                                    <div class="text-[9px] text-zinc-600 font-bold uppercase tracking-tighter">{{ $student['email'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-10 text-center">
                            <span class="text-lg font-black text-white italic tabular-nums">{{ $student['workouts'] }}</span>
                        </td>
                        <td class="py-6 px-10 text-center">
                            <span class="text-lg font-black text-zinc-400 italic tabular-nums">{{ $student['food_days'] }}</span>
                        </td>
                        <td class="py-6 px-10">
                            <div class="flex items-center gap-6">
                                <div class="flex-grow bg-zinc-950 h-1.5 rounded-full overflow-hidden border border-zinc-800 w-32">
                                    <div class="h-full bg-emerald-500" style="width: {{ $student['adherence_training'] }}%"></div>
                                </div>
                                <span class="text-xs font-black {{ $student['adherence_training'] >= 70 ? 'text-emerald-500' : 'text-amber-500' }} italic tabular-nums">
                                    {{ $student['adherence_training'] }}%
                                </span>
                            </div>
                        </td>
                        <td class="py-6 px-10 text-right">
                            <a href="{{ route('professional.patients.show', $student['id']) }}" class="text-[10px] font-black text-emerald-500 uppercase tracking-widest hover:text-white transition-colors">
                                Perfil Completo
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection



