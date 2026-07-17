@extends('layouts.admin')

@section('title', 'Monitoramento de Erros | NexShape Matrix')

@section('content')
<div class="space-y-8 animate-fade-in">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Relatórios de <span class="text-red-500">Exceção</span></h2>
            <p class="text-zinc-500 text-sm mt-1">Diagnóstico profundo de falhas e integridade do sistema.</p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="bg-zinc-900/50 px-5 py-2.5 rounded-2xl border border-white/5 flex items-center gap-3 shadow-xl">
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Registros Ativos:</span>
                <span class="text-sm font-black text-white tabular-nums">{{ $systemErrors->total() }}</span>
            </div>

            @if($systemErrors->total() > 0)
                <form action="{{ route('admin.system-errors.clear') }}" method="POST"
                data-confirm-delete
                data-confirm-title="Expurgar histórico"
                data-confirm-message="ATENÇÃO: Deseja realmente expurgar todo o histórico de erros? Esta ação não pode ser desfeita.">
                    @csrf
                    <button type="submit" class="p-3 bg-red-500/10 text-red-500 rounded-2xl border border-red-500/20 hover:bg-red-500 hover:text-white transition-all shadow-lg active:scale-95 group" title="Limpar tudo">
                        <i class="fas fa-trash-alt group-hover:rotate-12 transition-transform"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Errors Table -->
    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
        <div class="table-wrap overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Protocolo</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Mensagem de Kernel</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Identidade</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Localização</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($systemErrors as $err)
                        <tr class="hover:bg-white/5 transition-colors group">
                            <td class="p-6">
                                @php
                                    $types = [
                                        'sql' => ['bg' => 'red-500/10', 'text' => 'red-400', 'icon' => 'fa-database'],
                                        'validation' => ['bg' => 'amber-500/10', 'text' => 'amber-400', 'icon' => 'fa-check-circle'],
                                        'default' => ['bg' => 'blue-500/10', 'text' => 'blue-400', 'icon' => 'fa-exclamation-triangle']
                                    ];
                                    $t = $types[$err->type] ?? $types['default'];
                                @endphp
                                <span class="px-3 py-1 bg-{{ $t['bg'] }} text-{{ $t['text'] }} text-[9px] font-black uppercase rounded-lg border border-{{ $t['text'] }}/20 flex items-center gap-2 w-fit">
                                    <i class="fas {{ $t['icon'] }}"></i> {{ $err->type }}
                                </span>
                                <span class="block text-[10px] text-zinc-600 font-bold tabular-nums mt-2">{{ $err->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="p-6 max-w-md">
                                <p class="text-sm font-bold text-white leading-relaxed line-clamp-2" title="{{ $err->message }}">
                                    {{ $err->message }}
                                </p>
                            </td>
                            <td class="p-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-zinc-800 flex items-center justify-center text-[10px] font-black text-zinc-400 border border-white/5 shadow-inner">
                                        {{ substr($err->user?->name ?? 'G', 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold text-zinc-400">{{ $err->user?->name ?? 'GUEST' }}</span>
                                </div>
                            </td>
                            <td class="p-6">
                                <div class="space-y-1">
                                    <span class="text-[10px] font-black text-blue-500/80 uppercase tracking-widest">{{ $err->method }}</span>
                                    <code class="block text-[10px] text-zinc-500 font-mono truncate max-w-[200px]">{{ $err->url }}</code>
                                </div>
                            </td>
                            <td class="p-6">
                                <button onclick="showErrorDetail({{ $err->id }})" class="px-4 py-2 bg-zinc-950 text-white text-[10px] font-black uppercase rounded-xl border border-white/10 hover:border-blue-500/50 transition-all shadow-2xl active:scale-95">
                                    Analisar
                                </button>
                                <div id="detail-{{ $err->id }}" class="hidden">
                                    {!! json_encode([
                                        'message' => $err->message,
                                        'stack' => $err->stack_trace,
                                        'payload' => $err->payload,
                                        'ua' => $err->user_agent,
                                        'ip' => $err->ip
                                    ]) !!}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-20 text-center">
                                <div class="w-16 h-16 bg-zinc-900 rounded-full flex items-center justify-center mx-auto mb-4 text-zinc-700">
                                    <i class="fas fa-check-circle text-2xl"></i>
                                </div>
                                <h3 class="text-white font-black">Zero Ocorrências</h3>
                                <p class="text-zinc-600 text-sm mt-1">O sistema está operando em estabilidade absoluta.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($systemErrors->hasPages())
            <div class="p-6 border-t border-white/5 bg-zinc-950/20">
                {{ $systemErrors->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Analysis -->
<div id="errorModal" class="fixed inset-0 bg-black/90 backdrop-blur-xl z-[100] hidden items-center justify-center p-6 sm:p-12 overflow-y-auto">
    <div class="bg-zinc-900/80 border border-white/10 rounded-[3rem] w-full max-w-6xl shadow-3xl flex flex-col max-h-full">
        <!-- Modal Header -->
        <div class="p-10 border-b border-white/5 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-black text-red-500 uppercase tracking-[0.3em]">Causa Raiz</span>
                <h2 class="text-lg font-bold text-white tracking-tight mt-1">Diagnóstico do Incidente</h2>
            </div>
            <button onclick="closeModal()" class="w-12 h-12 bg-zinc-950 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-white transition-all shadow-inner border border-white/5">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-10 overflow-y-auto space-y-10">
            <div>
                <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-4">Mensagem de Erro</label>
                <div id="modalMessage" class="p-6 bg-red-500/5 text-red-400 font-bold rounded-2xl border border-red-500/10 text-sm leading-relaxed italic"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="flex flex-col">
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-4">Parâmetros de Request</label>
                    <pre id="modalPayload" class="flex-1 bg-zinc-950 p-6 rounded-3xl border border-white/5 text-[10px] font-mono text-blue-400 overflow-auto shadow-inner leading-relaxed"></pre>
                </div>
                <div class="flex flex-col">
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-4">Stack Trace</label>
                    <pre id="modalStack" class="flex-1 bg-zinc-950 p-6 rounded-3xl border border-white/5 text-[10px] font-mono text-zinc-500 overflow-auto shadow-inner h-[400px] leading-relaxed"></pre>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-white/5">
                <div class="bg-zinc-950/50 p-6 rounded-2xl border border-white/5">
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-2">Ponto de Origem (IP)</label>
                    <p id="modalIp" class="text-sm font-black text-white font-mono"></p>
                </div>
                <div class="bg-zinc-950/50 p-6 rounded-2xl border border-white/5">
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-2">User Agent</label>
                    <p id="modalUa" class="text-[10px] font-medium text-zinc-500 leading-relaxed"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showErrorDetail(id) {
        const raw = document.getElementById('detail-' + id).innerText;
        const data = JSON.parse(raw);
        
        document.getElementById('modalMessage').innerText = data.message;
        document.getElementById('modalPayload').innerText = JSON.stringify(data.payload, null, 2);
        document.getElementById('modalStack').innerText = data.stack;
        document.getElementById('modalIp').innerText = data.ip;
        document.getElementById('modalUa').innerText = data.ua;
        
        document.getElementById('errorModal').classList.remove('hidden');
        document.getElementById('errorModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('errorModal').classList.add('hidden');
        document.getElementById('errorModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('errorModal')) {
            closeModal();
        }
    }
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.8s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
