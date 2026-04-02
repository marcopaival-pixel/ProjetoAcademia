@extends('layouts.admin')

@section('title', 'Logs do Sistema')

@section('content')
    <div class="card">
        <h2 style="margin-top: 0;">Atividades Administrativas</h2>
        <div class="table-wrap" style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Usuário</th>
                        <th>Ação</th>
                        <th>IP</th>
                        <th>Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="tabular-nums">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $log->user?->name ?? 'Sistema' }}</td>
                            <td>{{ $log->action }}</td>
                            <td class="tabular-nums">{{ $log->ip_address }}</td>
                            <td>
                                @if($log->payload)
                                    <pre style="font-size: 0.75rem; background: rgba(255,255,255,0.05); padding: 0.5rem; border-radius: 4px; overflow: auto; max-width: 300px;">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:3rem; color:var(--text-muted);">
                                Nenhum log registrado até o momento.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 1.5rem;">
            {{ $logs->links() }}
        </div>
    </div>
@endsection
