@extends('layouts.admin')

@section('title', 'Controle Operacional e Resiliência')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h4 font-weight-bold text-white mb-0">Controle Operacional e Resiliência</h2>
            <p class="text-muted">Monitoramento em tempo real, modos de manutenção e integridade do sistema.</p>
        </div>
    </div>

    <!-- System Health Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-dark border-secondary mb-3">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Status Geral</h6>
                    <div class="d-flex align-items-center">
                        <span class="badge badge-pill {{ $health['status'] === 'healthy' ? 'bg-success' : ($health['status'] === 'critical' ? 'bg-danger' : 'bg-warning') }} mr-2">
                            {{ strtoupper($health['status']) }}
                        </span>
                    </div>
                    <small class="text-muted">Última checagem: {{ now()->format('H:i:s') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-secondary mb-3">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Banco de Dados</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-database mr-2 {{ $health['database']['status'] === 'ok' ? 'text-success' : 'text-danger' }}"></i>
                        <span>{{ $health['database']['message'] }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-secondary mb-3">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Fila (Queue)</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tasks mr-2 {{ $health['queue']['status'] === 'ok' ? 'text-success' : 'text-danger' }}"></i>
                        <span>{{ $health['queue']['status'] === 'ok' ? 'Ativa' : 'Parada' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-dark border-secondary mb-3">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Tempo de Resposta</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bolt mr-2 text-warning"></i>
                        <span>{{ $health['response_time'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Infrastructure Metrics -->
        <div class="col-md-8">
            <div class="card bg-dark border-secondary mb-4">
                <div class="card-header border-secondary d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Métricas de Infraestrutura</h5>
                    <a href="{{ route('admin.operations.index') }}" class="btn btn-sm btn-outline-light">Atualizar</a>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <label class="d-block text-muted mb-2">CPU (Load 1m)</label>
                            <h3 class="{{ $health['cpu']['status'] === 'ok' ? 'text-success' : 'text-warning' }}">
                                {{ $health['cpu']['load_1m'] ?? 'N/A' }}
                            </h3>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="d-block text-muted mb-2">Memória (Uso)</label>
                            <h3 class="{{ $health['memory']['status'] === 'ok' ? 'text-success' : 'text-warning' }}">
                                {{ $health['memory']['used_percent'] ?? 'N/A' }}%
                            </h3>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="d-block text-muted mb-2">Disco (Ocupado)</label>
                            <h3 class="{{ $health['disk']['status'] === 'ok' ? 'text-success' : 'text-danger' }}">
                                {{ $health['disk']['used_percent'] }}%
                            </h3>
                            <small class="text-muted">Livre: {{ $health['disk']['free'] }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Health Logs / History (Placeholder) -->
            <div class="card bg-dark border-secondary">
                <div class="card-header border-secondary">
                    <h5 class="mb-0">Eventos Recentes</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-dark table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Evento</th>
                                <th>Componente</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-muted">{{ now()->format('d/m H:i') }}</td>
                                <td>Health Check Automático</td>
                                <td>Sistema</td>
                                <td><span class="badge bg-success">OK</span></td>
                            </tr>
                            <!-- In a real app, this would be a loop over a database table -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Operational Controls -->
        <div class="col-md-4">
            <div class="card bg-dark border-secondary mb-4">
                <div class="card-header border-secondary">
                    <h5 class="mb-0">Modos de Operação</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.operations.update') }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-4">
                            <label class="text-muted">Modo de Manutenção</label>
                            <select name="maintenance_mode" class="form-control bg-dark border-secondary text-white">
                                <option value="off" {{ $settings['maintenance_mode'] === 'off' ? 'selected' : '' }}>Desativado (Normal)</option>
                                <option value="operable" {{ $settings['maintenance_mode'] === 'operable' ? 'selected' : '' }}>Operável (Apenas Admins)</option>
                                <option value="total" {{ $settings['maintenance_mode'] === 'total' ? 'selected' : '' }}>Total (Ninguém acessa)</option>
                            </select>
                            <small class="form-text text-muted">Em modo operável, apenas usuários com perfil administrador podem navegar.</small>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-muted">Mensagem de Manutenção</label>
                            <textarea name="maintenance_message" class="form-control bg-dark border-secondary text-white" rows="2">{{ $settings['maintenance_message'] }}</textarea>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-muted">Modo Somente Leitura</label>
                            <select name="read_only_mode" class="form-control bg-dark border-secondary text-white">
                                <option value="0" {{ !$settings['read_only_mode'] ? 'selected' : '' }}>Desativado (Escrita permitida)</option>
                                <option value="1" {{ $settings['read_only_mode'] ? 'selected' : '' }}>Ativado (Apenas leitura)</option>
                            </select>
                            <small class="form-text text-muted">Bloqueia todas as operações de criação, edição e exclusão de dados.</small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Salvar Configurações</button>
                    </form>
                </div>
                <div class="card-footer border-secondary">
                    <small class="text-muted">Última atualização: {{ \Carbon\Carbon::parse($settings['last_updated'])->format('d/m/Y H:i') }}</small>
                </div>
            </div>

            <!-- Auto-recovery Options -->
            <div class="card bg-dark border-secondary">
                <div class="card-header border-secondary">
                    <h5 class="mb-0">Ações de Recuperação</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-outline-warning btn-block mb-2" onclick="confirm('Reiniciar fila?')">
                        <i class="fas fa-sync-alt mr-2"></i> Reiniciar Queue Workers
                    </button>
                    <button class="btn btn-outline-danger btn-block" onclick="confirm('Limpar caches?')">
                        <i class="fas fa-trash-alt mr-2"></i> Limpar Todos os Caches
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .card { border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
    .bg-dark { background-color: #1e293b !important; }
    .border-secondary { border-color: #334155 !important; }
    .badge { padding: 0.5em 1em; font-weight: 600; }
    .form-control:focus { background-color: #0f172a; border-color: #6366f1; color: white; }
</style>
@endpush
