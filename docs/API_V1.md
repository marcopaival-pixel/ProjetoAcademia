# API REST v1 (Laravel Sanctum)



Base URL: `{APP_URL}/api/v1`



Autenticação: header `Authorization: Bearer {token}` (exceto health e emissão de token).



Especificação OpenAPI: [openapi-v1.yaml](./openapi-v1.yaml)



## Endpoints



| Método | Path | Auth | Descrição |

|--------|------|------|-----------|

| GET | `/health` | Não | Estado do serviço |

| POST | `/auth/token` | Não | Emite token (email + password) |

| GET | `/me` | Bearer | Perfil do utilizador autenticado |

| DELETE | `/auth/token` | Bearer | Revoga o token atual |

| GET | `/training-plans` | Bearer | Lista planos do utilizador |

| GET | `/training-plans/{id}` | Bearer | Detalhe do plano (exercícios e séries) |

| GET | `/payments/status` | Bearer | Gateway ativo e métodos (sem segredos) |

| GET | `/nutrition/diary?date=` | Bearer | Diário alimentar do dia |

| GET | `/workout-sessions` | Bearer | Sessões de treino (RPE) |

| POST | `/workout-sessions` | Bearer | Registar/atualizar sessão por data |



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



CI: `.github/workflows/laravel-tests.yml`, `.github/workflows/deploy-nexshape.yml`.

## Matriz de Controle de Acesso (ACL) - Perfis & Endpoints

| Perfil / Função | Endpoints Disponíveis | Regras de Negócio / Acesso |
|---|---|---|
| **Público** | `/leads`, `/health`, `/auth/token`, `/auth/register`, `/auth/forgot-password`, `/referral/verify` | Sem autenticação requerida. |
| **Autenticado (Comum)** | `/me`, `/organizations`, `/devices`, `/payments/status`, `/media/{type}/{id}`, `/chat/*`, `/ai/orchestrator` | Requer token Bearer válido. |
| **Aluno / Paciente** | `/training-plans`, `/exercise-catalog`, `/exercise-logs`, `/load-logs`, `/nutrition/diary`, `/nutrition/meal-templates`, `/hydration/*`, `/workout-sessions`, `/assessments`, `/evolution-photos`, `/uploads/*`, `/subscriptions/*`, `/student/*` | Acesso restrito a si próprio. Alunos vinculados podem herdar treinos criados por profissionais. |
| **Profissional / Instrutor / Supervisor** | `/professional/dashboard`, `/professional/patients`, `/professional/patients/requests`, `/professional/appointments`, `/professional/protocols`, `/professional/patients/{patient}/*`, `/professional/alerts` | Acesso gerencial para pacientes vinculados e solicitações recebidas. |

