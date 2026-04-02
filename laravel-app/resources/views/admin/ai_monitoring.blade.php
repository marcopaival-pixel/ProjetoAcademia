@extends('layouts.admin')

@section('title', 'Monitoramento da IA')

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total de Mensagens</div>
            <div class="stat-value">{{ $totalMessagesCount }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Desde o início</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Mensagens Hoje</div>
            <div class="stat-value">{{ $todayMessagesCount }}</div>
            <div style="font-size: 0.75rem; color: {{ $todayMessagesCount >= $yesterdayMessagesCount ? '#3fb950' : '#f85149' }}; margin-top: 0.5rem;">
                {{ $todayMessagesCount >= $yesterdayMessagesCount ? '↑' : '↓' }} vs Ontem ({{ $yesterdayMessagesCount }})
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Estimativa de Tokens</div>
            <div class="stat-value">{{ number_format($estimatedTokens, 0, ',', '.') }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Baseado em caracteres</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Custo Est. (USD)</div>
            <div class="stat-value">${{ number_format(($estimatedTokens / 1000000) * 0.5, 4) }}</div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Ref: GPT-4o mini</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card">
            <h2 style="margin-top: 0; font-size: 1.125rem;">Ranking de Utilizadores</h2>
            <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">Utilizadores que mais enviam mensagens.</p>
            
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach($topUsers as $top)
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem;">
                        <div>
                            <div style="font-weight: 600;">{{ $top->name }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $top->email }}</div>
                        </div>
                        <div class="badge badge-info" style="font-size: 0.875rem;">{{ $top->total }} mgs</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card">
            <h2 style="margin-top: 0; font-size: 1.125rem;">Diálogo Recente</h2>
            <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-top: 1.5rem;">
                @foreach($recentChats as $user)
                    @php($lastMsg = $user->aiChats->first())
                    <div style="padding: 1rem; background: rgba(255,255,255,0.03); border-radius: 8px; border-left: 3px solid var(--accent);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.75rem;">
                            <span style="font-weight: 700; color: var(--text-main);">{{ $user->name }}</span>
                            <span style="color: var(--text-muted);">{{ $lastMsg->created_at->diffForHumans() }}</span>
                        </div>
                        <p style="margin: 0; font-size: 0.875rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            "{{ $lastMsg->message }}"
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
