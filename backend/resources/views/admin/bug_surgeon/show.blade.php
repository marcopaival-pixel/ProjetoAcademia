@extends('layouts.admin')

@section('title', $incident->incident_code . ' | Bug Surgeon')

@section('content')
@php
    $stateColor = match($incident->state) {
        'WAITING_APPROVAL' => 'amber',
        'APPROVED', 'PATCH_APPLIED', 'TESTING' => 'emerald',
        'ROLLED_BACK' => 'red',
        'CLOSED' => 'zinc',
        default => 'blue',
    };
@endphp
<div class="space-y-8 animate-fade-in">
    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-6">
        <div>
            <a href="{{ route('admin.bug-surgeon.index') }}" class="text-[10px] font-black text-zinc-600 uppercase tracking-widest hover:text-white">&larr; Incidentes</a>
            <h2 class="text-2xl font-black text-white tracking-tight mt-2 font-mono">{{ $incident->incident_code }}</h2>
            <p class="text-zinc-500 text-sm mt-1">{{ $incident->error_summary }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <span class="px-4 py-2 bg-{{ $stateColor }}-500/10 text-{{ $stateColor }}-400 text-[10px] font-black uppercase rounded-xl border border-{{ $stateColor }}-500/20">
                {{ $incident->statusLabel() }}
            </span>
            @if($incident->occurrence_count > 1)
                <span class="px-4 py-2 bg-zinc-900 text-zinc-400 text-[10px] font-black uppercase rounded-xl border border-white/10">
                    {{ $incident->occurrence_count }} ocorrências
                </span>
            @endif
            @if($incident->confidence !== null)
                <span class="px-4 py-2 bg-zinc-900 text-white text-[10px] font-black uppercase rounded-xl border border-white/10">
                    Confiança {{ $incident->confidence }}%
                </span>
            @endif
            <span class="px-4 py-2 bg-zinc-900 text-zinc-400 text-[10px] font-black uppercase rounded-xl border border-white/10">
                Risco: {{ $incident->risk_level ?? 'n/d' }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-sm font-bold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-sm font-bold">{{ session('error') }}</div>
    @endif

    {{-- Painel de aprovação --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            @if($incident->analysis_checklist)
            <div class="bg-violet-500/5 border border-violet-500/20 rounded-[2rem] p-8">
                <h3 class="text-lg font-black text-white mb-4">Agente analisando</h3>
                <ul class="space-y-2">
                    @foreach($incident->analysis_checklist as $item)
                        <li class="flex items-center gap-2 text-sm {{ ($item['done'] ?? false) ? 'text-emerald-400' : 'text-zinc-600' }}">
                            <span>{{ ($item['done'] ?? false) ? '✓' : '○' }}</span>
                            {{ $item['label'] ?? '' }}
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if($incident->diagnosis || $incident->root_cause || $incident->file_path)
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8">
                <h3 class="text-lg font-black text-white mb-4">Diagnóstico do agente</h3>
                @if($incident->diagnosis)<p class="text-zinc-300 text-sm mb-3">{{ $incident->diagnosis }}</p>@endif
                @if($incident->root_cause)<p class="text-zinc-500 text-sm mb-3"><strong class="text-zinc-400">Causa:</strong> {{ $incident->root_cause }}</p>@endif
                @if($incident->file_path)
                    <p class="text-[10px] font-mono text-amber-400">{{ $incident->file_path }}@if($incident->line_start):{{ $incident->line_start }}@endif @if($incident->method_name)· {{ $incident->method_name }}()@endif</p>
                @endif
                @if($incident->error_reproduced !== null)
                    <p class="text-[10px] text-zinc-500 mt-2">Erro reproduzido: {{ $incident->error_reproduced ? 'Sim' : 'Não' }}</p>
                @endif
            </div>
            @endif

            {{-- Evidências --}}
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8">
                <h3 class="text-lg font-black text-white mb-4">Evidências</h3>
                @if($incident->stack_trace)
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-2">Stack trace</label>
                    <pre class="bg-zinc-950 p-4 rounded-2xl border border-white/5 text-[10px] font-mono text-zinc-500 overflow-auto max-h-48 mb-4">{{ $incident->stack_trace }}</pre>
                @endif
                @if($incident->evidence)
                    <pre class="bg-zinc-950 p-4 rounded-2xl border border-white/5 text-[10px] font-mono text-blue-400 overflow-auto">{{ json_encode($incident->evidence, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <p class="text-zinc-600 text-sm italic">Sem evidências estruturadas.</p>
                @endif
            </div>

            {{-- Impacto --}}
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8">
                <h3 class="text-lg font-black text-white mb-4">Análise de impacto</h3>
                @if($incident->impact_analysis)
                    <pre class="bg-zinc-950 p-4 rounded-2xl border border-white/5 text-[10px] font-mono text-emerald-400 overflow-auto">{{ json_encode($incident->impact_analysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <p class="text-zinc-600 text-sm italic">Preencha via formulário abaixo (JSON) ou pelo agente Cursor.</p>
                @endif
            </div>

            {{-- Diff --}}
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8">
                <h3 class="text-lg font-black text-white mb-4">Diff proposto</h3>
                @if($incident->code_snippet)
                    <label class="text-[10px] font-black text-zinc-600 uppercase tracking-widest block mb-2">Trecho identificado</label>
                    <pre class="bg-zinc-950 p-4 rounded-2xl border border-white/5 text-[10px] font-mono text-zinc-400 overflow-auto mb-4">{{ $incident->code_snippet }}</pre>
                @endif
                @if($incident->proposed_diff)
                    <pre class="bg-zinc-950 p-4 rounded-2xl border border-amber-500/20 text-[10px] font-mono text-amber-300 overflow-auto">{{ $incident->proposed_diff }}</pre>
                    @if($incident->approved_change_hash)
                        <p class="text-[10px] text-zinc-600 font-mono mt-2">Hash: {{ $incident->approved_change_hash }}</p>
                    @endif
                @else
                    <p class="text-zinc-600 text-sm italic">Nenhum patch proposto ainda.</p>
                @endif
            </div>

            {{-- Formulário de análise --}}
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-8">
                <h3 class="text-lg font-black text-white mb-6">Atualizar análise</h3>
                <form action="{{ route('admin.bug-surgeon.analysis', $incident) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Módulo</label>
                            <input type="text" name="module" value="{{ old('module', $incident->module) }}" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Perfil afetado</label>
                            <input type="text" name="affected_role" value="{{ old('affected_role', $incident->affected_role) }}" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm" placeholder="aluno_independente">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Diagnóstico (problema)</label>
                        <textarea name="diagnosis" rows="2" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm">{{ old('diagnosis', $incident->diagnosis) }}</textarea>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Causa raiz</label>
                        <textarea name="root_cause" rows="2" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm">{{ old('root_cause', $incident->root_cause) }}</textarea>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-zinc-400 mb-2">
                        <input type="checkbox" name="error_reproduced" value="1" @checked(old('error_reproduced', $incident->error_reproduced)) class="rounded bg-zinc-950 border-white/20">
                        Erro reproduzido em teste
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Arquivo</label>
                            <input type="text" name="file_path" value="{{ old('file_path', $incident->file_path) }}" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm font-mono" placeholder="backend/app/Services/Example.php">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Linha ini.</label>
                                <input type="number" name="line_start" value="{{ old('line_start', $incident->line_start) }}" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm">
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Linha fim</label>
                                <input type="number" name="line_end" value="{{ old('line_end', $incident->line_end) }}" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm">
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Confiança (%)</label>
                            <input type="number" name="confidence" min="0" max="100" value="{{ old('confidence', $incident->confidence) }}" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Risco</label>
                            <select name="risk_level" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm">
                                <option value="">—</option>
                                @foreach(['low','medium','high'] as $r)
                                    <option value="{{ $r }}" @selected(old('risk_level', $incident->risk_level) === $r)>{{ $r }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Trecho de código</label>
                        <textarea name="code_snippet" rows="3" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-white text-sm font-mono">{{ old('code_snippet', $incident->code_snippet) }}</textarea>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Diff proposto</label>
                        <textarea name="proposed_diff" rows="5" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-amber-300 text-sm font-mono">{{ old('proposed_diff', $incident->proposed_diff) }}</textarea>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Evidências (JSON array)</label>
                        <textarea name="evidence_json" rows="3" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-blue-400 text-sm font-mono">{{ old('evidence_json', $incident->evidence ? json_encode($incident->evidence, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest block mb-2">Impacto (JSON object)</label>
                        <textarea name="impact_json" rows="3" class="w-full bg-zinc-950 border border-white/10 rounded-xl px-4 py-3 text-emerald-400 text-sm font-mono">{{ old('impact_json', $incident->impact_analysis ? json_encode($incident->impact_analysis, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
                    </div>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <button type="submit" name="submit_action" value="save" class="px-6 py-3 bg-zinc-800 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-700">Salvar</button>
                        <button type="submit" name="submit_action" value="submit_for_approval" class="px-6 py-3 bg-amber-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-500">Enviar para aprovação</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar ações --}}
        <div class="space-y-6">
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-6 space-y-4">
                <h3 class="text-sm font-black text-white uppercase tracking-widest">Metadados</h3>
                <dl class="space-y-2 text-xs">
                    <div><dt class="text-zinc-600 font-black uppercase text-[9px]">Rota</dt><dd class="text-zinc-300 font-mono">{{ $incident->route_path ?? '—' }}</dd></div>
                    <div><dt class="text-zinc-600 font-black uppercase text-[9px]">Usuário</dt><dd class="text-zinc-300">{{ $incident->affectedUser?->name ?? '—' }}</dd></div>
                    <div><dt class="text-zinc-600 font-black uppercase text-[9px]">Branch</dt><dd class="text-zinc-300 font-mono">{{ $incident->branch_name ?? '—' }}</dd></div>
                    <div><dt class="text-zinc-600 font-black uppercase text-[9px]">Commit</dt><dd class="text-zinc-300 font-mono">{{ $incident->commit_hash ?? '—' }}</dd></div>
                </dl>
            </div>

            <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-6 space-y-3" id="aprovar">
                <h3 class="text-sm font-black text-white uppercase tracking-widest">Aprovações</h3>
                <div class="flex flex-wrap gap-2 text-[9px] font-black uppercase">
                    <span class="px-2 py-1 rounded-lg {{ $incident->approval_patch ? 'bg-emerald-500/20 text-emerald-400' : 'bg-zinc-800 text-zinc-600' }}">Correção</span>
                    <span class="px-2 py-1 rounded-lg {{ $incident->approval_staging ? 'bg-emerald-500/20 text-emerald-400' : 'bg-zinc-800 text-zinc-600' }}">Staging</span>
                    <span class="px-2 py-1 rounded-lg {{ $incident->approval_production ? 'bg-emerald-500/20 text-emerald-400' : 'bg-zinc-800 text-zinc-600' }}">Produção</span>
                </div>

                @if($incident->canApprovePatch())
                    <form action="{{ route('admin.bug-surgeon.approve-patch', $incident) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-500">Aprovar correção</button>
                    </form>
                    <form action="{{ route('admin.bug-surgeon.reject-patch', $incident) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-zinc-800 text-zinc-400 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-700">Rejeitar</button>
                    </form>
                    <form action="{{ route('admin.bug-surgeon.reanalysis', $incident) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-blue-600/20 text-blue-400 border border-blue-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600/30">Solicitar nova análise</button>
                    </form>
                @endif

                @if($incident->approval_patch)
                    <a href="{{ route('admin.bug-surgeon.export.authorization', $incident) }}" class="block w-full text-center px-4 py-3 bg-amber-600/20 text-amber-400 border border-amber-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-600/30">
                        Exportar authorization.json
                    </a>
                    <a href="{{ route('admin.bug-surgeon.export.session', $incident) }}" class="block w-full text-center px-4 py-3 bg-zinc-800 text-zinc-300 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-zinc-700">
                        Exportar session.json
                    </a>
                    <form action="{{ route('admin.bug-surgeon.sync-cursor', $incident) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-violet-600/20 text-violet-300 border border-violet-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-violet-600/30">
                            Sync → .cursor/bug-surgeon/
                        </button>
                    </form>
                @endif

                @if($incident->state === 'TESTING')
                    <form action="{{ route('admin.bug-surgeon.ci-passed', $incident) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-cyan-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-cyan-500">
                            Marcar CI aprovado
                        </button>
                    </form>
                @endif

                @if($incident->approval_patch && in_array($incident->state, ['READY_FOR_STAGING', 'WAITING_DEPLOY_APPROVAL']))
                    <form action="{{ route('admin.bug-surgeon.approve-staging', $incident) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-500" @disabled($incident->approval_staging)>Aprovar staging</button>
                    </form>
                @endif

                @if($incident->approval_staging && $incident->state === 'WAITING_DEPLOY_APPROVAL')
                    <form action="{{ route('admin.bug-surgeon.approve-production', $incident) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-500" @disabled($incident->approval_production)>Aprovar produção</button>
                    </form>
                @endif

                @if($incident->commit_hash || in_array($incident->state, ['DEPLOYED', 'MONITORING', 'CLOSED']))
                    <form action="{{ route('admin.bug-surgeon.rollback', $incident) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-3 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-500/20">Executar rollback</button>
                    </form>
                    @if($incident->rollback_command)
                        <code class="block text-[10px] text-zinc-500 font-mono p-3 bg-zinc-950 rounded-xl">{{ $incident->rollback_command }}</code>
                    @endif
                @endif
            </div>

            @if($incident->approval_patch)
            <div class="bg-zinc-900/40 border border-white/5 rounded-[2rem] p-6">
                <h3 class="text-sm font-black text-white uppercase tracking-widest mb-4">Registrar commit</h3>
                <form action="{{ route('admin.bug-surgeon.commit', $incident) }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="commit_hash" placeholder="7a93fd2" required class="flex-1 bg-zinc-950 border border-white/10 rounded-xl px-4 py-2 text-white text-sm font-mono">
                    <button type="submit" class="px-4 py-2 bg-zinc-800 text-white rounded-xl text-[10px] font-black uppercase">OK</button>
                </form>
            </div>
            @endif

            <div class="bg-zinc-950/50 border border-white/5 rounded-[2rem] p-6 space-y-4">
                @include('admin.bug_surgeon._cursor_export', ['cursor' => $cursor])
                <form action="{{ route('admin.bug-surgeon.sync-cursor', $incident) }}" method="POST" class="pt-2 border-t border-white/5">
                    @csrf
                    <button type="submit" class="w-full px-4 py-3 bg-violet-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-violet-500">
                        Exportar arquivos → .cursor/bug-surgeon/
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
