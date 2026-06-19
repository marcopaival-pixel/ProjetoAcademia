@extends('layouts.professional')

@section('title', 'Gestão & Churn — NexShape Pro')

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up max-w-[1400px] mx-auto px-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-4 border-b border-zinc-900">
        <div class="flex items-center gap-6">
            <a href="{{ route('professional.reports.index') }}" class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-600 hover:text-orange-500 hover:border-orange-500/30 transition-all shadow-xl">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="w-14 h-14 rounded-2xl bg-orange-600 text-white flex items-center justify-center shadow-lg shadow-orange-600/20">
                 <i class="fas fa-tasks text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">Gestão & <span class="text-orange-500">Churn</span></h1>
                <p class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.3em] mt-1">
                    Prevenção de Evasão • Monitoramento de Inatividade
                </p>
            </div>
        </div>

        <a href="{{ route('professional.reports.export', ['type' => 'management_reports']) }}" 
           class="px-5 py-2.5 rounded-2xl bg-zinc-950 border border-zinc-800 text-[10px] font-black text-zinc-400 uppercase tracking-widest hover:text-orange-500 hover:border-orange-500/30 transition-all shadow-xl flex items-center gap-3 group">
            <i class="fas fa-file-csv text-sm group-hover:scale-110 transition-transform"></i>
            Exportar CSV
        </a>
    </div>

    <!-- Alert Banner -->
    @if($data['total_at_risk'] > 0)
    <div class="bg-orange-500/10 border border-orange-500/20 p-8 rounded-[3rem] flex flex-col md:flex-row items-center gap-8 relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-orange-500/10 blur-[60px] rounded-full"></div>
        <div class="w-20 h-20 rounded-[2rem] bg-orange-600 flex items-center justify-center text-white shadow-2xl shadow-orange-600/40 shrink-0">
            <i class="fas fa-exclamation-triangle text-3xl"></i>
        </div>
        <div>
            <h4 class="text-2xl font-black text-white italic uppercase tracking-tighter mb-2">Atenção Necessária</h4>
            <p class="text-orange-200/60 font-medium text-lg leading-relaxed max-w-3xl">
                Identificamos <span class="text-white font-black">{{ $data['total_at_risk'] }} {{ mb_strtolower($patientsLabel) }}</span> com alto risco de evasão. Eles não registram atividades há mais de 15 dias. Uma abordagem proativa agora pode evitar o cancelamento.
            </p>
        </div>
    </div>
    @endif

    <!-- Churn Risk List -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-[3.5rem] overflow-hidden shadow-2xl">
        <div class="p-10 border-b border-zinc-800 flex items-center justify-between">
            <h3 class="text-2xl font-black text-white italic uppercase tracking-tighter">{{ $patientsLabel }} em <span class="text-orange-500">Risco</span></h3>
            <span class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">Inativos > 15 dias</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-zinc-950/50">
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest">{{ $patientLabel }}</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest">Última Atividade</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest text-center">Nível de Risco</th>
                        <th class="py-6 px-10 text-[10px] font-black text-zinc-700 uppercase tracking-widest text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-800">
                    @forelse($data['churn_risk'] as $student)
                    <tr class="hover:bg-orange-500/[0.02] transition-all group">
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
                        <td class="py-6 px-10">
                            <span class="text-sm font-black text-zinc-400 italic">{{ $student['last_activity'] }}</span>
                        </td>
                        <td class="py-6 px-10 text-center">
                            <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-500 text-[9px] font-black uppercase tracking-widest border border-rose-500/20">
                                {{ $student['risk_level'] }}
                            </span>
                        </td>
                        <td class="py-6 px-10 text-right">
                            <a href="{{ route('professional.patients.show', $student['id']) }}" class="text-[10px] font-black text-orange-500 uppercase tracking-widest hover:text-white transition-colors">
                                Recuperar {{ $patientLabel }}
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-20 text-center text-zinc-600 italic">Parabéns! Nenhum {{ mb_strtolower($patientLabel) }} com risco de churn identificado no momento.</td>
                    </tr>
                    @endforelse
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



