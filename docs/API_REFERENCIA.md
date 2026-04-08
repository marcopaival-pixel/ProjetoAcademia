# Referência da API Interna

Este documento descreve as principais rotas da API interna, payloads esperados e formatos de resposta. Embora a maioria das rotas do sistema seja baseada em formulários web (Sessão/Blade), existem endpoints dedicados que respondem em JSON para o Chat com IA e busca de alimentos.

## 1. Assistente de IA (Chat)

As rotas de chat exigem autenticação via sessão Laravel e utilizam proteção CSRF.

### Enviar Mensagem
`POST /api/chat/send`

Envia uma mensagem para o assistente e retorna a resposta gerada.

**Payload:**
```json
{
  "message": "Qual é a minha meta de calorias hoje?"
}
```

**Resposta de Sucesso (200 OK):**
```json
{
  "ok": true,
  "message": "Sua meta de calorias hoje é de 2500 kcal...",
  "chat_quota": {
    "is_premium": false,
    "daily_user_limit": 10,
    "daily_user_used": 1
  }
}
```

**Resposta de Erro - Limite Atingido (403 Forbidden):**
```json
{
  "ok": false,
  "code": "chat_quota_exceeded",
  "error": "Limite diário de mensagens do assistente atingido no plano grátis...",
  "plano_url": "http://.../plano",
  "quota": {
    "limit": 10,
    "used": 10
  }
}
```

---

### Histórico de Chat
`GET /api/chat/history`

Retorna as últimas mensagens da conversa do usuário.

**Parâmetros Query:**
- `limit` (opcional, padrão 50): Número de mensagens a retornar.

**Resposta (200 OK):**
```json
{
  "ok": true,
  "messages": [
    {
      "id": 123,
      "role": "user",
      "message": "Olá",
      "created_at": "2024-03-20T10:00:00Z"
    },
    {
      "id": 124,
      "role": "assistant",
      "message": "Olá! Como posso ajudar?",
      "created_at": "2024-03-20T10:00:05Z"
    }
  ],
  "chat_quota": { ... }
}
```

---

### Limpar Histórico
`POST /api/chat/clear`

Remove todas as mensagens do histórico do usuário logado.

**Resposta (200 OK):**
```json
{
  "ok": true,
  "message": "Histórico limpo"
}
```

---

## 2. Busca de Alimentos (Open Food Facts)

### Pesquisa de Alimentos
`GET /api/food/search`

Realiza uma busca textual por nomes de produtos.

**Parâmetros Query:**
- `q` (obrigatório, min 2 chars): Termo de busca.

**Resposta (200 OK):**
```json
{
  "ok": true,
  "products": [
    {
      "code": "12345678",
      "product_name": "Iogurte Natural",
      "brands": "Marca X",
      "image_url": "...",
      "nutriments": {
        "energy-kcal_100g": 60,
        "proteins_100g": 4.1,
        "carbohydrates_100g": 5.0,
        "fat_100g": 3.2
      }
    }
  ],
  "source": "Open Food Facts"
}
```

---

### Detalhe do Produto por Código
`GET /api/food/product/{code}`

Busca informações detalhadas de um produto específico via código de barras.

**Resposta (200 OK):**
```json
{
  "ok": true,
  "product": {
    "product_name": "Iogurte Natural",
    "nutriments": { ... }
  }
}
```

---

## 3. Endpoints de Webhook (Mercado Pago)

### Notificações de Pagamento
`POST /mp/webhook` e `POST /mp_webhook.php` (Legado)

Recebe notificações assíncronas do Mercado Pago sobre updates em pagamentos ou assinaturas.

---

## 4. Formulários Web e Payloads (Laravel FormRequests)

Embora esses endpoints retornem redirecionamentos (`302 Found`), os dados devem seguir as validações abaixo:

### Registrar Peso (`POST /weight`)
- `weighed_at`: data (YYYY-MM-DD)
- `weight_kg`: numérico (0 to 600)
- `notes`: string (opcional)

### Diário de Alimentos (`POST /diary`)
- `entry_date`: data (YYYY-MM-DD)
- `meal_type`: breakfast, lunch, dinner, snack, other
- `food_name`: string (max 120)
- `amount`: numérico
- `unit`: string (g, ml, un, etc)
- `calories`: inteiro
- `protein_g`, `carbs_g`, `fat_g`: numérico (opcional)

### Atividade Física (`POST /exercise`)
- `entry_date`: data (YYYY-MM-DD)
- `activity_type`: string (max 120)
- `duration_min`: inteiro (0 to 1440)
- `calories_burned`: inteiro (opcional)

---

## Observações Gerais
- **Autenticação**: Todas as rotas (exceto webhooks) exigem o cookie de sessão `laravel_session`.
- **CSRF**: Requisições `POST`, `PUT`, `DELETE` devem incluir o cabeçalho `X-CSRF-TOKEN`.
- **Throttling**: A busca de alimentos possui limites de taxa para evitar bloqueios na API do Open Food Facts.
