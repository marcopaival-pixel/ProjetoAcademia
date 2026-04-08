@extends('internal-email.layout')

@section('title', 'Caixa de Entrada')

@section('toolbar-left')
    <div class="d-flex align-items-center gap-3">
        <input type="checkbox" id="selectAll" class="form-check-input" style="width: 18px; height: 18px;">
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-link text-muted p-0" title="Atualizar" onclick="location.reload()"><i class="fas fa-sync-alt"></i></button>
            <button class="btn btn-sm btn-link text-muted p-0" title="Marcar como lida"><i class="fas fa-envelope-open"></i></button>
            <button class="btn btn-sm btn-link text-muted p-0" title="Arquivar"><i class="fas fa-archive"></i></button>
            <button class="btn btn-sm btn-link text-muted p-0" title="Excluir"><i class="fas fa-trash-alt"></i></button>
        </div>
    </div>
@endsection

@section('toolbar-right')
    <div class="dropdown">
        <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="fas fa-filter"></i></button>
        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end p-2 border-secondary shadow-lg">
            <li><a class="dropdown-item rounded" href="{{ route('internal-email.inbox') }}">Tudo</a></li>
            <li><a class="dropdown-item rounded" href="{{ route('internal-email.inbox', ['filter' => 'unread']) }}">Não Lidas</a></li>
            <li><a class="dropdown-item rounded" href="{{ route('internal-email.inbox', ['filter' => 'system']) }}">Sistema</a></li>
        </ul>
    </div>
@endsection

@section('email-content')
    <div class="email-list">
        @forelse($messages as $msg)
            <div onclick="window.location='{{ route('internal-email.show', $msg) }}'" 
                 class="email-row {{ $msg->lida ? '' : 'unread' }}">
                
                <div class="d-flex align-items-center gap-3 me-4 flex-shrink-0">
                    <input type="checkbox" onclick="event.stopPropagation()" class="form-check-input" style="width: 14px; height: 14px;">
                    <i class="far fa-star text-muted"></i>
                </div>

                <div class="flex-shrink-0 me-4" style="width: 180px;">
                    <span class="text-truncate d-block" style="font-size: 0.85rem; {{ $msg->lida ? 'color: var(--text-muted);' : 'color: var(--text-main); font-weight: bold;' }}">
                        {{ $msg->remetente->name }}
                    </span>
                </div>

                <div class="flex-grow-1 min-width-0 d-flex align-items-center gap-2">
                    @if(!$msg->lida)
                        <span class="badge bg-primary rounded-pill text-uppercase" style="font-size: 0.6rem; padding: 3px 7px;">NOVA</span>
                    @endif
                    @if($msg->anexos->count() > 0)
                        <i class="fas fa-paperclip text-muted small"></i>
                    @endif
                    <div class="text-truncate" style="font-size: 0.85rem;">
                        <span style="{{ $msg->lida ? 'color: var(--text-main);' : 'color: var(--text-main); font-weight: bold;' }}">{{ $msg->assunto }}</span>
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
                    <i class="fas fa-inbox fa-3x text-muted" style="width: 60px; height: 60px;"></i>
                </div>
                <h6 class="fw-bold" style="color: var(--text-main);">Caixa de entrada vazia</h6>
                <p class="text-muted small">Nenhuma mensagem encontrada nesta pasta.</p>
            </div>
        @endforelse
    </div>
@endsection
