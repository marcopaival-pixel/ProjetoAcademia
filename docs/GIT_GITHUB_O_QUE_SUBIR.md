# Git / GitHub — O que subir, o que não subir e o que já está no repo

**Projeto:** NexShape / ProjetoAcademia  
**Data:** 19 de junho de 2026  
**Objetivo:** Lista operacional do que deve estar no GitHub para o sistema funcionar, o que nunca versionar, e o que pode existir no repositório sem ser necessário para produção.

> **Nota:** Este documento reflete o estado do repositório local e do `git status` observado em junho/2026. Revise após commits grandes ou mudança de arquitetura.

---

## Resumo em uma frase

**Subir:** `laravel-app` (código + migrações + testes + exemplos `.env`) + `android-app` (código + documentação) + workflows de CI + documentação de deploy/monitoramento.  
**Não subir:** segredos, `vendor`, `node_modules`, `public/build`, keystores e Firebase.  
**No GitHub mas dispensável para rodar:** `php-app`, `web`, `Fabrica`, `governanca-ia`, prompts e auditorias em `docs/*.txt`.

---

## 1. O que precisa estar no GitHub (sistema funcionar)

### 1.1 Núcleo obrigatório — backend web (Laravel)

| Pasta / ficheiros | Porquê |
|-------------------|--------|
| `laravel-app/app/` | Código da aplicação (controllers, models, services, middleware) |
| `laravel-app/routes/` | Rotas web + API v1 |
| `laravel-app/config/` | Configuração Laravel |
| `laravel-app/database/migrations/` | Esquema da base de dados |
| `laravel-app/database/seeders/` | Dados iniciais (RBAC, homolog bootstrap, etc.) |
| `laravel-app/resources/` | Views Blade, JS/CSS fonte |
| `laravel-app/public/` (exceto `build/` gerado) | `index.php`, `.htaccess`, assets estáticos |
| `laravel-app/bootstrap/` | Bootstrap da aplicação |
| `laravel-app/composer.json` + `composer.lock` | Dependências PHP |
| `laravel-app/package.json` + lockfile | Build Vite do front |
| `laravel-app/vite.config.js` (e ficheiros Vite relacionados) | Compilação do front no deploy |
| `laravel-app/.env.example` | Modelo de ambiente (dev) |
| `laravel-app/.env.example.production` | Modelo de ambiente (produção) |
| `laravel-app/phpunit.xml`, `phpstan.neon.dist`, `phpstan-baseline.neon` | Testes e análise estática |
| `laravel-app/tests/` | Suíte automatizada (PHPUnit) |
| `laravel-app/VERSION` (se existir) | Versão para deploy/checklist |
| `laravel-app/artisan` | CLI Laravel |

**Correções recentes (junho/2026) — incluir no commit se ainda não versionadas:**

- `laravel-app/app/Services/PanelAccessService.php`
- `laravel-app/app/Services/MenuAccessService.php`
- `laravel-app/app/Http/Middleware/CheckRole.php`
- `laravel-app/routes/patient.php`
- `laravel-app/app/Services/CommissionClawbackService.php`
- `laravel-app/app/Services/AI/OrchestratorService.php`
- `laravel-app/app/Services/Operations/SystemHealthService.php`
- `laravel-app/tests/Feature/CommissionClawbackServiceTest.php` (e demais testes alterados)

### 1.2 API mobile + app Android

| Pasta / ficheiros | Porquê |
|-------------------|--------|
| `android-app/app/` | Código Kotlin (Compose, API v1, FCM, etc.) |
| `android-app/build.gradle.kts`, `settings.gradle.kts` | Configuração Gradle |
| `android-app/gradle/`, `gradlew`, `gradlew.bat` | Wrapper Gradle |
| `android-app/gradle.properties`, `gradle/libs.versions.toml` | Versões e propriedades |
| `android-app/keystore.properties.example` | Exemplo de assinatura (não o ficheiro real) |
| `android-app/.gitignore` | Exclusão de segredos Android |
| `android-app/README.md` | Documentação do app |
| `android-app/RELEASE_BUILD.md`, `RELEASE_CANDIDATE.md` | Build e RC |
| `android-app/MANUAL_TEST_PLAN.md` | Testes manuais |
| `android-app/PLAY_STORE_*.md`, `PRIVACY_POLICY_APP.md` | Play Store |

### 1.3 CI/CD e scripts de deploy

| Ficheiros | Porquê |
|-----------|--------|
| `.github/workflows/laravel-tests.yml` | PHPUnit + PHPStan + Pint na raiz |
| `.github/workflows/deploy-nexshape.yml` | Validação pré-deploy + artefacto |
| `.github/workflows/android-ci.yml` | `assembleDebug` + lint Android |
| `laravel-app/.github/workflows/laravel-ci.yml` | CI no subprojeto Laravel |
| `laravel-app/.github/PULL_REQUEST_TEMPLATE.md` | Disciplina de PR |
| `scripts/prepare-deploy-package.ps1` (raiz, se existir) | Pacote ZIP de deploy |
| `laravel-app/scripts/staging-release.ps1` | Release em homologação |
| `laravel-app/scripts/setup-dev.ps1` | Setup dev local |
| `laravel-app/deploy.bat`, `optimize_server.sh` (se existirem) | Deploy/otimização servidor |

### 1.4 Documentação operacional (recomendado no Git)

| Ficheiro | Porquê |
|----------|--------|
| `docs/DEPLOY_NEXSHAPE.md` | Runbook de deploy |
| `docs/MONITORAMENTO.md` | Operação, health, Sentry, filas |
| `laravel-app/docs/GO_LIVE_CHECKLIST.md` | Fases A–D go-live |
| `laravel-app/docs/DEPLOY.md` | Fluxo de branches e painel deploy |
| `docs/supervisor-nexshape.conf.example` | Workers e scheduler |
| `docs/AUDITORIA_360_2026-05-21.md` | Referência de readiness (opcional mas útil) |
| `docs/AUDITORIA_COMPLETA_NEXSHAPE_2026-06-17.md` | Riscos financeiros/segurança (opcional) |

### 1.5 Governança e IDE (não bloqueia runtime, mas recomendado)

| Ficheiros | Porquê |
|-----------|--------|
| `.gitignore` (raiz, `laravel-app/`, `android-app/`) | Proteção de segredos e artefactos |
| `AGENTS.md`, `GEMINI.md` | Governança IA |
| `MANUAL_AGENTE_GOVERNANCA.md`, `MANUAL_PEDIDOS_IA.md` | Manuais |
| `.cursor/rules/*.mdc` | Regras Cursor (se a equipa usa) |
| `.github/copilot-instructions.md` | Copilot |

---

## 2. O que NUNCA deve subir para o GitHub

| Item | Motivo |
|------|--------|
| `laravel-app/.env` | Segredos reais (BD, MP, OpenAI, etc.) |
| `laravel-app/.env.production` **com valores preenchidos** | Segredos de produção |
| `laravel-app/vendor/` | Gerado com `composer install` |
| `laravel-app/node_modules/` | Gerado com `npm ci` |
| `laravel-app/public/build/` | Gerado com `npm run build` — enviar no **deploy**, não no Git |
| `laravel-app/public/hot` | Dev Vite |
| `laravel-app/storage/logs/`, cache, sessões, `framework/views` compiladas | Runtime local |
| `laravel-app/.phpunit.result.cache`, `storage/phpstan/` | Cache de ferramentas |
| `laravel-app/auth.json` com tokens | Credenciais Composer |
| `android-app/google-services.json` | Config Firebase (segredo do projeto) |
| `android-app/keystore.properties` | Senhas do keystore |
| `android-app/*.jks`, `*.keystore` | Chaves de assinatura Play Store |
| `android-app/build/`, `*.apk`, `*.aab` | Artefactos de build |
| `docs/env.txt` | Cópia de ambiente com segredos |
| `docs/CHAVE_GOOGLE.txt` | Chaves Google |
| `docs/configuração_provedor.txt`, `docs/configuracao_provedor.txt` | Credenciais provedor |
| `laravel-app/ - Copia.env`, `.env copy.example` | Cópias de ambiente |
| `brain/`, `laravel-app/brain/`, `**/scratch/` | Rascunhos / artefactos IA |
| `.ftpquota` | Quota FTP do hosting |
| `php-app/public/debug.php` | Debug legado |
| Ficheiros com passwords, tokens, CPF/email reais em `docs/` | PII e segredos |

**Já protegido pelo `.gitignore` (confirmar antes de cada push):**

- Raiz: `docs/CHAVE_GOOGLE.txt`, `brain/`, `laravel-app/storage/phpstan/`
- `laravel-app/`: `.env`, `vendor/`, `node_modules/`, `public/build`
- `android-app/`: `google-services.json`, `keystore.properties`, `*.jks`

---

## 3. O que pode estar no GitHub mas NÃO é necessário para o sistema funcionar

Estes itens podem permanecer no repositório como **arquivo, documentação ou legado**, mas **não são necessários** para clonar, fazer deploy e operar o NexShape em produção.

| Item | Nota |
|------|------|
| **`php-app/`** | App PHP legado. Auditoria: webhook MP retorna **410 em produção**. Não usar em deploy. |
| **`web/`** | Template React/Vite isolado. O front ativo está em `laravel-app` (Vite integrado). |
| **`governanca-ia/`** | Pacote copiável de governança; duplica ficheiros já na raiz após `aplicar-na-raiz.ps1`. |
| **`Agentes/`** (raiz) | Documentação de agentes (`release-agent.md`, etc.), não é runtime. |
| **`laravel-app/Fabrica/`** | Fábrica de agentes Cursor (auditoria/evolução), não é a app. |
| **`laravel-app/agentesprd/`** | Especificações PRD de agentes. |
| **`docs/ETAPA *.txt`** | Prompts de auditoria IA (ETAPA 12–14, etc.). |
| **`docs/REALIZAR UMA AUDITORIA COMPLETA.txt`** | Prompt de auditoria. |
| **`docs/Ajuste_Agente_bug.txt`**, **`docs/Melhore o chatbot...txt`** | Notas/pedidos pontuais. |
| **`docs/AUDITORIA_*.md`** | Relatórios históricos — úteis como referência, não para executar o sistema. |
| **`docs/simulacao-custo-ia-planos.csv`** | Simulação interna. |
| **`bug-hunter.txt`** | Notas locais. |
| **`gerar_pdf_funcionalidades.py`** | Script utilitário pontual. |
| **`templates/governanca/`** | Templates do pacote de governança. |
| **`Imagens/`** | Só necessário se forem assets oficiais do produto; caso contrário, ruído. |
| **`package-lock.json` na raiz** | Provável órfão — o Node do projeto está em `laravel-app/`. |
| **`README.md` na raiz** | Atualmente descreve o pacote `governanca-ia`, não o projeto Academia (confuso, mas não afeta runtime). |
| **`test_chatbot_api.sh`**, **`test_db.php`**, **`setup_omni_standalone.php`** | Scripts de teste/dev local. |
| **`laravel-app/Fabrica/.github/workflows/validate.yml`** | CI da fábrica de agentes, não da app. |

---

## 4. O que parece estar local mas ainda NÃO no GitHub (prioridade de push)

Com base no `git status` de junho/2026, **grande parte do código estava `??` (untracked)** — ou seja, **ainda não commitada**.

### 4.1 Crítico (produto atual)

- Quase todo o diretório **`android-app/`** (app v1.8.1, API v1, FCM, ProGuard).
- Dezenas de ficheiros em **`laravel-app/app/`**:
  - API v1 (`Http/Controllers/Api/V1/*`)
  - Profissional, alertas, agenda, evolução, checkout
  - Push (`push/`), sync offline, client errors
  - Admin: deploy, observability, AI governance
- **`laravel-app/database/migrations/`** novas (2026).
- **`laravel-app/app/Console/Commands/`** novos (audit, release, finance, etc.).
- **`laravel-app/tests/Feature/ApiV1*`**, `AuditRemediationTest`, `ReleaseSmokeTest`, etc.
- Workflows **`.github/workflows/android-ci.yml`**, **`deploy-nexshape.yml`** (se ainda untracked).

### 4.2 Importante (operação e qualidade)

- `docs/DEPLOY_NEXSHAPE.md`
- Alterações em `docs/MONITORAMENTO.md`
- `laravel-app/docs/GO_LIVE_CHECKLIST.md`
- `laravel-app/database/seeders/DeployHomologBootstrapSeeder.php`
- `laravel-app/app/Models/DeployRelease.php` e controladores/policies de deploy

### 4.3 Modificado localmente (`M`) — precisa commit + push

Vários ficheiros **já versionados** tinham alterações locais sem commit, por exemplo:

- Controllers de auth, chat, pagamentos, nutrição, assessment
- `laravel-app/.env.example`, `.gitignore`
- `docs/MONITORAMENTO.md`, `docs/supervisor-nexshape.conf.example`
- Serviços e middleware de segurança/multi-tenant

**Ação:** `git status` → commit de tudo o que for código/docs operacionais → push.

---

## 5. O que o GitHub NÃO substitui (configurar no servidor)

Mesmo com o repositório completo no GitHub, **produção não funciona** só com `git clone`. É obrigatório no servidor:

| Passo | Detalhe |
|-------|---------|
| 1. `.env` real | Copiar de `laravel-app/.env.example.production` e preencher |
| 2. Dependências PHP | `composer install --no-dev --optimize-autoloader` |
| 3. Build front | `npm ci && npm run build` → gera `public/build/` |
| 4. Laravel | `php artisan key:generate`, `migrate --force`, `storage:link` |
| 5. Cache produção | `config:cache`, `route:cache`, `view:cache` |
| 6. Infra | MySQL, Redis (recomendado), Apache com `public/` como document root |
| 7. Filas | Supervisor + `schedule:run` (ver `docs/supervisor-nexshape.conf.example`) |
| 8. Secrets | `MP_ACCESS_TOKEN`, `MP_WEBHOOK_SECRET`, `APP_PUBLIC_URL`, SMTP, Sentry, `FCM_SERVER_KEY` |
| 9. Android (se Play Store) | `google-services.json` + keystore **fora do Git**, no ambiente de build |

---

## 6. Checklist rápido antes de cada push

- [ ] `git status` — nenhum `.env`, keystore ou `google-services.json` staged
- [ ] `vendor/`, `node_modules/`, `public/build/` **não** aparecem no staging
- [ ] `composer test` verde (ou CI passará no GitHub)
- [ ] Migrações novas incluídas em `database/migrations/`
- [ ] `.env.example` e `.env.example.production` atualizados se mudou contrato de config
- [ ] Sem passwords/tokens em `docs/*.txt` novos

---

## 7. Estrutura mínima após clone (visão deploy)

```
ProjetoAcademia/
├── laravel-app/          ← APP PRINCIPAL (obrigatório)
├── android-app/          ← App mobile (obrigatório se usar Play Store)
├── .github/workflows/    ← CI (recomendado)
├── docs/                 ← Runbooks (recomendado)
├── scripts/              ← Deploy (recomendado)
├── AGENTS.md             ← Governança (opcional)
├── php-app/              ← LEGADO — não deployar
├── web/                  ← Não usado em produção
└── governanca-ia/        ← Pacote cópia — não necessário no servidor
```

---

## 8. Referências no repositório

- Deploy: `docs/DEPLOY_NEXSHAPE.md`
- Go-live: `laravel-app/docs/GO_LIVE_CHECKLIST.md`
- Android release: `android-app/RELEASE_BUILD.md`, `RELEASE_CANDIDATE.md`
- Auditoria readiness: `docs/AUDITORIA_360_2026-05-21.md`
- `.gitignore` raiz: `/.gitignore`
- `.gitignore` Laravel: `laravel-app/.gitignore`
- `.gitignore` Android: `android-app/.gitignore`

---

*Documento gerado para apoio à equipa. Não substitui revisão manual de `git status` e política de segredos da organização.*
