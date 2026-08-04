@extends('layouts.admin')

@section('title', 'Monitoramento de Erros | NexShape Matrix')

@section('content')
<div class="space-y-8 animate-fade-in" x-data="bugSurgeonLogs()" @if(session('bug_surgeon_drawer')) x-init="openDrawer({{ session('bug_surgeon_drawer') }})" @endif>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Relatórios de <span class="text-red-500">Exceção</span></h2>
            <p class="text-zinc-500 text-sm mt-1">Erros agrupados por assinatura — investigação somente leitura pelo Agente de Bugs.</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-zinc-900/50 px-5 py-2.5 rounded-2xl border border-white/5 flex items-center gap-3 shadow-xl">
                <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest">Registros:</span>
                <span class="text-sm font-black text-white tabular-nums">{{ $systemErrors->total() }}</span>
            </div>
            @if($systemErrors->total() > 0)
                <form action="{{ route('admin.system-errors.clear') }}" method="POST"
                    data-confirm-delete
                    data-confirm-title="Expurgar histórico"
                    data-confirm-message="ATENÇÃO: Deseja realmente expurgar todo o histórico de erros?">
                    @csrf
                    <button type="submit" class="p-3 bg-red-500/10 text-red-500 rounded-2xl border border-red-500/20 hover:bg-red-500 hover:text-white transition-all" title="Limpar tudo">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm font-bold">{{ session('success') }}</div>
    @endif

    <div class="bg-zinc-900/40 backdrop-blur-3xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
        <div class="table-wrap overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-white/5 bg-white/5">
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Erro / Incidente</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Mensagem</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Contexto</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Status</th>
                        <th class="p-6 text-[10px] font-black text-zinc-500 uppercase tracking-[0.2em]">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse ($systemErrors as $err)
                        @php
                            $incident = $errorIncidents[$err->id] ?? null;
                            $types = [
                                'sql' => ['bg' => 'red-500/10', 'text' => 'red-400'],
                                'validation' => ['bg' => 'amber-500/10', 'text' => 'amber-400'],
                                'default' => ['bg' => 'blue-500/10', 'text' => 'blue-400'],
                            ];
                            $t = $types[$err->type] ?? $types['default'];
                        @endphp
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-6">
                                @if($incident)
                                    <span class="text-sm font-black text-amber-400 font-mono">{{ $incident->incident_code }}</span>
                                    @if($incident->occurrence_count > 1)
                                        <span class="block text-[10px] text-zinc-500 mt-1">{{ $incident->occurrence_count }} ocorrências</span>
                                    @endif
                                @else
                                    <span class="text-[10px] text-zinc-600 font-mono">#{{ $err->id }}</span>
                                @endif
                                <span class="block text-[10px] text-zinc-600 mt-1">{{ $err->created_at->format('d/m/Y H:i') }}</span>
                                <span class="px-2 py-0.5 mt-1 inline-block bg-{{ $t['bg'] }} text-{{ $t['text'] }} text-[8px] font-black uppercase rounded">{{ $err->type }}</span>
                            </td>
                            <td class="p-6 max-w-sm">
                                <p class="text-sm font-bold text-white line-clamp-2" title="{{ $err->message }}">{{ $err->message }}</p>
                            </td>
                            <td class="p-6">
                                <div class="text-[10px] space-y-1">
                                    <span class="font-black text-blue-500/80 uppercase">{{ $err->method }}</span>
                                    <code class="block text-zinc-500 font-mono truncate max-w-[180px]">{{ $err->url }}</code>
                                    <span class="text-zinc-600">{{ $err->user?->name ?? 'GUEST' }}</span>
                                </div>
                            </td>
                            <td class="p-6">
                                @if($incident)
                                    <span class="px-3 py-1 bg-zinc-800 text-zinc-300 text-[9px] font-black uppercase rounded-lg border border-white/10">
                                        {{ $incident->statusLabel() }}
                                    </span>
                                    @if($incident->is_critical)
                                        <span class="block mt-1 text-[9px] text-red-400 font-black uppercase">Correção crítica</span>
                                    @endif
                                @else
                                    <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 text-[9px] font-black uppercase rounded-lg">Novo</span>
                                @endif
                            </td>
                            <td class="p-6">
                                <div class="flex flex-col gap-2 min-w-[160px]">
                                    <button type="button" onclick="showErrorDetail({{ $err->id }})" class="px-3 py-2 bg-zinc-950 text-zinc-300 text-[9px] font-black uppercase rounded-xl border border-white/10 hover:border-blue-500/40">
                                        Ver detalhes
                                    </button>

                                    @if(!$incident || in_array($incident->uiActionGroup(), ['new']))
                                        <form action="{{ route('admin.bug-surgeon.start-analysis', $err) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-2 bg-violet-600/20 text-violet-300 text-[9px] font-black uppercase rounded-xl border border-violet-500/30 hover:bg-violet-600/30">
                                                Analisar com Agente de Bugs
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.bug-surgeon.ignore', $err) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full px-3 py-2 bg-zinc-800/50 text-zinc-600 text-[9px] font-black uppercase rounded-xl hover:text-zinc-400">Ignorar</button>
                                        </form>
                                    @elseif($incident->uiActionGroup() === 'analyzed')
                                        <button type="button" @click="openDrawer({{ $incident->id }})" class="px-3 py-2 bg-amber-500/10 text-amber-400 text-[9px] font-black uppercase rounded-xl border border-amber-500/20">
                                            Ver diagnóstico
                                        </button>
                                        <a href="{{ route('admin.bug-surgeon.show', $incident) }}" class="text-center px-3 py-2 bg-zinc-800 text-zinc-300 text-[9px] font-black uppercase rounded-xl">Análise completa</a>
                                    @elseif(in_array($incident->uiActionGroup(), ['patch_applied', 'homologated', 'in_progress']))
                                        <a href="{{ route('admin.bug-surgeon.show', $incident) }}" class="text-center px-3 py-2 bg-emerald-500/10 text-emerald-400 text-[9px] font-black uppercase rounded-xl border border-emerald-500/20">
                                            Acompanhar correção
                                        </a>
                                    @else
                                        <a href="{{ route('admin.bug-surgeon.show', $incident) }}" class="text-center px-3 py-2 bg-zinc-800 text-zinc-400 text-[9px] font-black uppercase rounded-xl">Ver histórico</a>
                                    @endif
                                </div>
                                <div id="detail-{{ $err->id }}" class="hidden">{!! json_encode([
                                    'message' => $err->message,
                                    'stack' => $err->stack_trace,
                                    'payload' => $err->payload,
                                    'ua' => $err->user_agent,
                                    'ip' => $err->ip,
                                ]) !!}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-20 text-center text-zinc-600">Zero ocorrências registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($systemErrors->hasPages())
            <div class="p-6 border-t border-white/5">{{ $systemErrors->links() }}</div>
        @endif
    </div>

    {{-- Painel lateral Agente de Bugs --}}
    <div x-show="drawerOpen" x-cloak class="fixed inset-0 z-[110] flex justify-end" @keydown.escape.window="drawerOpen = false">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="drawerOpen = false"></div>
        <div class="relative w-full max-w-md bg-zinc-900 border-l border-white/10 h-full overflow-y-auto shadow-2xl p-8 space-y-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-black text-violet-400 uppercase tracking-[0.3em]">Agente de Correção de Bugs</p>
                    <h3 class="text-xl font-black text-white font-mono mt-1" x-text="summary.incident_code || '—'"></h3>
                </div>
                <button type="button" @click="drawerOpen = false" class="text-zinc-500 hover:text-white"><i class="fas fa-times"></i></button>
            </div>

            <div class="grid grid-cols-2 gap-3 text-[10px]">
                <div class="bg-zinc-950 p-3 rounded-xl border border-white/5">
                    <span class="text-zinc-600 font-black uppercase block">Confiança</span>
                    <span class="text-white font-black text-lg" x-text="summary.confidence != null ? summary.confidence + '%' : '—'"></span>
                </div>
                <div class="bg-zinc-950 p-3 rounded-xl border border-white/5">
                    <span class="text-zinc-600 font-black uppercase block">Risco</span>
                    <span class="text-white font-black uppercase" x-text="summary.risk_level || 'n/d'"></span>
                </div>
            </div>

            <div class="bg-zinc-950/80 p-4 rounded-2xl border border-white/5 text-xs text-zinc-400 space-y-1">
                <p>Status: <strong class="text-white" x-text="summary.status"></strong></p>
                <p>Ocorrências: <strong class="text-white" x-text="summary.occurrence_count || 1"></strong></p>
                <template x-if="summary.context_pack_summary?.files">
                    <p>Arquivos no pack: <strong class="text-white" x-text="summary.context_pack_summary.files"></strong></p>
                </template>
                <template x-if="summary.context_pack_summary?.primary">
                    <p class="font-mono text-[10px] text-amber-400/80 break-all" x-text="summary.context_pack_summary.primary + (summary.context_pack_summary.method ? '::' + summary.context_pack_summary.method : '')"></p>
                </template>
                <template x-if="summary.auto_diagnosis?.awaiting">
                    <p class="text-violet-400 text-[10px] font-black uppercase">Diagnóstico IA em andamento…</p>
                </template>
                <template x-if="summary.auto_diagnosis?.completed_at">
                    <p class="text-emerald-400 text-[10px]">Diagnóstico IA: <span x-text="summary.auto_diagnosis.completed_at"></span></p>
                </template>
                <p x-show="summary.is_critical" class="text-red-400 font-black uppercase text-[10px] pt-2">⚠ Correção crítica — aprovação dupla</p>
            </div>

            <div x-show="(summary.checklist || []).length">
                <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-3">Agente analisando</p>
                <ul class="space-y-2">
                    <template x-for="item in summary.checklist || []" :key="item.key">
                        <li class="flex items-center gap-2 text-xs" :class="item.done ? 'text-emerald-400' : 'text-zinc-600'">
                            <span x-text="item.done ? '✓' : '○'"></span>
                            <span x-text="item.label"></span>
                        </li>
                    </template>
                </ul>
            </div>

            <div x-show="summary.cursor" class="space-y-3 pt-4 border-t border-white/5">
                <p class="text-[10px] font-black text-violet-400 uppercase tracking-[0.25em]">Exportar para Cursor</p>
                <template x-for="item in [
                    { key: 'cursor_prompt', label: 'Prompt Cursor' },
                    { key: 'fetch_context', label: 'Buscar contexto' },
                    { key: 'submit_analysis', label: 'Enviar análise' },
                ]" :key="item.key">
                    <div x-show="summary.cursor && summary.cursor[item.key]" class="bg-zinc-950 border border-white/5 rounded-xl p-3">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-[9px] font-black text-zinc-500 uppercase" x-text="item.label"></span>
                            <button type="button" @click="copyCursor(item.key)" class="text-[9px] font-black uppercase px-2 py-1 rounded-lg bg-violet-600/20 text-violet-300">Copiar</button>
                        </div>
                        <code class="block text-[10px] text-zinc-400 font-mono break-all" x-text="summary.cursor[item.key]"></code>
                    </div>
                </template>
                <p x-show="copied" class="text-[10px] text-emerald-400 font-black uppercase" x-text="'Copiado: ' + copied"></p>
                <button type="button" @click="syncCursor()" :disabled="syncing" class="w-full px-4 py-3 bg-violet-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-violet-500 disabled:opacity-50">
                    <span x-text="syncing ? 'Exportando…' : 'Exportar → .cursor/bug-surgeon/'"></span>
                </button>
                <p x-show="syncMessage" class="text-[10px] text-emerald-400" x-text="syncMessage"></p>
            </div>

            <div class="space-y-2 pt-4 border-t border-white/5">
                <a :href="summary.show_url" class="block w-full text-center px-4 py-3 bg-zinc-800 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-700">
                    Ver análise completa
                </a>
                <template x-if="summary.can_approve_patch">
                    <a :href="summary.show_url + '#aprovar'" class="block w-full text-center px-4 py-3 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500">
                        Aprovar correção
                    </a>
                </template>
            </div>
        </div>
    </div>
</div>

@include('admin.system_errors._detail_modal')

<script>
function bugSurgeonLogs() {
    return {
        drawerOpen: false,
        summary: {},
        syncing: false,
        syncMessage: '',
        copied: '',
        async openDrawer(id) {
            const res = await fetch(`/admin/bug-surgeon/${id}/summary`, { headers: { 'Accept': 'application/json' } });
            this.summary = await res.json();
            this.drawerOpen = true;
            this.syncMessage = '';
        },
        copyCursor(key) {
            const text = this.summary?.cursor?.[key];
            if (!text) return;
            navigator.clipboard.writeText(text);
            this.copied = key;
            setTimeout(() => this.copied = '', 2000);
        },
        async syncCursor() {
            if (!this.summary?.id) return;
            this.syncing = true;
            this.syncMessage = '';
            try {
                const res = await fetch(`/admin/bug-surgeon/${this.summary.id}/sync-cursor`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                });
                const data = await res.json();
                this.syncMessage = res.ok ? (data.message || 'Exportado.') : (data.message || 'Falha ao exportar.');
            } catch (e) {
                this.syncMessage = 'Falha ao exportar.';
            } finally {
                this.syncing = false;
            }
        }
    };
}
</script>
@endsection
