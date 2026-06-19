@extends('layouts.admin')

@section('title', 'Detalhes do Código de Indicação')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 font-weight-bold mb-0 text-gray-800">Detalhes do Código: {{ $referralCode->code }}</h2>
        <a href="{{ route('admin.referral-codes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informações Básicas</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Código:</strong>
                            <span>{{ $referralCode->code }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Status:</strong>
                            <span>
                                @if($referralCode->status === 'DISPONIVEL')
                                    <span class="badge bg-success">Disponível</span>
                                @elseif($referralCode->status === 'RESERVADO')
                                    <span class="badge bg-warning">Reservado</span>
                                @elseif($referralCode->status === 'UTILIZADO')
                                    <span class="badge bg-primary">Utilizado</span>
                                @elseif($referralCode->status === 'EXPIRADO')
                                    <span class="badge bg-danger">Expirado</span>
                                @else
                                    <span class="badge bg-secondary">{{ $referralCode->status }}</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Data de Emissão:</strong>
                            <span>{{ $referralCode->created_at->format('d/m/Y H:i:s') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Validade:</strong>
                            <span>{{ $referralCode->expires_at ? $referralCode->expires_at->format('d/m/Y H:i:s') : 'Vitalício' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Data de Uso:</strong>
                            <span>{{ $referralCode->used_at ? $referralCode->used_at->format('d/m/Y H:i:s') : '-' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Vínculos</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Representante:</strong><br>
                            {{ $referralCode->representative->name ?? 'N/A' }} 
                            <small class="text-muted">({{ $referralCode->representative->email ?? 'N/A' }})</small>
                        </li>
                        <li class="list-group-item">
                            <strong>Proposta Comercial ID:</strong><br>
                            @if($referralCode->commercial_proposal_id)
                                <a href="{{ route('admin.proposals.show', $referralCode->commercial_proposal_id) }}">
                                    #{{ $referralCode->commercial_proposal_id }}
                                </a>
                            @else
                                -
                            @endif
                        </li>
                        <li class="list-group-item">
                            <strong>Clínica Vinculada (Uso):</strong><br>
                            @if($referralCode->clinic_id)
                                {{ $referralCode->clinic->name ?? 'N/A' }}
                            @else
                                <span class="text-muted">Ainda não utilizado.</span>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
