@extends('layouts.app')

@section('title', 'AI Orchestrator Dashboard — NexShape Admin')

@section('content')
<div class="px-6 py-10 mx-auto max-w-7xl animate-fade-in">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">Infraestrutura IA</span>
                <span class="text-zinc-700">•</span>
                <span class="text-zinc-500 text-xs font-black italic uppercase tracking-tighter">Production Grade</span>
            </div>
            <h1 class="text-5xl font-black text-white tracking-tighter italic uppercase">
                ORCHESTRATOR <span class="text-emerald-500">INSIGHTS</span>
            </h1>
        </div>

        <div class="flex items-center gap-4">
            <form action="{{ route('admin.ai.orchestrator.dashboard') }}" method="GET" class="flex items-center gap-2">
                <select name="days" onchange="this.form.submit()" class="bg-zinc-900 border border-zinc-800 text-zinc-400 text-[10px] font-black uppercase tracking-widest px-4 py-2.5 rounded-xl outline-none focus:border-emerald-500/50 transition-all">
                    <option value="7" {{ $days == 7 ? 'selected' : '' }}>Últimos 7 dias</option>
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>Últimos 30 dias</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>Últimos 90 dias</option>
                </select>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/5 blur-3xl rounded-full group-hover:bg-emerald-500/10 transition-all"></div>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-4">Custo Acumulado (USD)</p>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-white italic">$</span>
                <span class="text-5xl font-black text-white tracking-tighter italic">{{ number_format($metrics['total_cost'], 2) }}</span>
            </div>
            <div class="mt-4 flex items-center gap-2">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[9px] text-emerald-500 font-black uppercase tracking-widest">Processamento Ativo</span>
            </div>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-500/5 blur-3xl rounded-full group-hover:bg-blue-500/10 transition-all"></div>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-4">Volume de Requisições</p>
            <span class="text-5xl font-black text-white tracking-tighter italic uppercase">{{ number_format($metrics['total_requests']) }}</span>
            <p class="mt-4 text-[9px] text-zinc-600 font-black uppercase tracking-widest">Ciclos de IA completados</p>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-500/5 blur-3xl rounded-full group-hover:bg-amber-500/10 transition-all"></div>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-4">Latência Média</p>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-black text-white tracking-tighter italic uppercase">{{ number_format($metrics['avg_response_time']) }}</span>
                <span class="text-xl font-black text-zinc-500 italic">ms</span>
            </div>
            <p class="mt-4 text-[9px] text-zinc-600 font-black uppercase tracking-widest">Tempo de resposta OpenAI</p>
        </div>

        <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-rose-500/5 blur-3xl rounded-full group-hover:bg-rose-500/10 transition-all"></div>
            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-4">Taxa de Erro</p>
            <div class="flex items-baseline gap-2">
                <span class="text-5xl font-black {{ $metrics['error_rate'] > 5 ? 'text-rose-500' : 'text-white' }} tracking-tighter italic uppercase">{{ number_format($metrics['error_rate'], 1) }}</span>
                <span class="text-xl font-black text-zinc-500 italic">%</span>
            </div>
            <p class="mt-4 text-[9px] text-zinc-600 font-black uppercase tracking-widest">Respostas com falha/timeout</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-12">
        <!-- Distribution by Agent -->
        <div class="bg-zinc-950 border border-zinc-800 rounded-[3rem] p-10 shadow-3xl">
            <div class="flex items-center gap-3 mb-8 pb-4 border-b border-zinc-900">
                <i data-lucide="users" class="w-5 h-5 text-emerald-500"></i>
                <h2 class="text-xl font-black text-white uppercase italic tracking-tighter">Top Agentes <span class="text-zinc-600">(Por Custo)</span></h2>
            </div>
            
            <div class="space-y-6">
                @foreach($byAgent as $agent)
                <div class="group">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-xs font-black text-zinc-400 uppercase tracking-widest">{{ $agent->agent_name }}</span>
                        <div class="text-right">
                            <span class="text-xs font-black text-white italic">${{ number_format($agent->total_cost, 4) }}</span>
                            <p class="text-[8px] text-zinc-600 font-black uppercase tracking-widest">{{ $agent->count }} requisições</p>
                        </div>
                    </div>
                    <div class="h-2 w-full bg-zinc-900 rounded-full overflow-hidden">
                        @php $width = $metrics['total_cost'] > 0 ? ($agent->total_cost / $metrics['total_cost']) * 100 : 0; @endphp
                        <div class="h-full bg-emerald-500 group-hover:bg-emerald-400 transition-all rounded-full" style="width: {{ $width }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Distribution by Model -->
        <div class="bg-zinc-950 border border-zinc-800 rounded-[3rem] p-10 shadow-3xl">
            <div class="flex items-center gap-3 mb-8 pb-4 border-b border-zinc-900">
                <i data-lucide="cpu" class="w-5 h-5 text-blue-500"></i>
                <h2 class="text-xl font-black text-white uppercase italic tracking-tighter">Mix de Modelos <span class="text-zinc-600">(Consumo)</span></h2>
            </div>

            <div class="space-y-6">
                @foreach($byModel as $model)
                <div class="group">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-xs font-black text-zinc-400 uppercase tracking-widest">{{ $model->model_name }}</span>
                        <div class="text-right">
                            <span class="text-xs font-black text-white italic">${{ number_format($model->total_cost, 4) }}</span>
                            <p class="text-[8px] text-zinc-600 font-black uppercase tracking-widest">{{ $model->count }} chamadas</p>
                        </div>
                    </div>
                    <div class="h-2 w-full bg-zinc-900 rounded-full overflow-hidden">
                        @php $width = $metrics['total_cost'] > 0 ? ($model->total_cost / $metrics['total_cost']) * 100 : 0; @endphp
                        <div class="h-full bg-blue-500 group-hover:bg-blue-400 transition-all rounded-full" style="width: {{ $width }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="bg-zinc-950 border border-zinc-800 rounded-[3.5rem] overflow-hidden shadow-3xl">
        <div class="p-10 pb-6 flex items-center justify-between border-b border-zinc-900">
            <h2 class="text-xl font-black text-white uppercase italic tracking-tighter">Logs de Operação <span class="text-zinc-600">Recentes</span></h2>
            <span class="px-4 py-1.5 rounded-full bg-zinc-900 text-zinc-500 text-[10px] font-black uppercase tracking-widest border border-zinc-800">Tempo Real</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-zinc-900/50">
                        <th class="px-10 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Usuário</th>
                        <th class="px-10 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Agente</th>
                        <th class="px-10 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Modelo</th>
                        <th class="px-10 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Tokens</th>
                        <th class="px-10 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Custo</th>
                        <th class="px-10 py-5 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-900">
                    @foreach($recentLogs as $log)
                    <tr class="hover:bg-zinc-900/30 transition-colors">
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-400 text-[10px] font-black italic">
                                    {{ substr($log->user->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-black text-white uppercase tracking-widest">{{ $log->user->name ?? 'Sistema' }}</p>
                                    <p class="text-[8px] text-zinc-600 font-black uppercase tracking-widest">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-6">
                            <span class="px-3 py-1 rounded-lg bg-emerald-500/5 text-emerald-500 text-[9px] font-black uppercase tracking-widest border border-emerald-500/10">
                                {{ $log->agent_name }}
                            </span>
                        </td>
                        <td class="px-10 py-6 text-[10px] text-zinc-400 font-medium font-mono">{{ $log->model_name }}</td>
                        <td class="px-10 py-6">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-white italic">{{ number_format($log->total_tokens) }}</span>
                                <span class="text-[8px] text-zinc-600 font-black uppercase tracking-widest">In: {{ $log->input_tokens }} • Out: {{ $log->output_tokens }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-6 text-xs font-black text-white italic">${{ number_format($log->cost_usd, 6) }}</td>
                        <td class="px-10 py-6">
                            @if($log->status === 'success')
                                <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-rose-500 inline-block shadow-[0_0_8px_rgba(244,63,94,0.5)]" title="{{ $log->error_message }}"></span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    body { background: #080a0f; }
</style>
@endsection
