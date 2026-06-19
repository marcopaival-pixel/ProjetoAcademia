@extends('layouts.admin')

@section('title', 'Controle Operacional e Resiliência')

@section('content')
<div class="space-y-10 pb-12">
    <!-- Top Stats Bar -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="glass-card p-8 group hover:border-emerald-500/20 transition-all duration-500 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"></div>
            <div class="relative z-10 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Status do Sistema</span>
                    <div class="flex items-center gap-2">
                        @if($health['status'] === 'healthy')
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest italic">Saudável</span>
                        @else
                            <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse shadow-[0_0_10px_rgba(239,68,68,0.5)]"></div>
                            <span class="text-[10px] font-black text-red-500 uppercase tracking-widest italic">{{ $health['status'] === 'critical' ? 'Crítico' : 'Instável' }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-white italic uppercase tracking-tighter">100.0</span>
                    <span class="text-zinc-600 text-xs font-bold uppercase tracking-widest italic">% Uptime</span>
                </div>
                <p class="text-[10px] text-zinc-600 font-medium italic leading-relaxed">Operando em níveis ótimos de estabilidade.</p>
            </div>
        </div>

        <div class="glass-card p-8 group hover:border-indigo-500/20 transition-all duration-500 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"></div>
            <div class="relative z-10 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Banco de Dados</span>
                    <i data-lucide="database" class="w-4 h-4 {{ $health['database']['status'] === 'ok' ? 'text-emerald-500' : 'text-red-500' }}"></i>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-white italic uppercase tracking-tighter">{{ $health['database']['status'] === 'ok' ? 'Online' : 'Erro' }}</span>
                </div>
                <p class="text-[10px] text-zinc-600 font-medium italic leading-relaxed">{{ $health['database']['message'] }}</p>
            </div>
        </div>

        <div class="glass-card p-8 group hover:border-amber-500/20 transition-all duration-500 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"></div>
            <div class="relative z-10 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Workers / Filas</span>
                    <i data-lucide="activity" class="w-4 h-4 {{ $health['queue']['status'] === 'ok' ? 'text-emerald-500' : 'text-amber-500' }}"></i>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-white italic uppercase tracking-tighter">{{ $health['queue']['status'] === 'ok' ? 'Ativo' : 'Pendente' }}</span>
                </div>
                <p class="text-[10px] text-zinc-600 font-medium italic leading-relaxed">Processamento assíncrono em dia.</p>
            </div>
        </div>

        <div class="glass-card p-8 group hover:border-blue-500/20 transition-all duration-500 relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"></div>
            <div class="relative z-10 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Latência Global</span>
                    <i data-lucide="zap" class="w-4 h-4 text-blue-500"></i>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-white italic uppercase tracking-tighter">{{ $health['response_time'] }}</span>
                    <span class="text-zinc-600 text-xs font-bold uppercase tracking-widest italic">ms</span>
                </div>
                <p class="text-[10px] text-zinc-600 font-medium italic leading-relaxed">Tempo de resposta médio do cluster.</p>
            </div>
        </div>
    </div>

    @if(!empty($health['queue_breakdown']))
    <div class="glass-card p-8 border border-white/5">
        <h3 class="text-sm font-black text-white uppercase tracking-widest mb-4">Filas por nome</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($health['queue_breakdown'] as $queueName => $count)
            <div class="bg-zinc-950/60 rounded-2xl p-4 border border-white/5">
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">{{ $queueName }}</span>
                <div class="text-2xl font-black text-white mt-1 tabular-nums">{{ $count }}</div>
                <span class="text-[9px] text-zinc-600 uppercase">pendentes</span>
            </div>
            @endforeach
        </div>
        <p class="text-[10px] text-zinc-600 mt-4 italic">Jobs: {{ $health['jobs']['pending'] ?? 0 }} total · {{ $health['jobs']['failed'] ?? 0 }} falhados · throughput {{ $health['jobs']['throughput'] ?? '—' }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Infrastructure and Monitoring -->
        <div class="lg:col-span-8 space-y-10">
            <!-- Resource Metrics -->
            <div class="glass-card p-10 relative overflow-hidden">
                <div class="flex items-center justify-between mb-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center text-zinc-500">
                            <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white uppercase italic tracking-tighter leading-none">Métricas de Infraestrutura</h3>
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">Uso de recursos do servidor em tempo real</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 px-4 py-2 bg-zinc-950/50 rounded-xl border border-white/5">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-[9px] font-black text-zinc-500 uppercase tracking-widest italic">Live Polling</span>
                        </div>
                        <a href="{{ route('admin.operations.index') }}" class="px-6 py-2 bg-zinc-950 border border-white/5 text-zinc-400 hover:text-white transition-all rounded-xl text-[10px] font-black tracking-widest uppercase italic shadow-xl">
                            Atualizar Agora
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <!-- CPU -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Processamento (CPU)</span>
                            <span class="text-xs font-black text-white italic">{{ $health['cpu']['load_1m'] ?? '0.00' }}{{ str_contains($health['cpu']['message'] ?? '', '%') ? '%' : '' }}</span>
                        </div>
                        <div class="h-1.5 w-full bg-zinc-950 rounded-full overflow-hidden border border-white/5">
                            @php($cpuPercent = min((($health['cpu']['load_1m'] ?? 0) * (str_contains($health['cpu']['message'] ?? '', '%') ? 1 : 10)), 100))
                            <div class="h-full {{ $cpuPercent > 80 ? 'bg-red-500' : 'bg-emerald-500' }} transition-all duration-1000" style="width: {{ $cpuPercent }}%"></div>
                        </div>
                        <p class="text-[10px] text-zinc-600 font-medium italic">Carga {{ str_contains($health['cpu']['message'] ?? '', 'Windows') ? 'atual do processador' : 'média no último minuto' }}.</p>
                    </div>

                    <!-- RAM -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Memória (RAM)</span>
                            <span class="text-xs font-black text-white italic">{{ $health['memory']['used_percent'] ?? '0' }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-zinc-950 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full {{ ($health['memory']['used_percent'] ?? 0) > 85 ? 'bg-red-500' : 'bg-indigo-500' }} transition-all duration-1000" style="width: {{ $health['memory']['used_percent'] ?? 0 }}%"></div>
                        </div>
                        <p class="text-[10px] text-zinc-600 font-medium italic">Total: {{ $health['memory']['total'] ?? 'N/A' }} | Livre: {{ $health['memory']['free'] ?? 'N/A' }}</p>
                    </div>

                    <!-- Disk -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest italic">Armazenamento</span>
                            <span class="text-xs font-black text-white italic">{{ $health['disk']['used_percent'] }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-zinc-950 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full {{ $health['disk']['used_percent'] > 90 ? 'bg-red-500' : 'bg-blue-500' }} transition-all duration-1000" style="width: {{ $health['disk']['used_percent'] }}%"></div>
                        </div>
                        <p class="text-[10px] text-zinc-600 font-medium italic">{{ $health['disk']['free'] }} livres de {{ $health['disk']['total'] }} .</p>
                    </div>
                </div>
            </div>

            <!-- Workers & Background Services -->
            <div class="glass-card overflow-hidden">
                <div class="px-10 py-8 border-b border-white/5 bg-white/[0.01]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-zinc-500">
                                <i data-lucide="cpu" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-white uppercase italic tracking-tighter leading-none">Workers e Serviços Ativos</h3>
                                <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">Processos em background detectados no servidor</p>
                            </div>
                        </div>
                        <span class="px-4 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-black text-zinc-400 uppercase italic">
                            Total: {{ count($workers) }} Ativos
                        </span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-zinc-950/50">
                                <th class="px-10 py-5 text-[10px] font-black text-zinc-600 uppercase tracking-widest italic">PID</th>
                                <th class="px-10 py-5 text-[10px] font-black text-zinc-600 uppercase tracking-widest italic">Serviço/Comando</th>
                                <th class="px-10 py-5 text-[10px] font-black text-zinc-600 uppercase tracking-widest italic">Memória</th>
                                <th class="px-10 py-5 text-[10px] font-black text-zinc-600 uppercase tracking-widest italic">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($workers as $worker)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-10 py-5">
                                    <span class="text-xs font-bold text-zinc-500 italic">#{{ $worker['pid'] }}</span>
                                </td>
                                <td class="px-10 py-5">
                                    <div class="text-[11px] font-black text-white uppercase italic tracking-tight group-hover:text-emerald-500 transition-colors truncate max-w-md">
                                        {{ $worker['command'] }}
                                    </div>
                                    <span class="text-[9px] text-zinc-600 font-bold uppercase tracking-widest italic">{{ $worker['type'] ?? 'Background' }}</span>
                                </td>
                                <td class="px-10 py-5">
                                    <span class="text-xs font-bold text-zinc-400 italic">{{ $worker['memory'] ?? ($worker['mem'] ?? 'N/A') }}</span>
                                </td>
                                <td class="px-10 py-5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></div>
                                        <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest italic">Executando</span>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-10 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <i data-lucide="alert-triangle" class="w-8 h-8 text-amber-500/50"></i>
                                        <p class="text-xs font-black text-zinc-600 uppercase italic tracking-widest">Nenhum worker ativo detectado.</p>
                                        <p class="text-[10px] text-zinc-700 italic">Inicie o processamento via terminal ou Task Scheduler.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Queue Analytics & Job Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="glass-card p-8 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-500/10 border border-indigo-500/20 rounded-xl flex items-center justify-center text-indigo-500">
                            <i data-lucide="layers" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-white uppercase italic tracking-tighter">Análise de Filas</h4>
                            <p class="text-[9px] text-zinc-600 font-bold uppercase italic">Volume pendente e processamento</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-zinc-950/50 p-6 rounded-2xl border border-white/5">
                            <span class="block text-[9px] font-black text-zinc-500 uppercase tracking-widest italic mb-2">Pendentes</span>
                            <span class="text-2xl font-black text-white italic tracking-tighter">{{ $health['jobs']['pending'] ?? 0 }}</span>
                        </div>
                        <div class="bg-zinc-950/50 p-6 rounded-2xl border border-white/5">
                            <span class="block text-[9px] font-black text-zinc-500 uppercase tracking-widest italic mb-2">Tempo Médio</span>
                            <span class="text-2xl font-black text-white italic tracking-tighter">{{ $health['jobs']['avg_time'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="glass-card p-8 space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-red-500/10 border border-red-500/20 rounded-xl flex items-center justify-center text-red-500">
                            <i data-lucide="alert-octagon" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-white uppercase italic tracking-tighter">Jobs Falhados</h4>
                            <p class="text-[9px] text-zinc-600 font-bold uppercase italic">Exceções e erros de execução</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-zinc-950/50 p-6 rounded-2xl border border-white/5">
                            <span class="block text-[9px] font-black text-zinc-500 uppercase tracking-widest italic mb-2">Total Falhas</span>
                            <span class="text-2xl font-black {{ ($health['jobs']['failed'] ?? 0) > 0 ? 'text-red-500' : 'text-white' }} italic tracking-tighter">{{ $health['jobs']['failed'] ?? 0 }}</span>
                        </div>
                        <div class="bg-zinc-950/50 p-6 rounded-2xl border border-white/5">
                            <span class="block text-[9px] font-black text-zinc-500 uppercase tracking-widest italic mb-2">Throughput</span>
                            <span class="text-2xl font-black text-white italic tracking-tighter">{{ $health['jobs']['throughput'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Logs -->
            <div class="glass-card overflow-hidden">
                <div class="px-10 py-8 border-b border-white/5 bg-white/[0.01]">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-zinc-800 border border-white/10 rounded-xl flex items-center justify-center text-zinc-400">
                            <i data-lucide="file-text" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-white uppercase italic tracking-tighter leading-none">Logs Rápidos (Laravel)</h3>
                            <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1">Últimas 50 entradas do log de aplicação</p>
                        </div>
                    </div>
                </div>
                <div class="p-4 bg-zinc-950/80 font-mono text-[10px] leading-relaxed max-h-96 overflow-y-auto custom-scrollbar">
                    @forelse($logs as $log)
                        <div class="mb-2 group">
                            <span class="text-zinc-600">[{{ $log['date'] }}]</span>
                            <span class="font-bold uppercase italic {{ $log['level'] === 'error' ? 'text-red-500' : ($log['level'] === 'warning' ? 'text-amber-500' : 'text-blue-500') }}">
                                {{ $log['level'] }}:
                            </span>
                            <span class="text-zinc-300 group-hover:text-white transition-colors">{{ $log['message'] }}</span>
                        </div>
                    @empty
                        <p class="text-zinc-700 italic">Nenhum log recente encontrado.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Controls -->
        <div class="lg:col-span-4 space-y-10">
            <!-- Operational Mode Form -->
            <div class="glass-card p-10 space-y-8 border-l-4 border-l-indigo-500/30 shadow-2xl">
                <div>
                    <h3 class="text-xl font-black text-white uppercase italic tracking-tighter leading-none">Controles Críticos</h3>
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1 italic">Governança e disponibilidade</p>
                </div>

                <form action="{{ route('admin.operations.update') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest italic ml-1">Modo de Exposição</label>
                        <div class="relative group">
                            <i data-lucide="server" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-600 group-hover:text-emerald-500 transition-colors"></i>
                            <select name="maintenance_mode" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-4 pl-12 text-xs font-bold text-white focus:border-indigo-500 transition-all outline-none appearance-none cursor-pointer italic">
                                <option value="off" {{ $settings['maintenance_mode'] === 'off' ? 'selected' : '' }}>PRODUÇÃO (ABERTO)</option>
                                <option value="operable" {{ $settings['maintenance_mode'] === 'operable' ? 'selected' : '' }}>MANUTENÇÃO OPERÁVEL (ADMIN)</option>
                                <option value="total" {{ $settings['maintenance_mode'] === 'total' ? 'selected' : '' }}>LOCKDOWN TOTAL (OFFLINE)</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest italic ml-1">Mensagem de Resiliência</label>
                        <textarea name="maintenance_message" class="w-full bg-zinc-950 border border-white/5 rounded-2xl p-6 text-xs font-bold text-white focus:border-indigo-500 transition-all outline-none italic" rows="3">{{ $settings['maintenance_message'] }}</textarea>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest italic ml-1">Integridade de Dados</label>
                        <div class="flex items-center justify-between p-4 bg-zinc-950 border border-white/5 rounded-2xl">
                            <div class="flex items-center gap-3">
                                <i data-lucide="shield-check" class="w-4 h-4 text-indigo-500"></i>
                                <span class="text-[10px] font-black text-white uppercase tracking-widest italic">Somente Leitura (Read-Only)</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="read_only_mode" value="0">
                                <input type="checkbox" name="read_only_mode" value="1" {{ $settings['read_only_mode'] ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-zinc-900 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-zinc-500 after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 peer-checked:after:bg-white"></div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-5 bg-white text-zinc-950 font-black rounded-[2rem] hover:bg-indigo-500 hover:text-white transition-all shadow-2xl active:scale-95 text-xs tracking-widest uppercase italic flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Implantar Configurações
                    </button>
                </form>

                <div class="pt-6 border-t border-white/5 flex items-center justify-between">
                    <span class="text-[10px] font-black text-zinc-700 uppercase tracking-widest italic">Última Modificação</span>
                    <span class="text-[10px] font-black text-zinc-500 uppercase italic tracking-tight">{{ \Carbon\Carbon::parse($settings['last_updated'])->diffForHumans() }}</span>
                </div>
            </div>

            <!-- Advanced Worker Controls -->
            <div class="glass-card p-10 space-y-8 bg-gradient-to-br from-indigo-500/[0.02] to-transparent">
                <div>
                    <h3 class="text-xl font-black text-white uppercase italic tracking-tighter leading-none">Gestão de Workers</h3>
                    <p class="text-[9px] text-zinc-600 font-black uppercase tracking-widest mt-1 italic">Controle avançado de processos</p>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <!-- Restart All -->
                    <form action="{{ route('admin.operations.restart-workers') }}" method="POST" id="form-restart-all">
                        @csrf
                        <button type="button" class="w-full group flex items-center justify-between p-6 bg-zinc-950 border border-white/5 rounded-2xl hover:border-amber-500/30 transition-all text-left outline-none shadow-xl" 
                            onclick="window.openNxConfirmAction({
                                title: 'Reiniciar Todos os Workers',
                                message: 'Deseja enviar o comando queue:restart? Isso instruirá todos os workers ativos a encerrarem graciosamente após terminarem o job atual.',
                                icon: 'refresh-cw',
                                type: 'warning',
                                confirmLabel: 'Confirmar Reinício',
                                onConfirm: () => { document.getElementById('form-restart-all').submit(); }
                            })">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="block text-xs font-black text-white uppercase italic tracking-tight">Reiniciar Workers</span>
                                    <span class="block text-[9px] text-zinc-600 font-medium italic mt-0.5 tracking-tight">Reinício gracioso global</span>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-zinc-800 group-hover:text-amber-500 transition-colors"></i>
                        </button>
                    </form>

                    <!-- Clear Queue -->
                    <form action="{{ route('admin.operations.clear-queue') }}" method="POST" id="form-clear-queue">
                        @csrf
                        <button type="button" class="w-full group flex items-center justify-between p-6 bg-zinc-950 border border-white/5 rounded-2xl hover:border-red-500/30 transition-all text-left outline-none shadow-xl" 
                            onclick="window.openNxConfirmAction({
                                title: 'Limpar Fila de Jobs',
                                message: 'ATENÇÃO: Todos os jobs pendentes na fila serão excluídos permanentemente. Esta ação não pode ser desfeita.',
                                icon: 'trash-2',
                                type: 'danger',
                                confirmLabel: 'Limpar Agora',
                                onConfirm: () => { document.getElementById('form-clear-queue').submit(); }
                            })">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-red-500/10 rounded-xl flex items-center justify-center text-red-500 group-hover:scale-110 transition-transform">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="block text-xs font-black text-white uppercase italic tracking-tight">Limpar Fila</span>
                                    <span class="block text-[9px] text-zinc-600 font-medium italic mt-0.5 tracking-tight">Remove todos os jobs pendentes</span>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-zinc-800 group-hover:text-red-500 transition-colors"></i>
                        </button>
                    </form>

                    <!-- Retry Failed -->
                    <form action="{{ route('admin.operations.retry-failed') }}" method="POST" id="form-retry-failed">
                        @csrf
                        <button type="button" class="w-full group flex items-center justify-between p-6 bg-zinc-950 border border-white/5 rounded-2xl hover:border-emerald-500/30 transition-all text-left outline-none shadow-xl" 
                            onclick="window.openNxConfirmAction({
                                title: 'Reprocessar Falhas',
                                message: 'Deseja reenviar todos os jobs falhados para a fila de execução?',
                                icon: 'zap',
                                type: 'success',
                                confirmLabel: 'Reprocessar',
                                onConfirm: () => { document.getElementById('form-retry-failed').submit(); }
                            })">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-500 group-hover:scale-110 transition-transform">
                                    <i data-lucide="zap" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="block text-xs font-black text-white uppercase italic tracking-tight">Reprocessar Falhas</span>
                                    <span class="block text-[9px] text-zinc-600 font-medium italic mt-0.5 tracking-tight">Tenta executar jobs que falharam</span>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-zinc-800 group-hover:text-emerald-500 transition-colors"></i>
                        </button>
                    </form>

                    <!-- Flush Failed -->
                    <form action="{{ route('admin.operations.flush-failed') }}" method="POST" id="form-flush-failed">
                        @csrf
                        <button type="button" class="w-full group flex items-center justify-between p-6 bg-zinc-950 border border-white/5 rounded-2xl hover:border-zinc-500/30 transition-all text-left outline-none shadow-xl" 
                            onclick="window.openNxConfirmAction({
                                title: 'Limpar Histórico de Falhas',
                                message: 'Isso apagará permanentemente todos os registros de jobs falhados.',
                                icon: 'eraser',
                                type: 'danger',
                                confirmLabel: 'Limpar Histórico',
                                onConfirm: () => { document.getElementById('form-flush-failed').submit(); }
                            })">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-zinc-500/10 rounded-xl flex items-center justify-center text-zinc-500 group-hover:scale-110 transition-transform">
                                    <i data-lucide="eraser" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="block text-xs font-black text-white uppercase italic tracking-tight">Limpar Falhas</span>
                                    <span class="block text-[9px] text-zinc-600 font-medium italic mt-0.5 tracking-tight">Apaga logs de jobs falhados</span>
                                </div>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-zinc-800 group-hover:text-zinc-500 transition-colors"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .animate-fade-in-up {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

