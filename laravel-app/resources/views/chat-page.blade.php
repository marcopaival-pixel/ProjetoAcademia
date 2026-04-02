@extends('layouts.app')

@section('title', 'Chat com IA')

@section('content')
<style>
    .chat-container {
        max-width: 600px;
        margin: 2rem auto;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 600px;
        background: white;
    }

    .chat-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem;
        font-weight: bold;
        text-align: center;
        flex-shrink: 0;
    }

    .chat-quota-bar {
        flex-shrink: 0;
        padding: 0.5rem 0.75rem;
        font-size: 0.8125rem;
        line-height: 1.35;
        background: #f3f4ff;
        color: #333;
        border-bottom: 1px solid #e8e8f0;
    }

    .chat-quota-bar--premium {
        background: #ecfdf5;
        color: #065f46;
        border-bottom-color: #a7f3d0;
    }

    .chat-quota-bar--warn {
        background: #fff8e6;
        color: #7c2d12;
        border-bottom-color: #fcd34d;
    }

    .chat-quota-bar a {
        color: #5a67d8;
        font-weight: 600;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .message {
        display: flex;
        margin-bottom: 0.5rem;
    }

    .message.user {
        justify-content: flex-end;
    }

    .message.assistant {
        justify-content: flex-start;
    }

    .message-bubble {
        max-width: 70%;
        padding: 0.75rem 1rem;
        border-radius: 12px;
        word-wrap: break-word;
        line-height: 1.4;
        white-space: pre-line;
    }

    .message.user .message-bubble {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 4px 12px 12px;
    }

    .message.assistant .message-bubble {
        background: #f0f0f0;
        color: #333;
        border-radius: 4px 12px 12px 12px;
    }

    .chat-form {
        display: flex;
        gap: 0.5rem;
        padding: 1rem;
        border-top: 1px solid #eee;
        background: #fafafa;
    }

    .chat-form input {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 20px;
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
        outline: none;
    }

    .chat-form input:focus {
        border-color: #667eea;
    }

    .chat-form button {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-size: 1rem;
        cursor: pointer;
    }

    .chat-form button:hover {
        transform: scale(1.05);
    }

    .loading {
        display: flex;
        gap: 4px;
        justify-content: center;
    }

    .loading span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #999;
        animation: pulse 1.4s infinite;
    }

    .loading span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .loading span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes pulse {
        0%, 60%, 100% {
            opacity: 0.5;
        }
        30% {
            opacity: 1;
        }
    }
</style>

<div class="chat-container">
    <div class="chat-header">
        🥗 Assistente Nutricional
    </div>

    <div id="chatQuotaBar" class="chat-quota-bar" hidden></div>

    <div class="chat-messages" id="messagesContainer">
        <div class="message assistant">
            <div class="message-bubble">
                👋 Olá! Sou seu assistente nutricional. Faça suas perguntas sobre alimentos, calorias, macros e nutrição!
            </div>
        </div>
    </div>

    <form class="chat-form" id="chatForm">
        @csrf
        <input 
            type="text" 
            name="message" 
            id="messageInput"
            placeholder="Faça sua pergunta..." 
            required
            maxlength="1000"
            autocomplete="off"
        >
        <button type="submit">📤</button>
    </form>
</div>

<script>
    const form = document.getElementById('chatForm');
    const input = document.getElementById('messageInput');
    const container = document.getElementById('messagesContainer');
    const quotaBar = document.getElementById('chatQuotaBar');

    function renderQuota(q) {
        if (!q || typeof q !== 'object') {
            quotaBar.hidden = true;
            input.disabled = false;
            input.placeholder = 'Faça sua pergunta...';
            return;
        }
        if (q.is_premium) {
            quotaBar.className = 'chat-quota-bar chat-quota-bar--premium';
            quotaBar.textContent = 'Plano Premium — sem limite diário de mensagens neste app.';
            quotaBar.hidden = false;
            input.disabled = false;
            input.placeholder = 'Faça sua pergunta...';
            return;
        }
        const limit = q.daily_user_limit;
        const used = q.daily_user_used;
        if (limit == null) {
            quotaBar.hidden = true;
            input.disabled = false;
            return;
        }
        const left = Math.max(0, limit - used);
        quotaBar.className = left === 0 ? 'chat-quota-bar chat-quota-bar--warn' : 'chat-quota-bar';
        let html = 'Mensagens hoje: ' + used + ' / ' + limit + ' · restam ' + left;
        if (left === 0) {
            html += ' — <a href="{{ route('plano') }}">Ver Premium</a>';
        }
        quotaBar.innerHTML = html;
        quotaBar.hidden = false;
        input.disabled = left === 0;
        input.placeholder = left === 0 ? 'Limite diário atingido — veja Premium' : 'Faça sua pergunta...';
    }

    async function loadQuota() {
        try {
            const r = await fetch('{{ route("chat.history") }}?limit=1', { headers: { 'Accept': 'application/json' } });
            const d = await r.json();
            if (d.ok && d.chat_quota) {
                renderQuota(d.chat_quota);
            }
        } catch (e) {
            quotaBar.hidden = true;
        }
    }

    loadQuota();

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const message = input.value.trim();
        if (!message) return;

        const userRow = addMessage(message, 'user');
        input.value = '';

        // Mostrar indicador de digitação
        const loadingDiv = document.createElement('div');
        loadingDiv.className = 'message assistant';
        loadingDiv.innerHTML = '<div class="message-bubble"><div class="loading"><span></span><span></span><span></span></div></div>';
        container.appendChild(loadingDiv);
        container.scrollTop = container.scrollHeight;

        try {
            const response = await fetch('{{ route("chat.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify({ message }),
            });

            const data = await response.json();
            
            // Remover indicador de digitação
            loadingDiv.remove();

            if (data.ok) {
                addMessage(data.message, 'assistant');
                if (data.chat_quota) {
                    renderQuota(data.chat_quota);
                }
            } else if (data.code === 'chat_quota_exceeded' && data.plano_url) {
                userRow.remove();
                if (data.quota && typeof data.quota === 'object') {
                    renderQuota({
                        is_premium: false,
                        daily_user_limit: data.quota.limit,
                        daily_user_used: data.quota.used,
                    });
                }
                addMessage(
                    (data.error || 'Limite diário atingido.') + '\n\nVer planos: ' + data.plano_url,
                    'assistant'
                );
            } else {
                addMessage('Erro: ' + (data.error || 'Algo deu errado'), 'assistant');
            }
        } catch (error) {
            loadingDiv.remove();
            addMessage('Desculpe, ocorreu um erro: ' + error.message, 'assistant');
        }
    });

    function addMessage(text, role) {
        const div = document.createElement('div');
        div.className = 'message ' + role;
        div.innerHTML = `<div class="message-bubble">${escapeHtml(text)}</div>`;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
        return div;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Focus no input ao carregar
    input.focus();
</script>
@endsection
