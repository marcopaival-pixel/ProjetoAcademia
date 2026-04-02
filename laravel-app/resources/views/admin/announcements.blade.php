@extends('layouts.admin')

@section('title', 'Avisos Globais')

@section('content')
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <div class="card">
            <h2 style="margin-top: 0; font-size: 1.125rem;">Emitir Novo Aviso</h2>
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="content">Conteúdo do Aviso</label>
                    <textarea id="content" name="content" rows="4" required placeholder="Escreva a mensagem aqui..."></textarea>
                </div>

                <div class="form-group">
                    <label for="type">Tipo / Cor</label>
                    <select id="type" name="type" required>
                        <option value="info">Informação (Azul)</option>
                        <option value="success">Sucesso (Verde)</option>
                        <option value="warning">Alerta (Amarelo)</option>
                        <option value="danger">Crítico (Vermelho)</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="starts_at">Início (Opcional)</label>
                        <input type="datetime-local" id="starts_at" name="starts_at">
                    </div>
                    <div class="form-group">
                        <label for="ends_at">Término (Opcional)</label>
                        <input type="datetime-local" id="ends_at" name="ends_at">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Publicar Imediatamente</button>
            </form>
        </div>

        <div class="card">
            <h2 style="margin-top: 0; font-size: 1.125rem;">Histórico de Avisos</h2>
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1.5rem;">
                @forelse($announcements as $an)
                    <div style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; position: relative;">
                        <span class="badge {{ 'badge-' . $an->type }}" style="margin-bottom: 0.5rem; display: inline-block;">{{ strtoupper($an->type) }}</span>
                        <p style="margin: 0.5rem 0; font-size: 0.875rem;">{{ $an->content }}</p>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            @if($an->starts_at) De: {{ $an->starts_at->format('d/m/Y H:i') }} @endif
                            @if($an->ends_at) Até: {{ $an->ends_at->format('d/m/Y H:i') }} @endif
                        </div>
                        <form action="{{ route('admin.announcements.delete', $an->id) }}" method="POST" style="position: absolute; top: 1rem; right: 1rem;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: #f85149; cursor: pointer;">Excluir</button>
                        </form>
                    </div>
                @empty
                    <p style="text-align: center; color: var(--text-muted);">Nenhum aviso emitido.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
