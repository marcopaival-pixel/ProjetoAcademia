@extends('layouts.admin')

@section('title', 'Deploy & Versões')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Deploy & Versões</h1>
            <p class="text-zinc-500 dark:text-zinc-400">Histórico de publicações, homologação e versão atual do sistema.</p>
        </div>
        <div class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">
            CLI: <code class="text-emerald-600">php artisan app:deploy:checklist --target=production</code> ·
            <code class="text-emerald-600">php artisan app:audit:tenant</code>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-700 dark:text-emerald-300 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-700 dark:text-red-300 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-2xl shadow-sm">
            <span class="text-sm text-zinc-500">Versão atual</span>
            <h3 class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $currentVersion }}</h3>
            <p class="text-xs text-zinc-500 mt-2">Laravel {{ $laravelVersion }}</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-2xl shadow-sm">
            <span class="text-sm text-zinc-500">Próxima minor (sugestão)</span>
            <h3 class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">{{ $nextVersionHint }}</h3>
            <p class="text-xs text-zinc-500 mt-2">Use <code class="text-amber-600">php artisan app:version --minor</code></p>
        </div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-2xl shadow-sm">
            <span class="text-sm text-zinc-500">Último deploy produção</span>
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mt-1">
                @if($lastProduction)
                    v{{ $lastProduction->version }} · {{ $lastProduction->deployed_at?->format('d/m/Y H:i') }}
                @else
                    —
                @endif
            </h3>
        </div>
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 rounded-2xl shadow-sm">
            <span class="text-sm text-zinc-500">Homologação</span>
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mt-1">
                @if($homologPending)
                    Pendente v{{ $homologPending->version }}
                @elseif($lastHomolog)
                    {{ $lastHomolog->homolog_status === 'approved' ? 'Aprovada' : ($lastHomolog->homolog_status ?? '—') }} v{{ $lastHomolog->version }}
                @else
                    —
                @endif
            </h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm p-6">
            <h3 class="font-semibold text-zinc-900 dark:text-white mb-4">Registrar release</h3>
            <form action="{{ route('admin.deploy.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-zinc-500 mb-1">Versão</label>
                        <input type="text" name="version" value="{{ ltrim($currentVersion, 'v') }}" required
                            class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-zinc-500 mb-1">Ambiente</label>
                        <select name="environment" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                            <option value="homologacao">Homologação</option>
                            <option value="production">Produção</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-zinc-500 mb-1">Status</label>
                        <select name="status" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                            <option value="success">Sucesso</option>
                            <option value="failed">Falhou</option>
                            <option value="in_progress">Em andamento</option>
                            <option value="pending">Pendente</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-zinc-500 mb-1">Homolog (opcional)</label>
                        <select name="homolog_status" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                            <option value="">—</option>
                            <option value="pending">Pendente</option>
                            <option value="approved">Aprovado</option>
                            <option value="rejected">Rejeitado</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-zinc-500 mb-1">Impacto</label>
                        <select name="impact_level" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                            <option value="low">Baixo</option>
                            <option value="medium" selected>Médio</option>
                            <option value="high">Alto</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-zinc-500 mb-1">Risco</label>
                        <select name="risk_level" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                            <option value="low" selected>Baixo</option>
                            <option value="medium">Médio</option>
                            <option value="high">Alto</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-zinc-500 mb-1">Branch Git</label>
                        <input type="text" name="git_branch" placeholder="homologacao / main"
                            class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-zinc-500 mb-1">Commit</label>
                        <input type="text" name="git_commit" maxlength="64"
                            class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-zinc-500 mb-1">Notas</label>
                    <textarea name="notes" rows="3" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white"></textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold">
                    Registrar deploy
                </button>
            </form>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-zinc-200 dark:border-zinc-800">
                <h3 class="font-semibold text-zinc-900 dark:text-white">Falhas recentes</h3>
            </div>
            <ul class="divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                @forelse($failures as $fail)
                    <li class="px-5 py-3">
                        <span class="font-medium text-red-600">v{{ $fail->version }}</span>
                        <span class="text-zinc-500"> · {{ $fail->environmentLabel() }}</span>
                        <p class="text-zinc-600 dark:text-zinc-400 mt-1 truncate">{{ $fail->failure_message ?? $fail->notes ?? 'Sem detalhe' }}</p>
                    </li>
                @empty
                    <li class="px-5 py-6 text-zinc-500">Nenhuma falha registrada.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-zinc-200 dark:border-zinc-800">
            <h3 class="font-semibold text-zinc-900 dark:text-white">Histórico de releases</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-950 text-zinc-500 text-xs uppercase">
                    <tr>
                        <th class="px-6 py-3">Versão</th>
                        <th class="px-6 py-3">Ambiente</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Homolog</th>
                        <th class="px-6 py-3">Impacto / Risco</th>
                        <th class="px-6 py-3">Data</th>
                        <th class="px-6 py-3">Por</th>
                        <th class="px-6 py-3 text-right">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                    @foreach($releases as $release)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                        <td class="px-6 py-3 font-medium">
                            v{{ $release->version }}
                            @if($release->is_current)<span class="ml-1 text-[10px] uppercase text-emerald-600 font-bold">atual</span>@endif
                        </td>
                        <td class="px-6 py-3">{{ $release->environmentLabel() }}</td>
                        <td class="px-6 py-3">{{ $release->statusLabel() }}</td>
                        <td class="px-6 py-3">{{ $release->homolog_status ?? '—' }}</td>
                        <td class="px-6 py-3">{{ $release->impact_level }} / {{ $release->risk_level }}</td>
                        <td class="px-6 py-3">{{ $release->deployed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="px-6 py-3">{{ $release->deployer?->name ?? '—' }}</td>
                        <td class="px-6 py-3 text-right">
                            @if($release->environment === 'homologacao' && $release->homolog_status === 'pending')
                            <form action="{{ route('admin.deploy.homolog', $release) }}" method="POST" class="inline-flex gap-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="homolog_status" value="approved" />
                                <button type="submit" class="text-xs px-2 py-1 rounded bg-emerald-600/20 text-emerald-700 dark:text-emerald-300">Aprovar</button>
                            </form>
                            <form action="{{ route('admin.deploy.homolog', $release) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="homolog_status" value="rejected" />
                                <button type="submit" class="text-xs px-2 py-1 rounded bg-red-600/20 text-red-700 dark:text-red-300">Rejeitar</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
