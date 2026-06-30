# Suplemento do dicionário de dados (maio/2026)

Tabelas e alterações **não** documentadas no ficheiro principal `dicionario_dados.md` (snapshot com ~100 tabelas). Após migrações P1, incluir colunas `clinic_id` e `academy_company_id` onde indicado.

**Migração tenant:** `2026_05_21_120000_add_tenant_columns_to_operational_logs.php`

---

## Multi-tenant (clínicas)

### TABELA: clinics

Sub-unidade da empresa (`academy_companies`). Slug público, branding, domínio customizado.

| Coluna | Tipo | Notas |
|--------|------|-------|
| id | BIGINT PK | |
| academy_company_id | BIGINT FK nullable | Empresa dona |
| name, slug | VARCHAR | slug único |
| logo_path, primary_color, custom_domain | | |
| is_active | BOOLEAN | |

### TABELA: clinic_user

Pivot utilizador ↔ clínica (membros da equipa).

### TABELA: professional_patient_clinic

Vínculo profissional–paciente por clínica/empresa.

---

## IA e importação de treino

### TABELA: ai_orchestrator_logs

Logs do orquestrador de agentes (tokens, custo, intenção). Usa `HasClinic`.

### TABELA: ai_vision_logs

Resultado de visão/OCR (ficha treino, refeição). Colunas: `user_id`, `clinic_id`, `document_type`, `extracted_data`, `image_hash`, `cost_usd`.

### TABELA: workout_import_logs

Importação de ficha por foto. Colunas: `user_id`, `clinic_id`, `academy_company_id`, `status`, `structured_json`, `ai_confidence`. Model: `WorkoutImportLog`.

---

## Configuration Center

### TABELA: admin_entities

Metadados de entidades CRUD dinâmico (`table_name`, labels).

### TABELA: admin_fields

Campos dinâmicos por entidade (`admin_entity_id`, `name`, `type`, validação).

### TABELA: audit_logs

Auditoria de alterações (polimórfico `entity_type`/`entity_id`). Tenant: `clinic_id`, `academy_company_id`.

### TABELA: record_versions

Versões JSON de registos editados via Configuration Center. Mesmas colunas tenant.

---

## Acesso e sistema

### TABELA: system_access_links

Links/QR de boas-vindas (`user_id`, `system_url`, `qr_code_path`). Tenant: `clinic_id`, `academy_company_id`.

### TABELA: system_settings

Chave/valor de configuração global (`key`, `value`).

---

## Pagamentos (gateway genérico)

### TABELA: payment_settings

Credenciais por gateway (`gateway`, `access_token` encriptado, `webhook_secret`, `environment`, `priority`).

### TABELA: payment_webhook_logs

Log de webhooks (`gateway`, `payload`, `status_code`, `ip_address`).

### TABELA: payments

Pagamentos associados a subscrições (migração `enhance_subscription_control`).

---

## Monetização e funcionalidades

### TABELA: app_features

Flags de funcionalidades (`key`, premium, etc.).

### TABELA: feature_limits

Limites por plano/funcionalidade.

### TABELA: upgrade_popups

Popups de upgrade (ex.: importação foto treino).

### TABELA: feature_usage_logs

Uso de funcionalidades para billing/analytics.

### TABELA: ai_credit_transactions / ai_credit_wallets

Carteira e movimentos de créditos IA.

---

## Comunidade

### TABELAS: community_posts, community_comments, community_reactions, community_reports, community_post_media, community_stickers, social_post_queue

Módulo social NexShape (maio/2026).

---

## Marketing e saúde

### TABELAS: marketing_banners, marketing_banner_views, marketing_banner_clicks, marketing_banner_dismissals, marketing_banner_targets

Banners in-app e métricas.

### TABELA: health_metrics

Métricas de wearables / bio-sinais.

---

## Conhecimento

### TABELAS: knowledge_categories, knowledge_articles

Base de conhecimento (KB).

---

## Índice de sincronização

Para regenerar o dicionário completo no futuro, correlacionar:

```bash
# Listar creates em migrações (referência dev)
rg "Schema::create\('" laravel-app/database/migrations -o
```

Documento principal: `dicionario_dados.md` — atualizar quando possível fundindo este suplemento.

**Suplementos adicionais:** `dicionario_dados_suplemento_shop_2026-06.md` (módulo Shopping).

---

## Suplemento 2026-05-27 (tenant empresa em IA + análises)

**Migração:** `2026_05_27_100000_add_academy_company_id_to_ai_and_body_tables.php`

| Tabela | Coluna nova | Trait / model |
|--------|-------------|---------------|
| `ai_orchestrator_logs` | `academy_company_id` | `AIOrchestratorLog` — `FillsTenantColumns`, `HasClinic` |
| `body_analyses` | `academy_company_id` | `BodyAnalysis` — `FillsTenantColumns`, `HasClinic` |
| `ai_chats` | `academy_company_id` | `AIChat` — `FillsTenantColumns`, `HasClinic` |

**Nota:** `ai_vision_logs` já tinha `academy_company_id` desde `2026_05_21_160000`.

**Inventário total estimado:** ~196 tabelas (229 migrações). Tabelas ainda sem `academy_company_id` em domínios legados (`food_entries`, `exercise_entries`, etc.) — isolamento via `user_id` + tenant do utilizador.

---

## Suplemento 2026-05-27 (tenant em tracking legado)

**Migração:** `2026_05_27_120000_add_tenant_columns_to_tracking_entries.php`

| Tabela | Colunas | Models |
|--------|---------|--------|
| `food_entries` | `clinic_id`, `academy_company_id` | `FoodEntry` |
| `exercise_entries` | idem | `ExerciseEntry` |
| `water_entries` | idem | `WaterEntry` |
| `weight_entries` | idem | `WeightEntry` |
| `load_logs` | idem | `LoadLog` |

Backfill automático a partir de `users.academy_company_id` e `users.clinic_id`. Traits: `BelongsToCompany`, `FillsTenantColumns`, `HasClinic`.

**Correção trait:** `BelongsToCompany` passa a preencher `academy_company_id` via `TenantContext::getCompanyId()` (não `clinic_id`).


