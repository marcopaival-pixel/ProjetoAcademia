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

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const message = input.value.trim();
        if (!message) return;

        // Adicionar mensagem do usuário
        addMessage(message, 'user');
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
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify({ message }),
            });

            const data = await response.json();
            
            // Remover indicador de digitação
            loadingDiv.remove();

            if (data.ok) {
                addMessage(data.message, 'assistant');
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
