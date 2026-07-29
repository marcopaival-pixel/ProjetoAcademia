# Auditoria Técnica, Funcional, Arquitetural e Operacional 360° — NexShape

**Repositório:** `c:\Projetos\PaivatechSolutions\NexShape`  
**Data:** 28 de julho de 2026  
**Modo:** auditoria estática (código, rotas, migrações, configuração, documentação); produção não validada em runtime nesta sessão.  
**Escopo:** monorepo completo — backend Laravel, app Android, documentação, infraestrutura e integrações.  
**Referências anteriores:** `docs/AUDITORIA_COMPLETA_NEXSHAPE_2026-06-17.md`, `docs/AUDITORIA_360_2026-05-21.md`, `docs/AUDITORIA_ANDROID_ALUNO_2026-07-17.md`

> **Atualização pós-implementação (28/07/2026):** correções P0–P2 aplicadas no código. Ver [`STATUS_IMPLEMENTACAO_2026-07-28.md`](./STATUS_IMPLEMENTACAO_2026-07-28.md) para o mapa atualizado (IDOR, clawback, API paciente/profissional, CI, scripts deploy). Notas deste relatório refletem o estado **antes** dessas correções onde não indicado.

---

## Resumo executivo

| Dimensão | Nota (0–10) | Avaliação | Principais riscos |
|----------|:-----------:|-----------|-------------------|
| Arquitetura | **6,5** | Monorepo maduro, multi-tenant v2, modular por persona | Superfície enorme (824 rotas); dual RBAC (roles + plan features + menus) |
| Segurança | **5,5** | CSRF, headers, rate limit sólidos | IDOR admin cross-tenant; tokens Sanctum sem expiração |
| Performance | **6,0** | Índices tenant, Pulse, cache configurável | Default dev (DB cache/queue); N+1 não auditado em runtime |
| Código | **6,0** | Services bem separados, 109 serviços | 194 controllers; cobertura de testes ~0,5% |
| UX/UI | **7,0** | Tailwind, Compose mobile, painéis por persona | Módulo paciente mobile parcial vs web |
| Testes | **2,0** | 5 arquivos PHPUnit | Sem testes auth, pagamento, tenant, admin |
| Deploy | **5,5** | Runbooks detalhados, script PowerShell | Sem CI/CD; scripts referenciados ausentes |
| Escalabilidade | **6,0** | Redis-ready, filas, Reverb, S3 backup | Queue default `database`; broadcast default `log` |

**Achados totais (consolidado):** 5 CRÍTICOS · 14 ALTOS · 22 MÉDIOS · 12 BAIXOS  
**Prontidão para produção:** **Condicional** — exige correção de itens CRÍTICOS/ALTOS de segurança e financeiro antes de go-live amplo.

---

# 1. Visão Geral do Sistema

## 1.1 Identificação

| Item | Valor |
|------|-------|
| **Nome** | NexShape |
| **Tipo** | SaaS multi-tenant — fitness, nutrição, clínica, prontuário, PDF oficial, IA, comercial/representantes |
| **Objetivo de negócio** | Plataforma unificada para academias/clínicas gerirem alunos, pacientes, profissionais, treinos, nutrição, evolução corporal, prontuário, assinaturas, comissões e documentos oficiais |
| **Público-alvo** | Administradores de clínica/academia, profissionais de saúde/educação física, alunos, pacientes, representantes comerciais |

## 1.2 Tecnologias utilizadas

| Camada | Stack |
|--------|-------|
| **Backend** | PHP 8.2+, Laravel 11.31, MySQL 8.x, Sanctum 4, Reverb 1, Pulse 1.7 |
| **Frontend web** | Blade, Vite 6, Tailwind 3.4, Axios, Laravel Echo + Pusher-js |
| **Mobile** | Android Kotlin 2.0, Jetpack Compose, Retrofit 2.11, Room, Firebase FCM/Crashlytics |
| **IA** | OpenAI (gpt-4o, gpt-4o-mini, gpt-5.6 workout import), Google Vision OCR |
| **Pagamentos** | Mercado Pago (primário), Asaas (secundário) |
| **Observabilidade** | Sentry, Laravel Pulse, health endpoints, `system_errors` |
| **Backup** | Spatie Laravel Backup → S3 |
| **Real-time** | Laravel Reverb (WebSockets) |
| **iOS / Orquestrador** | Pastas reservadas vazias (`ios/`, `ai-orchestrator/`) |

## 1.3 Estrutura de pastas (monorepo)

```text
NexShape/
├── backend/           # API REST v1 + painéis web (Admin, Profissional, Paciente, Aluno, Representante)
├── android/           # App Kotlin/Compose (aluno, profissional, paciente)
├── ios/               # Placeholder
├── ai-orchestrator/   # Placeholder
├── ai-agents/         # 13 definições de agentes IA (prompts)
├── docs/              # Documentação técnica, legal, deploy, auditorias
├── docker/            # Laravel Sail compose.yaml
└── scripts/           # PowerShell (deploy, dev setup)
```

## 1.4 Dependências principais

**Backend (`backend/composer.json`):**
- `laravel/framework ^11.31`, `laravel/sanctum ^4`, `laravel/reverb ^1`, `laravel/pulse ^1.7`
- `openai-php/client`, `google/apiclient`, `dompdf/dompdf`, `endroid/qr-code`
- `sentry/sentry-laravel`, `spatie/laravel-backup`, `league/flysystem-aws-s3-v3`
- Dev: PHPUnit 11, PHPStan/Larastan, Pint, Sail, Pail

**Frontend assets (`backend/package.json`):**
- Vite 6, Tailwind 3.4, laravel-vite-plugin, laravel-echo, pusher-js

**Android (`android/gradle/libs.versions.toml`):**
- AGP 8.7.3, Kotlin 2.0.21, Compose BOM 2024.10.01, Retrofit 2.11, Room 2.6.1, Firebase BOM 33.7.0

## 1.5 Versões de runtime

| Componente | Versão |
|------------|--------|
| PHP | ^8.2 (Sail compose referencia 8.5) |
| MySQL | 8.0+ / 8.4 (Sail) |
| Node.js (build) | 18+ |
| Android minSdk | 26 |
| Android targetSdk | 35 |
| App Android | 1.8.1 (versionCode 4) |

## 1.6 Serviços externos

| Serviço | Finalidade | Custo |
|---------|------------|-------|
| Mercado Pago | Checkout, assinaturas, webhooks | Pago (taxas por transação) |
| Asaas | Gateway alternativo | Pago |
| OpenAI | Chat nutricional, evolução, importação treino | Pago (por token) |
| Google OAuth | Login social web/mobile | Gratuito (quotas) |
| Google Cloud Vision | OCR fotos de treino | Pago (por request) |
| Open Food Facts | Catálogo alimentar | Gratuito (API pública) |
| AWS S3 | Backup, storage PDF | Pago |
| Sentry | Error tracking | Freemium/pago |
| Firebase (Android) | Push, Crashlytics | Freemium |
| WhatsApp (opcional) | Envio PDF | Depende do gateway |
| Omnichannel | Bot/mensagens | Webhook com secret |

## 1.7 Métricas do codebase

| Métrica | Quantidade |
|---------|------------|
| Rotas HTTP | ~824 |
| Controllers | 194 |
| Models Eloquent | 179 |
| Services | 109 |
| Migrations | 250 |
| Policies Laravel | 7 (+ 1 service policy) |
| Testes PHPUnit | 5 arquivos |
| Telas Android | 34 `*Screen.kt` |

---

# 2. Inventário de Funcionalidades

Organizado por módulos. Status: **Completo** / **Parcial** / **Pendente**.

## 2.1 Autenticação e sessão

| Item | Detalhe |
|------|---------|
| **Objetivo** | Login web (session), API mobile (Sanctum), Google OAuth, verificação e-mail, registro com aprovação |
| **Rotas** | `routes/auth.php`, `routes/api.php` (`/auth/*`), `routes/admin.php` (login admin) |
| **Controllers** | `Auth/*`, `Api/V1/AuthTokenController`, `MobileAuthController`, `SessionTimeoutController` |
| **Services** | `EmailVerificationService`, `TransactionalMailService` |
| **Models** | `User`, `AuthAuditLog` |
| **Views** | `resources/views/auth/*` |
| **Tabelas** | `users`, `password_reset_tokens`, `personal_access_tokens`, `auth_audit_logs` |
| **Status** | **Parcial** — API token sem expiração; gates de registro pending não replicados na API |
| **Riscos** | Token Sanctum permanente; `test_api_auth.php` gera token de debug |
| **Melhorias** | Expiração tokens; alinhar validações web→API |

## 2.2 Multi-tenant e isolamento

| Item | Detalhe |
|------|---------|
| **Objetivo** | Isolamento por `academy_company_id` / `clinic_id`; impersonation admin |
| **Middleware** | `TenantMiddleware`, `HandleClinicImpersonation`, `EnsurePanelIsolation`, `SetApiTenantContext` |
| **Migration** | `2026_05_03_120856_implement_multi_tenant_isolation_v2.php` |
| **Status** | **Parcial** — admin bypass em várias policies e `PatientAccessGuard` |
| **Riscos** | IDOR cross-tenant (CRÍTICO) |
| **Melhorias** | Exigir impersonation para admin em dados sensíveis |

## 2.3 Painel Admin (~304 rotas protegidas)

| Item | Detalhe |
|------|---------|
| **Objetivo** | Gestão global: clínicas, usuários, planos, financeiro, LGPD, PDF, IA, deploy, config center |
| **Rotas** | `routes/admin.php` — `middleware(['auth','admin'])` + `permission:*` |
| **Controllers** | 56 em `Admin\` |
| **Status** | **Parcial** — amplo; RBAC granular via permissions |
| **Riscos** | Admin global sem tenant scope em export LGPD e audit show |
| **Melhorias** | Scope tenant em todas ações delegadas |

## 2.4 Painel Profissional (~59 rotas)

| Item | Detalhe |
|------|---------|
| **Objetivo** | Pacientes vinculados, prontuário, prescrições, evolução, agenda, alertas |
| **Rotas** | `routes/professional.php` |
| **Controllers** | 13 em `Professional\` |
| **Policies/Gates** | `ProfessionalPatientPolicy`, `PatientAccessGuard` |
| **Status** | **Completo** (web) / **Parcial** (mobile Pro) |
| **Riscos** | `MedicalRecordController::checkLink` sem validação company |
| **Melhorias** | Unificar guard em todos endpoints |

## 2.5 Portal Paciente (web ~38 rotas)

| Item | Detalhe |
|------|---------|
| **Objetivo** | Dashboard unificado, documentos, agenda, prescrições, ativação por token |
| **Rotas** | `routes/patient.php` |
| **Controllers** | `Patient\`, `PortalController` |
| **Status** | **Completo** (web) |
| **Riscos** | `/patient/subscription` sem `role:paciente` |
| **Melhorias** | Middleware de role consistente |

## 2.6 Portal Aluno / Core fitness (~212 rotas web + features)

| Item | Detalhe |
|------|---------|
| **Objetivo** | Treinos, nutrição, hidratação, evolução, gamificação, comunidade, suplementos, chat IA |
| **Rotas** | `routes/web.php`, `routes/features.php` |
| **Controllers** | Root + `Student\` |
| **Status** | **Completo** (web) / **Parcial** (mobile aluno — gaps API documentados) |
| **Riscos** | IDOR em secure-files e assessments para admin |
| **Melhorias** | Testes de isolamento; alinhar API mobile |

## 2.7 Representante comercial (~6 rotas)

| Item | Detalhe |
|------|---------|
| **Objetivo** | Leads, propostas, contratos, comissões, saques |
| **Rotas** | `routes/representative.php` |
| **Status** | **Parcial** — comissões com gaps de clawback e créditos IA |
| **Riscos** | Race condition referral codes |
| **Melhorias** | Idempotência e clawback automático |

## 2.8 API REST v1 (~117 rotas)

| Item | Detalhe |
|------|---------|
| **Objetivo** | Mobile Android: auth, treino, nutrição, evolução, assinaturas, profissional, paciente |
| **Rotas** | `routes/api.php` |
| **Controllers** | 29 + 7 `Patient\` |
| **Docs** | `docs/API_V1.md`, `docs/openapi-v1.yaml` |
| **Status** | **Parcial** — sub-API paciente incompleta (documentos, avaliações pendentes) |
| **Riscos** | Endpoints paciente parciais vs telas Android |
| **Melhorias** | Completar rotas comentadas em `api.php:198` |

## 2.9 Pagamentos e assinaturas

| Item | Detalhe |
|------|---------|
| **Objetivo** | Checkout planos, créditos IA, webhooks, comissões |
| **Rotas** | `web.php`, `mercado_pago.php`, `api.php`, `patient.php` |
| **Services** | `PaymentGatewayManager`, `PaymentProcessor`, `MercadoPagoService`, `AsaasService`, `SubscriptionService`, `CommissionService` |
| **Models** | `Payment`, `Subscription`, `Commission`, `Coupon`, `CreditoCompra` |
| **Status** | **Parcial** — pipelines paralelos (legado créditos, simulação student) |
| **Riscos** | Double-credit webhook (CRÍTICO); double-count dashboard |
| **Melhorias** | Pipeline unificado; idempotência |

## 2.10 Módulo IA

| Item | Detalhe |
|------|---------|
| **Objetivo** | Chat nutricional, evolução corporal, importação treino OCR, body analysis |
| **Services** | `AI/OrchestratorService`, 15+ agents, `WorkoutImportOrchestrator` |
| **Models** | `AiCreditWallet`, `AiUsage`, `AIOrchestratorLog`, `BodyAnalysis` |
| **Status** | **Completo** (funcional) / **Parcial** (governança/custos) |
| **Riscos** | Custos OpenAI sem hard cap por tenant |
| **Melhorias** | Quotas por plano; auditoria de uso |

## 2.11 PDF oficial (SaaS multi-empresa)

| Item | Detalhe |
|------|---------|
| **Objetivo** | Templates, geração, assinatura, validação pública, WhatsApp/e-mail |
| **Controllers** | `Admin/Pdf*`, jobs de entrega |
| **Status** | **Completo** |
| **Riscos** | WhatsApp driver `none` default |
| **Melhorias** | Configurar gateway em produção |

## 2.12 LGPD e privacidade

| Item | Detalhe |
|------|---------|
| **Objetivo** | Consentimento, incidentes, exportação, exclusão conta |
| **Rotas** | `/legal/*`, `admin/lgpd/*` |
| **Tabelas** | `user_consents`, `security_incidents` |
| **Status** | **Parcial** — templates legais; DPIA template disponível |
| **Riscos** | Export admin sem tenant scope |
| **Melhorias** | Política publicada; registro de tratamento |

## 2.13 Omnichannel

| Item | Detalhe |
|------|---------|
| **Objetivo** | Bot, conversas, webhook externo |
| **Rotas** | `POST /omnichannel/webhook` (público com secret) |
| **Status** | **Parcial** |
| **Riscos** | Secret opcional fora production |
| **Melhorias** | Exigir secret em staging |

## 2.14 Deploy governance (in-app)

| Item | Detalhe |
|------|---------|
| **Objetivo** | Checklist go-live, releases rastreadas |
| **Artefatos** | `DeployReleaseController`, `DeployChecklistCommand` |
| **Status** | **Parcial** — `GO_LIVE_CHECKLIST.md` referenciado mas ausente |
| **Melhorias** | Restaurar checklist e scripts staging |

## 2.15 App Android

| Item | Detalhe |
|------|---------|
| **Modos** | STUDENT, PROFESSIONAL, PATIENT |
| **Auth** | Sanctum + EncryptedSharedPreferences + app lock biométrico |
| **Offline** | Room + WorkManager sync |
| **Status** | **Parcial** — módulo paciente novo; CI documentado mas ausente |
| **Melhorias** | CI GitHub Actions; alinhar API paciente |

---

# 3. Auditoria de Rotas

## 3.1 Resumo quantitativo

| Arquivo | Rotas ~ | Públicas ~ | Protegidas ~ |
|---------|--------:|-----------:|-------------:|
| `web.php` | 267 | 55 | 212 |
| `admin.php` | 307 | 3 | 304 |
| `features.php` | ~100 | 0 | ~100 |
| `api.php` | 117 | 6 | 111 |
| `professional.php` | 59 | 0 | 59 |
| `patient.php` | 38 | 0* | 38 |
| `auth.php` | 23 | 6 | 17 |
| `representative.php` | 6 | 0 | 6 |
| `mercado_pago.php` | 5 | 2 | 3 |
| **Total** | **~824** | **~74 (9%)** | **~750 (91%)** |

*Paciente: rotas de ativação/token públicas dentro do arquivo.

## 3.2 Middleware global (web)

Registrado em `bootstrap/app.php`:
- `SecurityHeaders`, `TenantMiddleware`, `EnsureEmailIsVerified`, `CheckRouteMenuAccess`
- `SessionTimeoutMiddleware`, `EnforcePatientReadOnly`, maintenance/read-only, demo safety

## 3.3 Rotas públicas de alto risco (requerem hardening)

| URL | Método | Controller | Middleware | Risco |
|-----|--------|------------|------------|-------|
| `/mp/webhook`, `/mp_webhook.php` | POST | MercadoPago Webhook | CSRF exempt; HMAC secret | **Alto** se `MP_WEBHOOK_SECRET` vazio |
| `/payment/webhook/{gateway}` | POST | Payment Webhook | CSRF exempt | **Alto** — validação por gateway |
| `/omnichannel/webhook` | POST | Omni | `X-Omni-Secret` | **Alto** se secret vazio |
| `/api/v1/auth/token` | POST | AuthToken | throttle 10/min | Brute force mitigado |
| `/api/v1/referral/verify` | POST | Referral | throttle 30/min | Enumeração códigos |
| `/checkout/{plan}` | GET | Checkout | guest OK | OK |
| `/legal/*` | GET | Privacy | público | OK |
| `GET /up`, `/health` | GET | Health | público | OK (não expor dados sensíveis) |

## 3.4 Rotas sem autenticação (intencionais vs problemáticas)

**Intencionais:** webhooks, health, legal, checkout guest, ativação paciente por token, proposta pública.

**Problemáticas / atenção:**
- `POST admin/logout` fora do grupo `auth+admin` (baixo risco)
- Rotas demo com `block.demo.prod` — validar em prod
- Catch-all `/{slug}` — risco de shadowing; monitorar

## 3.5 Rotas sem autorização adequada

| Rota | Problema | Prioridade |
|------|----------|------------|
| `/secure-files/evolution/{id}` | Admin bypass tenant | P0 |
| `/secure-files/body-analysis/{id}` | Admin bypass tenant | P0 |
| `GET /api/v1/training-plans/{id}` | Policy admin true | P0 |
| `GET /admin/audit/{id}` | show sem tenant scope | P1 |
| `/patient/subscription` | Sem role paciente | P2 |

## 3.6 Rotas duplicadas / legado

| Padrão | Observação |
|--------|------------|
| `/mp/webhook` + `/mp_webhook.php` | Compatibilidade legado MP |
| `CreditoController::webhook` | Pipeline fora do unificado — deprecar |
| Student checkout simulado | Paralelo ao gateway real |

## 3.7 Rotas de debug (não HTTP, mas no repo)

| Arquivo | Risco se deploy incorreto |
|---------|---------------------------|
| `backend/scratch_debug.php` | Muta roles/onboarding |
| `backend/test_api.php` | Teste cURL local |
| `backend/test_api_auth.php` | Imprime Sanctum token |
| `backend/test_view.html` | Snapshot com IP LAN |

**Mitigação:** DocumentRoot = `public/`; excluir do pacote deploy.

## 3.8 Checklist rotas

- [x] 91% rotas protegidas por auth
- [ ] 100% rotas sensíveis com policy/tenant scope
- [x] CSRF em rotas web (exceto webhooks)
- [ ] Rate limit em todas APIs públicas sensíveis
- [ ] Remover/ignorar rotas legado créditos
- [ ] Completar sub-rotas API paciente

---

# 4. Auditoria de Banco de Dados

## 4.1 Visão geral

- **250 migrations** em `backend/database/migrations/`
- **Multi-tenant v2:** colunas `academy_company_id`, `clinic_id` propagadas
- **Índices performance:** migrations `add_tenant_performance_indexes`, `add_tenant_columns_to_*`
- **Seeds:** `RolesAndPermissionsSeeder`, `ExerciseCatalogSeeder`, `DeployHomologBootstrapSeeder`, etc.

## 4.2 Domínios de tabelas (principais)

| Domínio | Tabelas representativas |
|---------|-------------------------|
| Tenant | `academy_companies`, `clinics`, `organizations` |
| RBAC | `roles`, `permissions`, `user_roles`, `role_permissions`, `role_menu_permissions` |
| Users | `users`, `user_profiles`, `professional_profiles` |
| Clinical | `patients`, `professional_patient`, medical_* |
| Fitness | `training_plans`, `workout_sessions`, `exercise_catalog`, `body_assessments` |
| Nutrition | `foods`, `food_entries`, `meal_templates`, `nutrients` |
| Finance | `payments`, `subscriptions`, `commissions`, `financial_logs`, `coupons` |
| AI | `ai_credit_wallets`, `ai_usages`, `ai_orchestrator_logs`, `body_analyses` |
| PDF | `pdf_templates`, `historico_pdfs`, `pdf_signatures` |
| LGPD | `user_consents`, `security_incidents` |
| Ops | `audit_logs`, `system_errors`, `deploy_releases`, `api_access_logs` |

## 4.3 Relacionamentos e FKs

| Área | Estado | Risco |
|------|--------|-------|
| Financeiro (`payments`, `commissions`) | FKs presentes; UNIQUE payment_id comissão | CASCADE apaga histórico |
| Tenant cols | Nem todas com FK explícita | Integridade lógica vs DB |
| Observability logs | user_id sem FK em alguns | Logs órfãos |
| `subscriptions.plan_id` | CASCADE | Apagar plano apaga subs |

## 4.4 Soft deletes

- **Predominantemente ausente** em tabelas financeiras e audit
- **Risco:** perda irreversível de histórico em delete user/payment

## 4.5 Índices

- Migrations dedicadas a índices tenant — **positivo**
- Dynamic CRUD Configuration Center — validar índices em tabelas dinâmicas

## 4.6 Multi-tenant

- **Implementado:** middleware + colunas + impersonation
- **Gap:** policies admin não respeitam tenant consistentemente

## 4.7 Comando de verificação

```bash
php artisan finance:reconcile --days=30
```

## 4.8 Checklist BD

- [x] Migrations versionadas (250)
- [x] Seeds RBAC e catálogos
- [ ] Soft delete financeiro
- [ ] FK em todas colunas tenant
- [ ] Job cleanup comissões payment_id null
- [ ] Documentação dicionário atualizada (`docs/dicionario_dados.md`)

---

# 5. Auditoria de Segurança

## 5.1 Matriz de controles

| Controlo | Estado | Notas |
|----------|--------|-------|
| Autenticação web | **Bom** | Session regenerate, timeout, email verified |
| Autenticação API | **Médio** | Sanctum sem expiração |
| Autorização | **Médio–Baixo** | IDOR admin; poucas policies (7) |
| CSRF | **Bom** | Exceções webhooks documentadas |
| XSS | **Médio** | CSP com unsafe-inline; markdown AI |
| SQL Injection | **Baixo** | Eloquent; exceção sort_by Dynamic CRUD |
| Rate limiting | **Bom** | Login, API token, referral |
| Security headers | **Bom** | `SecurityHeaders` middleware (HSTS prod) |
| Criptografia | **Bom** | bcrypt 12; arquivos sensíveis em disk privado |
| Upload arquivos | **Médio** | Validar MIME/size em todos endpoints |
| Secrets | **Médio** | `.env.example` com APP_DEBUG=true default |
| Logs sensíveis | **Médio** | Debug log email em APP_DEBUG |

## 5.2 Achados críticos de segurança

| ID | Achado | Impacto | Correção |
|----|--------|---------|----------|
| SEC-01 | Admin bypass `PatientAccessGuard` | Acesso cross-tenant a fotos/dados | Exigir impersonation |
| SEC-02 | Policies médicas admin `return true` | IDOR prontuário/treinos | Alinhar impersonation |
| SEC-03 | Tokens API sem `expires_at` | Token roubado = acesso permanente | TTL 30d + refresh |
| SEC-04 | Double-credit webhook IA | Perda financeira | Idempotência PaymentProcessor |
| SEC-05 | Race referral codes | Double discount/comissão | UPDATE condicional transactional |

## 5.3 LGPD (segurança de dados)

- Consentimento: tabela + banner cookies
- Exportação: `PrivacyController`, admin LGPD
- Exclusão: fluxo account deletion
- **Gap:** DPIA template existe; registro de tratamento operacional pendente
- **Gap:** Sentry `send_default_pii` deve permanecer false

---

# 6. Auditoria de Permissões

## 6.1 Arquitetura RBAC

**Camadas sobrepostas:**
1. Roles DB (`roles`, `permissions`, pivots)
2. Plan features (`User::hasFeature()`)
3. Menu permissions (`CheckRouteMenuAccess`, `MenuAccessService`)
4. Policies + Gates pontuais
5. Middleware por painel (`admin`, `professional.panel`, `role:paciente`, `role:representative`)

## 6.2 Roles principais (seeder)

| Role | Escopo |
|------|--------|
| `admin` | Todas permissions |
| `manager` | Clínica + PDF + config center |
| `instructor` | Treinos + PDF limitado |
| `professional` | Painel profissional |
| `aluno` | Portal aluno |
| `paciente` | Portal paciente |
| `representative` | Comercial |
| `receptionist` | Recepção |

## 6.3 Permissions seed (~20)

Inclui: `admin.access`, `finance.access`, `pdf.*`, `configuration-center.access`, `training.manage`, etc.

## 6.4 Policies registradas (7)

`PatientPolicy`, `TrainingPlanPolicy`, `CouponPolicy`, `PdfTemplatePolicy`, `CommunicationGroupPolicy`, `FinancialReportPolicy`, `DeployReleasePolicy`

**Gap:** muitos recursos usam Gates inline ou middleware apenas — cobertura policy incompleta vs superfície de rotas.

## 6.5 Escalonamento de privilégio — vetores

| Vetor | Severidade |
|-------|------------|
| Admin global sem impersonation | CRÍTICO |
| Manager isento em `FiltersByProfessional` | MÉDIO |
| API sem `EnsurePanelIsolation` | BAIXO–MÉDIO |
| Premium bypass via plan feature misconfig | MÉDIO |

---

# 7. Auditoria de APIs

## 7.1 API v1 — visão geral

- **Base:** `/api/v1`
- **Auth:** Sanctum Bearer (+ headers contexto paciente)
- **Documentação:** `docs/API_V1.md`, `docs/openapi-v1.yaml`
- **Versionamento:** v1 prefix; sem v2
- **Rate limit:** auth token 10/min; API autenticada 120/min

## 7.2 Endpoints públicos

| Endpoint | Método | Auth |
|----------|--------|------|
| `/api/v1/health` | GET | Não |
| `/api/v1/auth/token` | POST | Não (credenciais) |
| `/api/v1/auth/register` | POST | Não |
| `/api/v1/auth/google` | POST | Não |
| `/api/v1/auth/forgot-password` | POST | Não |
| `/api/v1/auth/reset-password` | POST | Não |

## 7.3 Grupos autenticados (Sanctum)

| Grupo | Endpoints principais |
|-------|---------------------|
| Perfil | `GET/PATCH /me`, revoke token |
| Treino | training-plans, workout-sessions, load-logs, workout-import |
| Nutrição | diary, goal, meal-templates, hydration |
| Evolução | evolution-photos, evolution-reports, body-analysis |
| Assinaturas | plans, checkout, current, cancel |
| Social | community, messages, communication-groups |
| Aluno clínico | professionals, appointments, medical-documents |
| Profissional | (via role headers) patients, agenda |
| **Paciente** | contexts, dashboard, messages, medical-records, evolution, agenda |
| Utilitários | devices, client-errors, chat, ai/credits |

## 7.4 API Paciente — headers de contexto

Android envia via `AuthInterceptor`:
- `X-Active-Patient-Id`, `X-Active-Role`, `X-Active-Tenant`, `X-Active-Context-ID`

Middleware backend: `EnsureActivePatientContext` (`active.patient`)

## 7.5 Gaps API vs Mobile

| Funcionalidade Android | API | Status |
|------------------------|-----|--------|
| Paciente dashboard | `GET /patient/dashboard` | OK |
| Paciente agenda | `GET/POST /patient/agenda/*` | OK |
| Paciente mensagens | `GET/POST /patient/messages` | OK |
| Paciente documentos | — | **Pendente** |
| Paciente evolução | `GET /patient/evolution` | Parcial |
| Real-time mensagens | Reverb/Pusher | Config incompleta `.env` |

---

# 8. Integrações Externas

| Integração | Finalidade | Config | Webhook | Riscos | Alternativas |
|------------|------------|--------|---------|--------|--------------|
| **Mercado Pago** | Pagamentos, subs | `MP_ACCESS_TOKEN`, `MP_WEBHOOK_SECRET` | `/mp/webhook` | Secret obrigatório prod | Asaas |
| **Asaas** | Gateway alt. | Payment settings admin | `/payment/webhook/asaas` | Menos testado | MP |
| **OpenAI** | IA chat/evolução/treino | `OPENAI_API_KEY`, models | — | Custo, dados saúde | Azure OpenAI |
| **Google OAuth** | Login | `GOOGLE_*` | callback | Redirect URI mismatch | — |
| **Google Vision** | OCR treino | `GOOGLE_VISION_API_KEY` | — | Custo por foto | Tesseract local |
| **Open Food Facts** | Alimentos | URL + rate limits | — | Dependência externa | Cache local OK |
| **AWS S3** | Backup/storage | `AWS_*`, `BACKUP_*` | — | Credenciais | MinIO self-host |
| **Sentry** | Errors | `SENTRY_LARAVEL_DSN` | — | PII se mal config | Pulse only |
| **WhatsApp** | PDF delivery | `WHATSAPP_*` | — | Driver `none` default | Twilio, Meta API |
| **Omnichannel** | Bot | `OMNI_WEBHOOK_SECRET` | `/omnichannel/webhook` | Secret opcional dev | — |
| **Firebase** | Push Android | google-services.json | FCM | Config por flavor | — |
| **Reverb/Echo** | WebSockets | REVERB_* (gap .env) | channels.php | Default log | Pusher cloud |

---

# 9. Processamento de Pagamentos

## 9.1 Gateways

- **Primário:** Mercado Pago (`MercadoPagoService`, `routes/mercado_pago.php`)
- **Secundário:** Asaas (`AsaasService`, `PaymentGatewayManager`)
- **Interface:** `PaymentGatewayInterface`, registry pattern

## 9.2 Fluxo checkout (ideal)

```text
Seleção plano → CheckoutController / API SubscriptionController
→ Gateway (MP/Asaas) → Redirect/checkout
→ Webhook (HMAC/signature) → PaymentProcessor
→ Payment + Subscription + Commission + FinancialLog + Invoice Job
```

## 9.3 Fluxos paralelos (risco)

| Fluxo | Problema |
|-------|----------|
| `CreditoController::webhook` | Fora pipeline unificado |
| `Student/SubscriptionController` | Simula pagamento |
| `Professional/SubscriptionController` | Dados mock |

## 9.4 Webhooks

- MP: validação HMAC via `MP_WEBHOOK_SECRET` — 503 se vazio em production
- Generic: `/payment/webhook/{gateway}`
- Idempotência MP: OK; **AI credits Asaas: frágil**

## 9.5 Assinaturas

- Model `Subscription`, `MercadoPagoSubscription`
- Cancel: `SubscriptionService::cancel`
- Upgrade: sem cobrança implementada (gap)

## 9.6 Comissões

- Ciclo: PENDENTE → AGUARDANDO_PAGAMENTO → PENDENTE (carencia) → DISPONIVEL → PAGO
- Dedup por `payment_id` (migration 2026-06-06)
- Clawback manual se comissão já PAGO

## 9.7 Reembolsos

- `PaymentRefundService` — OK parcial
- Vocabulario log `REFUND` vs `PAYMENT_REFUNDED` quebra reconciliação

## 9.8 PCI DSS

- **SAQ A provável:** checkout redirect/hosted fields MP
- Não armazenar PAN/CVV — verificar integração atual MP
- Tokens MP apenas server-side

---

# 10. Auditoria de Performance

## 10.1 Backend

| Área | Estado | Recomendação |
|------|--------|--------------|
| Cache | Default `database` | Redis em prod |
| Queue | Default `database` | Redis + Supervisor |
| Sessions | Default `database` | Redis em prod |
| Índices tenant | Migrations dedicadas | Manter após novas tabelas |
| N+1 queries | Não auditado runtime | Laravel Debugbar/Pulse em staging |
| Paginação | Presente em listagens admin | Auditar endpoints API sem paginate |
| Assets | Vite build + Tailwind | CDN opcional para static |

## 10.2 Frontend web

- Vite code splitting padrão
- Blade server-rendered — TTFB depende PHP
- CSP inline scripts — impacto mínimo performance

## 10.3 Android

- Coil image loading autenticado
- Room offline queue
- ProGuard release enabled

## 10.4 Filas e jobs

5 jobs: evolution reports, PDF delivery, subscription invoice, omni messages, queue heartbeat

---

# 11. Auditoria de Código

## 11.1 Organização

| Aspecto | Avaliação |
|---------|-----------|
| Separação Services | **Boa** — 109 services |
| Controllers | **Médio** — 194, muitos fat |
| Models | **Boa** — domínios claros |
| DRY | **Médio** — pipelines pagamento duplicados |
| SOLID | **Médio** — PaymentGatewayManager OK; controllers grandes |

## 11.2 Qualidade estática

- PHPStan/Larastan configurado em composer scripts
- Laravel Pint disponível
- Baseline generation script presente

## 11.3 Dead code / debug

- `scratch_debug.php`, `test_api*.php`, `test_view.html`
- APKs na raiz do repo (`NexShape-android-debug.apk`)
- Pastas vazias `ios/`, `ai-orchestrator/`

## 11.4 Comentários

- Código majoritariamente auto-explicativo
- Runbooks e docs externos compensam falta inline

---

# 12. Auditoria de Testes

## 12.1 Inventário

| Arquivo | Tipo | Área |
|---------|------|------|
| `BodyAnalysisCreditTest.php` | Feature | Créditos IA + body analysis |
| `EvolutionSessionAnalysisTest.php` | Feature | Evolução + Sanctum + jobs |
| `BodyAnalysisInterpretationServiceTest.php` | Unit | Interpretação |
| `EvolutionSchemaValidatorTest.php` | Unit | Schema IA |
| `EvolutionClaimPublicationPolicyTest.php` | Unit | Policy evolução |

## 12.2 Cobertura estimada

**< 1%** do codebase (5 arquivos vs 824 rotas)

## 12.3 Ausências críticas

- [ ] Auth / Sanctum / registro
- [ ] Tenant isolation / IDOR
- [ ] Payment webhooks / idempotência
- [ ] Commission lifecycle
- [ ] Admin RBAC
- [ ] Patient API context headers
- [ ] E2E (Dusk/Selenium configurado no Sail mas não usado)

## 12.4 Android QA

- `android/MANUAL_TEST_PLAN.md` — manual only
- Sem CI automated tests

---

# 13. Auditoria de Frontend

## 13.1 Web

| Aspecto | Estado |
|---------|--------|
| Responsividade | Tailwind — **Bom** (parcial por tela) |
| UX/UI | Painéis consistentes por persona — **Bom** |
| Acessibilidade | Não auditado WCAG — **Indeterminado** |
| Performance | Vite build — **Médio** |
| SEO | Rotas marketing/legal — **Médio** |

## 13.2 Android

| Aspecto | Estado |
|---------|--------|
| Material/Compose | **Bom** |
| App lock biométrico | **Bom** |
| Offline sync | **Parcial** |
| Deep links assinatura | `nexshape://subscription/{status}` |
| Play Store readiness | Docs RC/checklist presentes |

---

# 14. Auditoria de Deploy

## 14.1 Artefatos disponíveis

| Artefato | Path | Status |
|----------|------|--------|
| Runbook deploy | `docs/DEPLOY_NEXSHAPE.md` | OK |
| Runbook produção | `docs/RUNBOOK_PRODUCAO_NEXSHAPE.md` | OK |
| Git flow | `docs/DEPLOY.md` | OK |
| Package script | `scripts/prepare-deploy-package.ps1` | OK |
| Dev setup | `scripts/setup-dev.ps1` | OK |
| Supervisor example | `docs/supervisor-nexshape.conf.example` | OK |
| Sail compose | `docker/compose.yaml` | OK (dev) |
| `deploy.bat` | Referenciado | **Ausente** |
| `optimize_server.sh` | Referenciado | **Ausente** |
| `GO_LIVE_CHECKLIST.md` | Referenciado | **Ausente** |
| CI/CD GitHub Actions | `android/README.md` | **Ausente** |

## 14.2 Dependências servidor

PHP 8.2+, Composer 2, MySQL 8+, Node 18+ (build), Apache mod_rewrite, Redis recomendado, Supervisor, SSL

## 14.3 Cron / scheduler

Comandos referenciados: backup, purge logs, pulse purge, mysql health, commission release, finance reconcile

---

# 15. O que Enviar para Produção

## 15.1 Enviar

```
app/, bootstrap/, config/, routes/
public/ (inclui build/)
resources/views/
artisan, composer.json, composer.lock
vendor/ (ou composer install --no-dev no servidor)
database/migrations/ (para migrate)
storage/ (estrutura vazia)
```

## 15.2 NÃO enviar

```
.env (criar no servidor)
node_modules/
tests/
.git/
backend/scratch_debug.php
backend/test_api.php
backend/test_api_auth.php
backend/test_view.html
*.apk na raiz
storage/logs/*
public/build.zip
```

## 15.3 Permissões

- `storage/` — writable (775/ www-data)
- `bootstrap/cache/` — writable
- `public/` — readable

## 15.4 Build steps

```powershell
cd backend
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

---

# 16. Checklist Configuração do Servidor

- [ ] PHP 8.2+ com extensões: pdo_mysql, mbstring, openssl, curl, fileinfo, zip, json, xml
- [ ] Composer 2.x
- [ ] MySQL/MariaDB 8.0+/10.6+
- [ ] Node.js 18+ (build local/CI)
- [ ] Redis (cache, session, queue)
- [ ] Supervisor — `queue:work`, scheduler
- [ ] Apache/Nginx — DocumentRoot → `public/`
- [ ] SSL/TLS válido
- [ ] OPcache habilitado
- [ ] Firewall — apenas 80/443
- [ ] Backup S3 configurado
- [ ] Sentry DSN (opcional recomendado)
- [ ] MP + Omni webhook secrets configurados

---

# 17. Passo a Passo de Deploy

1. **Provisionar servidor** — PHP, MySQL, Redis, Supervisor, SSL
2. **Configurar domínio** — DNS → servidor, certificado
3. **Clonar/upload** — branch homologação/main ou pacote ZIP `dist/`
4. **Instalar dependências** — `composer install --no-dev`
5. **Configurar `.env`** — produção (seção 18)
6. **Gerar chaves** — `php artisan key:generate`
7. **Executar migrations** — `php artisan migrate --force`
8. **Build frontend** — `npm ci && npm run build` (se não incluído no pacote)
9. **Ajustar permissões** — storage, bootstrap/cache
10. **Configurar filas e cron** — Supervisor + `* * * * * artisan schedule:run`
11. **Configurar webhooks** — MP/Asaas URLs públicas
12. **Validar aplicação** — smoke tests (seção 26)
13. **Cache config** — `config:cache`, `route:cache`, `view:cache`

---

# 18. Variáveis de Ambiente

## 18.1 Obrigatórias produção

| Variável | Descrição |
|----------|-----------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_KEY` | Chave Laravel |
| `APP_URL` | URL canônica HTTPS |
| `APP_PUBLIC_URL` | URL pública (MP) |
| `DB_*` | Conexão MySQL |
| `MP_ACCESS_TOKEN` | Mercado Pago |
| `MP_WEBHOOK_SECRET` | HMAC webhooks MP |
| `OMNI_WEBHOOK_SECRET` | Webhook omnichannel |
| `MAIL_*` | SMTP real (não `log`) |
| `SESSION_ENCRYPT` | `true` |
| `SESSION_SECURE_COOKIE` | `true` |

## 18.2 Recomendadas produção

| Variável | Descrição |
|----------|-----------|
| `REDIS_*` | Cache/session/queue |
| `QUEUE_CONNECTION` | `redis` |
| `CACHE_STORE` | `redis` |
| `SESSION_DRIVER` | `redis` |
| `AWS_*` / `BACKUP_*` | Backup S3 |
| `SENTRY_LARAVEL_DSN` | Error tracking |
| `OPENAI_API_KEY` | Se IA habilitada |
| `GOOGLE_*` | OAuth + Vision |
| `OPERATIONAL_ALERT_EMAIL` | Alertas críticos |
| `ADMIN_EMAIL` | Seed admin (só deploy inicial) |

## 18.3 Opcionais / feature flags

| Variável | Descrição |
|----------|-----------|
| `WHATSAPP_*` | Envio PDF |
| `PDF_*` | Storage/validação PDF |
| `OPENFOODFACTS_*` | Rate/cache OFF |
| `CHAT_FREE_DAILY_USER_MESSAGES` | Limite chat grátis |
| `BROADCAST_CONNECTION` | `reverb` se real-time |
| `REVERB_*` | **Ausente em .env.example — adicionar** |
| `VITE_*` | Build frontend |

---

# 19. Backup e Recuperação

## 19.1 Estratégia

- **Spatie Laravel Backup** → S3 (`config/backup.php`)
- Comando: `php artisan backup:run` (scheduler)
- Backup nativo alternativo: `app:backup:native`

## 19.2 Retenção

- Configurável em `config/backup.php` — default Spatie (daily/weekly/monthly)
- Definir política conforme LGPD e financeiro

## 19.3 Restore

1. Restaurar dump MySQL
2. Restaurar files S3/storage
3. Validar `.env` e migrations version
4. Smoke test health + login + pagamento sandbox

## 19.4 Testes periódicos

- [ ] Restore mensal em ambiente isolado
- [ ] Verificar integridade backup (tamanho, checksum)
- [ ] Documentar RTO/RPO (não definido — **criar**)

---

# 20. Monitoramento e Logs

## 20.1 Health checks

| Endpoint | Uso |
|----------|-----|
| `GET /up` | Laravel bootstrap |
| `GET /health` | DB, queue, disk |
| `GET /api/v1/health` | Mobile |

## 20.2 Ferramentas recomendadas

| Ferramenta | Finalidade | Status repo |
|------------|------------|-------------|
| **Laravel Pulse** | APM queries/jobs | Instalado, admin-gated |
| **Sentry** | Error tracking | Config presente |
| **UptimeRobot/Pingdom** | Uptime externo | Env vars placeholder |
| **CloudWatch/Datadog** | Infra metrics | Não configurado |
| **Firebase Crashlytics** | Mobile crashes | Android configurado |

## 20.3 Logs aplicacionais

| Log | Cobertura |
|-----|-----------|
| `AuthAuditLog` | Auth lifecycle — **Bom** |
| `FinancialLog` | Pagamentos — **Parcial** (actor admin) |
| `AuditLog` | Config Center only — **Limitado** |
| `ApiAccessLog` | API sampled — **Bom** |
| `system_errors` | HTTP 5xx — **Bom** |
| Laravel log | `storage/logs/laravel.log` | Rotacionar |

## 20.4 Alertas

- `OperationalAlertService` + `OPERATIONAL_ALERT_EMAIL`
- Backup notification email

---

# 21. Conformidade e LGPD

| Requisito | Status | Evidência |
|-----------|--------|-----------|
| Política privacidade | Template | `docs/legal/privacy-policy-template.md`, `/legal/privacy-policy` |
| Consentimento | Implementado | `user_consents`, cookie banner |
| Exportação dados | Implementado | `PrivacyController`, admin LGPD |
| Exclusão conta | Implementado | `/legal/account-deletion` |
| Incidentes segurança | Implementado | `security_incidents`, admin |
| DPIA IA saúde | Template | `docs/legal/DPIA-IA-tratamento-dados-saude-template.md` |
| Registro tratamento | **Pendente** | Operacionalizar |
| DPO/contato | Verificar | Em templates legais |
| Dados sensíveis saúde | Tratamento ativo | Medidas técnicas parciais |
| Transferência internacional | OpenAI/Sentry/AWS | DPIA necessário |
| Android Data Safety | Documentado | `android/PRIVACY_POLICY_APP.md` |

---

# 22. Documentação

| Documento | Status | Qualidade |
|-----------|--------|-----------|
| `README.md` (root) | OK | Básico |
| `docs/API_V1.md` | OK | Detalhado |
| `docs/openapi-v1.yaml` | OK | OpenAPI |
| `docs/DEPLOY_NEXSHAPE.md` | OK | Runbook completo |
| `docs/MONITORAMENTO.md` | OK | Operacional |
| `docs/dicionario_dados.md` | OK | BD |
| `android/README.md` | OK | Detalhado (CI desatualizado) |
| Manual usuário aluno | OK | `docs/manual_portal_aluno_nexshape.md` |
| GO_LIVE_CHECKLIST | **Ausente** | Referenciado |
| API docs inline | Parcial | Swagger não auto-gerado |

---

# 23. Riscos Críticos

| ID | Risco | Impacto | Probabilidade | Mitigação |
|----|-------|---------|---------------|-----------|
| R-01 | IDOR admin cross-tenant | Vazamento prontuário/fotos | Alta | Impersonation obrigatória |
| R-02 | Double-credit webhook IA | Perda financeira | Média | Idempotência |
| R-03 | Token Sanctum permanente | Conta comprometida | Média | TTL + revoke |
| R-04 | Race referral code | Fraude comercial | Média | Lock transacional |
| R-05 | Deploy sem MP_WEBHOOK_SECRET | Pagamentos não confirmados | Alta | Validar env prod |
| R-06 | Zero CI/CD | Regressões em prod | Alta | Pipeline mínimo |
| R-07 | CASCADE delete financeiro | Perda histórico/legal | Baixa | Soft delete |
| R-08 | API paciente incompleta | App quebrada | Alta | Completar endpoints |
| R-09 | Debug files no repo | Escalada se mal deploy | Baixa | Excluir pacote |
| R-10 | Clawback comissão manual | Perda em refund | Média | Automatizar |

---

# 24. Melhorias Recomendadas

## 24.1 Crítica (P0 — 48–72h)

1. Corrigir admin bypass em `PatientAccessGuard` e policies médicas
2. Idempotência `PaymentProcessor::processAiCredits`
3. Lock transacional referral codes
4. Validar webhooks secrets em staging/prod
5. Completar endpoints API paciente críticos (documentos)

## 24.2 Alta (P1 — 1–2 semanas)

1. Expiração tokens Sanctum
2. Deprecar `CreditoController::webhook`
3. Clawback automático comissões
4. CI/CD GitHub Actions (test + build + deploy homolog)
5. Testes IDOR e payment webhooks
6. Restaurar GO_LIVE_CHECKLIST e scripts deploy ausentes
7. Alinhar gates MobileAuth com LoginController

## 24.3 Média (P2 — 1 mês)

1. Soft delete financeiro
2. Audit CRUD models críticos
3. Redis em produção (cache/session/queue)
4. Reverb env vars + broadcast prod
5. Reconciliação vocabulary REFUND
6. Completar Reverb no Android para mensagens real-time
7. PHPStan baseline enforcement

## 24.4 Baixa (P3 — backlog)

1. Normalizar status subscriptions enum
2. CouponUsage tracking
3. WCAG accessibility audit
4. iOS app
5. Remover APKs da raiz git
6. Swagger auto-gen from routes

---

# 25. Nota Geral do Sistema

| Dimensão | Nota | Justificativa |
|----------|:----:|---------------|
| **Arquitetura** | **6,5** | Monorepo sólido, multi-tenant, modular; complexidade alta |
| **Segurança** | **5,5** | Base Laravel boa; IDOR e tokens são gaps sérios |
| **Performance** | **6,0** | Preparado para Redis; defaults dev |
| **Código** | **6,0** | Services organizados; controllers grandes; duplicação pagamentos |
| **UX/UI** | **7,0** | Painéis maduros; mobile paciente em construção |
| **Testes** | **2,0** | Cobertura mínima |
| **Deploy** | **5,5** | Docs bons; execução manual; gaps scripts/CI |
| **Escalabilidade** | **6,0** | Filas, S3, Pulse; precisa Redis e testes carga |

**Média ponderada:** **5,6 / 10** — sistema funcionalmente rico, operacionalmente imaturo para escala enterprise.

---

# 26. Checklist Go-Live

## 26.1 Concluído

- [x] Multi-tenant v2 implementado
- [x] RBAC + menu permissions
- [x] Gateways pagamento MP + Asaas
- [x] API mobile v1 extensa
- [x] App Android 1.8.1 release-ready (aluno/pro)
- [x] LGPD módulo base
- [x] Health endpoints
- [x] Backup Spatie configurável
- [x] Security headers middleware
- [x] Documentação deploy/runbook
- [x] Pulse + Sentry integráveis

## 26.2 Pendências

- [ ] Corrigir IDOR admin (bloqueador)
- [ ] Idempotência créditos IA webhook (bloqueador)
- [ ] MP_WEBHOOK_SECRET + OMNI_WEBHOOK_SECRET prod
- [ ] APP_DEBUG=false, SESSION_ENCRYPT=true
- [ ] Redis produção
- [ ] CI/CD pipeline
- [ ] Testes automatizados mínimos
- [ ] API paciente completa
- [ ] GO_LIVE_CHECKLIST restaurado
- [ ] Restore backup testado
- [ ] Política privacidade publicada (não só template)
- [ ] Remover debug files do pacote deploy

## 26.3 Bloqueadores

1. **SEC-01/02** — IDOR cross-tenant admin
2. **SEC-04** — Double-credit webhook
3. **R-05** — Secrets webhook produção
4. **R-06** — Ausência CI/CD com suite mínima
5. **R-08** — API paciente vs telas Android

---

# 27. Plano de Ação Prioritizado

| Prioridade | Item | Descrição | Impacto | Esforço |
|:----------:|------|-----------|---------|:-------:|
| **P0** | IDOR admin | Exigir impersonation em PatientAccessGuard e policies médicas | Segurança/LGPD | 1–2 dias |
| **P0** | Webhook idempotência IA | Guard em PaymentProcessor antes de creditar wallet | Financeiro | 4h |
| **P0** | Referral race | UPDATE condicional + transaction em markAsUsed | Financeiro/fraude | 4h |
| **P0** | Secrets prod | MP_WEBHOOK_SECRET, OMNI, APP_DEBUG=false | Operacional | 2h |
| **P0** | API paciente docs | Implementar GET documentos + download | Mobile go-live | 2–3 dias |
| **P1** | Token TTL | Sanctum expires_at 30d + refresh flow | Segurança | 4h |
| **P1** | Deprecar webhook legado | Migrar CreditoController → pipeline unificado | Auditoria financeira | 1 dia |
| **P1** | Clawback comissão | Automatizar estorno em refund pós-PAGO | Financeiro | 2 dias |
| **P1** | CI/CD | GitHub Actions: phpunit + npm build + android assemble | Qualidade | 2–3 dias |
| **P1** | Testes IDOR/payment | Feature tests mínimos | Regressão | 3–5 dias |
| **P1** | MobileAuth gates | Email verified + registration approved | Segurança | 4h |
| **P2** | Redis prod | Cache/session/queue | Performance | 1 dia |
| **P2** | Soft delete financeiro | payments, commissions | Compliance | 2 dias |
| **P2** | Audit CRUD | Observers User/Clinic/Payment | Compliance | 3 dias |
| **P2** | Reconciliação REFUND | Unificar action names | Financeiro | 4h |
| **P2** | Reverb prod | Env vars + Echo Android | UX mensagens | 2 dias |
| **P2** | Restaurar scripts | GO_LIVE_CHECKLIST, staging-release.ps1 | Operacional | 1 dia |
| **P3** | iOS app | Implementar ou remover placeholder | Produto | Meses |
| **P3** | WCAG audit | Acessibilidade web | UX/legal | 1 semana |
| **P3** | Swagger auto | Gerar docs API | DX | 2 dias |

---

## Apêndice A — Referências cruzadas de auditorias anteriores

A auditoria de 17/06/2026 (`AUDITORIA_COMPLETA_NEXSHAPE_2026-06-17.md`) detalha achados financeiros (F1–F12), comissões (C1–C6), referral (R1–R4), logs (L1–L5) e BD (D1–D4). **Este documento consolida e atualiza** com estado de julho/2026 incluindo módulo paciente mobile/API.

## Apêndice B — Comandos úteis pós-deploy

```bash
php artisan about
php artisan migrate:status
php artisan finance:reconcile --days=30
php artisan queue:work --once
php artisan schedule:list
php artisan backup:run
curl -s https://app.nexshape.com.br/health | jq .
```

---

*Relatório gerado por auditoria estática do repositório NexShape em 28/07/2026. Recomenda-se validação dinâmica (pentest, load test, restore backup) antes de go-live em escala.*
