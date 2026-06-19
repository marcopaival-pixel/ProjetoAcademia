# API REST v1 (Laravel Sanctum)

Base URL: `{APP_URL}/api/v1`

Autenticação: header `Authorization: Bearer {token}` (exceto health e emissão de token).

Especificação OpenAPI: [openapi-v1.yaml](./openapi-v1.yaml)

## Endpoints

### Públicos

| Método | Path | Descrição |
|--------|------|-----------|
| GET | `/health` | Estado do serviço |
| POST | `/auth/token` | Emite token (email + password) |
| POST | `/client-errors` | Telemetria de erros do cliente |
| POST | `/referral/verify` | Valida código de indicação |

### Autenticados (Bearer)

| Método | Path | Descrição |
|--------|------|-----------|
| GET | `/me` | Perfil mobile (roles, panels, branding, active_patient_id) |
| PATCH | `/me` | Atualizar perfil |
| POST | `/auth/refresh` | Renovar token (revoga o anterior) |
| DELETE | `/auth/token` | Revogar token atual |
| GET | `/training-plans` | Lista planos de treino |
| GET | `/training-plans/{id}` | Detalhe do plano |
| GET | `/exercise-logs?date=` | Registos de exercício do dia |
| POST | `/exercise-logs/sync` | Criar/atualizar registo (offline sync) |
| DELETE | `/exercise-logs/{id}` | Remover registo |
| GET | `/nutrition/diary?date=` | Diário alimentar (leitura) |
| POST | `/nutrition/diary` | Adicionar alimento |
| PUT | `/nutrition/diary/{id}` | Atualizar alimento |
| DELETE | `/nutrition/diary/{id}` | Remover alimento |
| GET | `/workout-sessions` | Sessões RPE |
| POST | `/workout-sessions` | Registar sessão |
| GET | `/assessments` | Avaliações físicas |
| POST | `/assessments` | Nova avaliação |
| GET | `/assessments/{id}` | Detalhe |
| GET | `/evolution-photos` | Fotos de evolução |
| POST | `/evolution-photos` | Upload multipart |
| DELETE | `/evolution-photos/{id}` | Remover foto |
| GET | `/media/{type}/{id}` | Stream seguro de mídia |
| POST | `/uploads/workout-photo` | OCR ficha de treino (IA) |
| POST | `/uploads/nutrition-photo` | Análise foto refeição (IA) |
| POST | `/chat/send` | Mensagem NexBot |
| GET | `/chat/history` | Histórico do chat |
| DELETE | `/chat/history` | Limpar histórico |
| POST | `/chat/actions` | Executar ação estruturada da IA |
| POST | `/ai/orchestrator` | Orquestrador IA |
| GET | `/ai/orchestrator/status/{jobKey}` | Status job assíncrono |
| POST | `/devices` | Registar token FCM |
| DELETE | `/devices` | Revogar token FCM |
| GET | `/subscriptions/plans` | Planos student |
| POST | `/subscriptions/checkout` | Iniciar checkout (gateway); resposta `pending_payment` inclui `app_return_links` |
| GET | `/student/professionals` | Profissionais vinculados ao aluno |
| GET | `/student/appointments` | Consultas do aluno |
| GET | `/student/appointments/slots` | Horários disponíveis (`professional_id`, `date`) |
| POST | `/student/appointments` | Agendar avaliação/consulta |
| GET | `/professional/dashboard` | Indicadores do painel profissional |
| GET | `/professional/patients` | Alunos/pacientes vinculados |
| GET | `/professional/patients/{id}` | Detalhe resumido do paciente |
| GET | `/professional/appointments` | Agenda do profissional |
| PATCH | `/professional/appointments/{id}/status` | Atualizar status da consulta |
| GET | `/professional/alerts` | Alertas de saúde dos alunos |
| PATCH | `/professional/alerts/{id}/read` | Marcar alerta como lido |
| GET | `/professional/protocols` | Protocolos da clínica |
| GET | `/professional/patients/{id}/training-plans` | Planos de treino do aluno |
| POST | `/professional/patients/{id}/training-plans` | Prescrição rápida / aplicar protocolo |
| GET | `/professional/patients/{id}/training-plans/{planId}` | Detalhe do plano |
| GET | `/professional/patients/{id}/assessments` | Avaliações do aluno |
| POST | `/professional/patients/{id}/assessments` | Registrar avaliação |
| GET | `/professional/patients/{id}/evolution-photos` | Fotos de evolução do aluno |
| POST | `/professional/patients/{id}/evolution-photos` | Upload de foto (multipart) |
| GET | `/payments/status` | Gateway ativo |

## POST /auth/token

```json
{
  "email": "user@example.com",
  "password": "secret",
  "device_name": "app-mobile"
}
```

Utilizadores com role `aluno` recebem automaticamente role `paciente` para acesso unificado ao portal.

## GET /me (campos mobile — Sprint 1)

| Campo | Descrição |
|-------|-----------|
| `is_professional` | Utilizador é profissional/instructor/supervisor |
| `panels` | Painéis web permitidos (`student`, `patient`, `professional`, …) |
| `active_patient_id` | ID do aluno ativo (próprio id para aluno; header `X-Active-Patient-Id` para profissional) |
| `branding` | Cores e nome da clínica (profissional vinculado ou branding próprio) |

Rotas de treino, nutrição, evolução e assinatura exigem role `aluno` ou `paciente` (`403 forbidden` caso contrário).

Rotas `/professional/*` exigem role `professional`, `instructor` ou `supervisor`. Profissionais podem enviar header `X-Active-Patient-Id` para contexto de aluno ativo (validado contra vínculo em `pacientes`).

**Rate limit:** 10 pedidos/minuto por IP.

## POST /auth/refresh

Renova o token Bearer. Body opcional: `{ "device_name": "app-mobile" }`.

## POST /devices

```json
{
  "token": "fcm-registration-id",
  "platform": "android",
  "device_name": "Pixel 8",
  "app_version": "1.0.0"
}
```

Requer `FCM_SERVER_KEY` no `.env` para envio de push. Alertas de saúde (`HealthAlert`) disparam push automático para profissionais vinculados ao aluno.

### Retorno mobile (checkout)

- Deep link: `nexshape://subscription/{success|pending|cancelled|failure}`
- Redirect web (gateway): `GET /app/subscription/return/{status}` → abre o app
- Checkout pago inclui `app_return_links` na resposta JSON

## Respostas

Sucesso: `{ "data": { ... }, "meta": { ... } }` (meta opcional)

Erro (padronizado em toda API v1):

```json
{
  "error": {
    "message": "Descrição legível",
    "code": "validation_error",
    "errors": {}
  }
}
```

Códigos comuns: `unauthenticated`, `forbidden`, `not_found`, `validation_error`, `plan_limit_reached`.

OpenAPI completo: [openapi-v1.yaml](./openapi-v1.yaml) (v1.1.0)

## Migração

```bash
php artisan migrate
```

Inclui `device_tokens`, `personal_access_tokens`, tenant em logs.

## Qualidade

```bash
composer test
composer phpstan
```

CI: `.github/workflows/laravel-ci.yml`.
