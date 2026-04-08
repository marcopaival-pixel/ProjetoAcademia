@extends('internal-email.layout')

@section('title', $message->assunto)

@section('toolbar-left')
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('internal-email.inbox') }}" class="btn btn-sm btn-link text-muted p-0" title="Voltar"><i class="fas fa-arrow-left"></i></a>
        <div class="d-flex gap-2">
            @if($message->excluded_at_sender || $message->excluded_at_receiver)
                <form action="{{ route('internal-email.restore', $message) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-link text-muted p-0" title="Restaurar"><i class="fas fa-undo"></i></button>
                </form>
                <form action="{{ route('internal-email.permanent', $message) }}" method="POST" class="m-0" onsubmit="return confirm('Excluir permanentemente?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="Excluir Permanentemente"><i class="fas fa-trash"></i></button>
                </form>
            @else
                <form action="{{ route('internal-email.unread', $message) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-link text-muted p-0" title="Marcar como não lida"><i class="fas fa-envelope"></i></button>
                </form>
                <form action="{{ route('internal-email.destroy', $message) }}" method="POST" class="m-0">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-link text-muted p-0" title="Excluir"><i class="fas fa-trash-alt"></i></button>
                </form>
            @endif
        </div>
    </div>
@endsection

@section('email-content')
    <div class="p-4 p-md-5">
        <div class="d-flex align-items-center justify-content-between mb-5">
            <h1 class="h3 fw-bold text-white mb-0">{{ $message->assunto }}</h1>
            <span class="badge bg-secondary bg-opacity-10 text-muted px-3 py-2 rounded-pill small">
                {{ $message->created_at->format('d/m/Y H:i') }}
            </span>
        </div>

        <div class="d-flex align-items-start gap-3 mb-5 pb-4 border-bottom border-white border-opacity-5">
            <div class="rounded-circle bg-primary bg-opacity-20 d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 48px; height: 48px; font-size: 1.25rem;">
                {{ substr($message->remetente->name, 0, 1) }}
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="fw-bold text-white">{{ $message->remetente->name }}</span>
                        <span class="text-muted small ms-2">&lt;{{ $message->remetente->email }}&gt;</span>
                    </div>
                </div>
                <div class="text-muted small mt-1">
                    Para: {{ $message->destinatario->name }}
                </div>
            </div>
        </div>

        <div class="message-body text-light mb-5" style="font-size: 1rem; line-height: 1.7; white-space: pre-wrap;">
            {{ $message->mensagem }}
        </div>

        @if($message->anexos->count() > 0)
            <div class="attachments-section mt-5 p-4 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-5">
                <h6 class="text-white mb-3 d-flex align-items-center gap-2">
                    <i class="fas fa-paperclip"></i> Anexos ({{ $message->anexos->count() }})
                </h6>
                <div class="row g-3">
                    @foreach($message->anexos as $anexo)
                        <div class="col-md-4 col-lg-3">
                            <a href="{{ Storage::url($anexo->file_path) }}" target="_blank" class="d-block p-3 rounded-3 bg-white bg-opacity-5 border border-white border-opacity-5 text-decoration-none hover-bg-opacity-10 transition-all">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-20 p-2 rounded text-primary">
                                        @if(in_array($anexo->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                            <i class="fas fa-image"></i>
                                        @elseif($anexo->file_type === 'pdf')
                                            <i class="fas fa-file-pdf"></i>
                                        @else
                                            <i class="fas fa-file-alt"></i>
                                        @endif
                                    </div>
                                    <div class="min-width-0">
                                        <div class="text-white small text-truncate fw-bold">{{ $anexo->file_name }}</div>
                                        <div class="text-muted" style="font-size: 0.65rem;">{{ number_format($anexo->file_size / 1024, 1) }} KB</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-5 pt-4 d-flex gap-3">
            <a href="{{ route('internal-email.create', ['reply_to' => $message->id]) }}" class="btn btn-outline-light rounded-pill px-4 d-flex align-items-center gap-2">
                <i class="fas fa-reply"></i> Responder
            </a>
            <a href="{{ route('internal-email.create', ['forward_from' => $message->id]) }}" class="btn btn-outline-secondary rounded-pill px-4 d-flex align-items-center gap-2">
                <i class="fas fa-share"></i> Encaminhar
            </a>
        </div>

        @if($message->replies->count() > 0)
            <div class="mt-5 pt-5">
                <h6 class="text-muted text-uppercase small fw-bold mb-4">Respostas ({{ $message->replies->count() }})</h6>
                <div class="d-flex flex-column gap-4">
                    @foreach($message->replies as $reply)
                        <div class="p-3 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-5">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="fw-bold text-white small">{{ $reply->remetente->name }}</span>
                                <span class="text-muted" style="font-size: 0.7rem;">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-muted small line-clamp-2">
                                {{ Str::limit($reply->mensagem, 150) }}
                            </div>
                            <a href="{{ route('internal-email.show', $reply) }}" class="stretched-link"></a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <style>
        .hover-bg-opacity-10:hover { background-color: rgba(255, 255, 255, 0.1) !important; }
        .transition-all { transition: all 0.2s ease; }
    </style>
@endsection
