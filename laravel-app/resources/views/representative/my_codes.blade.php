@extends('layouts.app')

@section('title', 'Meus Códigos de Indicação')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 font-weight-bold mb-0 text-gray-800">Meus Códigos de Indicação</h2>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Códigos Gerados e Suas Situações</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Proposta ID</th>
                            <th>Clínica Vinculada</th>
                            <th>Status</th>
                            <th>Emissão</th>
                            <th>Validade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($codes as $code)
                        <tr>
                            <td><strong>{{ $code->code }}</strong></td>
                            <td>{{ $code->commercialProposal->id ?? '-' }}</td>
                            <td>{{ $code->clinic->name ?? '-' }}</td>
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
                            <td>{{ $code->expires_at ? $code->expires_at->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Nenhum código gerado ainda. Crie propostas comerciais para gerar códigos.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $codes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
