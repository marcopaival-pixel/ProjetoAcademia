@extends('layouts.admin')

@section('title', 'Gestão de Códigos de Indicação')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 font-weight-bold mb-0 text-gray-800">Códigos de Indicação</h2>
    </div>

    <!-- Indicadores -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Códigos Gerados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalCodes }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-barcode fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Códigos Utilizados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $usedCodes }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Códigos Expirados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expiredCodes }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Taxa de Conversão</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $conversionRate }}%</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-percentage fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtrar Códigos</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.referral-codes.index') }}" method="GET" class="row">
                <div class="col-md-3 mb-3">
                    <label for="code">Código</label>
                    <input type="text" name="code" id="code" class="form-control" value="{{ request('code') }}" placeholder="Ex: REP-JOAO-001">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Todos</option>
                        <option value="DISPONIVEL" {{ request('status') == 'DISPONIVEL' ? 'selected' : '' }}>Disponível</option>
                        <option value="RESERVADO" {{ request('status') == 'RESERVADO' ? 'selected' : '' }}>Reservado</option>
                        <option value="UTILIZADO" {{ request('status') == 'UTILIZADO' ? 'selected' : '' }}>Utilizado</option>
                        <option value="EXPIRADO" {{ request('status') == 'EXPIRADO' ? 'selected' : '' }}>Expirado</option>
                        <option value="CANCELADO" {{ request('status') == 'CANCELADO' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3 align-self-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
                    <a href="{{ route('admin.referral-codes.index') }}" class="btn btn-secondary">Limpar</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Todos os Códigos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Representante</th>
                            <th>Proposta</th>
                            <th>Status</th>
                            <th>Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($codes as $code)
                        <tr>
                            <td><strong>{{ $code->code }}</strong></td>
                            <td>{{ $code->representative->name ?? 'N/A' }}</td>
                            <td>{{ $code->commercialProposal->id ?? '-' }}</td>
                            <td>
                                @if($code->status === 'DISPONIVEL')
                                    <span class="badge bg-success">Disponível</span>
                                @elseif($code->status === 'RESERVADO')
                                    <span class="badge bg-warning">Reservado</span>
                                @elseif($code->status === 'UTILIZADO')
                                    <span class="badge bg-primary">Utilizado</span>
                                @elseif($code->status === 'EXPIRADO')
                                    <span class="badge bg-danger">Expirado</span>
                                @else
                                    <span class="badge bg-secondary">{{ $code->status }}</span>
                                @endif
                            </td>
                            <td>{{ $code->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.referral-codes.show', $code) }}" class="btn btn-sm btn-info" title="Detalhes">
                                    <i class="fas fa-eye"></i> Detalhes
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Nenhum código encontrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $codes->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
