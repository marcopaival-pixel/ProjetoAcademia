@extends('layouts.admin')

@section('title', 'IA de Retenção — NexShape')

@section('content')
<div class="animate-fade-in space-y-6">
    <!-- Saudação NexShape Pattern -->
    <div class="mb-10 animate-fade-in flex flex-wrap items-end justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-3">
                <div class="px-2.5 py-1 rounded bg-indigo-600/10 border border-indigo-500/20 flex items-center gap-2">
                    <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></span>
                    <span class="text-indigo-400 text-[9px] font-black uppercase tracking-widest">NexBot AI Activo</span>
                </div>
                <span class="text-zinc-600 text-[10px] font-bold tracking-tight">• Última análise: {{ $metrics['last_analysis_time'] }}</span>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter italic uppercase">
                Inteligência <span class="text-indigo-500">Preditiva</span>
            </h1>
            <p class="text-zinc-500 text-sm font-medium mt-2 max-w-2xl">
                O motor de retenção analisa padrões comportamentais, presença em treinos e histórico financeiro para identificar evasões antes que aconteçam.
            </p>
        </div>
    </div>

    <!-- Hierarquia de KPIs (Principais) -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
        <div class="glass-card p-6 rounded-3xl relative overflow-hidden group border border-white/5 shadow-xl">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-600/10 rounded-full blur-2xl group-hover:bg-rose-600/20 transition-all"></div>
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest block mb-4">Pacientes em Risco Alto</span>
            <div class="text-4xl font-black text-white tracking-tight italic uppercase">{{ $metrics['kpi_risk_patients'] }}</div>
            <div class="mt-3 text-[10px] text-rose-500 font-bold tracking-widest uppercase flex items-center gap-1">
                <i data-lucide="alert-triangle" class="w-3 h-3"></i> Ação Imediata
            </div>
        </div>

        <div class="glass-card p-6 rounded-3xl relative overflow-hidden group border border-white/5 shadow-xl">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-600/10 rounded-full blur-2xl group-hover:bg-emerald-600/20 transition-all"></div>
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest block mb-4">Média de Engajamento</span>
            <div class="text-4xl font-black text-white tracking-tight italic uppercase">{{ $metrics['kpi_average_engagement'] }}%</div>
            <div class="mt-3 text-[10px] text-emerald-500 font-bold tracking-widest uppercase flex items-center gap-1">
                <i data-lucide="activity" class="w-3 h-3"></i> Saúde do App
            </div>
        </div>

        <div class="glass-card p-6 rounded-3xl relative overflow-hidden group border border-white/5 shadow-xl">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-600/10 rounded-full blur-2xl group-hover:bg-indigo-600/20 transition-all"></div>
            <span class="text-[10px] text-zinc-500 font-black uppercase tracking-widest block mb-4">Recuperados este Mês</span>
            <div class="text-4xl font-black text-white tracking-tight italic uppercase">{{ $metrics['kpi_recovered_this_month'] }}</div>
            <div class="mt-3 text-[10px] text-indigo-400 font-bold tracking-widest uppercase flex items-center gap-1">
                <i data-lucide="shield-check" class="w-3 h-3"></i> Sucesso de Retenção
            </div>
        </div>

        <div class="glass-card p-6 rounded-3xl relative overflow-hidden group border border-white/5 shadow-xl bg-gradient-to-br from-indigo-900/20 to-transparent">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] text-indigo-400 font-black uppercase tracking-widest">Base Ativa Total</span>
                <i data-lucide="users" class="w-5 h-5 text-indigo-500"></i>
            </div>
            <div class="text-3xl font-black text-white tracking-tight italic uppercase">{{ $metrics['kpi_total_patients'] }}</div>
            <div class="mt-3 text-[9px] text-zinc-400 font-bold tracking-widest uppercase">
                Monitorados pela IA
            </div>
        </div>
    </div>

    <!-- Lista de Pacientes em Risco -->
    <div class="glass-card rounded-[2.5rem] p-8 border border-white/5 shadow-2xl relative overflow-hidden mt-8">
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-indigo-600/5 rounded-full blur-3xl"></div>
        
        <div class="flex items-center justify-between mb-8 relative z-10">
            <div>
                <h3 class="text-xl font-black text-white tracking-tighter uppercase italic">Radar de Evasão</h3>
                <p class="text-[10px] text-zinc-500 font-black uppercase tracking-[0.2em] mt-1">Identificação de anomalias de engajamento</p>
            </div>
            <button class="px-5 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white border border-white/10 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-lg flex items-center gap-2">
                <i data-lucide="filter" class="w-3 h-3"></i> Filtrar
            </button>
        </div>

        <div class="overflow-x-auto relative z-10">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5">
                        <th class="py-4 px-4 text-[9px] font-black text-zinc-600 uppercase tracking-widest">Paciente</th>
                        <th class="py-4 px-4 text-[9px] font-black text-zinc-600 uppercase tracking-widest">Risk Score</th>
                        <th class="py-4 px-4 text-[9px] font-black text-zinc-600 uppercase tracking-widest">Diagnóstico da IA</th>
                        <th class="py-4 px-4 text-[9px] font-black text-zinc-600 uppercase tracking-widest text-right">Ação Recomendada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($metrics['patients_at_risk'] as $patient)
                    <tr class="group hover:bg-white/[0.02] transition-colors">
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <img src="{{ $patient['avatar'] }}" alt="{{ $patient['name'] }}" class="w-10 h-10 rounded-xl shadow-lg border border-white/10 group-hover:scale-105 transition-transform">
                                <div>
                                    <p class="text-sm font-black text-white truncate">{{ $patient['name'] }}</p>
                                    <p class="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mt-0.5">ID: #{{ $patient['id'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-full max-w-[100px] h-1.5 bg-zinc-900 rounded-full overflow-hidden border border-white/5">
                                    @php
                                        $color = $patient['risk_score'] >= 70 ? 'bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.5)]' : 'bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.5)]';
                                    @endphp
                                    <div class="h-full {{ $color }} rounded-full" style="width: {{ $patient['risk_score'] }}%"></div>
                                </div>
                                <span class="text-xs font-black {{ $patient['risk_score'] >= 70 ? 'text-rose-500' : 'text-amber-500' }}">{{ $patient['risk_score'] }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="space-y-1">
                                @foreach($patient['reasons'] as $reason)
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1 h-1 rounded-full {{ $patient['risk_score'] >= 70 ? 'bg-rose-500' : 'bg-amber-500' }}"></span>
                                    <span class="text-[10px] text-zinc-400 font-medium">{{ $reason }}</span>
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex flex-col items-end gap-2">
                                <p class="text-[10px] text-indigo-400 font-bold italic max-w-xs text-right">{{ $patient['suggested_action'] }}</p>
                                <button onclick="recoverPatient({{ $patient['id'] }}, 'WhatsApp')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all shadow-lg hover:shadow-indigo-500/20 flex items-center gap-2">
                                    <i data-lucide="message-circle" class="w-3 h-3"></i> Executar Ação
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center">
                            <div class="w-16 h-16 bg-zinc-900/50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white/5">
                                <i data-lucide="shield-check" class="w-8 h-8 text-emerald-500"></i>
                            </div>
                            <p class="text-sm font-black text-white uppercase tracking-widest mb-1">Nenhum Risco Iminente</p>
                            <p class="text-[10px] text-zinc-500 font-bold tracking-widest uppercase">A base de alunos está com engajamento saudável.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function recoverPatient(patientId, actionType) {
        Swal.fire({
            title: 'Recuperar Paciente',
            text: `Deseja acionar o fluxo de ${actionType} para este paciente? A IA formulará a mensagem.`,
            icon: 'question',
            showCancelButton: true,
            background: '#09090b',
            color: '#fff',
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#3f3f46',
            confirmButtonText: 'Sim, Executar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-3xl border border-white/10',
                title: 'font-black uppercase tracking-tighter italic text-xl',
                htmlContainer: 'text-sm text-zinc-400 font-medium'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Chama API interna de recuperação
                fetch('{{ route("admin.ai-intelligence.recover") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ patient_id: patientId, action_type: actionType })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: data.message,
                            icon: 'success',
                            background: '#09090b',
                            color: '#fff',
                            confirmButtonColor: '#10b981',
                            customClass: {
                                popup: 'rounded-3xl border border-emerald-500/20'
                            }
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
@endsection
