@extends('layouts.professional')

@section('title', 'Dashboard KPIs — NexShape Pro')

@section('content')
<div class="py-10 space-y-12 animate-fade-in-up max-w-[1400px] mx-auto px-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pb-4 border-b border-zinc-900">
        <div class="flex items-center gap-6">
            <a href="{{ route('professional.reports.index') }}" class="w-12 h-12 rounded-2xl bg-zinc-950 border border-zinc-800 flex items-center justify-center text-zinc-600 hover:text-rose-500 hover:border-rose-500/30 transition-all shadow-xl">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="w-14 h-14 rounded-2xl bg-rose-600 text-white flex items-center justify-center shadow-lg shadow-rose-600/20">
                 <i class="fas fa-tachometer-alt text-2xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase italic">Dashboard <span class="text-rose-500">KPIs</span></h1>
                <p class="text-zinc-600 text-[10px] font-black uppercase tracking-[0.3em] mt-1">
                    Indicadores de Performance e Saúde do Negócio
                </p>
            </div>
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        
        <!-- Retention Rate -->
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[3rem] shadow-2xl relative overflow-hidden flex flex-col items-center text-center">
            <div class="absolute inset-0 bg-gradient-to-b from-emerald-500/5 to-transparent"></div>
            <div class="w-20 h-20 rounded-full border-4 border-zinc-800 border-t-emerald-500 flex items-center justify-center mb-6 relative z-10">
                <span class="text-2xl font-black text-white italic tabular-nums">{{ $data['retention_rate'] }}%</span>
            </div>
            <h4 class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2 relative z-10">Taxa de Retenção</h4>
            <p class="text-xs text-zinc-500 font-medium relative z-10">Alunos ativos há mais de 30 dias.</p>
        </div>

        <!-- Avg Sessions -->
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[3rem] shadow-2xl relative overflow-hidden flex flex-col items-center text-center">
             <div class="absolute inset-0 bg-gradient-to-b from-blue-500/5 to-transparent"></div>
             <div class="w-20 h-20 rounded-full border-4 border-zinc-800 border-t-blue-500 flex items-center justify-center mb-6 relative z-10">
                <span class="text-2xl font-black text-white italic tabular-nums">{{ $data['avg_sessions_per_student'] }}</span>
            </div>
            <h4 class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2 relative z-10">Média de Treinos</h4>
            <p class="text-xs text-zinc-500 font-medium relative z-10">Sessões por aluno nos últimos 30 dias.</p>
        </div>

        <!-- Plans % -->
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[3rem] shadow-2xl relative overflow-hidden flex flex-col items-center text-center">
            <div class="absolute inset-0 bg-gradient-to-b from-purple-500/5 to-transparent"></div>
            <div class="w-20 h-20 rounded-full border-4 border-zinc-800 border-t-purple-500 flex items-center justify-center mb-6 relative z-10">
                <span class="text-2xl font-black text-white italic tabular-nums">{{ $data['active_plans_percentage'] }}%</span>
            </div>
            <h4 class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2 relative z-10">Taxa de Conversão</h4>
            <p class="text-xs text-zinc-500 font-medium relative z-10">Alunos com planos pagos ativos.</p>
        </div>

        <!-- Growth Score -->
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[3rem] shadow-2xl relative overflow-hidden flex flex-col items-center text-center">
            <div class="absolute inset-0 bg-gradient-to-b from-amber-500/5 to-transparent"></div>
            <div class="w-20 h-20 rounded-full border-4 border-zinc-800 border-t-amber-500 flex items-center justify-center mb-6 relative z-10">
                <span class="text-2xl font-black text-white italic tabular-nums">{{ $data['growth_score'] }}</span>
            </div>
            <h4 class="text-[10px] font-black text-zinc-600 uppercase tracking-widest mb-2 relative z-10">Score de Crescimento</h4>
            <p class="text-xs text-zinc-500 font-medium relative z-10">Pontuação de escala da clínica.</p>
        </div>

    </div>

    <!-- Charts Section (Placeholder for complex charts) -->
    <div class="bg-zinc-900 border border-zinc-800 rounded-[4rem] p-12 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-rose-500/5 blur-[120px] rounded-full"></div>
        <div class="flex flex-col md:flex-row items-center gap-12 relative z-10">
            <div class="flex-grow">
                <h3 class="text-3xl font-black text-white italic uppercase tracking-tighter mb-6">Diagnóstico de <span class="text-rose-500">Saúde Financeira</span></h3>
                <p class="text-zinc-500 text-lg font-medium leading-relaxed max-w-2xl">
                    Baseado nos seus KPIs atuais, sua taxa de conversão está em <span class="text-white font-black">{{ $data['active_plans_percentage'] }}%</span>. 
                    Isso indica que uma parte da sua base ainda utiliza recursos gratuitos. 
                    Recomendamos criar campanhas direcionadas para converter os {{ 100 - $data['active_plans_percentage'] }}% restantes em planos Premium.
                </p>
                
                <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-6 bg-zinc-950 rounded-3xl border border-white/5">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-500 flex items-center justify-center">
                                <i class="fas fa-check text-xs"></i>
                            </div>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">Ponto Forte</span>
                        </div>
                        <p class="text-xs text-zinc-500 font-medium">Sua taxa de retenção de {{ $data['retention_rate'] }}% está acima da média do mercado (75%).</p>
                    </div>
                    <div class="p-6 bg-zinc-950 rounded-3xl border border-white/5">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-500 flex items-center justify-center">
                                <i class="fas fa-exclamation text-xs"></i>
                            </div>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">Oportunidade</span>
                        </div>
                        <p class="text-xs text-zinc-500 font-medium">A média de {{ $data['avg_sessions_per_student'] }} treinos/mês pode ser otimizada com lembretes automáticos.</p>
                    </div>
                </div>
            </div>
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
