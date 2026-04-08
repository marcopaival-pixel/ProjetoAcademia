@extends('layouts.app', ['navCurrent' => 'messages'])

@section('title', 'Conversa com ' . $conversation->getOtherUser(auth()->id())->name)

@section('content')
<div class="chat-wrapper">
    <div class="chat-header-bar glass">
        <a href="{{ route('messages.index') }}" class="btn btn-sm btn-ghost">← Voltar</a>
        <div class="chat-user-info">
            <strong>{{ $conversation->getOtherUser(auth()->id())->name }}</strong>
            <span class="status-dot online"></span>
        </div>
        <div class="chat-actions">
            <button type="button" class="btn btn-sm btn-danger" id="btnShowSelection" onclick="toggleSelectionMode()">Selecionar</button>
        </div>
    </div>

    <form id="bulkDeleteForm" action="{{ route('messages.bulk-delete') }}" method="POST">
        @csrf
        <div class="selection-toolbar glass" id="selectionToolbar" style="display: none;">
            <div class="toolbar-info">
                <input type="checkbox" id="masterCheckbox" onclick="toggleAllMessages(this)">
                <label for="masterCheckbox">Selecionar Tudo</label>
                <span id="selectedCount" style="margin-left: 1rem; font-size: 0.85rem; font-weight: 600;">0 selecionadas</span>
            </div>
            <button type="submit" class="btn btn-sm btn-danger">Excluir Selecionadas</button>
        </div>

        <div class="message-list" id="messageList">
            @foreach($messages as $msg)
                <div class="message-row {{ $msg->sender_id === auth()->id() ? 'outgoing' : 'incoming' }} animate-fade-up">
                    <div class="message-selection" style="display: none;">
                        <input type="checkbox" name="ids[]" value="{{ $msg->id }}" onclick="updateSelectedCount()">
                    </div>
                    <div class="message-bubble glass">
                        <div class="message-content">{{ $msg->content }}</div>
                        <div class="message-meta">
                            {{ $msg->created_at->format('H:i') }}
                            @if($msg->sender_id === auth()->id())
                                <span class="read-receipt {{ $msg->is_read ? 'read' : '' }}">✓✓</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </form>

    <div class="chat-input-area glass">
        <form action="{{ route('messages.store', $conversation) }}" method="POST" class="chat-form">
            @csrf
            <textarea name="content" placeholder="Escreva sua mensagem..." required></textarea>
            <button type="submit" class="btn btn-primary">Enviar</button>
        </form>
    </div>
</div>

<style>
    .chat-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 180px);
        max-width: 900px;
        margin: 0 auto;
    }

    .chat-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1.25rem;
        border-radius: 16px 16px 0 0;
        margin-bottom: 2px;
    }

    .chat-user-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background: #34c759;
        border-radius: 50%;
        box-shadow: 0 0 5px #34c759;
    }

    .selection-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1.25rem;
        background: rgba(255, 69, 58, 0.1) !important;
        border-color: rgba(255, 69, 58, 0.3);
        margin-bottom: 2px;
    }

    .message-list {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        background: rgba(0,0,0,0.05);
    }

    .message-row {
        display: flex;
        align-items: flex-end;
        gap: 0.75rem;
        max-width: 80%;
    }

    .message-row.outgoing {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .message-bubble {
        padding: 0.75rem 1rem;
        border-radius: 16px;
        position: relative;
    }

    .incoming .message-bubble {
        border-bottom-left-radius: 4px;
        background: rgba(255,255,255,0.05);
    }

    .outgoing .message-bubble {
        border-bottom-right-radius: 4px;
        background: color-mix(in oklab, var(--accent) 20%, transparent);
        border-color: color-mix(in oklab, var(--accent) 30%, transparent);
    }

    .message-meta {
        font-size: 0.7rem;
        color: var(--muted);
        margin-top: 0.25rem;
        display: flex;
        justify-content: flex-end;
        gap: 0.25rem;
    }

    .read-receipt.read {
        color: var(--accent);
    }

    .chat-input-area {
        padding: 1rem 1.25rem;
        border-radius: 0 0 16px 16px;
    }

    .chat-form {
        display: flex;
        gap: 0.75rem;
        align-items: flex-end;
    }

    .chat-form textarea {
        flex: 1;
        min-height: 45px;
        max-height: 120px;
        border-radius: 12px;
        background: rgba(0,0,0,0.2);
        border-color: var(--border);
        color: var(--text);
        padding: 0.6rem 0.8rem;
        resize: none;
    }

    /* Selection Mode Styles */
    .message-selection {
        padding-bottom: 8px;
    }

    .message-selection input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
</style>

<script>
    let selectionMode = false;

    function toggleSelectionMode() {
        selectionMode = !selectionMode;
        const toolbar = document.getElementById('selectionToolbar');
        const selections = document.querySelectorAll('.message-selection');
        const btn = document.getElementById('btnShowSelection');

        toolbar.style.display = selectionMode ? 'flex' : 'none';
        selections.forEach(el => el.style.display = selectionMode ? 'block' : 'none');
        btn.textContent = selectionMode ? 'Cancelar' : 'Selecionar';
        btn.classList.toggle('btn-danger', !selectionMode);
        btn.classList.toggle('btn-ghost', selectionMode);

        if (!selectionMode) {
            document.getElementById('masterCheckbox').checked = false;
            toggleAllMessages({checked: false});
        }
    }

    function toggleAllMessages(master) {
        const checkboxes = document.querySelectorAll('input[name="ids[]"]');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checked = document.querySelectorAll('input[name="ids[]"]:checked').length;
        document.getElementById('selectedCount').textContent = `${checked} selecionadas`;
    }

    // Auto-scroll para o fim
    const list = document.getElementById('messageList');
    list.scrollTop = list.scrollHeight;
</script>
@endsection
