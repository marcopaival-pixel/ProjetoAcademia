@extends('internal-email.layout')

@section('title', 'Lixeira')

@section('toolbar-left')
    <div class="d-flex align-items-center gap-3">
        <input type="checkbox" id="selectAll" class="form-check-input" style="width: 18px; height: 18px;">
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-link text-muted p-0" title="Restaurar Selecionados"><i class="fas fa-undo"></i></button>
            <button class="btn btn-sm btn-link text-danger p-0" title="Excluir Permanentemente"><i class="fas fa-trash"></i></button>
        </div>
    </div>
@endsection

@section('email-content')
    <div class="email-list">
        @forelse($messages as $msg)
            <div onclick="window.location='{{ route('internal-email.show', $msg) }}'" 
                 class="email-row opacity-75">
                
                <div class="d-flex align-items-center gap-3 me-4 flex-shrink-0">
                    <input type="checkbox" onclick="event.stopPropagation()" class="form-check-input" style="width: 14px; height: 14px;">
                </div>

                <div class="flex-shrink-0 me-4" style="width: 180px;">
                    <span class="text-truncate d-block" style="font-size: 0.85rem; color: var(--text-muted);">
                        @if($msg->remetente_id === auth()->id())
                            Para: {{ $msg->destinatario->name }}
                        @else
                            De: {{ $msg->remetente->name }}
                        @endif
                    </span>
                </div>

                <div class="flex-grow-1 min-width-0 d-flex align-items-center gap-2">
                    <div class="text-truncate" style="font-size: 0.85rem;">
                        <span style="color: var(--text-muted);">{{ $msg->assunto }}</span>
                        <span class="mx-1 text-muted">—</span>
                        <span class="text-muted" style="font-weight: 400;">{{ Str::limit(strip_tags($msg->mensagem), 100) }}</span>
                    </div>
                </div>

                <div class="flex-shrink-0 ms-4 text-end" style="width: 100px;">
                    <span class="text-muted" style="font-size: 0.75rem;">
                        Excluído em {{ $msg->updated_at->format('d/m') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="d-flex flex-column align-items-center justify-content-center py-5 opacity-50 h-100">
                <div class="bg-secondary bg-opacity-10 p-4 rounded-circle mb-4">
                    <i class="fas fa-trash-alt fa-3x text-muted" style="width: 60px; height: 60px;"></i>
                </div>
                <h6 class="fw-bold">Lixeira vazia</h6>
                <p class="text-muted small">Nada para mostrar aqui.</p>
            </div>
        @endforelse
    </div>
@endsection
