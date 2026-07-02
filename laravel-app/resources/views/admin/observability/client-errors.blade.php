@extends('layouts.admin')

@section('title', 'Erros JavaScript')

@section('content')
<div class="space-y-8 animate-fade-in text-white">
    <div>
        <h2 class="text-2xl font-black text-white tracking-tight">Erros do Cliente (JavaScript)</h2>
        <p class="text-zinc-500 text-sm mt-1">Capturados via exceções no navegador e reportados à plataforma.</p>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="text-[10px] font-black text-zinc-500 uppercase block mb-1">URL</label>
            <input type="text" name="url" value="{{ request('url') }}" class="bg-zinc-950 border border-white/10 rounded-xl px-4 py-2 text-sm text-white" placeholder="Filtrar por URL...">
        </div>
        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-xs font-black uppercase rounded-xl">Filtrar</button>
        <a href="{{ route('admin.observability.client-errors.export', request()->query()) }}" class="px-4 py-2 bg-zinc-800 text-zinc-300 text-xs font-black uppercase rounded-xl border border-white/10">Export CSV</a>
    </form>

    <div class="bg-zinc-900/40 border border-white/5 rounded-[2.5rem] overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-wider">Data</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-wider">Tipo</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-wider">Mensagem</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-wider">URL</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($logs as $log)
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-6 text-sm text-zinc-300 tabular-nums">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="p-6">
                                <span class="px-2 py-0.5 bg-amber-500/10 text-amber-400 text-[9px] font-black uppercase rounded border border-amber-500/20">
                                    {{ $log->type }}
                                </span>
                            </td>
                            <td class="p-6 text-sm text-zinc-200 max-w-md truncate font-bold" title="{{ $log->message }}">{{ $log->message }}</td>
                            <td class="p-6 text-xs font-mono text-zinc-500 max-w-xs truncate" title="{{ $log->url }}">{{ $log->url }}</td>
                            <td class="p-6">
                                <button onclick="showClientErrorDetail({{ $log->id }})" class="px-3 py-1.5 bg-zinc-950 text-white text-[9px] font-black uppercase rounded-lg border border-white/10 hover:border-blue-500/50 transition-all active:scale-95 shadow-inner">
                                    Analisar
                                </button>
                                <div id="detail-{{ $log->id }}" class="hidden">
                                    {!! json_encode([
                                        'message' => $log->message,
                                        'stack' => $log->stack,
                                        'url' => $log->url,
                                        'ua' => $log->user_agent,
                                        'ip' => $log->ip,
                                        'user' => $log->user?->name ?? 'GUEST'
                                    ]) !!}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-16 text-center text-zinc-500">Nenhum erro de cliente registrado nas últimas horas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="p-6 border-t border-white/5">{{ $logs->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal Analysis -->
<div id="errorModal" class="fixed inset-0 bg-black/90 backdrop-blur-xl z-[100] hidden items-center justify-center p-6 sm:p-12 overflow-y-auto">
    <div class="bg-zinc-900/80 border border-white/10 rounded-[3rem] w-full max-w-5xl shadow-3xl flex flex-col max-h-full">
        <!-- Modal Header -->
        <div class="p-10 border-b border-white/5 flex items-center justify-between">
            <div>
                <span class="text-[10px] font-black text-amber-500 uppercase tracking-[0.3em]">Frontend Crash</span>
                <h2 class="text-lg font-bold text-white tracking-tight mt-1">Diagnóstico de Falha Client-Side</h2>
            </div>
            <button onclick="closeModal()" class="w-12 h-12 bg-zinc-950 rounded-2xl flex items-center justify-center text-zinc-500 hover:text-white transition-all shadow-inner border border-white/5">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-10 overflow-y-auto space-y-8">
            <div>
                <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-2">Mensagem do Navegador</label>
                <div id="modalMessage" class="p-5 bg-amber-500/5 text-amber-400 font-bold rounded-2xl border border-amber-500/10 text-sm leading-relaxed italic"></div>
            </div>

            <div>
                <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-2">Stack Trace / Trace do Console</label>
                <pre id="modalStack" class="bg-zinc-950 p-6 rounded-3xl border border-white/5 text-[10px] font-mono text-zinc-500 overflow-auto shadow-inner h-[280px] leading-relaxed"></pre>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-zinc-950/50 p-6 rounded-2xl border border-white/5">
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-2">URL de Impacto</label>
                    <p id="modalUrl" class="text-xs font-mono text-zinc-300 break-all"></p>
                </div>
                <div class="bg-zinc-950/50 p-6 rounded-2xl border border-white/5">
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-2">Usuário Impactado</label>
                    <p id="modalUser" class="text-xs font-bold text-white"></p>
                </div>
                <div class="bg-zinc-950/50 p-6 rounded-2xl border border-white/5">
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-2">Ponto de Origem (IP)</label>
                    <p id="modalIp" class="text-xs font-mono text-white"></p>
                </div>
            </div>

            <div class="bg-zinc-950/50 p-6 rounded-2xl border border-white/5">
                <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-2">User Agent (Navegador/SO)</label>
                <p id="modalUa" class="text-[10px] text-zinc-500 font-mono leading-relaxed"></p>
            </div>
        </div>
    </div>
</div>

<script>
    function showClientErrorDetail(id) {
        const raw = document.getElementById('detail-' + id).innerText;
        const data = JSON.parse(raw);
        
        document.getElementById('modalMessage').innerText = data.message;
        document.getElementById('modalStack').innerText = data.stack || 'Sem trace de depuração disponível.';
        document.getElementById('modalUrl').innerText = data.url;
        document.getElementById('modalUser').innerText = data.user;
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
@endsection
