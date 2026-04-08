@extends('internal-email.layout')

@section('title', 'Enviados')

@section('toolbar-left')
    <div class="d-flex align-items-center gap-3">
        <input type="checkbox" id="selectAll" class="form-check-input" style="width: 18px; height: 18px;">
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-link text-muted p-0" title="Atualizar" onclick="location.reload()"><i class="fas fa-sync-alt"></i></button>
            <button class="btn btn-sm btn-link text-muted p-0" title="Excluir"><i class="fas fa-trash-alt"></i></button>
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
                    <i class="far fa-star text-muted"></i>
                </div>

                <div class="flex-shrink-0 me-4" style="width: 180px;">
                    <span class="text-truncate d-block" style="font-size: 0.85rem; color: var(--text);">
                        Para: {{ $msg->destinatario->name }}
                    </span>
                </div>

                <div class="flex-grow-1 min-width-0 d-flex align-items-center gap-2">
                    @if($msg->anexos->count() > 0)
                        <i class="fas fa-paperclip text-muted small"></i>
                    @endif
                    <div class="text-truncate" style="font-size: 0.85rem;">
                        <span style="color: white;">{{ $msg->assunto }}</span>
                        <span class="mx-1 text-muted">—</span>
                        <span class="text-muted" style="font-weight: 400;">{{ Str::limit(strip_tags($msg->mensagem), 100) }}</span>
                    </div>
                </div>

                <div class="flex-shrink-0 ms-4 text-end" style="width: 100px;">
                    <span class="text-muted" style="font-size: 0.75rem;">
                        {{ $msg->data_envio->isToday() ? $msg->data_envio->format('H:i') : $msg->data_envio->format('d M') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="d-flex flex-column align-items-center justify-content-center py-5 opacity-50 h-100">
                <div class="bg-secondary bg-opacity-10 p-4 rounded-circle mb-4">
                    <i class="fas fa-paper-plane fa-3x text-muted" style="width: 60px; height: 60px;"></i>
                </div>
                <h6 class="fw-bold">Nenhuma mensagem enviada</h6>
                <p class="text-muted small">As mensagens que você enviar aparecerão aqui.</p>
            </div>
        @endforelse
    </div>
@endsection
