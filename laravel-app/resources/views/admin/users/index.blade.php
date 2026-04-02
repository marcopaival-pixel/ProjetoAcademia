@extends('layouts.admin')

@section('title', 'Gerenciamento de Usuários')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Contas Registradas</div>
            <div class="stat-value tabular-nums">{{ $overview['total_users'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Novos (7 dias)</div>
            <div class="stat-value tabular-nums">{{ $overview['new_users_7d'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Diário Ativo (7 dias)</div>
            <div class="stat-value tabular-nums">{{ $overview['distinct_food_loggers_7d'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Assinaturas Premium</div>
            <div class="stat-value tabular-nums">{{ $overview['premium_subscriptions_active'] }}</div>
        </div>
    </div>

    <div class="card" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="margin:0;">Lista de Usuários</h2>
        <a href="{{ route('admin.export.users') }}" class="btn" style="border: 1px solid var(--accent); color: var(--accent); background: transparent;">📥 Exportar CSV</a>
    </div>

    <div class="card">
        <div class="table-wrap" style="overflow-x: auto;">
            <table>
                <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Premium</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td class="tabular-nums">{{ $u->id }}</td>
                                <td>
                                    <strong>{{ $u->name }}</strong>
                                    @if($u->is_admin)
                                        <span class="badge badge-warning">Admin</span>
                                    @endif
                                </td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @if($u->email_verified_at)
                                        <span class="badge badge-success">Verificado</span>
                                    @else
                                        <span class="badge badge-info">Pendente</span>
                                    @endif
                                </td>
                                <td>{{ $u->is_premium ? 'Ativo' : '—' }}</td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $u->id) }}" class="btn" style="padding: 0.25rem 0.75rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); font-size: 0.75rem; color: var(--text-main);">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
        
        @if ($users->hasPages())
            <nav style="margin-top: 1.5rem; display: flex; gap: 0.5rem; justify-content: flex-end;">
                @if ($users->onFirstPage())
                    <button class="btn" disabled>Anterior</button>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="btn btn-primary">Anterior</a>
                @endif

                @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="btn btn-primary">Próximo</a>
                @else
                    <button class="btn" disabled>Próximo</button>
                @endif
            </nav>
        @endif
    </div>
@endsection
