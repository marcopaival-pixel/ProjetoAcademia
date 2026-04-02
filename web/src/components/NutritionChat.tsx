import { useEffect, useRef, useState } from 'react';
import './NutritionChat.css';

function laravelBaseUrl(): string {
  const raw = import.meta.env.VITE_LARAVEL_URL?.trim() ?? '';
  const base = raw === '' ? 'http://localhost:8000' : raw.replace(/\/$/, '');
  return base;
}

function csrfToken(): string {
  return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

interface Message {
  id?: number;
  role: 'user' | 'assistant';
  message: string;
  created_at?: string;
}

interface ChatQuotaPayload {
  is_premium: boolean;
  daily_user_limit: number | null;
  daily_user_used: number;
}

function parseChatQuota(raw: unknown): ChatQuotaPayload | null {
  if (!raw || typeof raw !== 'object') {
    return null;
  }
  const q = raw as Record<string, unknown>;
  if (typeof q.is_premium !== 'boolean') {
    return null;
  }
  const limitRaw = q.daily_user_limit;
  const limitNum =
    limitRaw == null ? NaN : typeof limitRaw === 'number' ? limitRaw : Number(limitRaw);
  const usedRaw = q.daily_user_used;
  const used = typeof usedRaw === 'number' ? usedRaw : Number(usedRaw ?? 0);

  return {
    is_premium: q.is_premium,
    daily_user_limit: Number.isFinite(limitNum) ? limitNum : null,
    daily_user_used: Number.isFinite(used) ? used : 0,
  };
}

export default function NutritionChat() {
  const [messages, setMessages] = useState<Message[]>([]);
  const [input, setInput] = useState('');
  const [loading, setLoading] = useState(false);
  const [showChat, setShowChat] = useState(false);
  const [isAuthenticated, setIsAuthenticated] = useState(false);
  const [historyLoaded, setHistoryLoaded] = useState(false);
  const [chatQuota, setChatQuota] = useState<ChatQuotaPayload | null>(null);
  const messagesEndRef = useRef<HTMLDivElement>(null);

  const freeQuotaRemaining =
    chatQuota && !chatQuota.is_premium && chatQuota.daily_user_limit != null
      ? Math.max(0, chatQuota.daily_user_limit - chatQuota.daily_user_used)
      : null;

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
      const response = await fetch(`${laravelBaseUrl()}/api/chat/history?limit=20`, {
        credentials: 'include',
      });
      if (response.status === 401) {
        setIsAuthenticated(false);
        setMessages([]);
        setChatQuota(null);
        return;
      }
      if (response.ok) {
        const data = await response.json();
        if (data.ok) {
          setMessages(data.messages);
          setIsAuthenticated(true);
          const parsed = parseChatQuota(data.chat_quota);
          if (parsed) {
            setChatQuota(parsed);
          }
        }
      }
    } catch (error) {
      console.error('Erro ao verificar autenticação:', error);
      setIsAuthenticated(false);
    } finally {
      setHistoryLoaded(true);
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
      const response = await fetch(`${laravelBaseUrl()}/api/chat/send`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken(),
        },
        body: JSON.stringify({ message: userMessage }),
      });

      const data = await response.json();

      if (data.ok) {
        const parsed = parseChatQuota(data.chat_quota);
        if (parsed) {
          setChatQuota(parsed);
        }
        setMessages(prev => [...prev, {
          role: 'assistant',
          message: data.message
        }]);
      } else if (data.code === 'chat_quota_exceeded') {
        if (data.quota && typeof data.quota === 'object') {
          const q = data.quota as Record<string, unknown>;
          const lim = typeof q.limit === 'number' ? q.limit : Number(q.limit);
          const u = typeof q.used === 'number' ? q.used : Number(q.used);
          setChatQuota({
            is_premium: false,
            daily_user_limit: Number.isFinite(lim) ? lim : null,
            daily_user_used: Number.isFinite(u) ? u : 0,
          });
        }
        const plano = typeof data.plano_url === 'string' ? data.plano_url : `${laravelBaseUrl()}/plano`;
        setMessages(prev => {
          const next = [...prev];
          if (next.length > 0 && next[next.length - 1]?.role === 'user') {
            next.pop();
          }
          return [...next, {
            role: 'assistant',
            message: `${data.error || 'Limite diário atingido no plano grátis.'}\n\nVer planos: ${plano}`,
          }];
        });
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
      const response = await fetch(`${laravelBaseUrl()}/api/chat/clear`, {
        method: 'POST',
        credentials: 'include',
        headers: {
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrfToken(),
        },
      });
      const data = await response.json();
      if (data.ok) {
        setMessages([]);
        try {
          const r = await fetch(`${laravelBaseUrl()}/api/chat/history?limit=1`, { credentials: 'include' });
          if (r.ok) {
            const h = await r.json();
            if (h.ok) {
              const pq = parseChatQuota(h.chat_quota);
              if (pq) {
                setChatQuota(pq);
              }
            }
          }
        } catch {
          setChatQuota((prev) =>
            prev && !prev.is_premium && prev.daily_user_limit != null
              ? { ...prev, daily_user_used: 0 }
              : prev
          );
        }
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

          {isAuthenticated && chatQuota && (chatQuota.is_premium || chatQuota.daily_user_limit != null) && (
            <div
              className={
                chatQuota.is_premium
                  ? 'nutrition-chat-quota nutrition-chat-quota--premium'
                  : freeQuotaRemaining === 0
                    ? 'nutrition-chat-quota nutrition-chat-quota--warn'
                    : 'nutrition-chat-quota'
              }
            >
              {chatQuota.is_premium ? (
                <span>Plano Premium — sem limite diário de mensagens neste app.</span>
              ) : chatQuota.daily_user_limit != null ? (
                <span>
                  Mensagens hoje: {chatQuota.daily_user_used} / {chatQuota.daily_user_limit}
                  {' · '}
                  restam {freeQuotaRemaining}
                  {freeQuotaRemaining === 0 && (
                    <>
                      {' — '}
                      <a href={`${laravelBaseUrl()}/plano`} target="_blank" rel="noreferrer">
                        Ver Premium
                      </a>
                    </>
                  )}
                </span>
              ) : null}
            </div>
          )}

          <div className="chat-messages">
            {!isAuthenticated && historyLoaded ? (
              <div className="chat-welcome" style={{ color: '#d32f2f' }}>
                <h4>🔒 Não autenticado</h4>
                <p>Por favor, faça <strong>login</strong> em:</p>
                <p>
                  <a href={`${laravelBaseUrl()}/login`} target="_blank" rel="noreferrer" style={{ color: '#667eea' }}>
                    {laravelBaseUrl()}
                  </a>
                </p>
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
              placeholder={
                !isAuthenticated
                  ? 'Faça login para usar o chat...'
                  : freeQuotaRemaining === 0
                    ? 'Limite diário atingido — veja Premium...'
                    : 'Faça sua pergunta...'
              }
              disabled={loading || !isAuthenticated || freeQuotaRemaining === 0}
              maxLength={1000}
            />
            <button 
              type="submit" 
              disabled={loading || !input.trim() || !isAuthenticated || freeQuotaRemaining === 0}
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
