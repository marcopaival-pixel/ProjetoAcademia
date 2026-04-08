@extends('layouts.app', ['navCurrent' => 'messages'])

@section('title', 'Mensagens')

@section('content')
<div class="messages-page">
    <div class="header-with-actions">
        <h1>Suas Conversas</h1>
        <button class="btn btn-primary btn-sm">Nova Mensagem</button>
    </div>

    <div class="conversations-grid">
        @forelse($conversations as $conv)
            @php($otherUser = $conv->getOtherUser(auth()->id()))
            @php($lastMsg = $conv->messages->first())
            
            <a href="{{ route('messages.show', $conv) }}" class="card glass conversation-card animate-fade-up">
                <div class="conv-avatar">
                    {{ substr($otherUser->name, 0, 1) }}
                </div>
                <div class="conv-info">
                    <div class="conv-header">
                        <span class="conv-name">{{ $otherUser->name }}</span>
                        <span class="conv-time muted">{{ $lastMsg ? $lastMsg->created_at->diffForHumans() : '' }}</span>
                    </div>
                    <div class="conv-preview @if($lastMsg && !$lastMsg->is_read && $lastMsg->sender_id !== auth()->id()) unread @endif">
                        {{ $lastMsg ? Str::limit($lastMsg->content, 60) : 'Sem mensagens ainda.' }}
                    </div>
                </div>
                @if($lastMsg && !$lastMsg->is_read && $lastMsg->sender_id !== auth()->id())
                    <div class="unread-indicator"></div>
                @endif
            </a>
        @empty
            <div class="card glass empty-state">
                <p>Nenhuma conversa iniciada.</p>
                <button class="btn btn-ghost">Iniciar primeira conversa</button>
            </div>
        @endforelse
    </div>
</div>

<style>
    .header-with-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .conversations-grid {
        display: grid;
        gap: 1rem;
    }

    .conversation-card {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        padding: 1.25rem;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s, background 0.2s;
        position: relative;
    }

    .conversation-card:hover {
        transform: translateX(8px);
        background: color-mix(in oklab, var(--surface) 95%, var(--accent));
    }

    .conv-avatar {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: var(--primary-gradient);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
    }

    .conv-info {
        flex: 1;
        min-width: 0;
    }

    .conv-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.25rem;
    }

    .conv-name {
        font-weight: 700;
        font-size: 1.05rem;
    }

    .conv-time {
        font-size: 0.75rem;
    }

    .conv-preview {
        font-size: 0.9rem;
        color: var(--muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .conv-preview.unread {
        color: var(--text);
        font-weight: 600;
    }

    .unread-indicator {
        width: 10px;
        height: 10px;
        background: var(--accent);
        border-radius: 50%;
        position: absolute;
        right: 1.25rem;
        bottom: 1.25rem;
        box-shadow: 0 0 10px var(--accent);
    }
</style>
@endsection
