@extends('internal-email.layout')

@section('title', 'Caixa de Saída')

@section('toolbar-left')
    <div class="d-flex align-items-center gap-3">
        <input type="checkbox" id="selectAll" class="form-check-input" style="width: 18px; height: 18px;">
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-link text-muted p-0" title="Atualizar" onclick="location.reload()"><i class="fas fa-sync-alt"></i></button>
        </div>
    </div>
@endsection

@section('email-content')
    <div class="email-list">
        @forelse($messages as $msg)
            <div onclick="window.location='{{ route('internal-email.show', $msg) }}'" 
                 class="email-row">
                
                <div class="d-flex align-items-center gap-3 me-4 flex-shrink-0">
                    <input type="checkbox" onclick="event.stopPropagation()" class="form-check-input" style="width: 14px; height: 14px;">
                </div>

                <div class="flex-shrink-0 me-4" style="width: 180px;">
                    <span class="text-truncate d-block" style="font-size: 0.85rem; color: var(--text);">
                        Para: {{ $msg->destinatario->name }}
                    </span>
                </div>

                <div class="flex-grow-1 min-width-0 d-flex align-items-center gap-2">
                    <span class="badge bg-warning text-dark rounded-pill" style="font-size: 0.6rem; padding: 3px 7px;">PENDENTE</span>
                    <div class="text-truncate" style="font-size: 0.85rem;">
                        <span style="color: white;">{{ $msg->assunto }}</span>
                    </div>
                </div>

                <div class="flex-shrink-0 ms-4 text-end" style="width: 100px;">
                    <span class="text-muted" style="font-size: 0.75rem;">
                        Aguardando envio...
                    </span>
                </div>
            </div>
        @empty
            <div class="d-flex flex-column align-items-center justify-content-center py-5 opacity-50 h-100">
                <div class="bg-secondary bg-opacity-10 p-4 rounded-circle mb-4">
                    <i class="fas fa-clock fa-3x text-muted" style="width: 60px; height: 60px;"></i>
                </div>
                <h6 class="fw-bold">Caixa de saída vazia</h6>
                <p class="text-muted small">Nenhuma mensagem aguardando envio.</p>
            </div>
        @endforelse
    </div>
@endsection
