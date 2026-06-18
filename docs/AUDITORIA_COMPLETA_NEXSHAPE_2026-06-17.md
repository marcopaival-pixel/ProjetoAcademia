# Auditoria Completa — NexShape (Financeiro e Segurança)

**Repositório:** `c:\Projetos\ProjetoAcademia\laravel-app`  
**Data:** 17 de junho de 2026  
**Modo:** auditoria estática (código, rotas, migrações, policies, serviços); produção não validada nesta sessão.  
**Escopo:** todos os painéis (Admin, Profissional, Paciente, Representante, Financeiro), APIs, BD e integrações externas.  
**Referência do pedido:** `docs/REALIZAR UMA AUDITORIA COMPLETA.txt`

---

## Resumo executivo

| Dimensão | Avaliação | Principais riscos |
|----------|-----------|-------------------|
| Permissões e isolamento | **Parcial** | Admin global bypassa tenant/impersonation em várias policies |
| Vazamento de dados (IDOR) | **Alto** | Fotos, avaliações corporais e planos de treino acessíveis por ID |
| Financeiro / cálculos | **Médio–Alto** | Double-credit em webhooks, comissões duplicadas/incorretas, dashboard com double-count |
| Comissões | **Médio** | Comissão em créditos IA, comissões presas em `AGUARDANDO_PAGAMENTO`, clawback manual |
| Códigos de indicação | **Médio** | Race condition no resgate; verify sem consumo/reserva |
| Logs / auditoria | **Parcial** | Auth e API bem instrumentados; CRUD geral e actor admin em ações financeiras ausentes |
| Banco de dados | **Médio** | Sem soft delete financeiro; CASCADE apaga histórico |
| Segurança | **Médio** | CSRF/senhas sólidos; tokens API sem expiração; gaps de autorização |
| Conciliação | **Médio** | Serviço existe mas ações de log divergem (`REFUND` vs `PAYMENT_REFUNDED`) |
| Dashboard executivo | **Parcial** | KPIs SaaS existem; comissões e clínicas canceladas não expostas |

**Achados totais:** 4 CRÍTICOS · 12 ALTOS · 18 MÉDIOS · 9 BAIXOS  
**Correção urgente recomendada:** sim — itens CRÍTICOS/ALTOS de IDOR e double-credit (48–72 h).

---

## ETAPA 1 — Auditoria de permissões

### Arquitetura de defesa (positivo)

O sistema usa camadas sobrepostas:

1. **Middleware global (web):** `TenantMiddleware`, `EnsurePanelIsolation`, `CheckRouteMenuAccess`, `EnforcePatientReadOnly`, `HandleClinicImpersonation`
2. **Middleware por painel:** `admin`, `professional.panel`, `role:paciente`, `role:representative`
3. **Policies (19)** + Gates em `AppServiceProvider`
4. **Scoping em queries:** `FiltersByProfessional`, `FiltersByRepresentative`, pivot profissional→paciente

| Painel | Proteção principal | Ficheiros |
|--------|-------------------|-----------|
| Admin | `auth` + `admin` + `panel.isolation` | `routes/admin.php` |
| Profissional | `professional.panel` + `patient_linked` + Gate `professionalPatient.*` | `routes/professional.php`, `ProfessionalPatientPolicy` |
| Paciente | `role:paciente` + read-only + escopo `auth()->user()` | `routes/patient.php`, `PortalController` |
| Representante | `role:representative` + query por `auth()->id()` + policies | `routes/representative.php` |
| Aluno (core) | `active_patient` + `panel.isolation` | `routes/web.php` |
| API v1 | `auth:sanctum` + `SetApiTenantContext` | `routes/api.php` |

### Conformidade por perfil

| Perfil | Esperado | Estado | Evidência |
|--------|----------|--------|-----------|
| **Administrador** | Ver tudo com auditoria/tenant | **Parcial** | Impersonation auditada em clínicas; mas várias policies dão `return true` sem tenant |
| **Profissional** | Só seus pacientes e financeiro | **Bom** | `authorizePatient()`, pivot, `PatientAccessGuard::assertProfessionalPatientLink` |
| **Paciente** | Só seus dados | **Bom** | Portal escopado; downloads com `$this->authorize('view', $model)` |
| **Representante** | Clínicas/comissões vinculadas | **Bom** | Index filtrado + policies em `{lead}`, `{proposal}`, `{contract}` |

### Achados — permissões

| # | Problema | Risco | Impacto operacional | Correção sugerida | Prioridade | Estimativa |
|---|----------|-------|---------------------|-------------------|------------|------------|
| P1 | Admin bypass em `PatientAccessGuard::canAccessStudentData` | **CRÍTICO** | Admin acede fotos/dados de qualquer aluno sem impersonation | Exigir `patientBelongsToImpersonatedTenant()` como em `ProfessionalPatientPolicy` | P0 | 2–4 h |
| P2 | Policies médicas (`MedicalReport`, `BodyAssessment`, `TrainingPlan`, etc.) — admin `return true` | **ALTO** | IDOR cross-tenant em prontuário e treinos | Alinhar todas ao padrão de impersonation | P0 | 1 dia |
| P3 | `/patient/subscription` sem `role:paciente` + bypass em `PanelAccessService` | **MÉDIO** | Qualquer role autenticado acede checkout de subscrição | Adicionar middleware de role ou remover bypass | P2 | 2 h |
| P4 | `MedicalRecordController::checkLink` não valida `academy_company_id` | **MÉDIO** | Acesso cross-company se pivot corrompido | Usar `PatientAccessGuard::assertProfessionalPatientLink` | P1 | 3 h |
| P5 | `FiltersByProfessional` isenta admin/manager/supervisor | **MÉDIO** | Manager vê todos os registos da empresa | Documentar ou restringir por tenant | P2 | 4 h |
| P6 | Admin LGPD export por `{user}` sem scope de tenant | **MÉDIO** | Admin delegado exporta qualquer utilizador | Scope por tenant ou permissão granular | P2 | 4 h |
| P7 | API sem `EnsurePanelIsolation` | **BAIXO** | Depende de policies por controller | Revisão sistemática de endpoints | P3 | 1 dia |

---

## ETAPA 2 — Teste de vazamento de dados (IDOR)

Análise estática de alteração de IDs em URL/API — sem pentest em runtime.

### Vetores confirmados no código

| Vetor | Severidade | Ficheiro | Padrão |
|-------|------------|----------|--------|
| `/secure-files/evolution/{id}` | **CRÍTICO** | `SecureFileController`, `PatientAccessGuard:95-97` | Admin → `true` sem impersonation |
| `/secure-files/body-analysis/{id}` | **CRÍTICO** | Idem | Idem |
| `/assessments/{assessment}` | **ALTO** | `BodyAssessmentPolicy:12-14` | Admin vê qualquer avaliação |
| `GET /api/v1/training-plans/{id}` | **ALTO** | `TrainingPlanPolicy`, `api.php` | Admin com token Bearer |
| Exercício de plano por ID | **ALTO** | `PatientAccessGuard:140-141` | Admin bypass |
| `GET /admin/audit/{id}` | **MÉDIO** | `AuditController::show` | `index()` tem tenant scope; `show()` não |
| Omni conversação por ID | **MÉDIO** | `OmniChatController` | Sem policy de ownership |
| Códigos de indicação — verify | **MÉDIO** | `Api/ReferralCodeController` | Enumeração de códigos válidos (30/min) |

### Vetores com controles adequados (referência)

- `PhotoGalleryController` — `assertStudentDataAccess`
- `Professional\PatientController` — Gate `professionalPatient.*`
- `PortalController` — policies em documentos médicos
- `SecureFileController::servePatientDocument` — exige impersonation para admin
- `DataIsolationSecurityTest` — cobre `professionalPatient.view` (mas não medical/secure-files)

### Relatório IDOR — resumo

| Categoria | Sim (risco) | Não (controlado) | Indeterminado |
|-----------|-------------|------------------|---------------|
| Alterar URL/registo de terceiros | **Sim** (admin, alguns recursos) | Profissional→paciente, paciente portal | Contratos representante (policies OK) |
| Alterar IDs em API | **Sim** (training-plans, admin) | Nutrition/workout sessions (scope user) | — |
| Download ficheiros terceiros | **Sim** (evolution, body-analysis) | patient-document com impersonation | — |
| Pagamentos/contratos terceiros | **Não** (queries scoped) | Representante com policy | — |

---

## ETAPA 3 — Auditoria financeira

### Fluxos mapeados

```
Checkout → MercadoPago/Asaas → Webhook → PaymentProcessor/MercadoPagoService
         → Subscription (premium) + Commission + FinancialLog + Invoice Job

Paralelo legado: CreditoController::webhook (sem Payment/FinancialLog)
Paralelo simulado: Student/SubscriptionController (sem gateway)
Paralelo mock: Professional/SubscriptionController (dados hardcoded)
```

### Cálculos e inconsistências

| Área | Problema | Risco | Impacto financeiro | Correção | Prioridade | Estimativa |
|------|----------|-------|-------------------|----------|------------|------------|
| F1 | `PaymentProcessor::processAiCredits` sem idempotência | **CRÍTICO** | Double-credit em retry de webhook | Check payment/wallet antes de creditar | P0 | 4 h |
| F2 | `CreditoController::webhook` fora do pipeline unificado | **ALTO** | Receita sem audit trail; sem assinatura | Deprecar rota; migrar para MP unificado | P0 | 1 dia |
| F3 | Dashboard soma `payments` + `legacy_mp_revenue` | **ALTO** | Double-count após backfill | Flag mutuamente exclusiva ou dedupe | P1 | 4 h |
| F4 | Comissão em compras de créditos IA/gerais | **MÉDIO** | Pagamento indevido a representantes | Excluir `ai_credits:*` e `credits:*` de `recordOnPayment` | P1 | 2 h |
| F5 | Comissões free checkout presas em `AGUARDANDO_PAGAMENTO` | **MÉDIO** | Comissão fantasma ou nunca paga | Cancelar ou confirmar com amount=0 explícito | P2 | 4 h |
| F6 | `revenueByPlan` join por `user_id` only | **MÉDIO** | Inflação de receita por plano | Join via `payment.subscription_id` | P2 | 3 h |
| F7 | `Coupon::calculateDiscount` sem `round()` | **BAIXO** | Centavos divergentes vs gateway | `round(..., 2)` antes de MP | P3 | 1 h |
| F8 | `AiCreditPurchaseLog` nunca populado | **MÉDIO** | Dashboard `ai_credits_sold` = 0 | Escrever log ou usar `AiCreditTransaction` | P2 | 3 h |
| F9 | Student checkout simula pagamento | **MÉDIO** | Relatórios não refletem receita real | Integrar gateway ou marcar como demo | P3 | 2 dias |
| F10 | `SubscriptionService::upgrade` sem cobrança | **MÉDIO** | Upgrade gratuito não intencional | Implementar cobrança ou bloquear | P2 | 1 dia |
| F11 | Dois vocabulários de status (`active`/`ATIVO`/…) | **BAIXO** | Métricas omitem registos | Normalizar enum/status | P3 | 2 dias |
| F12 | `Payment.subscription_id` raramente preenchido | **MÉDIO** | Refund pega `latest()` subscription errada | Preencher no webhook | P2 | 4 h |

### Domínios auditados

| Domínio | Estado | Observação |
|---------|--------|------------|
| Mensalidades / assinaturas | Parcial | Fluxo MP/Asaas OK; simulações paralelas |
| Planos | OK | Checkout valida plano ativo |
| Comissões | Ver Etapa 4 | Dedup por `payment_id` (migration 2026-06-06) |
| Recebimentos | Parcial | Idempotência MP OK; Asaas AI credits frágil |
| Descontos / cupons | Parcial | `CouponUsage` não usado |
| Indicações | Ver Etapa 5 | — |
| Cancelamentos | Parcial | `SubscriptionService::cancel` OK; refund local sem gateway |
| Reembolsos | Parcial | `PaymentRefundService` OK; clawback comissão paga manual |

---

## ETAPA 4 — Auditoria de comissões

### Ciclo de vida

| Estado | Trigger | Ficheiro |
|--------|---------|----------|
| `PENDENTE` (prospect) | Registo com código rep | `CommissionService::recordProspectiveOnRegistration` |
| `AGUARDANDO_PAGAMENTO` | Checkout com referral | `CommissionService::recordAwaitingPayment` |
| `PENDENTE` + carencia 7d | Pagamento confirmado | `CommissionService::recordOnPayment` |
| `DISPONIVEL` | Cron `commission:release` | `ReleaseCommissionsCommand` |
| `PAGO` | Saque FIFO | `CommissionWithdrawalService` |
| `CANCELADO` | Refund | `PaymentRefundService` |

### Achados

| # | Problema | Risco | Impacto financeiro | Correção | Prioridade | Estimativa |
|---|----------|-------|-------------------|----------|------------|------------|
| C1 | Comissão duplicada (mitigado) | **BAIXO** | Unique `payment_id` + early return | Manter; monitorar null `payment_id` | P3 | — |
| C2 | Comissões com `payment_id` null órfãs | **MÉDIO** | Comissão sem venda concretizada | Job de limpeza após N dias | P2 | 4 h |
| C3 | Clawback comissão já `PAGO` manual | **ALTO** | Perda financeira em refund | Workflow automático de estorno | P1 | 2 dias |
| C4 | Base awaiting vs final diverge | **MÉDIO** | Relatório inconsistente | Unificar base = `payment.amount` | P2 | 3 h |
| C5 | Withdrawal não faz parcial | **MÉDIO** | Saque bloqueado se valor > restante | Suportar alocação parcial | P3 | 1 dia |
| C6 | Comissão em créditos (não subscrição) | **MÉDIO** | Pagamento indevido | Filtrar tipo de pagamento | P1 | 2 h |

---

## ETAPA 5 — Códigos de indicação

### Implementação

- Modelo: `ReferralCode` — status `DISPONIVEL`, `RESERVADO`, `UTILIZADO`, `EXPIRADO`
- Resolução: `ReferralCodeResolver` (proposal fixa ou profile %)
- Admin: read-only (`ReferralCodeController`)
- API: `POST /api/v1/referral/verify` (throttle 30/min)
- Checkout: `markAsUsed()` + `RepresentativeAudit`

### Validações

| Requisito | Implementado? | Gap |
|-----------|---------------|-----|
| Uso único | **Parcial** | `markAsUsed()` sem lock condicional — race condition |
| Expiração | **Parcial** | `isValid()` checa data; status `EXPIRADO` nunca auto-set |
| Histórico | **Parcial** | Só `used_at` + `RepresentativeAudit`; sem tabela de redemptions |
| Auditoria | **Parcial** | Sem IP/origem no resgate |
| Anti-fraude | **Fraco** | Verify não consome/reserva; profile codes multi-use |

| # | Problema | Risco | Impacto | Correção | Prioridade | Estimativa |
|---|----------|-------|---------|----------|------------|------------|
| R1 | Race condition no resgate | **ALTO** | Double-discount / double-comissão | `UPDATE … WHERE status=DISPONIVEL` em transaction | P0 | 4 h |
| R2 | Verify enumera códigos | **MÉDIO** | Fraude / probing | Rate limit por IP+code; não revelar discount exato | P1 | 4 h |
| R3 | `RegisterController` não usa `ReferralCodeResolver` | **MÉDIO** | Caminhos inconsistentes | Unificar resolver | P2 | 3 h |
| R4 | Admin read-only — sem revoke | **BAIXO** | Código fraudulento permanece ativo | Ação admin cancelar código | P3 | 4 h |

---

## ETAPA 6 — Auditoria de logs

### Cobertura por tipo de log

| Log | Quem | Quando | IP | Origem | CRUD/Pagamentos |
|-----|------|--------|----|--------|-----------------|
| `AuditLog` | Sim | Sim | Sim | HTTP | Só 5 models Config Center |
| `AuthAuditLog` | Sim | Sim | Sim | guard+meta | Auth lifecycle completo |
| `FinancialLog` | Subject user (não admin actor) | Sim | Auto | gateway | Pagamentos, refunds, subs |
| `MenuPermissionAuditLog` | Sim (bug role_id) | Sim | Sim | — | Só update menu |
| `ApiAccessLog` | Sim | Sim | Sim | path/method | Toda API (sampled) |
| `ClientErrorLog` | Se auth | Sim | Sim | URL | Erros frontend |
| `SubscriptionLog` | **Não** | Sim | **Não** | **Não** | Eventos subscription |
| `RepresentativeAudit` | Parcial | Sim | **Não** | **Não** | Referral/comercial |

### Lacunas críticas

| # | Problema | Risco | Impacto operacional | Correção | Prioridade | Estimativa |
|---|----------|-------|---------------------|----------|------------|------------|
| L1 | Sem audit CRUD geral (User, Clinic, Payment) | **ALTO** | Impossível rastrear alterações sensíveis | Observer/trait audit em models críticos | P1 | 3 dias |
| L2 | Ações admin financeiras logam owner, não actor | **MÉDIO** | Compliance / disputas | Passar `admin_id` em `FinancialLog` | P2 | 4 h |
| L3 | `MenuPermissionAuditLog` — `profile_id` vs `role_id` | **MÉDIO** | Audit quebrado | Corrigir controller fillable | P1 | 1 h |
| L4 | `FinancialLog` não idempotente | **MÉDIO** | Duplicatas em retry webhook | Unique (transaction_id, action) | P2 | 4 h |
| L5 | `AuditReportCommand` ignora FinancialLog | **BAIXO** | Monitoramento incompleto | Incluir no report | P3 | 2 h |

---

## ETAPA 7 — Auditoria de banco de dados

### Integridade referencial (financeiro)

| Tabela | FKs | Soft delete | Risco |
|--------|-----|-------------|-------|
| `payments` | user CASCADE, subscription SET NULL | **Não** | CASCADE apaga histórico |
| `commissions` | payment UNIQUE, user CASCADE | **Não** | Orphans com payment_id null |
| `referral_codes` | representative CASCADE, clinic SET NULL | **Não** | — |
| `financial_logs` | user (sem FK em observability) | **Não** | Logs órfãos |
| `audit_logs` | user SET NULL; tenant cols sem FK | **Não** | — |

| # | Problema | Risco | Impacto | Correção | Prioridade | Estimativa |
|---|----------|-------|---------|----------|------------|------------|
| D1 | Hard delete CASCADE em payments/commissions | **ALTO** | Perda histórico financeiro | Soft delete + archive | P1 | 2 dias |
| D2 | Comissões `payment_id` null permanentes | **MÉDIO** | Registos órfãos | Cleanup job + constraint parcial | P2 | 4 h |
| D3 | Observability logs sem FK user | **BAIXO** | Integridade referencial fraca | FK SET NULL | P3 | 2 h |
| D4 | `subscriptions.plan_id` CASCADE | **MÉDIO** | Apagar plano apaga subs | RESTRICT ou archive | P2 | 4 h |

**Comando de verificação disponível:** `php artisan finance:reconcile --days=30`

---

## ETAPA 8 — Auditoria de segurança

| Controlo | Estado | Notas |
|----------|--------|-------|
| SQL Injection | **Baixo risco** | Queries parametrizadas; exceção: `sort_by` não whitelist em Dynamic CRUD |
| XSS | **Médio** | `SafeHtml::markdown` em AI search; CSP com unsafe-inline |
| CSRF | **Bom** | Laravel default; exceções webhooks/logout |
| Rate limit | **Bom** | Login 20/min, API token 10/min, API auth 120/min |
| Sessões | **Bom** | Regenerate on login; invalidate on logout |
| JWT/Tokens | **Médio** | Sanctum sem `expires_at`; sem scopes |
| Senhas | **Bom** | `Hash::make`, min 8 chars |
| Criptografia | **Bom** | Sensitive files em disk privado |

| # | Problema | Risco | Correção | Prioridade | Estimativa |
|---|----------|-------|----------|------------|------------|
| S1 | Tokens API sem expiração | **ALTO** | Definir `expires_at` (ex. 30d) | P1 | 2 h |
| S2 | AuthTokenController sem gates de LoginController | **MÉDIO** | Email verified, pending approval | P1 | 4 h |
| S3 | Omni webhook sem secret fora prod | **MÉDIO** | Exigir secret em staging | P2 | 1 h |
| S4 | Debug log email em APP_DEBUG | **MÉDIO** | Remover log de credenciais | P2 | 1 h |
| S5 | Path traversal potencial em SecureFileService | **MÉDIO** | Normalizar path | P2 | 2 h |

---

## ETAPA 9 — Auditoria de conciliação financeira

### `PaymentReconciliationService`

Compara:
- `SUM(payments)` (status paid)
- `SUM(financial_logs)` (actions `PAYMENT_RECEIVED`, `AI_CREDITS_PURCHASED`, `PAYMENT_REFUNDED`)
- `CreditoCompra PAGO` sem `payment_id`

| Problema | Impacto |
|----------|---------|
| Refunds logam `REFUND`, reconciliação busca `PAYMENT_REFUNDED` | Falso "healthy" |
| Threshold `< R$1` | Drift sistemático oculto |
| Legacy MP + payments no dashboard | Double-count |
| Sem reconciliação wallet IA vs transactions | Saldo incorreto possível |
| Sem reconciliação comissão vs payment.amount | Comissões erradas não detectadas |

**Comandos operacionais:**
```bash
php artisan finance:reconcile --days=30 --stale=7
php artisan finance:backfill-legacy-mp --dry-run
php artisan commission:release
```

---

## ETAPA 10 — Melhorias recomendadas (consolidado)

### CRÍTICOS (ação imediata — 48–72 h)

| ID | Problema | Impacto financeiro | Impacto operacional | Correção | Estimativa |
|----|----------|-------------------|----------------------|----------|------------|
| P1/F1 | IDOR admin em ficheiros + double-credit webhook | Perda/receita incorreta | Violação LGPD; saldo IA inflado | Patch guards + idempotência | 1 dia |
| R1 | Race referral code | Desconto/comissão duplicada | Fraude comercial | Transaction lock | 4 h |

### ALTOS (1–2 semanas)

| ID | Problema | Impacto financeiro | Correção | Estimativa |
|----|----------|-------------------|----------|------------|
| P2 | Policies admin inconsistentes | Exposição PII massiva | Unificar impersonation | 2 dias |
| F2/F3 | Pipeline legado + double-count dashboard | Relatórios errados | Unificar + dedupe | 2 dias |
| C3 | Clawback comissão paga | Perda em refunds | Workflow estorno | 2 dias |
| L1 | Audit CRUD ausente | Compliance | Observers | 3 dias |
| S1/S2 | Tokens API | Conta comprometida | Expiração + gates | 1 dia |
| D1 | Hard delete financeiro | Perda histórico | Soft delete | 2 dias |

### MÉDIOS (2–4 semanas)

- Comissões em créditos IA (F4/C6)
- Conciliação REFUND vs PAYMENT_REFUNDED
- Referral verify hardening (R2)
- Dashboard métricas quebradas (F8)
- Subscription/payment linkage (F12)
- Panel subscription bypass (P3)

### BAIXOS (backlog)

- Password policy complexity
- CSP tightening
- CSRF logout exceptions
- Status vocabulary normalization

---

## ETAPA 11 — Dashboard financeiro executivo

### `ExecutiveDashboardController` + `ExecutiveDashboardService`

| Métrica | Existe? | Fonte |
|---------|---------|-------|
| Receita do dia | **Sim** | `FinancialMetricsService::dailyRevenue()` |
| Receita do mês | **Sim** | `monthly_revenue` |
| Receita anual | **Sim** | `annualRevenue()` |
| Inadimplência | **Sim** | `delinquency_count` |
| Ticket médio | **Sim** | `average_ticket` |
| MRR | **Sim** | `SaaSMetricsService::calculateMRR()` |
| LTV | **Sim (estimado)** | total revenue / paying users |
| CAC | **Sim (estimado)** | commissions / new subs |
| Churn | **Sim** | `SaaSMetricsService::getChurnRate()` |
| Comissões (KPI) | **Não** | Só indireto via CAC |
| Clínicas ativas | **Parcial** | `active_academies` + `total_clinics` (entidades distintas) |
| Clínicas canceladas | **Não** | Só `cancellations_month` de subs |

### `FinancialDashboardController`

Possui: receita diária/mensal, inadimplência, ticket, LTV/CAC estimados, reconciliação, receita por plano/gateway/clínica, créditos IA.

**Não possui:** MRR, churn, receita anual explícita, comissões agregadas, clínicas canceladas.

### Sugestões de implementação (dashboard)

| Widget | Prioridade | Estimativa | Valor |
|--------|------------|------------|-------|
| Comissões: pendente/disponível/pago/mês | P1 | 1 dia | Gestão comercial |
| Clínicas canceladas vs ativas (unificar entidade) | P1 | 1 dia | Retention |
| Alertas reconciliação > R$1 | P1 | 4 h | Integridade |
| Cohort LTV/CAC (substituir heurística) | P2 | 3 dias | Decisão estratégica |
| Inadimplência por faixa (5/10/15 dias) | P2 | 4 h | Cobrança |

---

## Oportunidades de automação

| Automação | Benefício | Esforço |
|-----------|-----------|---------|
| Job diário `finance:reconcile` + alerta Slack/email | Detectar divergências cedo | 4 h |
| Cleanup comissões `AGUARDANDO_PAGAMENTO` > 30d | Reduzir ruído financeiro | 4 h |
| Auto-expire referral codes (cron) | Status coerente | 2 h |
| Expiração automática tokens API | Segurança | 2 h |
| Reconciliação wallet IA vs transactions | Integridade créditos | 1 dia |

---

## Fluxos financeiros faltantes ou incompletos

1. **Professional SubscriptionController** — UI mock; não reflete billing real
2. **Student SubscriptionController** — pagamento simulado sem `Payment`
3. **SubscriptionService::refund** — local only; sem gateway nem `FinancialLog`
4. **CouponUsage** — tabela existe; nunca populada
5. **Clawback comissão paga** — manual
6. **Asaas subscription checkout** — possível falta de `init_point` no retorno

---

## Painéis — síntese por área

| Painel | Segurança | Financeiro | UX/Gestão |
|--------|-----------|------------|-----------|
| Admin | Gaps IDOR admin | Dashboard robusto; double-count | Executive dashboard completo |
| Profissional | Bom isolamento paciente | Finance clinic manual OK; SaaS mock | Selector paciente global OK |
| Paciente | Bom; bypass subscription | Pagamentos scoped | Read-only enforced |
| Representante | Policies OK | Comissões via CommissionService | Audit parcial |
| API | Tokens long-lived | Payment status read | — |
| BD | CASCADE risk | Sem soft delete | — |

---

## Plano de ação priorizado

### Semana 1 (P0)
- [ ] Corrigir `PatientAccessGuard::canAccessStudentData` e policies admin (P1, P2)
- [ ] Idempotência `processAiCredits` (F1)
- [ ] Transaction lock em `markAsUsed()` referral (R1)
- [ ] Desativar/redirect `CreditoController::webhook` legado (F2)

### Semana 2 (P1)
- [ ] Token expiration + login parity API (S1, S2)
- [ ] Fix dashboard double-count (F3)
- [ ] Excluir comissão em créditos (F4)
- [ ] Fix `MenuPermissionAuditLog` (L3)
- [ ] Alinhar REFUND reconciliation (Etapa 9)

### Semana 3–4 (P2)
- [ ] Audit observers models críticos (L1)
- [ ] Soft delete financial (D1)
- [ ] Dashboard comissões + clínicas canceladas (Etapa 11)
- [ ] Comissão clawback workflow (C3)

---

## Limitações desta auditoria

- **Estática:** sem pentest, sem validação `.env` produção, sem execução de queries em BD real.
- **Hipóteses marcadas:** impacto de manager/supervisor bypass depende de atribuição de roles em produção.
- **Admin global:** se política de negócio exige admin ver tudo sem impersonation, reclassificar P1/P2 como aceite documentado — hoje o código é **inconsistente** (alguns paths exigem impersonation, outros não).

---

## Referências de código (achados críticos)

```89:97:laravel-app/app/Support/PatientAccessGuard.php
    public static function canAccessStudentData(User $user, int $studentId): bool
    {
        if ((int) $user->id === $studentId) {
            return true;
        }

        if ($user->isAdministrator()) {
            return true;
        }
```

```19:27:laravel-app/app/Policies/ProfessionalPatientPolicy.php
    public function view(User $professional, User $patient): bool
    {
        if ($professional->isAdministrator()) {
            return PatientAccessGuard::patientBelongsToImpersonatedTenant($patient);
        }
        // ... profissional validado por pivot
```

O contraste entre estes dois padrões é a **causa raiz** da maioria dos achados IDOR CRÍTICOS/ALTOS.

---

**Próximo passo recomendado:** revisão humana deste relatório → priorizar itens P0 → implementação via PRs incrementais (gate AGENTS.md / System Builder). Nenhum código foi alterado nesta auditoria.
