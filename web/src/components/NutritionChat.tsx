import { useEffect, useRef, useState } from 'react';
import './NutritionChat.css';

interface Message {
  id?: number;
  role: 'user' | 'assistant';
  message: string;
  created_at?: string;
}

export default function NutritionChat() {
  const [messages, setMessages] = useState<Message[]>([]);
  const [input, setInput] = useState('');
  const [loading, setLoading] = useState(false);
  const [showChat, setShowChat] = useState(false);
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [historyLoaded, setHistoryLoaded] = useState(false);
  const messagesEndRef = useRef<HTMLDivElement>(null);

  // Auto-scroll para a última mensagem
  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  // Verificar autenticação ao abrir chat
  useEffect(() => {
    if (showChat) {
      checkAuthAndLoadHistory();
    } else {
      // Reset quando fechar
      setHistoryLoaded(false);
    }
  }, [showChat]);

  const checkAuthAndLoadHistory = async () => {
    try {
      const response = await fetch('http://localhost:8000/api/chat/history?limit=20');
      if (response.status === 401) {
        setIsAuthenticated(false);
        setMessages([]);
        return;
      }
      if (response.ok) {
        const data = await response.json();
        if (data.ok) {
          setMessages(data.messages);
          setIsAuthenticated(true);
        }
      }
    } catch (error) {
      console.error('Erro ao verificar autenticação:', error);
      setIsAuthenticated(false);
    } finally {
      setHistoryLoaded(true);
    }
  };

  const loadHistory = async () => {
    try {
      const response = await fetch('http://localhost:8000/api/chat/history?limit=20');
      const data = await response.json();
      if (data.ok) {
        setMessages(data.messages);
      }
    } catch (error) {
      console.error('Erro ao carregar histórico:', error);
    }
  };

  const sendMessage = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!input.trim() || loading) return;

    const userMessage = input;
    setInput('');

    // Adicionar mensagem do usuário localmente
    setMessages(prev => [...prev, {
      role: 'user',
      message: userMessage
    }]);

    setLoading(true);

    try {
      const response = await fetch('http://localhost:8000/api/chat/send', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
        body: JSON.stringify({ message: userMessage }),
      });

      const data = await response.json();

      if (data.ok) {
        setMessages(prev => [...prev, {
          role: 'assistant',
          message: data.message
        }]);
      } else {
        setMessages(prev => [...prev, {
          role: 'assistant',
          message: `Erro: ${data.error}`
        }]);
      }
    } catch (error) {
      setMessages(prev => [...prev, {
        role: 'assistant',
        message: 'Desculpe, ocorreu um erro ao processar sua mensagem.'
      }]);
      console.error('Erro:', error);
    } finally {
      setLoading(false);
    }
  };

  const clearChat = async () => {
    if (!confirm('Tem certeza que deseja limpar o histórico do chat?')) return;

    try {
      const response = await fetch('http://localhost:8000/api/chat/clear', { 
        method: 'POST',
        credentials: 'include',
      });
      if (data.ok) {
        setMessages([]);
      }
    } catch (error) {
      console.error('Erro ao limpar chat:', error);
    }
  };

  return (
    <>
      {/* Botão flutuante */}
      <button 
        className="nutrition-chat-button"
        onClick={() => setShowChat(!showChat)}
        title="Assistente Nutricional"
      >
        💬
      </button>

      {/* Janela do chat */}
      {showChat && (
        <div className="nutrition-chat-container">
          <div className="chat-header">
            <h3>🥗 Assistente Nutricional</h3>
            <div className="chat-header-actions">
              <button 
                className="chat-clear-btn"
                onClick={clearChat}
                title="Limpar histórico"
              >
                🗑️
              </button>
              <button 
                className="chat-close-btn"
                onClick={() => setShowChat(false)}
              >
                ✕
              </button>
            </div>
          </div>

          <div className="chat-messages">
            {!isAuthenticated && historyLoaded ? (
              <div className="chat-welcome" style={{ color: '#d32f2f' }}>
                <h4>🔒 Não autenticado</h4>
                <p>Por favor, faça <strong>login</strong> em:</p>
                <p><a href="http://localhost:8000/login" target="_blank" style={{ color: '#667eea' }}>http://localhost:8000</a></p>
                <p>Depois volte aqui!</p>
              </div>
            ) : messages.length === 0 ? (
              <div className="chat-welcome">
                <h4>👋 Bem-vindo ao Assistente Nutricional!</h4>
                <p>Tire suas dúvidas sobre:</p>
                <ul>
                  <li>Alimentos e calorias</li>
                  <li>Macronutrientes (proteína, carbos, gorduras)</li>
                  <li>Dicas de nutrição</li>
                  <li>Seu progresso e metas</li>
                </ul>
              </div>
            ) : (
              messages.map((msg, idx) => (
                <div 
                  key={idx} 
                  className={`chat-message ${msg.role}`}
                >
                  <div className="message-bubble">
                    {msg.message}
                  </div>
                </div>
              ))
            )}
            {loading && (
              <div className="chat-message assistant">
                <div className="message-bubble typing">
                  <span></span><span></span><span></span>
                </div>
              </div>
            )}
            <div ref={messagesEndRef} />
          </div>

          <form onSubmit={sendMessage} className="chat-input-form">
            <input
              type="text"
              value={input}
              onChange={(e) => setInput(e.target.value)}
              placeholder={!isAuthenticated ? "Faça login para usar o chat..." : "Faça sua pergunta..."}
              disabled={loading || !isAuthenticated}
              maxLength={1000}
            />
            <button 
              type="submit" 
              disabled={loading || !input.trim() || !isAuthenticated}
              className="chat-send-btn"
            >
              📤
            </button>
          </form>
        </div>
      )}
    </>
  );
}
