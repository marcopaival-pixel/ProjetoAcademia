# 🤖 Assistente IA Nutritivo - Guia de Implementação

## ✨ O que foi desenvolvido

Um **chatbot nutritivo inteligente** que conversa com os usuários sobre nutrição, alimentos e metas de emagrecimento/ganho de massa. O assistente usa **OpenAI GPT-4o-mini** para fornecer respostas contextualizadas.

## 📦 Arquivos criados/modificados

### Backend (Laravel)

| Arquivo | Descrição |
|---------|-----------|
| `app/Services/AIChatService.php` | Service para comunicação com OpenAI |
| `app/Http/Controllers/ChatController.php` | Controller com rotas do chat |
| `app/Models/AIChat.php` | Model para armazenar mensagens |
| `database/migrations/2026_03_31_120000_create_ai_chats_table.php` | Migração da tabela |
| `routes/web.php` | **MODIFICADO**: Adicionadas 3 rotas de API |

### Frontend (React)

| Arquivo | Descrição |
|---------|-----------|
| `web/src/components/NutritionChat.tsx` | Componente principal do chat |
| `web/src/components/NutritionChat.css` | Estilos modernos e responsivos |
| `web/src/App.example.tsx` | Exemplo de como integrar |

### Documentação

| Arquivo | Descrição |
|---------|-----------|
| `CHATBOT_SETUP.md` | Guia completo de setup |
| `.env.example.openai` | Template de configuração |
| `test_chatbot_api.sh` | Script de testes da API |
| `CHATBOT_QUICK_START.md` | Este arquivo |

## 🚀 Quick Start (5 min)

### Passo 1: Instalar dependência
```bash
cd laravel-app
composer require openai-php/client
```

### Passo 2: Configurar OpenAI
```bash
# No arquivo .env do laravel-app, adicione:
OPENAI_API_KEY=sk-proj-xxx...suas-chaves
```

**Como obter chave gratuita:**
1. Ir em: https://platform.openai.com/api-keys
2. Login com Google/GitHub
3. Clicar em "Create new secret key"
4. Copiar e adicionar ao .env
5. Adicionar método de pagamento para ter créditos

### Passo 3: Executar migração
```bash
cd laravel-app
php artisan migrate
```

### Passo 4: Integrar no React
Em `web/src/App.tsx`:
```tsx
import NutritionChat from './components/NutritionChat';

function App() {
  return (
    <>
      <NutritionChat /> {/* Adicione esta linha */}
      {/* resto do seu código */}
    </>
  );
}
```

### Passo 5: Certifique-se do CSRF Token
No seu layout HTML, adicione (geralmente já existe):
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

**Pronto! 🎉 O chat está funcionando**

## 🎯 Rotas API criadas

```
POST   /api/chat/send      - Enviar mensagem para IA
GET    /api/chat/history   - Obter histórico de chat
POST   /api/chat/clear     - Limpar histórico
```

## 💡 Como funciona

```
Usuário digita pergunta
       ↓
React envia para /api/chat/send
       ↓
Controller salva mensagem do usuário
       ↓
AIChatService busca contexto (peso, objetivo, etc)
       ↓
AIChatService chama OpenAI com contexto
       ↓
IA retorna resposta personalizada
       ↓
Controller salva resposta
       ↓
React exibe mensagem no chat
```

## 🎨 Recursos do UI

- ✅ Botão flutuante para abrir/fechar chat
- ✅ Histórico persistente (salvo no DB)
- ✅ Animação de digitação
- ✅ Design responsivo
- ✅ Limpar histórico
- ✅ Totalmente customizável

## 💬 Exemplos de conversas

**Usuário:** "Posso comer essa pizza?"
**IA:** "Depende! Uma fatia tem ~250kcal. Se sua meta é 2000kcal e já consumiu 1500, terá apenas 500 restantes. Considere comer menos 1 fatia ou ajustar outra refeição."

**Usuário:** "Qual alimento tem mais proteína?"
**IA:** "Por 100g, a classificação é: Peito de frango (31g), Atum (26g), Ovos (13g), Lentilha (9g). Seu objetivo seria ganhar massa, então peito de frango é excelente!"

## 📊 Contexto personalizado

O chatbot recebe automaticamente:
- Peso atual e objetivo
- Objetivo do usuário (ganhar/perder/manter)
- Histórico da conversa anterior

Isso permite **respostas muito mais precisas e relevantes**.

## ⚙️ Configurações do OpenAI

No `AIChatService.php`:
```php
private string $model = 'gpt-4o-mini'; // Modelo mais barato
private $temperature = 0.7; // Criatividade
private $max_tokens = 500; // Limite de resposta
```

**Modelos disponíveis:**
- `gpt-4o-mini` (recomendado: rápido e barato)
- `gpt-4` (premium: mais inteligente)
- `gpt-3.5-turbo` (legacy: ainda funciona)

## 🔐 Segurança

- ✅ Autenticação obrigatória
- ✅ CSRF Protection
- ✅ Histórico privado por usuário
- ✅ Rate limiting recomendado (próximo passo)

## 💰 Custos estimados

- **Pergunta média:** ~0.0005 USD (~$0.003 BRL)
- **1000 perguntas:** ~$0.50 USD
- **10000 perguntas/mês:** ~$5 USD

Muito barato comparado ao valor gerado!

## 🔧 Solução de problemas

**P: Chat não aparece**
- R: Verificar se importou `NutritionChat` em App.tsx

**P: Erro "não autorizado"**
- R: Fazer login primeiro

**P: Erro "chave OpenAI inválida"**
- R: Verificar `.env` tem chave correta com crédit

**P: Histórico não salva**
- R: Executar `php artisan migrate`

## 📈 Próximas melhorias recomendadas

1. **Rate Limiting** - Limitar 10 msgs/min por usuário
2. **Premium Feature** - Bloquear para usuários free
3. **Better Prompt** - Pode melhorar contexto ainda mais
4. **Quick Buttons** - "Posso comer X?", "Quantas calorias?"
5. **Analytics** - Rastrear perguntas mais comuns

## 🎓 Recursos úteis

- [OpenAI Documentation](https://platform.openai.com/docs)
- [OpenAI PHP Client](https://github.com/openai-php/client)
- [Laravel HTTP Client](https://laravel.com/docs/11.x/http-client)
- [React Hooks](https://react.dev/reference/react/hooks)

## 📞 Dúvidas?

Verifique:
1. `CHATBOT_SETUP.md` - Setup completo
2. `test_chatbot_api.sh` - Testar API
3. Console browser (F12) - Ver erros frontend
4. `storage/logs/laravel.log` - Ver erros backend

---

**Status:** ✅ Pronto para usar
**Última atualização:** 31 de Março de 2026
