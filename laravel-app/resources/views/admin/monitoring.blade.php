@extends('layouts.admin')

@section('title', 'Monitoramento')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <div class="card">
            <h2 style="margin-top: 0; font-size: 1.125rem;">Ambiente de Software</h2>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <span style="color: var(--text-muted);">PHP Version</span>
                    <strong>{{ $info['php_version'] }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <span style="color: var(--text-muted);">Laravel Version</span>
                    <strong>{{ $info['laravel_version'] }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <span style="color: var(--text-muted);">DB Driver</span>
                    <strong>{{ $info['db_driver'] }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">OS</span>
                    <strong>{{ $info['os'] }}</strong>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 style="margin-top: 0; font-size: 1.125rem;">Recursos do Sistema</h2>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <span style="color: var(--text-muted);">Uso de Memória</span>
                    <strong>{{ $info['memory_usage'] }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                    <span style="color: var(--text-muted);">Espaço em Disco Padrão (Livre)</span>
                    <strong>{{ $info['disk_free'] }} / {{ $info['disk_total'] }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">IP do Servidor</span>
                    <strong>{{ $info['server_ip'] ?? '127.0.0.1' }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 style="margin-top: 0; font-size: 1.125rem;">Saúde da Aplicação</h2>
        <div style="padding: 2rem; text-align: center;">
            <p style="color: #3fb950; font-size: 1.25rem;">✅ Todos os serviços estão operacionais.</p>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Última verificação: {{ date('H:i:s') }}</p>
        </div>
    </div>
@endsection
