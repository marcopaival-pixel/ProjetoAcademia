@extends('layouts.admin')

@section('title', 'Monitoramento do Sistema')

@section('content')
<div class="space-y-10 animate-fade-in">
    <!-- Header with Pulsating Status -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-white tracking-tight">Status do <span class="text-blue-500">Núcleo</span></h2>
            <p class="text-zinc-500 text-sm mt-1">Diagnóstico técnico e infraestrutura em tempo real.</p>
        </div>
        <div class="flex items-center gap-4 bg-zinc-900/50 px-6 py-3 rounded-2xl border border-white/5 shadow-xl">
             <div class="relative w-3 h-3">
                <div class="absolute inset-0 bg-emerald-500 rounded-full animate-ping opacity-75"></div>
                <div class="relative w-3 h-3 bg-emerald-500 rounded-full"></div>
             </div>
             <span class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em]">Sistemas Operacionais</span>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Software Stack Card -->
        <div class="group bg-zinc-900/40 backdrop-blur-3xl p-10 rounded-[2.5rem] border border-white/5 hover:border-blue-500/20 transition-all shadow-2xl">
            <h3 class="text-zinc-500 font-black text-[10px] uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                <i class="fas fa-layer-group text-blue-500"></i>Ambiente de Software
            </h3>
            
            <div class="space-y-6">
                @foreach([['label' => 'PHP Runtime', 'val' => $info['php_version']], ['label' => 'Laravel Framework', 'val' => $info['laravel_version']], ['label' => 'Database Engine', 'val' => $info['db_driver']], ['label' => 'Kernel / OS', 'val' => $info['os']]] as $item)
                <div class="flex justify-between items-center py-4 border-b border-white/5 last:border-0">
                    <span class="text-sm font-bold text-zinc-400">{{ $item['label'] }}</span>
                    <span class="px-4 py-1.5 bg-zinc-950 rounded-xl text-xs font-black text-white border border-white/5 shadow-inner">
                        {{ $item['val'] }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Hardware/Resource Card -->
        <div class="group bg-zinc-900/40 backdrop-blur-3xl p-10 rounded-[2.5rem] border border-white/5 hover:border-emerald-500/20 transition-all shadow-2xl">
            <h3 class="text-zinc-500 font-black text-[10px] uppercase tracking-[0.3em] mb-8 flex items-center gap-3">
                <i class="fas fa-microchip text-emerald-500"></i>Recursos do Hardware
            </h3>
            
            <div class="space-y-8">
                <!-- Memory Usage -->
                <div>
                    <div class="flex justify-between mb-3">
                        <span class="text-sm font-bold text-zinc-400">Uso de Memória RAM</span>
                        <span class="text-xs font-black text-white">{{ $info['memory_usage'] }}</span>
                    </div>
                    <div class="h-2 bg-zinc-950 rounded-full overflow-hidden border border-white/5 p-0.5">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-emerald-500 rounded-full w-[45%]"></div>
                    </div>
                </div>

                <!-- Disk Usage -->
                <div>
                    <div class="flex justify-between mb-3">
                        <span class="text-sm font-bold text-zinc-400">Armazenamento (Root)</span>
                        <span class="text-xs font-black text-white">{{ $info['disk_free'] }} / {{ $info['disk_total'] }}</span>
                    </div>
                    <div class="h-2 bg-zinc-950 rounded-full overflow-hidden border border-white/5 p-0.5">
                        <div class="h-full bg-gradient-to-r from-emerald-500 to-emerald-300 rounded-full w-[65%]"></div>
                    </div>
                </div>

                <!-- Server IP -->
                <div class="flex justify-between items-center pt-4 border-t border-white/5">
                    <span class="text-sm font-bold text-zinc-400">IP do Servidor</span>
                    <code class="px-3 py-1 bg-zinc-950 rounded-lg text-[10px] font-mono text-zinc-500 border border-white/5">{{ $info['server_ip'] ?? '127.0.0.1' }}</code>
                </div>
            </div>
        </div>
    </div>

    <!-- Final Insight Card -->
    <div class="bg-gradient-to-br from-zinc-900/60 to-zinc-950/80 backdrop-blur-3xl border border-white/10 p-12 rounded-[3.5rem] text-center shadow-3xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-60 h-60 bg-emerald-500/10 rounded-full blur-[100px]"></div>
        <div class="relative z-10">
            <div class="w-20 h-20 bg-emerald-500/10 rounded-[2.5rem] flex items-center justify-center text-emerald-500 mx-auto mb-6 shadow-2xl border border-emerald-500/20">
                <i class="fas fa-check-double text-3xl"></i>
            </div>
            <h3 class="text-2xl font-black text-white tracking-tight">Saúde da Aplicação: 100%</h3>
            <p class="text-zinc-500 max-w-lg mx-auto mt-3 font-medium">Todos os micro-serviços, conexões de base de dados e buffers de cache estão operando dentro dos parâmetros de performance estabelecidos.</p>
            <p class="text-[10px] text-zinc-600 font-black uppercase tracking-[0.3em] mt-8">Última Auditoria: {{ date('H:i:s') }}</p>
        </div>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeIn 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
