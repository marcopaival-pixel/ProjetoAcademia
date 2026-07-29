# API REST v1 (Laravel Sanctum)



Base URL: `{APP_URL}/api/v1`



Autenticação: header `Authorization: Bearer {token}` (exceto health e emissão de token).



Especificação OpenAPI: [openapi-v1.yaml](./openapi-v1.yaml)



## Endpoints



| Método | Path | Auth | Descrição |

|--------|------|------|-----------|

| GET | `/health` | Não | Estado do serviço |

| POST | `/auth/token` | Não | Emite token (email + password) |
| POST | `/auth/token/refresh` | Bearer | Renova token (revoga o anterior) |
| GET | `/me` | Bearer | Perfil do utilizador autenticado |
| DELETE | `/auth/token` | Bearer | Revoga o token atual |

| GET | `/training-plans` | Bearer | Lista planos do utilizador |

| GET | `/training-plans/{id}` | Bearer | Detalhe do plano (exercícios e séries) |

| GET | `/payments/status` | Bearer | Gateway ativo e métodos (sem segredos) |

| GET | `/nutrition/diary?date=` | Bearer | Diário alimentar do dia |

| GET | `/workout-sessions` | Bearer | Sessões de treino (RPE) |

| POST | `/workout-sessions` | Bearer | Registar/atualizar sessão por data |

### API Paciente (app mobile)

Requer Bearer. Endpoints abaixo (exceto contextos/links) exigem header **`X-Active-Context`** ou **`X-Active-Context-ID`** com o ID do vínculo (`professional_patients.id`, status `Sim`).

| Método | Path | Contexto | Descrição |
|--------|------|----------|-----------|
| GET | `/patient/contexts` | Não | Contextos de vínculo ativos |
| GET | `/patient/links` | Não | Lista vínculos paciente-profissional |
| POST | `/patient/active-context` | Não | Valida `context_id` do vínculo |
| GET | `/patient/dashboard` | Sim | Resumo (peso, consulta, plano) |
| GET/POST | `/patient/messages` | Sim | Mensagens com o profissional |
| GET | `/patient/medical-records` | Sim | Documentos partilhados |
| GET | `/patient/medical-records/{type}/{id}/download` | Sim | PDF (`report`, `prescription`, `certificate`) |
| GET | `/patient/assessments` | Sim | Avaliações físicas aprovadas |
| GET | `/patient/evolution` | Sim | Evolução corporal |
| GET/POST | `/patient/agenda/appointments` | Sim | Consultas |
| GET | `/patient/agenda/slots` | Sim | Horários disponíveis |

### API Profissional (app mobile)

Requer Bearer + role profissional (`api.professional`).

| Método | Path | Descrição |
|--------|------|-----------|
| GET | `/dashboard` | Métricas, consultas do dia |
| GET | `/professional/patients` | Lista pacientes vinculados |
| GET | `/professional/patients/{id}` | Detalhe do paciente |
| GET | `/professional/patients/requests` | Solicitações pendentes |
| POST | `/professional/patients/requests/{id}/approve` | Aprovar vínculo |
| POST | `/professional/patients/requests/{id}/reject` | Rejeitar vínculo |
| GET | `/professional/appointments` | Agenda |
| PATCH | `/professional/appointments/{id}/status` | Atualizar status |
| GET | `/professional/alerts` | Alertas NexSense |
| PATCH | `/professional/alerts/{id}/read` | Marcar alerta lido |
| GET | `/professional/protocols` | Protocolos da clínica |
| GET/POST | `/professional/patients/{id}/training-plans` | Planos do paciente |
| GET/POST | `/professional/patients/{id}/assessments` | Avaliações |
| GET/POST | `/professional/patients/{id}/evolution-photos` | Fotos de evolução |

### Onboarding mobile

| Método | Path | Descrição |
|--------|------|-----------|
| POST | `/onboarding/profile` | Completa perfil inicial (Android) |



## POST /auth/token



**Body (JSON):**



```json

{

  "email": "user@example.com",

  "password": "secret",

  "device_name": "app-mobile"

}

```



**Rate limit:** 10 pedidos/minuto por IP.

**Resposta:** inclui `expires_at` (ISO-8601). TTL padrão: 30 dias (`SANCTUM_TOKEN_EXPIRATION_DAYS`).

## POST /auth/token/refresh

Renova o token atual (Bearer obrigatório). O token anterior é revogado.

**Body (JSON):**

```json
{
  "device_name": "app-mobile"
}
```

**Resposta:** mesmo formato de `POST /auth/token`.

## Comissões (scheduler)

```bash
php artisan commissions:release-available
php artisan commissions:cleanup-orphans --dry-run
```

Agendados em `routes/console.php` (hourly / daily).

## DELETE /auth/token

Revoga o token Bearer atual.

## Smoke test (homologação)

```bash
php artisan app:api:smoke --url=https://beta.seudominio.com.br
```

Opcional (auth + paciente): variáveis `SMOKE_TEST_EMAIL` e `SMOKE_TEST_PASSWORD` no `.env`, ou flags `--email` / `--password`. Com vínculo ativo, valida também `/patient/links` e `/patient/dashboard`.

## GET /nutrition/diary



Query `date` (opcional, padrão: hoje). Resposta inclui `entries` e `totals` (calorias e macros).



## POST /workout-sessions



```json

{

  "session_date": "2026-05-20",

  "rpe_score": 8,

  "mood": "good",

  "notes": "Treino completo"

}

```



## Migração



```bash

php artisan migrate

```



Inclui `personal_access_tokens`, tenant em logs, `academy_company_id` em `ai_vision_logs`.



## Qualidade



```bash

composer test

composer phpstan

```



CI: `.github/workflows/backend-ci.yml`, `.github/workflows/android-ci.yml`.

## Matriz de Controle de Acesso (ACL) - Perfis & Endpoints

| Perfil / Função | Endpoints Disponíveis | Regras de Negócio / Acesso |
|---|---|---|
| **Público** | `/leads`, `/health`, `/auth/token`, `/auth/register`, `/auth/forgot-password`, `/referral/verify` | Sem autenticação requerida. |
| **Autenticado (Comum)** | `/me`, `/organizations`, `/devices`, `/payments/status`, `/media/{type}/{id}`, `/chat/*`, `/ai/orchestrator` | Requer token Bearer válido. |
| **Aluno / Paciente** | `/training-plans`, `/exercise-catalog`, `/exercise-logs`, `/load-logs`, `/nutrition/diary`, `/nutrition/meal-templates`, `/hydration/*`, `/workout-sessions`, `/assessments`, `/evolution-photos`, `/uploads/*`, `/subscriptions/*`, `/student/*` | Acesso restrito a si próprio. Alunos vinculados podem herdar treinos criados por profissionais. |
| **Profissional / Instrutor / Supervisor** | `/professional/dashboard`, `/professional/patients`, `/professional/patients/requests`, `/professional/appointments`, `/professional/protocols`, `/professional/patients/{patient}/*`, `/professional/alerts` | Acesso gerencial para pacientes vinculados e solicitações recebidas. |

