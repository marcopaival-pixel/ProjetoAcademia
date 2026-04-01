# Assistente Nutritivo com IA

## 🤖 O que foi implementado

Um chatbot inteligente que responde dúvidas sobre nutrição, alimentos, macronutrientes e progresso do usuário. O assistente usa OpenAI GPT para fornecer respostas contextualizadas baseadas no plano nutricional do usuário.

## 📋 Componentes adicionados

### Backend (Laravel)

1. **AIChatService** (`app/Services/AIChatService.php`)
   - Integração com API OpenAI
   - Contexto personalizado com métricas do usuário
   - Histórico de conversa para respostas coerentes

2. **ChatController** (`app/Http/Controllers/ChatController.php`)
   - Endpoint: `POST /api/chat/send` - Enviar mensagem
   - Endpoint: `GET /api/chat/history` - Obter histórico
   - Endpoint: `POST /api/chat/clear` - Limpar histórico

3. **Model AIChat** (`app/Models/AIChat.php`)
   - Armazena mensagens do chat (usuário e assistente)
   - Relacionado com usuário

4. **Migração** (`database/migrations/2026_03_31_120000_create_ai_chats_table.php`)
   - Cria tabela `ai_chats` para armazenar histórico

### Frontend (React)

1. **Componente NutritionChat** (`web/src/components/NutritionChat.tsx`)
   - Interface de chat moderna e responsiva
   - Botão flutuante para abrir/fechar
   - Histórico persistente
   - Indicador de digitação
   - Suporta limpeza de histórico

2. **Estilos** (`web/src/components/NutritionChat.css`)
   - Design moderno com gradiente roxo
   - Animações suaves
   - Totalmente responsivo

## 🚀 Como configurar

### 1. Instalar dependência OpenAI

```bash
cd laravel-app
composer require openai-php/client
```

### 2. Configurar chave OpenAI

**Crie um arquivo `.env` no laravel-app com:**

```env
OPENAI_API_KEY=sk-proj-xxx... # Sua chave da OpenAI
```

**Obter chave gratuita/paga:**
- Ir para: https://platform.openai.com/api-keys
- Criar nova chave
- Adicionar créditos se necessário

### 3. Executar migração

```bash
cd laravel-app
php artisan migrate
```

Isso vai criar a tabela `ai_chats` no banco de dados.

### 4. Importar componente React

Em `web/src/App.tsx`, adicione:

```tsx
import NutritionChat from './components/NutritionChat';

function App() {
  return (
    <>
      {/* Seu conteúdo */}
      <NutritionChat />
    </>
  );
}
```

### 5. Adicionar CSRF Token

Certifique-se que o layout tem a meta tag CSRF no `<head>`:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

## 💬 Como usar

1. **Clique no botão 💬** - Abre o chat
2. **Digite sua pergunta** - Ex: "Quantas calorias tem um ovo?"
3. **Envie e receba resposta** - IA responde com contexto do seu plano nutricional
4. **Histórico persistente** - Todas as mensagens são salvas
5. **Limpar chat** - Clique em 🗑️ para limpar histórico

## 🎯 Exemplos de perguntas

- "Posso comer essa pizza? Tenho consumido 1500 kcal"
- "Qual é a melhor fonte de proteína para ganhar massa?"
- "Quantos gramas de carboidrato devo comer?"
- "Estou fazendo academia, qual deve ser meu objetivo calórico?"
- "Como aumentar meu progresso?"

## 🔐 Segurança

- ✅ Autenticação obrigatória
- ✅ CSRF protection
- ✅ Histórico privado por usuário
- ✅ Rate limiting recomendado (adicione depois)

## 📊 Contexto personalizado

O assistente recebe automaticamente:
- Peso atual
- Peso objetivo
- Objetivo (ganhar, perder ou manter peso)
- Perfil do usuário

## 💰 Custo

- OpenAI GPT-4o-mini: ~$0.15 por 1M tokens
- Modelo mais barato: ~$0.001 por pergunta

## 🔄 Próximos passos

1. **Rate limiting** - Limitar requisições por usuário
2. **Premium feature**- Mostrar paywall para não-premium
3. **Analytics** - Tracking de conversas mais comuns
4. **Melhor prompt** - Ajustar prompt para mais contexto
5. **Sugestões** - Quick buttons com perguntas populares

## ⚠️ Solução de problemas

**"Erro de chave OpenAI"**
- Verificar `.env` tem OPENAI_API_KEY
- Chave deve ser válida e ter créditos
- Testar em: https://platform.openai.com

**"Chat não aparece"**
- Verificar se NutritionChat foi importado em App.tsx
- Verificar console do navegador para erros
- Verificar rotas em web.php

**"Histórico não salva"**
- Verificar se migração foi executada: `php artisan migrate`
- Verificar autenticação do usuário
- Verificar logs em storage/logs

