# Dicionário de Dados do Sistema

Este documento contém a estrutura detalhada de todas as tabelas do banco de dados.

> **Atualização (mai/2026):** tabelas criadas após este snapshot estão em **`dicionario_dados_suplemento_2026-05.md`** (clinics, IA, Configuration Center, pagamentos, comunidade, etc.).

========================================
TABELA: academy_companies
========================================

Descrição:
Tabela responsável por armazenar dados de academy_companies.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- uuid (CHAR(36), NULL DEFAULT NULL) → Dado do tipo CHAR(36)
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- slug (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- logo_path (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- primary_color (VARCHAR(7), NOT NULL DEFAULT '#3b82f6') → Dado do tipo VARCHAR(7)
- accent_color (VARCHAR(7), NOT NULL DEFAULT '#10b981') → Dado do tipo VARCHAR(7)
- legal_name (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- tax_id (VARCHAR(64), NULL DEFAULT NULL) → Dado do tipo VARCHAR(64)
- responsible_name (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- responsible_email (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- phone (VARCHAR(32), NULL DEFAULT NULL) → Dado do tipo VARCHAR(32)
- address (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- city (VARCHAR(100), NULL DEFAULT NULL) → Dado do tipo VARCHAR(100)
- state (VARCHAR(2), NULL DEFAULT NULL) → Dado do tipo VARCHAR(2)
- zip_code (VARCHAR(20), NULL DEFAULT NULL) → Dado do tipo VARCHAR(20)
- pdf_settings (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- onboarding_status (VARCHAR(255), NOT NULL DEFAULT 'pending') → Dado do tipo VARCHAR(255)
- current_onboarding_step (TINYINT(3) UNSIGNED, NOT NULL DEFAULT '1') → Dado do tipo TINYINT(3) UNSIGNED
- shared_medical_records (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: academy_units
========================================

Descrição:
Tabela responsável por armazenar dados de academy_units.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- academy_company_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `academy_companies`
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- code (VARCHAR(64), NULL DEFAULT NULL) → Dado do tipo VARCHAR(64)
- settings (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)

----------------------------------------

========================================
TABELA: achievements
========================================

Descrição:
Tabela responsável por armazenar dados de achievements.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- badge_slug (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- achieved_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: active_rest_favorites
========================================

Descrição:
Tabela responsável por armazenar dados de active_rest_favorites.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- active_rest_routine_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `active_rest_routines`
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `active_rest_routines` via `active_rest_routine_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: active_rest_logs
========================================

Descrição:
Tabela responsável por armazenar dados de active_rest_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- active_rest_routine_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `active_rest_routines`
- duration_spent (INT(11), NULL DEFAULT NULL) → Dado do tipo INT(11)
- feedback_score (INT(11), NULL DEFAULT NULL) → Dado do tipo INT(11)
- notes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `active_rest_routines` via `active_rest_routine_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: active_rest_routines
========================================

Descrição:
Tabela responsável por armazenar dados de active_rest_routines.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- title (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- category (VARCHAR(255), NOT NULL DEFAULT 'Mobilidade') → Dado do tipo VARCHAR(255)
- duration (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- intensity (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- recommended_level (VARCHAR(255), NOT NULL DEFAULT 'Iniciante') → Dado do tipo VARCHAR(255)
- thumbnail (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- guide_image (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- video_id (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- benefit (TEXT, NOT NULL) → Dado do tipo TEXT
- is_premium (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- exercises (LONGTEXT, NOT NULL) → Dado do tipo LONGTEXT
- execution_steps (LONGTEXT, NOT NULL) → Dado do tipo LONGTEXT
- tips (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- common_errors (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- order (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: admin_clinic_access_logs
========================================

Descrição:
Tabela responsável por armazenar dados de admin_clinic_access_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- admin_user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- clinic_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `academy_companies`
- motivo_acesso (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- descricao (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- data_hora_entrada (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP
- data_hora_saida (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- ip (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- duracao_acesso (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `admin_user_id`)
- 1:N (Pertence à tabela `academy_companies` via `clinic_id`)

----------------------------------------

========================================
TABELA: admin_logs
========================================

Descrição:
Tabela responsável por armazenar dados de admin_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- action (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- ip_address (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- user_agent (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- payload (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- created_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora de criação do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: admin_settings
========================================

Descrição:
Tabela responsável por armazenar dados de admin_settings.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- key (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- value (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- label (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- type (VARCHAR(255), NOT NULL DEFAULT 'text') → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: agenda_settings
========================================

Descrição:
Tabela responsável por armazenar dados de agenda_settings.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- key (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- value (TEXT, NOT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: ai_chats
========================================

Descrição:
Tabela responsável por armazenar dados de ai_chats.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- role (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- message (LONGTEXT, NOT NULL) → Dado do tipo LONGTEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: ai_credits_packages
========================================

Descrição:
Tabela responsável por armazenar dados de ai_credits_packages.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- credits (INT(11), NOT NULL) → Dado do tipo INT(11)
- price (DECIMAL(10,2), NOT NULL) → Dado do tipo DECIMAL(10,2)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: ai_credits_purchase_logs
========================================

Descrição:
Tabela responsável por armazenar dados de ai_credits_purchase_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- package_name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- credits_amount (INT(11), NOT NULL) → Dado do tipo INT(11)
- price (DECIMAL(10,2), NOT NULL) → Dado do tipo DECIMAL(10,2)
- payment_status (VARCHAR(255), NOT NULL DEFAULT 'pending') → Dado do tipo VARCHAR(255)
- payment_method (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- payment_id (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: ai_credits_usage_logs
========================================

Descrição:
Tabela responsável por armazenar dados de ai_credits_usage_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- action_type (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- credits_consumed (INT(11), NOT NULL) → Dado do tipo INT(11)
- metadata (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- response_cache_key (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: ai_usage
========================================

Descrição:
Tabela responsável por armazenar dados de ai_usage.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- feature (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- credits_used (INT(11), NOT NULL DEFAULT '1') → Dado do tipo INT(11)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: announcements
========================================

Descrição:
Tabela responsável por armazenar dados de announcements.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- content (TEXT, NOT NULL) → Dado do tipo TEXT
- type (VARCHAR(255), NOT NULL DEFAULT 'info') → Dado do tipo VARCHAR(255)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- starts_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- ends_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: api_integration_logs
========================================

Descrição:
Tabela responsável por armazenar dados de api_integration_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- api_name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- endpoint (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- status_code (INT(11), NOT NULL) → Dado do tipo INT(11)
- response_time_ms (INT(11), NULL DEFAULT NULL) → Dado do tipo INT(11)
- request_payload (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- response_payload (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- error_message (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: api_integrations
========================================

Descrição:
Tabela responsável por armazenar dados de api_integrations.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- type (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- base_url (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- api_key (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- secret_key (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- timeout (INT(11), NOT NULL DEFAULT '30') → Dado do tipo INT(11)
- status (ENUM('ACTIVE','INACTIVE'), NOT NULL DEFAULT 'active') → Dado do tipo ENUM('ACTIVE','INACTIVE')
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: appointment_waitlists
========================================

Descrição:
Tabela responsável por armazenar dados de appointment_waitlists.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- professional_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- requested_date (DATETIME, NOT NULL) → Dado do tipo DATETIME
- status (VARCHAR(255), NOT NULL DEFAULT 'waiting') → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: biblioteca_inteligente
========================================

Descrição:
Tabela responsável por armazenar dados de biblioteca_inteligente.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- modulo (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- categoria (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- tipo_item (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- titulo (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- descricao (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- pergunta (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- palavras_chave (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- conteudo (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- origem (VARCHAR(255), NOT NULL DEFAULT 'IA') → Dado do tipo VARCHAR(255)
- visibilidade (VARCHAR(255), NOT NULL DEFAULT 'PUBLICO') → Dado do tipo VARCHAR(255)
- status (VARCHAR(255), NOT NULL DEFAULT 'ATIVO') → Dado do tipo VARCHAR(255)
- versao (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- uso_count (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- created_by (BIGINT(20) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- parent_id (BIGINT(20) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- ativo (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: body_analyses
========================================

Descrição:
Tabela responsável por armazenar dados de body_analyses.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- photo_path (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- view_type (VARCHAR(20), NOT NULL DEFAULT 'front') → Dado do tipo VARCHAR(20)
- landmarks (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- metrics (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- ai_summary (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora de criação do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: body_assessments
========================================

Descrição:
Tabela responsável por armazenar dados de body_assessments.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- academy_company_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- professional_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- weight_kg (DECIMAL(6,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(6,2)
- bf_percent (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- muscle_percent (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- neck (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- chest (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- waist (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- abdomen (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- hips (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- bicep_l (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- bicep_r (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- forearm_l (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- forearm_r (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- thigh_l (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- thigh_r (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- calf_l (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- calf_r (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- blood_pressure (VARCHAR(20), NULL DEFAULT NULL) → Dado do tipo VARCHAR(20)
- heart_rate (SMALLINT(5) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo SMALLINT(5) UNSIGNED
- assessment_date (DATE, NOT NULL) → Dado do tipo DATE
- notes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- status (ENUM('PENDING','APPROVED','REJECTED'), NOT NULL DEFAULT 'approved') → Dado do tipo ENUM('PENDING','APPROVED','REJECTED')
- created_by (ENUM('PATIENT','PROFESSIONAL'), NOT NULL DEFAULT 'professional') → Dado do tipo ENUM('PATIENT','PROFESSIONAL')
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: cache
========================================

Descrição:
Tabela responsável por armazenar dados de cache.

Colunas:
- key (VARCHAR(255), PK, NOT NULL) → Dado do tipo VARCHAR(255)
- value (MEDIUMTEXT, NOT NULL) → Dado do tipo MEDIUMTEXT
- expiration (INT(11), NOT NULL) → Dado do tipo INT(11)

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: cache_locks
========================================

Descrição:
Tabela responsável por armazenar dados de cache_locks.

Colunas:
- key (VARCHAR(255), PK, NOT NULL) → Dado do tipo VARCHAR(255)
- owner (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- expiration (INT(11), NOT NULL) → Dado do tipo INT(11)

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: clinic_onboarding_steps
========================================

Descrição:
Tabela responsável por armazenar dados de clinic_onboarding_steps.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- academy_company_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `academy_companies`
- step_key (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- is_completed (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- completed_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- data (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)

----------------------------------------

========================================
TABELA: clinic_protocols
========================================

Descrição:
Tabela responsável por armazenar dados de clinic_protocols.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- academy_company_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `academy_companies`
- especialidade_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `especialidades`
- type (VARCHAR(255), NOT NULL DEFAULT 'medical') → Dado do tipo VARCHAR(255)
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- description (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- objective (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- protocol (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- frequency (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- duration (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `especialidades` via `especialidade_id`)

----------------------------------------

========================================
TABELA: clinic_user
========================================

Descrição:
Tabela responsável por armazenar dados de clinic_user.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- uuid (CHAR(36), NOT NULL) → Dado do tipo CHAR(36)
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- academy_company_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `academy_companies`
- role (VARCHAR(255), NOT NULL DEFAULT 'patient') → Dado do tipo VARCHAR(255)
- status (VARCHAR(255), NOT NULL DEFAULT 'active') → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: commercial_proposals
========================================

Descrição:
Tabela responsável por armazenar dados de commercial_proposals.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- lead_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `leads`
- plan_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `plans`
- valor (DECIMAL(10,2), NOT NULL) → Dado do tipo DECIMAL(10,2)
- desconto (DECIMAL(10,2), NOT NULL DEFAULT '0.00') → Dado do tipo DECIMAL(10,2)
- validade (DATE, NOT NULL) → Dado do tipo DATE
- status (ENUM('PENDENTE','ENVIADA','APROVADA','REJEITADA'), NOT NULL DEFAULT 'Pendente') → Dado do tipo ENUM('PENDENTE','ENVIADA','APROVADA','REJEITADA')
- token (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- observacoes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `leads` via `lead_id`)
- 1:N (Pertence à tabela `plans` via `plan_id`)

----------------------------------------

========================================
TABELA: commissions
========================================

Descrição:
Tabela responsável por armazenar dados de commissions.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- representative_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- payment_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `payments`
- subscription_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `subscriptions`
- base_amount (DECIMAL(12,2), NOT NULL) → Valor base do pagamento
- commission_rate (DECIMAL(5,2), NOT NULL) → Taxa aplicada no momento
- commission_amount (DECIMAL(12,2), NOT NULL) → Valor final da comissão
- status (VARCHAR(32), NOT NULL DEFAULT 'PENDENTE') → PENDENTE, DISPONIVEL, PAGO, CANCELADO
- available_at (TIMESTAMP, NULL DEFAULT NULL) → Data em que a comissão ficará disponível para saque
- paid_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- notes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `payments` via `payment_id`)
- 1:N (Pertence à tabela `users` via `representative_id`)
- 1:N (Pertence à tabela `subscriptions` via `subscription_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: communication_group_user
========================================

Descrição:
Tabela responsável por armazenar dados de communication_group_user.

Colunas:
- id (INT(10) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- group_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `communication_groups`
- status (ENUM('PENDING','APPROVED','REJECTED'), NOT NULL DEFAULT 'pending') → Dado do tipo ENUM('PENDING','APPROVED','REJECTED')
- role (ENUM('MEMBER','MODERATOR','ADMIN'), NULL DEFAULT 'member') → Dado do tipo ENUM('MEMBER','MODERATOR','ADMIN')
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `communication_groups` via `group_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: communication_groups
========================================

Descrição:
Tabela responsável por armazenar dados de communication_groups.

Colunas:
- id (INT(10) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(100), NOT NULL) → Dado do tipo VARCHAR(100)
- slug (VARCHAR(100), NOT NULL) → Dado do tipo VARCHAR(100)
- description (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- is_private (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- allow_self_join (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- can_members_send_messages (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: configuracao_email
========================================

Descrição:
Tabela responsável por armazenar dados de configuracao_email.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- empresa_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `academy_companies`
- nome_provedor (VARCHAR(120), NOT NULL) → Dado do tipo VARCHAR(120)
- tipo_envio (VARCHAR(16), NOT NULL DEFAULT 'smtp') → Dado do tipo VARCHAR(16)
- preset (VARCHAR(32), NOT NULL DEFAULT 'custom') → Dado do tipo VARCHAR(32)
- smtp_host (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- smtp_porta (SMALLINT(5) UNSIGNED, NOT NULL DEFAULT '587') → Dado do tipo SMALLINT(5) UNSIGNED
- smtp_usuario (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- smtp_senha (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- criptografia (VARCHAR(16), NOT NULL DEFAULT 'tls') → Dado do tipo VARCHAR(16)
- email_remetente (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- nome_remetente (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- timeout (SMALLINT(5) UNSIGNED, NOT NULL DEFAULT '30') → Dado do tipo SMALLINT(5) UNSIGNED
- limite_envio_por_hora (INT(10) UNSIGNED, NOT NULL DEFAULT '100') → Dado do tipo INT(10) UNSIGNED
- ativo (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `empresa_id`)

----------------------------------------

========================================
TABELA: contracts
========================================

Descrição:
Tabela responsável por armazenar dados de contracts.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- lead_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `leads`
- proposal_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `commercial_proposals`
- status (ENUM('RASCUNHO','ASSINADO','CANCELADO'), NOT NULL DEFAULT 'Rascunho') → Dado do tipo ENUM('RASCUNHO','ASSINADO','CANCELADO')
- signed_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- content (LONGTEXT, NOT NULL) → Dado do tipo LONGTEXT
- token (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `leads` via `lead_id`)
- 1:N (Pertence à tabela `commercial_proposals` via `proposal_id`)

----------------------------------------

========================================
TABELA: conversations
========================================

Descrição:
Tabela responsável por armazenar dados de conversations.

Colunas:
- id (INT(10) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_one_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- user_two_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- tipo (VARCHAR(255), NOT NULL DEFAULT 'SUPORTE') → Dado do tipo VARCHAR(255)
- status (VARCHAR(255), NOT NULL DEFAULT 'ABERTO') → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_one_id`)
- 1:N (Pertence à tabela `users` via `user_two_id`)

----------------------------------------

========================================
TABELA: coupon_usages
========================================

Descrição:
Tabela responsável por armazenar dados de coupon_usages.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- coupon_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `coupons`
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- used_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `coupons` via `coupon_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: coupons
========================================

Descrição:
Tabela responsável por armazenar dados de coupons.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- patient_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- code (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- discount_type (ENUM('PERCENT','FIXED'), NOT NULL) → Dado do tipo ENUM('PERCENT','FIXED')
- discount_value (DECIMAL(10,2), NOT NULL) → Dado do tipo DECIMAL(10,2)
- expiration_date (DATE, NOT NULL) → Dado do tipo DATE
- max_uses (INT(10) UNSIGNED, NOT NULL DEFAULT '1') → Dado do tipo INT(10) UNSIGNED
- used_count (INT(10) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo INT(10) UNSIGNED
- status (ENUM('PENDING','ACTIVE','USED','EXPIRED','CANCELLED'), NOT NULL DEFAULT 'pending') → Dado do tipo ENUM('PENDING','ACTIVE','USED','EXPIRED','CANCELLED')
- admin_notes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: creditos_compras
========================================

Descrição:
Tabela responsável por armazenar dados de creditos_compras.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (BIGINT(20) UNSIGNED, NOT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- quantidade (INT(11), NOT NULL) → Dado do tipo INT(11)
- valor (DECIMAL(10,2), NOT NULL) → Dado do tipo DECIMAL(10,2)
- status (VARCHAR(255), NOT NULL DEFAULT 'PENDENTE') → Dado do tipo VARCHAR(255)
- gateway (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- payment_id (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: creditos_pacotes
========================================

Descrição:
Tabela responsável por armazenar dados de creditos_pacotes.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- nome (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- quantidade (INT(11), NOT NULL) → Dado do tipo INT(11)
- valor (DECIMAL(10,2), NOT NULL) → Dado do tipo DECIMAL(10,2)
- ativo (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: email_templates
========================================

Descrição:
Tabela responsável por armazenar dados de email_templates.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- empresa_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- tipo (VARCHAR(64), NOT NULL) → Dado do tipo VARCHAR(64)
- nome_template (VARCHAR(160), NOT NULL) → Dado do tipo VARCHAR(160)
- assunto (VARCHAR(500), NOT NULL) → Dado do tipo VARCHAR(500)
- mensagem (TEXT, NOT NULL) → Dado do tipo TEXT
- variaveis (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- ativo (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `empresa_id`)

----------------------------------------

========================================
TABELA: especialidades
========================================

Descrição:
Tabela responsável por armazenar dados de especialidades.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- codigo (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- nome (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- categoria (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- icone (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- status (ENUM('ATIVO','INATIVO'), NOT NULL DEFAULT 'Ativo') → Dado do tipo ENUM('ATIVO','INATIVO')
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro
- profession_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `professions`

Relacionamentos:
- 1:N (Pertence à tabela `professions` via `profession_id`)

----------------------------------------

========================================
TABELA: evolution_photos
========================================

Descrição:
Tabela responsável por armazenar dados de evolution_photos.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- photo_path (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- type (ENUM('FRONT','SIDE','BACK','CUSTOM'), NOT NULL DEFAULT 'front') → Dado do tipo ENUM('FRONT','SIDE','BACK','CUSTOM')
- registered_date (DATE, NOT NULL) → Dado do tipo DATE
- weight_kg (DECIMAL(6,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(6,2)
- notes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: exercise_entries
========================================

Descrição:
Tabela responsável por armazenar dados de exercise_entries.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- entry_date (DATE, NOT NULL) → Dado do tipo DATE
- activity_type (VARCHAR(120), NOT NULL) → Dado do tipo VARCHAR(120)
- duration_min (SMALLINT(5) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo SMALLINT(5) UNSIGNED
- rpe (TINYINT(3) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo TINYINT(3) UNSIGNED
- rest_default (SMALLINT(5) UNSIGNED, NULL DEFAULT '60') → Dado do tipo SMALLINT(5) UNSIGNED
- calories_burned (SMALLINT(5) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo SMALLINT(5) UNSIGNED
- sets_data (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- notes (VARCHAR(500), NULL DEFAULT NULL) → Dado do tipo VARCHAR(500)
- created_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora de criação do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: exercise_muscles
========================================

Descrição:
Tabela responsável por armazenar dados de exercise_muscles.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- exercise_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `exercises_catalog`
- muscle_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `muscles`
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `exercises_catalog` via `exercise_id`)
- 1:N (Pertence à tabela `muscles` via `muscle_id`)

----------------------------------------

========================================
TABELA: exercise_sets
========================================

Descrição:
Tabela responsável por armazenar dados de exercise_sets.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- training_plan_exercise_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `training_plan_exercises`
- set_number (SMALLINT(5) UNSIGNED, NOT NULL) → Dado do tipo SMALLINT(5) UNSIGNED
- reps_target (SMALLINT(5) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo SMALLINT(5) UNSIGNED
- weight_target (DECIMAL(8,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(8,2)
- rest_seconds (SMALLINT(5) UNSIGNED, NOT NULL DEFAULT '60') → Dado do tipo SMALLINT(5) UNSIGNED
- rpe_target (SMALLINT(5) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo SMALLINT(5) UNSIGNED
- cadence (VARCHAR(10), NULL DEFAULT NULL) → Dado do tipo VARCHAR(10)
- set_type (VARCHAR(20), NOT NULL DEFAULT 'work') → Dado do tipo VARCHAR(20)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `training_plan_exercises` via `training_plan_exercise_id`)

----------------------------------------

========================================
TABELA: exercises_catalog
========================================

Descrição:
Tabela responsável por armazenar dados de exercises_catalog.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(120), NOT NULL) → Dado do tipo VARCHAR(120)
- muscle_group (VARCHAR(64), NOT NULL) → Dado do tipo VARCHAR(64)
- equipment (VARCHAR(64), NULL DEFAULT NULL) → Dado do tipo VARCHAR(64)
- difficulty (VARCHAR(24), NOT NULL DEFAULT 'beginner') → Dado do tipo VARCHAR(24)
- instructions (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- video_url (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: failed_jobs
========================================

Descrição:
Tabela responsável por armazenar dados de failed_jobs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- uuid (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- connection (TEXT, NOT NULL) → Dado do tipo TEXT
- queue (TEXT, NOT NULL) → Dado do tipo TEXT
- payload (LONGTEXT, NOT NULL) → Dado do tipo LONGTEXT
- exception (LONGTEXT, NOT NULL) → Dado do tipo LONGTEXT
- failed_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: financial_logs
========================================

Descrição:
Tabela responsável por armazenar dados de financial_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- academy_company_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- action (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- amount (DECIMAL(12,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(12,2)
- status_before (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- status_after (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- transaction_id (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- origin (VARCHAR(255), NOT NULL DEFAULT 'system') → Dado do tipo VARCHAR(255)
- ip_address (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- observation (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- payload (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: food_entries
========================================

Descrição:
Tabela responsável por armazenar dados de food_entries.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- entry_date (DATE, NOT NULL) → Dado do tipo DATE
- meal_type (VARCHAR(32), NOT NULL DEFAULT 'other') → Dado do tipo VARCHAR(32)
- food_name (VARCHAR(200), NOT NULL) → Dado do tipo VARCHAR(200)
- amount (DOUBLE, NULL DEFAULT NULL) → Dado do tipo DOUBLE
- unit (VARCHAR(20), NOT NULL DEFAULT 'g') → Dado do tipo VARCHAR(20)
- calories (SMALLINT(5) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo SMALLINT(5) UNSIGNED
- protein_g (DECIMAL(6,2), NOT NULL DEFAULT '0.00') → Dado do tipo DECIMAL(6,2)
- carbs_g (DECIMAL(6,2), NOT NULL DEFAULT '0.00') → Dado do tipo DECIMAL(6,2)
- fat_g (DECIMAL(6,2), NOT NULL DEFAULT '0.00') → Dado do tipo DECIMAL(6,2)
- created_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora de criação do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: food_nutrient
========================================

Descrição:
Tabela responsável por armazenar dados de food_nutrient.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- food_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `foods`
- nutrient_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `nutrients`
- amount (DECIMAL(12,4), NOT NULL) → Dado do tipo DECIMAL(12,4)

Relacionamentos:
- 1:N (Pertence à tabela `foods` via `food_id`)
- 1:N (Pertence à tabela `nutrients` via `nutrient_id`)

----------------------------------------

========================================
TABELA: foods
========================================

Descrição:
Tabela responsável por armazenar dados de foods.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- brand (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- barcode (VARCHAR(20), NULL DEFAULT NULL) → Dado do tipo VARCHAR(20)
- base_amount (DECIMAL(10,2), NOT NULL DEFAULT '100.00') → Dado do tipo DECIMAL(10,2)
- unit (ENUM('G','ML','UNIT'), NOT NULL DEFAULT 'g') → Dado do tipo ENUM('G','ML','UNIT')
- data_source (VARCHAR(255), NOT NULL DEFAULT 'local') → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: generated_reports
========================================

Descrição:
Tabela responsável por armazenar dados de generated_reports.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- document_id (CHAR(36), NOT NULL) → Dado do tipo CHAR(36)
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- type (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- version (INT(11), NOT NULL DEFAULT '1') → Dado do tipo INT(11)
- hash (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- generated_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP
- metadata (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: goals
========================================

Descrição:
Tabela responsável por armazenar dados de goals.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- title (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- type (ENUM('REVENUE','NEW_USERS','ACTIVE_USERS','TICKETS_RESOLVED','CUSTOM'), NOT NULL) → Dado do tipo ENUM('REVENUE','NEW_USERS','ACTIVE_USERS','TICKETS_RESOLVED','CUSTOM')
- target_value (DECIMAL(15,2), NOT NULL) → Dado do tipo DECIMAL(15,2)
- current_value (DECIMAL(15,2), NOT NULL DEFAULT '0.00') → Dado do tipo DECIMAL(15,2)
- start_date (DATE, NOT NULL) → Dado do tipo DATE
- end_date (DATE, NOT NULL) → Dado do tipo DATE
- description (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: historico_pdfs
========================================

Descrição:
Tabela responsável por armazenar dados de historico_pdfs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- academy_company_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- academy_unit_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_units`
- user_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- pdf_template_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `pdf_templates`
- document_type (VARCHAR(64), NOT NULL) → Dado do tipo VARCHAR(64)
- related_document_type (VARCHAR(64), NULL DEFAULT NULL) → Dado do tipo VARCHAR(64)
- related_document_id (BIGINT(20) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- numero_oficial (VARCHAR(64), NULL DEFAULT NULL) → Dado do tipo VARCHAR(64)
- nome_arquivo (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- caminho_arquivo (VARCHAR(512), NOT NULL) → Dado do tipo VARCHAR(512)
- codigo_validacao (VARCHAR(64), NOT NULL) → Dado do tipo VARCHAR(64)
- validation_status (VARCHAR(32), NOT NULL DEFAULT 'valid') → Dado do tipo VARCHAR(32)
- issued_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP
- expires_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- generation_status (VARCHAR(32), NOT NULL DEFAULT 'complete') → Dado do tipo VARCHAR(32)
- source_variables (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- metadata (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- ip_address (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `academy_units` via `academy_unit_id`)
- 1:N (Pertence à tabela `pdf_templates` via `pdf_template_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: internal_email_attachments
========================================

Descrição:
Tabela responsável por armazenar dados de internal_email_attachments.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- email_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `internal_emails`
- file_name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- file_path (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- file_type (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- file_size (BIGINT(20) UNSIGNED, NOT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `internal_emails` via `email_id`)

----------------------------------------

========================================
TABELA: internal_emails
========================================

Descrição:
Tabela responsável por armazenar dados de internal_emails.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- sender_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- recipient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- subject (VARCHAR(200), NOT NULL) → Dado do tipo VARCHAR(200)
- content (TEXT, NOT NULL) → Dado do tipo TEXT
- is_read (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- sent_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP
- read_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- excluded_at_sender (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- excluded_at_receiver (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- status (ENUM('DRAFT','OUTBOX','SENT','FAILED'), NOT NULL DEFAULT 'sent') → Dado do tipo ENUM('DRAFT','OUTBOX','SENT','FAILED')
- parent_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `internal_emails`
- is_system (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `recipient_id`)
- 1:N (Pertence à tabela `internal_emails` via `parent_id`)
- 1:N (Pertence à tabela `users` via `sender_id`)

----------------------------------------

========================================
TABELA: job_batches
========================================

Descrição:
Tabela responsável por armazenar dados de job_batches.

Colunas:
- id (VARCHAR(255), PK, NOT NULL) → Identificador único da tabela
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- total_jobs (INT(11), NOT NULL) → Dado do tipo INT(11)
- pending_jobs (INT(11), NOT NULL) → Dado do tipo INT(11)
- failed_jobs (INT(11), NOT NULL) → Dado do tipo INT(11)
- failed_job_ids (LONGTEXT, NOT NULL) → Dado do tipo LONGTEXT
- options (MEDIUMTEXT, NULL DEFAULT NULL) → Dado do tipo MEDIUMTEXT
- cancelled_at (INT(11), NULL DEFAULT NULL) → Dado do tipo INT(11)
- created_at (INT(11), NOT NULL) → Data e hora de criação do registro
- finished_at (INT(11), NULL DEFAULT NULL) → Dado do tipo INT(11)

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: jobs
========================================

Descrição:
Tabela responsável por armazenar dados de jobs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- queue (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- payload (LONGTEXT, NOT NULL) → Dado do tipo LONGTEXT
- attempts (TINYINT(3) UNSIGNED, NOT NULL) → Dado do tipo TINYINT(3) UNSIGNED
- reserved_at (INT(10) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo INT(10) UNSIGNED
- available_at (INT(10) UNSIGNED, NOT NULL) → Dado do tipo INT(10) UNSIGNED
- created_at (INT(10) UNSIGNED, NOT NULL) → Data e hora de criação do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: knowledge_articles
========================================

Descrição:
Tabela responsável por armazenar dados de knowledge_articles.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- titulo (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- slug (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- conteudo (LONGTEXT, NOT NULL) → Dado do tipo LONGTEXT
- categoria_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `knowledge_categories`
- tipo_usuario (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- ativo (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `knowledge_categories` via `categoria_id`)

----------------------------------------

========================================
TABELA: knowledge_categories
========================================

Descrição:
Tabela responsável por armazenar dados de knowledge_categories.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- nome (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- slug (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- descricao (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- tipo_usuario (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- ativo (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: lead_interactions
========================================

Descrição:
Tabela responsável por armazenar dados de lead_interactions.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- lead_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `leads`
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- tipo_contato (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- descricao (TEXT, NOT NULL) → Dado do tipo TEXT
- data_contato (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `leads` via `lead_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: leads
========================================

Descrição:
Tabela responsável por armazenar dados de leads.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- nome (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- email (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- telefone (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- empresa (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- origem (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- responsavel_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- converted_user_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- status (ENUM('NOVO','EM CONTATO','EM NEGOCIAçãO','CONVERTIDO','PERDIDO'), NOT NULL DEFAULT 'Novo') → Dado do tipo ENUM('NOVO','EM CONTATO','EM NEGOCIAçãO','CONVERTIDO','PERDIDO')
- observacao (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- valor_estimado (DECIMAL(10,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(10,2)
- previsao_fechamento (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro
- deleted_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de exclusão lógica (soft delete)

Relacionamentos:
- 1:N (Pertence à tabela `users` via `converted_user_id`)
- 1:N (Pertence à tabela `users` via `responsavel_id`)

----------------------------------------

========================================
TABELA: load_logs
========================================

Descrição:
Tabela responsável por armazenar dados de load_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- training_plan_exercise_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `training_plan_exercises`
- exercise_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `exercises_catalog`
- log_date (DATE, NOT NULL) → Dado do tipo DATE
- set_number (SMALLINT(5) UNSIGNED, NOT NULL) → Dado do tipo SMALLINT(5) UNSIGNED
- reps_done (SMALLINT(5) UNSIGNED, NOT NULL) → Dado do tipo SMALLINT(5) UNSIGNED
- to_failure (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- weight_kg (DECIMAL(8,2), NOT NULL) → Dado do tipo DECIMAL(8,2)
- one_rm (DECIMAL(10,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(10,2)
- rpe (SMALLINT(5) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo SMALLINT(5) UNSIGNED
- notes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `exercises_catalog` via `exercise_id`)
- 1:N (Pertence à tabela `training_plan_exercises` via `training_plan_exercise_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: log_envio_email
========================================

Descrição:
Tabela responsável por armazenar dados de log_envio_email.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- empresa_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- usuario_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- tipo_envio (VARCHAR(64), NULL DEFAULT NULL) → Dado do tipo VARCHAR(64)
- email_destino (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- assunto (VARCHAR(500), NULL DEFAULT NULL) → Dado do tipo VARCHAR(500)
- mensagem (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- status (VARCHAR(16), NOT NULL) → Dado do tipo VARCHAR(16)
- erro (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- ip (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- data_envio (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `empresa_id`)
- 1:N (Pertence à tabela `users` via `usuario_id`)

----------------------------------------

========================================
TABELA: meal_template_items
========================================

Descrição:
Tabela responsável por armazenar dados de meal_template_items.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- meal_template_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `meal_templates`
- meal_type (VARCHAR(32), NOT NULL DEFAULT 'other') → Dado do tipo VARCHAR(32)
- food_name (VARCHAR(200), NOT NULL) → Dado do tipo VARCHAR(200)
- calories (SMALLINT(5) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo SMALLINT(5) UNSIGNED
- protein_g (DECIMAL(6,2), NOT NULL DEFAULT '0.00') → Dado do tipo DECIMAL(6,2)
- carbs_g (DECIMAL(6,2), NOT NULL DEFAULT '0.00') → Dado do tipo DECIMAL(6,2)
- fat_g (DECIMAL(6,2), NOT NULL DEFAULT '0.00') → Dado do tipo DECIMAL(6,2)
- position (SMALLINT(5) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo SMALLINT(5) UNSIGNED

Relacionamentos:
- 1:N (Pertence à tabela `meal_templates` via `meal_template_id`)

----------------------------------------

========================================
TABELA: meal_templates
========================================

Descrição:
Tabela responsável por armazenar dados de meal_templates.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- professional_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- name (VARCHAR(120), NOT NULL) → Dado do tipo VARCHAR(120)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `professional_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: medical_certificates
========================================

Descrição:
Tabela responsável por armazenar dados de medical_certificates.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- date (DATETIME, NOT NULL) → Dado do tipo DATETIME
- reason (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- start_date (DATE, NOT NULL) → Dado do tipo DATE
- end_date (DATE, NOT NULL) → Dado do tipo DATE
- period (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- observations (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- pdf_path (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: medical_evolutions
========================================

Descrição:
Tabela responsável por armazenar dados de medical_evolutions.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- date (DATETIME, NOT NULL) → Dado do tipo DATETIME
- type (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- chief_complaint (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- assessment (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- diagnosis (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- conduct (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- observations (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- attachments (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: medical_histories
========================================

Descrição:
Tabela responsável por armazenar dados de medical_histories.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- action_type (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- module (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- description (TEXT, NOT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: medical_prescriptions
========================================

Descrição:
Tabela responsável por armazenar dados de medical_prescriptions.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- especialidade_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `especialidades`
- academy_company_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- date (DATETIME, NOT NULL) → Dado do tipo DATETIME
- objective (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- protocol (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- medicine (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- dosage (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- frequency (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- duration (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- observations (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- pdf_path (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `especialidades` via `especialidade_id`)
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: medical_reports
========================================

Descrição:
Tabela responsável por armazenar dados de medical_reports.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- title (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- date (DATETIME, NOT NULL) → Dado do tipo DATETIME
- description (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- conclusion (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- observations (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- pdf_path (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- qr_code (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: menu_permission_audit_logs
========================================

Descrição:
Tabela responsável por armazenar dados de menu_permission_audit_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- role_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `roles`
- academy_company_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- action (VARCHAR(64), NOT NULL) → Dado do tipo VARCHAR(64)
- payload (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- ip_address (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `roles` via `role_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: menus
========================================

Descrição:
Tabela responsável por armazenar dados de menus.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- parent_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `menus`
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- label (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- route (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- match_mode (VARCHAR(16), NOT NULL DEFAULT 'exact') → Dado do tipo VARCHAR(16)
- is_container (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- icon (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- order (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- is_required (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- portal (VARCHAR(32), NOT NULL DEFAULT 'app') → Dado do tipo VARCHAR(32)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `menus` via `parent_id`)

----------------------------------------

========================================
TABELA: mercadopago_payment_credits
========================================

Descrição:
Tabela responsável por armazenar dados de mercadopago_payment_credits.

Colunas:
- mp_payment_id (BIGINT(20) UNSIGNED, PK, NOT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- plan_code (VARCHAR(16), NOT NULL) → Dado do tipo VARCHAR(16)
- transaction_amount (DECIMAL(12,2), NOT NULL) → Dado do tipo DECIMAL(12,2)
- currency_id (VARCHAR(8), NOT NULL DEFAULT 'BRL') → Dado do tipo VARCHAR(8)
- created_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora de criação do registro
- coupon_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `coupons`

Relacionamentos:
- 1:N (Pertence à tabela `coupons` via `coupon_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: mercadopago_subscriptions
========================================

Descrição:
Tabela responsável por armazenar dados de mercadopago_subscriptions.

Colunas:
- mp_preapproval_id (VARCHAR(48), PK, NOT NULL) → Dado do tipo VARCHAR(48)
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- plan_code (VARCHAR(16), NOT NULL) → Dado do tipo VARCHAR(16)
- status (VARCHAR(24), NOT NULL DEFAULT 'pending') → Dado do tipo VARCHAR(24)
- created_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora de criação do registro
- updated_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora da última atualização do registro
- coupon_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `coupons`

Relacionamentos:
- 1:N (Pertence à tabela `coupons` via `coupon_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: messages
========================================

Descrição:
Tabela responsável por armazenar dados de messages.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- conversation_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `conversations`
- sender_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- content (TEXT, NOT NULL) → Dado do tipo TEXT
- is_read (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `conversations` via `conversation_id`)
- 1:N (Pertence à tabela `users` via `sender_id`)

----------------------------------------

========================================
TABELA: migrations
========================================

Descrição:
Tabela responsável por armazenar dados de migrations.

Colunas:
- id (INT(10) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- migration (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- batch (INT(11), NOT NULL) → Dado do tipo INT(11)

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: muscle_groups
========================================

Descrição:
Tabela responsável por armazenar dados de muscle_groups.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- region (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: muscles
========================================

Descrição:
Tabela responsável por armazenar dados de muscles.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- group_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `muscle_groups`
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- type (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `muscle_groups` via `group_id`)

----------------------------------------

========================================
TABELA: notifications
========================================

Descrição:
Tabela responsável por armazenar dados de notifications.

Colunas:
- id (CHAR(36), PK, NOT NULL) → Identificador único da tabela
- type (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- notifiable_type (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- notifiable_id (BIGINT(20) UNSIGNED, NOT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- data (TEXT, NOT NULL) → Dado do tipo TEXT
- read_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: nutrients
========================================

Descrição:
Tabela responsável por armazenar dados de nutrients.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- slug (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- unit (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- is_main (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: omni_agents
========================================

Descrição:
Tabela responsável por armazenar dados de omni_agents.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- company_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `omni_companies`
- status (ENUM('ONLINE','OFFLINE','BUSY'), NOT NULL DEFAULT 'offline') → Dado do tipo ENUM('ONLINE','OFFLINE','BUSY')
- max_simultaneous_chats (INT(11), NOT NULL DEFAULT '5') → Dado do tipo INT(11)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `omni_companies` via `company_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: omni_bot_options
========================================

Descrição:
Tabela responsável por armazenar dados de omni_bot_options.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- step_id (BIGINT(20) UNSIGNED, NOT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- trigger_value (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- label (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- destination_step_id (BIGINT(20) UNSIGNED, NOT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: omni_bot_steps
========================================

Descrição:
Tabela responsável por armazenar dados de omni_bot_steps.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- bot_id (BIGINT(20) UNSIGNED, NOT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- label (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- type (ENUM('MESSAGE','MENU','QUESTION','TRANSFER'), NOT NULL DEFAULT 'message') → Dado do tipo ENUM('MESSAGE','MENU','QUESTION','TRANSFER')
- content (TEXT, NOT NULL) → Dado do tipo TEXT
- is_start (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- next_step_id (BIGINT(20) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: omni_bots
========================================

Descrição:
Tabela responsável por armazenar dados de omni_bots.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- company_id (BIGINT(20) UNSIGNED, NOT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- whatsapp_phone (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- business_hours (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- out_of_office_message (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: omni_business_hours
========================================

Descrição:
Tabela responsável por armazenar dados de omni_business_hours.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- company_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `omni_companies`
- day_of_week (TINYINT(4), NOT NULL) → Dado do tipo TINYINT(4)
- open_time (TIME, NOT NULL) → Dado do tipo TIME
- close_time (TIME, NOT NULL) → Dado do tipo TIME
- is_closed (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `omni_companies` via `company_id`)

----------------------------------------

========================================
TABELA: omni_channels
========================================

Descrição:
Tabela responsável por armazenar dados de omni_channels.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- company_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `omni_companies`
- type (ENUM('WHATSAPP','WIDGET','API'), NOT NULL DEFAULT 'widget') → Dado do tipo ENUM('WHATSAPP','WIDGET','API')
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- config (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `omni_companies` via `company_id`)

----------------------------------------

========================================
TABELA: omni_chatbot_rules
========================================

Descrição:
Tabela responsável por armazenar dados de omni_chatbot_rules.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- company_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `omni_companies`
- trigger_type (VARCHAR(255), NOT NULL DEFAULT 'keyword') → Dado do tipo VARCHAR(255)
- pattern (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- response (TEXT, NOT NULL) → Dado do tipo TEXT
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `omni_companies` via `company_id`)

----------------------------------------

========================================
TABELA: omni_companies
========================================

Descrição:
Tabela responsável por armazenar dados de omni_companies.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- slug (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- logo (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- settings (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: omni_conversations
========================================

Descrição:
Tabela responsável por armazenar dados de omni_conversations.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- company_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `omni_companies`
- bot_id (BIGINT(20) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- channel_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `omni_channels`
- customer_external_id (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- customer_name (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- agent_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `omni_agents`
- queue_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `omni_queues`
- status (ENUM('PENDING','OPEN','CLOSED','BOT'), NOT NULL DEFAULT 'bot') → Dado do tipo ENUM('PENDING','OPEN','CLOSED','BOT')
- current_bot_step_id (BIGINT(20) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- last_message_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `omni_agents` via `agent_id`)
- 1:N (Pertence à tabela `omni_channels` via `channel_id`)
- 1:N (Pertence à tabela `omni_companies` via `company_id`)
- 1:N (Pertence à tabela `omni_queues` via `queue_id`)

----------------------------------------

========================================
TABELA: omni_messages
========================================

Descrição:
Tabela responsável por armazenar dados de omni_messages.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- conversation_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `omni_conversations`
- sender_type (ENUM('CUSTOMER','AGENT','BOT','SYSTEM'), NOT NULL) → Dado do tipo ENUM('CUSTOMER','AGENT','BOT','SYSTEM')
- sender_id (INT(10) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo INT(10) UNSIGNED
- content (TEXT, NOT NULL) → Dado do tipo TEXT
- content_type (VARCHAR(255), NOT NULL DEFAULT 'text') → Dado do tipo VARCHAR(255)
- file_path (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- read_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `omni_conversations` via `conversation_id`)

----------------------------------------

========================================
TABELA: omni_queues
========================================

Descrição:
Tabela responsável por armazenar dados de omni_queues.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- company_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `omni_companies`
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- description (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `omni_companies` via `company_id`)

----------------------------------------

========================================
TABELA: omnichannel_tables
========================================

Descrição:
Tabela responsável por armazenar dados de omnichannel_tables.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: onboarding_steps
========================================

Descrição:
Tabela responsável por armazenar dados de onboarding_steps.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- lead_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `leads`
- title (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- description (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- is_completed (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- completed_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- order (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `leads` via `lead_id`)

----------------------------------------

========================================
TABELA: organization_patient
========================================

Descrição:
Tabela responsável por armazenar dados de organization_patient.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- organization_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `organizations`
- patient_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `patients`
- internal_code (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `organizations` via `organization_id`)
- 1:N (Pertence à tabela `patients` via `patient_id`)

----------------------------------------

========================================
TABELA: organization_professional_patient
========================================

Descrição:
Tabela responsável por armazenar dados de organization_professional_patient.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- organization_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `organizations`
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- patient_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `patients`
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `organizations` via `organization_id`)
- 1:N (Pertence à tabela `patients` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: organization_user
========================================

Descrição:
Tabela responsável por armazenar dados de organization_user.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- organization_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `organizations`
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- role (VARCHAR(255), NOT NULL DEFAULT 'member') → Dado do tipo VARCHAR(255)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `organizations` via `organization_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: organizations
========================================

Descrição:
Tabela responsável por armazenar dados de organizations.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- uuid (CHAR(36), NOT NULL) → Dado do tipo CHAR(36)
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- type (ENUM('CLINICA','PROFISSIONAL_AUTONOMO','EDUCACIONAL'), NOT NULL) → Dado do tipo ENUM('CLINICA','PROFISSIONAL_AUTONOMO','EDUCACIONAL')
- owner_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- tax_id (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `owner_id`)

----------------------------------------

========================================
TABELA: pacientes
========================================

Descrição:
Tabela responsável por armazenar dados de pacientes.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- patient_type (VARCHAR(32), NULL DEFAULT NULL) → Dado do tipo VARCHAR(32)
- insurance_type (VARCHAR(32), NULL DEFAULT NULL) → Dado do tipo VARCHAR(32)
- insurance_card_number (VARCHAR(64), NULL DEFAULT NULL) → Dado do tipo VARCHAR(64)
- insurance_expiry (DATE, NULL DEFAULT NULL) → Dado do tipo DATE
- responsible_legal (VARCHAR(120), NULL DEFAULT NULL) → Dado do tipo VARCHAR(120)
- patient_permissions (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- linked_by (INT(10) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo INT(10) UNSIGNED
- linking_ip (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- linking_device (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- profissional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- data_cadastro (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP
- status (ENUM('SIM','NãO'), NULL DEFAULT 'Sim') → Dado do tipo ENUM('SIM','NãO')
- main_diagnosis (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- important_notes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- empresa_id (INT(10) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo INT(10) UNSIGNED
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro
- tracking_status (VARCHAR(255), NULL DEFAULT 'Início') → Dado do tipo VARCHAR(255)
- professional_notes_for_patient (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)
- 1:N (Pertence à tabela `users` via `profissional_id`)

----------------------------------------

========================================
TABELA: password_reset_tokens
========================================

Descrição:
Tabela responsável por armazenar dados de password_reset_tokens.

Colunas:
- email (VARCHAR(255), PK, NOT NULL) → Dado do tipo VARCHAR(255)
- token (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: patient_access_tokens
========================================

Descrição:
Tabela responsável por armazenar dados de patient_access_tokens.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- patient_id (BIGINT(20) UNSIGNED, NOT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- type (VARCHAR(255), NOT NULL DEFAULT 'access') → Dado do tipo VARCHAR(255)
- token_hash (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- expires_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- used_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- status (VARCHAR(255), NOT NULL DEFAULT 'active') → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: patient_documents
========================================

Descrição:
Tabela responsável por armazenar dados de patient_documents.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- academy_company_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- title (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- category (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- file_path (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- file_type (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- file_size (BIGINT(20), NULL DEFAULT NULL) → Dado do tipo BIGINT(20)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: patient_transfers
========================================

Descrição:
Tabela responsável por armazenar dados de patient_transfers.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- from_professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- to_professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- status (ENUM('PENDING','ACCEPTED','REJECTED'), NOT NULL DEFAULT 'pending') → Dado do tipo ENUM('PENDING','ACCEPTED','REJECTED')
- requested_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP
- processed_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `from_professional_id`)
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `to_professional_id`)

----------------------------------------

========================================
TABELA: patient_treatment_plans
========================================

Descrição:
Tabela responsável por armazenar dados de patient_treatment_plans.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- academy_company_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- diagnosis (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- objectives (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- care_plan (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- orientations (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: patients
========================================

Descrição:
Armazena os perfis clínicos/pacientes associados a um usuário e profissional.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- uuid (CHAR(36), NOT NULL) → Dado do tipo CHAR(36)
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- cpf (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- email (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- birth_date (DATE, NULL DEFAULT NULL) → Dado do tipo DATE
- gender (ENUM('M','F','O'), NULL DEFAULT NULL) → Dado do tipo ENUM('M','F','O')
- user_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: payment_settings
========================================

Descrição:
Tabela responsável por armazenar dados de payment_settings.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- gateway (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- environment (ENUM('SANDBOX','PRODUCTION'), NOT NULL DEFAULT 'sandbox') → Dado do tipo ENUM('SANDBOX','PRODUCTION')
- public_key (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- access_token (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- webhook_secret (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- enable_credit_card (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- enable_pix (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- enable_boleto (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- boleto_expiration_days (INT(11), NOT NULL DEFAULT '3') → Dado do tipo INT(11)
- pix_expiration_minutes (INT(11), NOT NULL DEFAULT '30') → Dado do tipo INT(11)
- status (ENUM('ACTIVE','INACTIVE'), NOT NULL DEFAULT 'active') → Dado do tipo ENUM('ACTIVE','INACTIVE')
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: payments
========================================

Descrição:
Tabela responsável por armazenar dados de payments.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- subscription_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `subscriptions`
- gateway (VARCHAR(255), NOT NULL DEFAULT 'mercadopago') → Dado do tipo VARCHAR(255)
- gateway_id (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- amount (DECIMAL(12,2), NOT NULL) → Dado do tipo DECIMAL(12,2)
- currency (VARCHAR(3), NOT NULL DEFAULT 'BRL') → Dado do tipo VARCHAR(3)
- status (VARCHAR(32), NOT NULL DEFAULT 'PENDENTE') → Dado do tipo VARCHAR(32)
- payload (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `subscriptions` via `subscription_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: pdf_delivery_logs
========================================

Descrição:
Tabela responsável por armazenar dados de pdf_delivery_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- historico_pdf_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `historico_pdfs`
- channel (VARCHAR(32), NOT NULL) → Dado do tipo VARCHAR(32)
- email_destinatario (VARCHAR(191), NULL DEFAULT NULL) → Dado do tipo VARCHAR(191)
- telefone_destinatario (VARCHAR(64), NULL DEFAULT NULL) → Dado do tipo VARCHAR(64)
- data_envio (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- status_envio (VARCHAR(32), NOT NULL DEFAULT 'pending') → Dado do tipo VARCHAR(32)
- tentativas (SMALLINT(5) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo SMALLINT(5) UNSIGNED
- ultimo_erro (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `historico_pdfs` via `historico_pdf_id`)

----------------------------------------

========================================
TABELA: pdf_generation_logs
========================================

Descrição:
Tabela responsável por armazenar dados de pdf_generation_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- pdf_template_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `pdf_templates`
- historico_pdf_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `historico_pdfs`
- document_type (VARCHAR(64), NOT NULL) → Dado do tipo VARCHAR(64)
- template_name (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- action (VARCHAR(32), NOT NULL DEFAULT 'download') → Dado do tipo VARCHAR(32)
- filename (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- status (VARCHAR(16), NOT NULL DEFAULT 'success') → Dado do tipo VARCHAR(16)
- error_message (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- ip_address (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- user_agent (VARCHAR(512), NULL DEFAULT NULL) → Dado do tipo VARCHAR(512)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `historico_pdfs` via `historico_pdf_id`)
- 1:N (Pertence à tabela `pdf_templates` via `pdf_template_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: pdf_number_sequences
========================================

Descrição:
Tabela responsável por armazenar dados de pdf_number_sequences.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- academy_company_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `academy_companies`
- tipo_documento (VARCHAR(64), NOT NULL) → Dado do tipo VARCHAR(64)
- ano (SMALLINT(5) UNSIGNED, NOT NULL) → Dado do tipo SMALLINT(5) UNSIGNED
- sequencia_atual (INT(10) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo INT(10) UNSIGNED
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)

----------------------------------------

========================================
TABELA: pdf_signature_audit_logs
========================================

Descrição:
Tabela responsável por armazenar dados de pdf_signature_audit_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- historico_pdf_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `historico_pdfs`
- user_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- evento (VARCHAR(64), NOT NULL) → Dado do tipo VARCHAR(64)
- detalhe (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- ip_address (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `historico_pdfs` via `historico_pdf_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: pdf_signatures
========================================

Descrição:
Tabela responsável por armazenar dados de pdf_signatures.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- historico_pdf_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `historico_pdfs`
- user_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- signer_name (VARCHAR(191), NULL DEFAULT NULL) → Dado do tipo VARCHAR(191)
- tipo_assinatura (VARCHAR(32), NOT NULL) → Dado do tipo VARCHAR(32)
- modo (VARCHAR(32), NOT NULL DEFAULT 'upload') → Dado do tipo VARCHAR(32)
- imagem_assinatura (VARCHAR(512), NOT NULL) → Dado do tipo VARCHAR(512)
- ip_address (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- data_assinatura (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `historico_pdfs` via `historico_pdf_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: pdf_templates
========================================

Descrição:
Tabela responsável por armazenar dados de pdf_templates.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- academy_company_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- academy_unit_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_units`
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- document_type (VARCHAR(64), NOT NULL) → Dado do tipo VARCHAR(64)
- description (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- html_body (LONGTEXT, NOT NULL) → Dado do tipo LONGTEXT
- css_extra (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- logo_path (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- primary_color (VARCHAR(32), NOT NULL DEFAULT '#1e293b') → Dado do tipo VARCHAR(32)
- secondary_color (VARCHAR(32), NULL DEFAULT NULL) → Dado do tipo VARCHAR(32)
- accent_color (VARCHAR(32), NULL DEFAULT NULL) → Dado do tipo VARCHAR(32)
- footer_html (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- auto_email_enabled (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- auto_email_recipients (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- auto_whatsapp_enabled (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- whatsapp_message_template (VARCHAR(500), NULL DEFAULT NULL) → Dado do tipo VARCHAR(500)
- auto_whatsapp_recipients (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- duplicated_from_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `pdf_templates`
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- is_default (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- sort_order (SMALLINT(5) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo SMALLINT(5) UNSIGNED
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `academy_units` via `academy_unit_id`)
- 1:N (Pertence à tabela `pdf_templates` via `duplicated_from_id`)

----------------------------------------

========================================
TABELA: permissions
========================================

Descrição:
Tabela responsável por armazenar dados de permissions.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- label (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- description (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: photos
========================================

Descrição:
Tabela responsável por armazenar dados de photos.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- student_id (BIGINT(20) UNSIGNED, NOT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- file_path (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- category (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- description (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- plan_type (VARCHAR(255), NOT NULL DEFAULT 'Free') → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: plan_features
========================================

Descrição:
Tabela responsável por armazenar dados de plan_features.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- plan_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `plans`
- feature_key (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- is_enabled (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `plans` via `plan_id`)

----------------------------------------

========================================
TABELA: plan_permissions
========================================

Descrição:
Tabela responsável por armazenar dados de plan_permissions.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- plan_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `plans`
- permission_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `permissions`
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `permissions` via `permission_id`)
- 1:N (Pertence à tabela `plans` via `plan_id`)

----------------------------------------

========================================
TABELA: plans
========================================

Descrição:
Tabela responsável por armazenar dados de plans.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- description (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- type (VARCHAR(32), NOT NULL) → Dado do tipo VARCHAR(32)
- is_corporate (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- price (DECIMAL(10,2), NOT NULL) → Dado do tipo DECIMAL(10,2)
- commission_rate (DECIMAL(5,2), NOT NULL DEFAULT '0.00') → Percentual de comissão (ex: 10.00 para 10%)
- ai_credits (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- max_students (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- max_workouts (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- max_diets (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- max_assessments (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- max_patients (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- max_professionals (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- max_exercises_per_workout (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- price_per_professional (DECIMAL(10,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(10,2)
- min_professionals (INT(11), NOT NULL DEFAULT '1') → Dado do tipo INT(11)
- features (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- status (ENUM('ACTIVE','INACTIVE'), NOT NULL DEFAULT 'active') → Dado do tipo ENUM('ACTIVE','INACTIVE')
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: prescription_templates
========================================

Descrição:
Tabela responsável por armazenar dados de prescription_templates.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- especialidade_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `especialidades`
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- title (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- content (TEXT, NOT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `especialidades` via `especialidade_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: professional_appointments
========================================

Descrição:
Tabela responsável por armazenar dados de professional_appointments.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- appointment_at (DATETIME, NOT NULL) → Dado do tipo DATETIME
- status (VARCHAR(255), NOT NULL DEFAULT 'scheduled') → Dado do tipo VARCHAR(255)
- service_type (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- notes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: professional_availabilities
========================================

Descrição:
Tabela responsável por armazenar dados de professional_availabilities.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- professional_id (BIGINT(20) UNSIGNED, NOT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- day_of_week (TINYINT(4), NOT NULL) → Dado do tipo TINYINT(4)
- start_time (TIME, NOT NULL) → Dado do tipo TIME
- end_time (TIME, NOT NULL) → Dado do tipo TIME
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: professional_brandings
========================================

Descrição:
Tabela responsável por armazenar dados de professional_brandings.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- clinic_name (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- primary_color (VARCHAR(7), NOT NULL DEFAULT '#3b82f6') → Dado do tipo VARCHAR(7)
- accent_color (VARCHAR(7), NOT NULL DEFAULT '#10b981') → Dado do tipo VARCHAR(7)
- logo_path (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- custom_domain (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: professional_patient
========================================

Descrição:
Tabela responsável por armazenar dados de professional_patient.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- patient_permissions (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- linked_by (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- linking_ip (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- linking_device (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `linked_by`)
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: professional_patient_clinic
========================================

Descrição:
Tabela responsável por armazenar dados de professional_patient_clinic.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- academy_company_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `academy_companies`
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: professional_patient_requests
========================================

Descrição:
Tabela responsável por armazenar dados de professional_patient_requests.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- patient_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- professional_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- status (ENUM('PENDING','APPROVED','REJECTED'), NOT NULL DEFAULT 'pending') → Dado do tipo ENUM('PENDING','APPROVED','REJECTED')
- message (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `patient_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)

----------------------------------------

========================================
TABELA: professional_plans
========================================

Descrição:
Tabela responsável por armazenar dados de professional_plans.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- max_patients (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: professional_profiles
========================================

Descrição:
Tabela responsável por armazenar dados de professional_profiles.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- academy_unit_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_units`
- room (VARCHAR(64), NULL DEFAULT NULL) → Dado do tipo VARCHAR(64)
- profession_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `professions`
- specialty (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- experience_years (INT(11), NULL DEFAULT NULL) → Dado do tipo INT(11)
- education (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- certifications (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- about (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- professional_photo_path (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- offered_services (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- service_types (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- consultation_price (DECIMAL(10,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(10,2)
- appointment_duration (INT(11), NOT NULL DEFAULT '60') → Dado do tipo INT(11)
- appointment_interval (INT(11), NOT NULL DEFAULT '15') → Dado do tipo INT(11)
- company_name (VARCHAR(120), NULL DEFAULT NULL) → Dado do tipo VARCHAR(120)
- clinic_address (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- clinic_city (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- clinic_state (VARCHAR(2), NULL DEFAULT NULL) → Dado do tipo VARCHAR(2)
- work_days (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- work_start_time (TIME, NULL DEFAULT NULL) → Dado do tipo TIME
- work_end_time (TIME, NULL DEFAULT NULL) → Dado do tipo TIME
- is_public (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- internal_permissions (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- registration_number (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- council (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- registration_uf (VARCHAR(2), NOT NULL) → Dado do tipo VARCHAR(2)
- registration_expiry_date (DATE, NOT NULL) → Dado do tipo DATE
- last_audit_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- audit_status (VARCHAR(255), NOT NULL DEFAULT 'verified') → Dado do tipo VARCHAR(255)
- document_path (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- signature_path (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_by (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- updated_by (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- document_version (INT(11), NOT NULL DEFAULT '1') → Dado do tipo INT(11)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_units` via `academy_unit_id`)
- 1:N (Pertence à tabela `users` via `created_by`)
- 1:N (Pertence à tabela `professions` via `profession_id`)
- 1:N (Pertence à tabela `users` via `updated_by`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: professions
========================================

Descrição:
Tabela responsável por armazenar dados de professions.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- slug (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: pulse_aggregates
========================================

Descrição:
Tabela responsável por armazenar dados de pulse_aggregates.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- bucket (INT(10) UNSIGNED, NOT NULL) → Dado do tipo INT(10) UNSIGNED
- period (MEDIUMINT(8) UNSIGNED, NOT NULL) → Dado do tipo MEDIUMINT(8) UNSIGNED
- type (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- key (MEDIUMTEXT, NOT NULL) → Dado do tipo MEDIUMTEXT
- key_hash (BINARY(16), NULL DEFAULT NULL) → Dado do tipo BINARY(16)
- aggregate (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- value (DECIMAL(20,2), NOT NULL) → Dado do tipo DECIMAL(20,2)
- count (INT(10) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo INT(10) UNSIGNED

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: pulse_entries
========================================

Descrição:
Tabela responsável por armazenar dados de pulse_entries.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- timestamp (INT(10) UNSIGNED, NOT NULL) → Dado do tipo INT(10) UNSIGNED
- type (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- key (MEDIUMTEXT, NOT NULL) → Dado do tipo MEDIUMTEXT
- key_hash (BINARY(16), NULL DEFAULT NULL) → Dado do tipo BINARY(16)
- value (BIGINT(20), NULL DEFAULT NULL) → Dado do tipo BIGINT(20)

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: pulse_values
========================================

Descrição:
Tabela responsável por armazenar dados de pulse_values.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- timestamp (INT(10) UNSIGNED, NOT NULL) → Dado do tipo INT(10) UNSIGNED
- type (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- key (MEDIUMTEXT, NOT NULL) → Dado do tipo MEDIUMTEXT
- key_hash (BINARY(16), NULL DEFAULT NULL) → Dado do tipo BINARY(16)
- value (MEDIUMTEXT, NOT NULL) → Dado do tipo MEDIUMTEXT

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: role_menu_permissions
========================================

Descrição:
Tabela responsável por armazenar dados de role_menu_permissions.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- role_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `roles`
- menu_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `menus`
- pode_visualizar (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- pode_criar (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- pode_editar (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- pode_excluir (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- pode_exportar (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- pode_imprimir (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- academy_company_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `menus` via `menu_id`)
- 1:N (Pertence à tabela `roles` via `role_id`)

----------------------------------------

========================================
TABELA: role_permissions
========================================

Descrição:
Tabela responsável por armazenar dados de role_permissions.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- role_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `roles`
- permission_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `permissions`
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `permissions` via `permission_id`)
- 1:N (Pertence à tabela `roles` via `role_id`)

----------------------------------------

========================================
TABELA: roles
========================================

Descrição:
Tabela responsável por armazenar dados de roles.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- label (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- description (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: security_incidents
========================================

Descrição:
Tabela responsável por armazenar dados de security_incidents.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- reporter_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- title (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- description (TEXT, NOT NULL) → Dado do tipo TEXT
- severity (ENUM('LOW','MEDIUM','HIGH','CRITICAL'), NOT NULL DEFAULT 'low') → Dado do tipo ENUM('LOW','MEDIUM','HIGH','CRITICAL')
- status (ENUM('OPEN','INVESTIGATING','RESOLVED','CLOSED'), NOT NULL DEFAULT 'open') → Dado do tipo ENUM('OPEN','INVESTIGATING','RESOLVED','CLOSED')
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `reporter_id`)

----------------------------------------

========================================
TABELA: sessions
========================================

Descrição:
Tabela responsável por armazenar dados de sessions.

Colunas:
- id (VARCHAR(255), PK, NOT NULL) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo INT(10) UNSIGNED
- ip_address (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- user_agent (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- payload (LONGTEXT, NOT NULL) → Dado do tipo LONGTEXT
- last_activity (INT(11), NOT NULL) → Dado do tipo INT(11)

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: smart_stacks
========================================

Descrição:
Tabela responsável por armazenar dados de smart_stacks.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- professional_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- goal (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- target_audience (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- responsible_type (VARCHAR(255), NOT NULL DEFAULT 'ia') → Dado do tipo VARCHAR(255)
- status (VARCHAR(255), NOT NULL DEFAULT 'ativo') → Dado do tipo VARCHAR(255)
- start_date (DATE, NULL DEFAULT NULL) → Dado do tipo DATE
- end_date (DATE, NULL DEFAULT NULL) → Dado do tipo DATE
- adherence_rate (DOUBLE, NOT NULL DEFAULT '0') → Dado do tipo DOUBLE
- notes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `professional_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: subscription_logs
========================================

Descrição:
Tabela responsável por armazenar dados de subscription_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- subscription_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `subscriptions`
- event (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- old_status (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- new_status (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- amount (DECIMAL(12,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(12,2)
- payload (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- notes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `subscriptions` via `subscription_id`)

----------------------------------------

========================================
TABELA: subscriptions
========================================

Descrição:
Tabela responsável por armazenar dados de subscriptions.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- gateway_id (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- gateway_type (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- user_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- academy_company_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- billing_type (VARCHAR(255), NOT NULL DEFAULT 'individual') → Dado do tipo VARCHAR(255)
- max_professionals (INT(11), NULL DEFAULT NULL) → Dado do tipo INT(11)
- plan_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `plans`
- start_date (DATE, NOT NULL) → Dado do tipo DATE
- end_date (DATE, NULL DEFAULT NULL) → Dado do tipo DATE
- status (VARCHAR(32), NOT NULL DEFAULT 'PENDENTE') → Dado do tipo VARCHAR(32)
- days_overdue (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- payment_method (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- card_brand (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- card_last_four (CHAR(4), NULL DEFAULT NULL) → Dado do tipo CHAR(4)
- card_expiry (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- next_billing_date (DATE, NULL DEFAULT NULL) → Dado do tipo DATE
- retry_count (INT(10) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo INT(10) UNSIGNED
- last_attempt_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- pending_plan_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `plans`
- cancelled_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- refunded_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- refunded_amount (DECIMAL(12,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(12,2)
- reason_for_suspension (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `plans` via `pending_plan_id`)
- 1:N (Pertence à tabela `plans` via `plan_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: supplement_logs
========================================

Descrição:
Tabela responsável por armazenar dados de supplement_logs.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- supplement_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `supplements`
- taken_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `supplements` via `supplement_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: supplements
========================================

Descrição:
Tabela responsável por armazenar dados de supplements.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- smart_stack_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `smart_stacks`
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- dosage (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- unit (VARCHAR(255), NOT NULL DEFAULT 'g') → Dado do tipo VARCHAR(255)
- frequency (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- duration_days (INT(11), NULL DEFAULT NULL) → Dado do tipo INT(11)
- supplement_goal (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- observations (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- time_of_day (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- last_taken_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `smart_stacks` via `smart_stack_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: supplements_catalog
========================================

Descrição:
Tabela responsável por armazenar dados de supplements_catalog.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- name (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- category (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- default_dosage (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- default_unit (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- description (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- benefits (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- side_effects (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: support_tickets
========================================

Descrição:
Tabela responsável por armazenar dados de support_tickets.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- subject (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- priority (ENUM('LOW','MEDIUM','HIGH','CRITICAL'), NOT NULL DEFAULT 'Medium') → Dado do tipo ENUM('LOW','MEDIUM','HIGH','CRITICAL')
- status (ENUM('OPEN','IN PROGRESS','RESOLVED','CLOSED'), NOT NULL DEFAULT 'Open') → Dado do tipo ENUM('OPEN','IN PROGRESS','RESOLVED','CLOSED')
- category (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: system_errors
========================================

Descrição:
Tabela responsável por armazenar dados de system_errors.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (BIGINT(20) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo BIGINT(20) UNSIGNED
- type (VARCHAR(32), NOT NULL) → Dado do tipo VARCHAR(32)
- url (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- method (VARCHAR(10), NULL DEFAULT NULL) → Dado do tipo VARCHAR(10)
- message (TEXT, NOT NULL) → Dado do tipo TEXT
- stack_trace (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- payload (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- ip (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- user_agent (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora de criação do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: system_settings
========================================

Descrição:
Tabela responsável por armazenar dados de system_settings.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- key (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- value (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- description (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: ticket_messages
========================================

Descrição:
Tabela responsável por armazenar dados de ticket_messages.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- support_ticket_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `support_tickets`
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- message (TEXT, NOT NULL) → Dado do tipo TEXT
- is_admin_reply (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `support_tickets` via `support_ticket_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: training_lessons
========================================

Descrição:
Tabela responsável por armazenar dados de training_lessons.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- module_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `training_modules`
- title (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- slug (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- video_url (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- content (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- order (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `training_modules` via `module_id`)

----------------------------------------

========================================
TABELA: training_modules
========================================

Descrição:
Tabela responsável por armazenar dados de training_modules.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- title (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- slug (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- description (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- image (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- order (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- Nenhum relacionamento estrangeiro direto mapeado nesta tabela.

----------------------------------------

========================================
TABELA: training_plan_exercises
========================================

Descrição:
Tabela responsável por armazenar dados de training_plan_exercises.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- training_plan_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `training_plans`
- exercise_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `exercises_catalog`
- position (SMALLINT(5) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo SMALLINT(5) UNSIGNED
- notes (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `exercises_catalog` via `exercise_id`)
- 1:N (Pertence à tabela `training_plans` via `training_plan_id`)

----------------------------------------

========================================
TABELA: training_plans
========================================

Descrição:
Armazena os planos de treino criados pelos profissionais.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- professional_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- creator_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- name (VARCHAR(100), NOT NULL) → Dado do tipo VARCHAR(100)
- plan_label (VARCHAR(10), NULL DEFAULT NULL) → Dado do tipo VARCHAR(10)
- description (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- goal (VARCHAR(50), NULL DEFAULT NULL) → Dado do tipo VARCHAR(50)
- frequency (SMALLINT(5) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo SMALLINT(5) UNSIGNED
- days_of_week (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- difficulty (VARCHAR(20), NULL DEFAULT NULL) → Dado do tipo VARCHAR(20)
- student_profile (VARCHAR(30), NULL DEFAULT NULL) → Dado do tipo VARCHAR(30)
- split_type (VARCHAR(30), NULL DEFAULT NULL) → Dado do tipo VARCHAR(30)
- estimated_duration (SMALLINT(5) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo SMALLINT(5) UNSIGNED
- total_volume (DECIMAL(12,2), NOT NULL DEFAULT '0.00') → Dado do tipo DECIMAL(12,2)
- muscles_worked (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT
- is_active (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- status (VARCHAR(20), NOT NULL DEFAULT 'Rascunho') → Dado do tipo VARCHAR(20)
- is_template (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `creator_id`)
- 1:N (Pertence à tabela `users` via `professional_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: user_achievements
========================================

Descrição:
Tabela responsável por armazenar dados de user_achievements.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- badge_code (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- title (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- description (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- icon_url (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- unlocked_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: user_blocks
========================================

Descrição:
Tabela responsável por armazenar dados de user_blocks.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- blocker_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- blocked_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `blocked_id`)
- 1:N (Pertence à tabela `users` via `blocker_id`)

----------------------------------------

========================================
TABELA: user_consents
========================================

Descrição:
Tabela responsável por armazenar dados de user_consents.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- version (VARCHAR(255), NOT NULL DEFAULT '1.0') → Dado do tipo VARCHAR(255)
- consent_type (VARCHAR(50), NOT NULL) → Dado do tipo VARCHAR(50)
- ip_address (VARCHAR(45), NULL DEFAULT NULL) → Dado do tipo VARCHAR(45)
- user_agent (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora de criação do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: user_menu_preferences
========================================

Descrição:
Tabela responsável por armazenar dados de user_menu_preferences.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- menu_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `menus`
- visible (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `menus` via `menu_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: user_plans
========================================

Descrição:
Tabela responsável por armazenar dados de user_plans.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- plan_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `plans`
- start_date (DATETIME, NOT NULL) → Dado do tipo DATETIME
- end_date (DATETIME, NULL DEFAULT NULL) → Dado do tipo DATETIME
- status (VARCHAR(255), NOT NULL DEFAULT 'active') → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `plans` via `plan_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: user_profiles
========================================

Descrição:
Tabela responsável por armazenar dados de user_profiles.

Colunas:
- user_id (INT(10) UNSIGNED, PK, FK, NOT NULL) → Referência à tabela `users`
- birth_date (DATE, NULL DEFAULT NULL) → Dado do tipo DATE
- sex (CHAR(1), NOT NULL DEFAULT '') → Dado do tipo CHAR(1)
- height_cm (SMALLINT(5) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo SMALLINT(5) UNSIGNED
- target_weight_kg (DECIMAL(5,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(5,2)
- training_days_per_week (VARCHAR(24), NULL DEFAULT NULL) → Dado do tipo VARCHAR(24)
- activity_level (VARCHAR(32), NOT NULL DEFAULT 'moderate') → Dado do tipo VARCHAR(32)
- climate (VARCHAR(20), NOT NULL DEFAULT 'moderate') → Dado do tipo VARCHAR(20)
- goal (VARCHAR(16), NOT NULL DEFAULT 'maintain') → Dado do tipo VARCHAR(16)
- daily_calorie_target (INT(10) UNSIGNED, NULL DEFAULT NULL) → Dado do tipo INT(10) UNSIGNED
- protein_target_g (DECIMAL(6,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(6,2)
- carbs_target_g (DECIMAL(6,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(6,2)
- fat_target_g (DECIMAL(6,2), NULL DEFAULT NULL) → Dado do tipo DECIMAL(6,2)
- water_target_ml (SMALLINT(5) UNSIGNED, NULL DEFAULT '2000') → Dado do tipo SMALLINT(5) UNSIGNED
- is_water_target_auto (TINYINT(1), NOT NULL DEFAULT '1') → Dado do tipo TINYINT(1)
- updated_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora da última atualização do registro
- address (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- city (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- state (VARCHAR(2), NULL DEFAULT NULL) → Dado do tipo VARCHAR(2)
- has_disease (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- disease_details (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- has_injury (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- injury_details (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- uses_medication (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- medication_details (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- has_allergy (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- allergy_details (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- emergency_contact_name (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- emergency_contact_phone (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- profile_completed_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: user_roles
========================================

Descrição:
Tabela responsável por armazenar dados de user_roles.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- role_id (BIGINT(20) UNSIGNED, FK, NOT NULL) → Referência à tabela `roles`
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `roles` via `role_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: users
========================================

Descrição:
Armazena os usuários do sistema (alunos, profissionais, clínicas).

Colunas:
- id (INT(10) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- representative_id (INT(10) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `users`
- is_representative (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- uuid (CHAR(36), NULL DEFAULT NULL) → Dado do tipo CHAR(36)
- google_id (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- provider (VARCHAR(20), NULL DEFAULT NULL) → Dado do tipo VARCHAR(20)
- profile_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `roles`
- plan_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `plans`
- academy_company_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `academy_companies`
- user_type (VARCHAR(32), NULL DEFAULT NULL) → Dado do tipo VARCHAR(32)
- admission_date (DATE, NULL DEFAULT NULL) → Dado do tipo DATE
- link_type (VARCHAR(32), NULL DEFAULT NULL) → Dado do tipo VARCHAR(32)
- clinic_role (VARCHAR(64), NULL DEFAULT NULL) → Dado do tipo VARCHAR(64)
- sector (VARCHAR(64), NULL DEFAULT NULL) → Dado do tipo VARCHAR(64)
- status (VARCHAR(32), NOT NULL DEFAULT 'active') → Dado do tipo VARCHAR(32)
- activated_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- remember_profile (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- registration_approval_status (VARCHAR(24), NOT NULL DEFAULT 'approved') → Dado do tipo VARCHAR(24)
- registration_reviewed_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- registration_rejection_note (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- professional_code (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- qr_code_path (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- professional_plan_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `professional_plans`
- email (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- creditos (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- ai_credits (INT(11), NOT NULL DEFAULT '0') → Dado do tipo INT(11)
- avatar (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- phone (VARCHAR(25), NULL DEFAULT NULL) → Dado do tipo VARCHAR(25)
- whatsapp (VARCHAR(32), NULL DEFAULT NULL) → Dado do tipo VARCHAR(32)
- cpf (VARCHAR(11), NULL DEFAULT NULL) → Dado do tipo VARCHAR(11)
- cnpj (VARCHAR(20), NULL DEFAULT NULL) → Dado do tipo VARCHAR(20)
- email_verified_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- email_verified (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- email_verification_token (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- email_verification_expires_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- data_envio_confirmacao (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- tentativas_envio (INT(10) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo INT(10) UNSIGNED
- password_hash (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- name (VARCHAR(120), NOT NULL) → Dado do tipo VARCHAR(120)
- username (VARCHAR(50), NULL DEFAULT NULL) → Dado do tipo VARCHAR(50)
- is_premium (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- is_demo (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- demo_expires_at (DATETIME, NULL DEFAULT NULL) → Dado do tipo DATETIME
- is_admin (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- department (VARCHAR(50), NULL DEFAULT NULL) → Dado do tipo VARCHAR(50)
- premium_expires_at (DATETIME, NULL DEFAULT NULL) → Dado do tipo DATETIME
- onboarding_status (VARCHAR(32), NOT NULL DEFAULT 'pending') → Dado do tipo VARCHAR(32)
- perfil_paciente_completo (TINYINT(1), NOT NULL DEFAULT '0') → Dado do tipo TINYINT(1)
- profile_completion_percentage (TINYINT(3) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo TINYINT(3) UNSIGNED
- created_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro
- last_activity_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- health_score (INT(11), NOT NULL DEFAULT '100') → Dado do tipo INT(11)
- churn_risk (ENUM('LOW','MEDIUM','HIGH'), NOT NULL DEFAULT 'Low') → Dado do tipo ENUM('LOW','MEDIUM','HIGH')
- usage_stats (LONGTEXT, NULL DEFAULT NULL) → Dado do tipo LONGTEXT

Relacionamentos:
- 1:N (Pertence à tabela `academy_companies` via `academy_company_id`)
- 1:N (Pertence à tabela `plans` via `plan_id`)
- 1:N (Pertence à tabela `professional_plans` via `professional_plan_id`)
- 1:N (Pertence à tabela `users` via `representative_id`)
- 1:N (Pertence à tabela `roles` via `profile_id`)

----------------------------------------

========================================
TABELA: water_entries
========================================

Descrição:
Tabela responsável por armazenar dados de water_entries.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- entry_date (DATE, NOT NULL) → Dado do tipo DATE
- drank_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- amount_ml (SMALLINT(5) UNSIGNED, NOT NULL DEFAULT '0') → Dado do tipo SMALLINT(5) UNSIGNED
- source (VARCHAR(20), NOT NULL DEFAULT 'manual') → Dado do tipo VARCHAR(20)
- created_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora de criação do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: weight_entries
========================================

Descrição:
Tabela responsável por armazenar dados de weight_entries.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- weighed_at (DATE, NOT NULL) → Dado do tipo DATE
- weight_kg (DECIMAL(5,2), NOT NULL) → Dado do tipo DECIMAL(5,2)
- created_at (TIMESTAMP, NOT NULL DEFAULT 'current_timestamp()') → Data e hora de criação do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: withdrawal_requests
========================================

Descrição:
Tabela responsável por armazenar dados de withdrawal_requests.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- representative_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- amount (DECIMAL(15,2), NOT NULL) → Dado do tipo DECIMAL(15,2)
- pix_key (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- bank_info (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- status (ENUM('PENDENTE','APROVADO','PAGO','RECUSADO'), NOT NULL DEFAULT 'PENDENTE') → Dado do tipo ENUM('PENDENTE','APROVADO','PAGO','RECUSADO')
- admin_notes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- paid_at (TIMESTAMP, NULL DEFAULT NULL) → Dado do tipo TIMESTAMP
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `representative_id`)

----------------------------------------

========================================
TABELA: workout_sessions
========================================

Descrição:
Tabela responsável por armazenar dados de workout_sessions.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- session_date (DATE, NOT NULL) → Dado do tipo DATE
- rpe_score (INT(11), NULL DEFAULT NULL) → Dado do tipo INT(11)
- mood (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- notes (TEXT, NULL DEFAULT NULL) → Dado do tipo TEXT
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

========================================
TABELA: workout_target_areas
========================================

Descrição:
Tabela responsável por armazenar dados de workout_target_areas.

Colunas:
- id (BIGINT(20) UNSIGNED, PK, NOT NULL AUTO_INCREMENT) → Identificador único da tabela
- user_id (INT(10) UNSIGNED, FK, NOT NULL) → Referência à tabela `users`
- training_plan_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `training_plans`
- muscle_id (BIGINT(20) UNSIGNED, FK, NULL DEFAULT NULL) → Referência à tabela `muscles`
- target_area (VARCHAR(255), NOT NULL) → Dado do tipo VARCHAR(255)
- reference_photo_path (VARCHAR(255), NULL DEFAULT NULL) → Dado do tipo VARCHAR(255)
- created_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora de criação do registro
- updated_at (TIMESTAMP, NULL DEFAULT NULL) → Data e hora da última atualização do registro

Relacionamentos:
- 1:N (Pertence à tabela `muscles` via `muscle_id`)
- 1:N (Pertence à tabela `training_plans` via `training_plan_id`)
- 1:N (Pertence à tabela `users` via `user_id`)

----------------------------------------

