@extends('internal-email.layout')

@section('title', 'Nova Mensagem')

@section('toolbar-left')
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('internal-email.inbox') }}" class="btn btn-sm btn-link text-muted p-0" title="Cancelar"><i class="fas fa-arrow-left"></i></a>
        <span class="text-white fw-bold small">Nova Mensagem</span>
    </div>
@endsection

@section('email-content')
    <div class="p-4 p-md-5">
        <form action="{{ route('internal-email.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            @if(isset($replyTo))
                <input type="hidden" name="parent_id" value="{{ $replyTo->id }}">
                <input type="hidden" name="destinatario_id[]" value="{{ $replyTo->remetente_id }}">
                <div class="mb-4 p-3 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-20">
                    <div class="d-flex align-items-center gap-2 text-primary small fw-bold">
                        <i class="fas fa-reply"></i> Respondendo para: {{ $replyTo->remetente->name }}
                    </div>
                </div>
            @else
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold text-uppercase">Para</label>
                    <select name="destinatario_id[]" class="form-select bg-white bg-opacity-5 border-white border-opacity-10 text-white rounded-3 shadow-none p-3" multiple required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted small">Mantenha pressionado Ctrl para selecionar múltiplos destinatários.</div>
                </div>
            @endif

            <div class="mb-4">
                <label class="form-label text-muted small fw-bold text-uppercase">Assunto</label>
                <input type="text" name="assunto" value="{{ isset($replyTo) ? 'Re: ' . $replyTo->assunto : '' }}" class="form-control bg-white bg-opacity-5 border-white border-opacity-10 text-white rounded-3 shadow-none p-3" placeholder="Assunto da mensagem" required>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted small fw-bold text-uppercase">Mensagem</label>
                <textarea name="mensagem" rows="12" class="form-control bg-white bg-opacity-5 border-white border-opacity-10 text-white rounded-3 shadow-none p-3" placeholder="Escreva sua mensagem aqui..." required></textarea>
            </div>

            <div class="mb-5">
                <label class="form-label text-muted small fw-bold text-uppercase d-flex align-items-center gap-2">
                    <i class="fas fa-paperclip"></i> Anexar Arquivos
                </label>
                <input type="file" name="anexos[]" class="form-control bg-white bg-opacity-5 border-white border-opacity-10 text-white rounded-3 shadow-none" multiple>
                <div class="form-text text-muted small">Formatos aceitos: PDF, Imagens, Documentos. Máx 10MB por arquivo.</div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold d-flex align-items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Enviar Agora
                </button>
                <button type="button" onclick="history.back()" class="btn btn-link text-muted text-decoration-none">Descartar</button>
            </div>
        </form>
    </div>

    <style>
        .form-select option {
            background-color: #1a1d23;
            color: white;
            padding: 10px;
        }
    </style>
@endsection
