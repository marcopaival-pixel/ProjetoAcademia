@extends('layouts.admin')

@section('title', 'Painel de Controlo')

@section('content')
    <div class="stats-grid">
        <div class="stat-card" style="border-left: 4px solid var(--accent);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div class="stat-label">Faturamento Total (BRL)</div>
                <a href="{{ route('admin.export.payments') }}" title="Baixar CSV" style="text-decoration: none; font-size: 1.2rem;">📥</a>
            </div>
            <div class="stat-value">R$ {{ number_format($metrics['total_revenue'], 2, ',', '.') }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Créditos Mercado Pago</div>
        </div>
        <div class="stat-card" style="border-left: 4px solid #388bfd;">
            <div class="stat-label">Subscrições Ativas</div>
            <div class="stat-value">{{ $metrics['active_subs'] }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Planos Recorrentes</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Usuários</div>
            <div class="stat-value">{{ $metrics['total_users'] }}</div>
            <div style="font-size: 0.75rem; color: #3fb950; margin-top: 0.5rem;">+{{ $overview['new_users_7d'] }} nos últimos 7 dias</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Premium Ativo</div>
            <div class="stat-value">{{ $metrics['total_premium'] }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">{{ round(($metrics['total_premium'] / max(1, $metrics['total_users'])) * 100, 1) }}% de conversão</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card">
            <h2 style="margin-top: 0; font-size: 1.125rem;">Atividade Recente</h2>
            <table>
                <thead>
                    <tr>
                        <th>Ação</th>
                        <th>Usuário</th>
                        <th>Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($metrics['recent_logs'] as $log)
                        <tr>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->user?->name ?? 'Sistema' }}</td>
                            <td style="color: var(--text-muted);">{{ $log->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align: center; padding: 2rem;">Nenhuma atividade.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2 style="margin-top: 0; font-size: 1.125rem;">Objetivos dos Alunos</h2>
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1.5rem;">
                @php
                    $goalLabels = [
                        'maintain' => 'Manter Peso',
                        'lose' => 'Emagrecer',
                        'gain' => 'Ganhar Massa',
                    ];
                    $maxGoal = $metrics['goals']->max('total') ?: 1;
                @endphp
                @foreach($metrics['goals'] as $g)
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; margin-bottom: 0.4rem;">
                            <span>{{ $goalLabels[$g->goal] ?? ucfirst($g->goal) }}</span>
                            <span style="font-weight: 600;">{{ $g->total }}</span>
                        </div>
                        <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.05); border-radius: 4px; overflow: hidden;">
                            <div style="width: {{ ($g->total / $maxGoal) * 100 }}%; height: 100%; background: var(--accent);"></div>
                        </div>
                    </div>
                @endforeach
                @if($metrics['goals']->isEmpty())
                    <p style="text-align:center; color:var(--text-muted);">Sem dados de objetivo.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <h2 style="margin-top: 0; font-size: 1.125rem;">Últimos Utilizadores Registados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Subscrição</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metrics['recent_users'] as $user)
                    <tr>
                        <td class="tabular-nums">#{{ $user->id }}</td>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->is_premium)
                                <span class="badge badge-success">Premium</span>
                            @else
                                <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-muted);">Grátis</span>
                            @endif
                        </td>
                        <td style="color: var(--text-muted);">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
