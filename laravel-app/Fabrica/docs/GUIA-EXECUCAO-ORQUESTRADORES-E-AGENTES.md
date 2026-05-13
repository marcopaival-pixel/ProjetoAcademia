# Guia de execução — orquestradores e agentes (Fabrica)

Este documento explica **como usar** o ecossistema no Cursor: qual orquestrador ativar, em que ordem pedir as etapas e qual arquivo JSON cada agente tende a produzir. A referência normativa de contratos e schemas continua em [AGENTS.md](../AGENTS.md).

---

## 1. Antes de começar (vale para as três fábricas)

| Conceito | O que fazer |
|----------|-------------|
| **Onde guardar saídas** | Uma pasta por execução: `outputs/<run_id>/`. Ex.: `outputs/2026-05-13-meu-produto-builder/`. |
| **Não misturar fábricas** | Builder, Audit e Evolution usam nomes de arquivo que colidem (`00-*`, `99-*`). Use `run_id` diferentes ou sufixos (`-builder`, `-audit`, `-evol`). |
| **Envelope JSON** | Todo artefato tem raiz com `schema_version`, `run_id`, `agent`, `artifact_type`, `created_at`, `depends_on`, `assumptions`, `open_questions` e `payload` (ver `schemas/_envelope.json`). |
| **Validar** | Na raiz do repo: `npm install` e `npm run validate -- outputs/<run_id>`. |
| **Ativar a regra certa** | Em **Cursor → Rules**, habilite a regra do orquestrador desejado **ou** escreva no chat algo como: “Atue como **System Builder Orchestrator** com a regra `sbo-00`.” |

**Dica de prompt genérico**: diga o **objetivo**, o **run_id** (ou peça para sugerir um), e se quer **só artefatos** ou também **código** (código no Builder só após gate; ver secção 2).

---

## 2. System Builder Orchestrator (criar sistema / especificação do zero)

**Regra**: `.cursor/rules/sbo-00-orchestrator.mdc`  
**Papel no chat**: *System Builder Orchestrator* (cite sub-regras `sbo-01` … `sbo-14` quando pedir uma etapa específica).

### 2.1 O que este orquestrador faz

Coordena a cadeia **negócio → UX → arquitetura → dados → backend → frontend → QA → segurança → performance → DevOps → documentação → revisão → release**, com artefatos numerados em `outputs/<run_id>/`.

### 2.2 Execução recomendada (passo a passo)

1. **Fase 0 (obrigatória antes de código de aplicação)**  
   Peça: inventário do repo (stack, pastas, integrações), reuso vs novo, riscos, milestones.  
   **Saídas**: `00-project-inventory.json` + esboço de `99-master-plan.json`.  
   **Parar** para **aprovação humana explícita** antes de implementar código.

2. **Fases 1–11**  
   Siga a ordem de [AGENTS.md](../AGENTS.md) (tabela “Fluxo do Orchestrator”) ou peça ao orquestrador para **executar a próxima fase** com `depends_on` apontando para artefatos anteriores.

3. **Fase 12 (consolidação)**  
   `13-code-review-report.json`, `14-release-checklist.json`, `99-master-plan.json` final.

4. **Implementação de código**  
   Só após gate: aprovação explícita no chat e/ou campos de aprovação coerentes no master plan (`payload.approval_gate`, `approved_at` / `approved_by` quando o plano exigir).

### 2.3 Agentes Builder — referência rápida

| Ordem | Agente | Regra Cursor | Artefato típico | `artifact_type` (sugestão) |
|------:|--------|---------------|------------------|----------------------------|
| 0 | Orquestrador (fase 0 + consolidação) | `sbo-00-orchestrator.mdc` | `00-project-inventory.json`, `99-master-plan.json` | `project-inventory`, `master-plan` |
| 1 | Product Manager | `sbo-01-product-manager.mdc` | `01-business-brief.json` | `business-brief` |
| 1 | Business Analyst | `sbo-02-business-analyst.mdc` | `02-prd.json` | `prd` |
| 2 | UX/UI | `sbo-03-ux-ui.mdc` | `03-ux-spec.json` | `ux-spec` |
| 3 | Solution Architect | `sbo-04-solution-architect.mdc` | `04-architecture.json` | `architecture` |
| 4 | Database Architect | `sbo-05-database-architect.mdc` | `05-database-design.json` | `database-design` |
| 5 | Backend | `sbo-06-backend.mdc` | `06-backend-spec.json` (+ opcional `06-api-spec.openapi.yaml`) | `backend-spec` |
| 6 | Frontend | `sbo-07-frontend.mdc` | `07-frontend-spec.json` | `frontend-spec` |
| 7 | QA | `sbo-08-qa.mdc` | `08-test-plan.json` | `test-plan` |
| 8 | Security | `sbo-09-security.mdc` | `09-security-review.json` | `security-review` |
| 9 | Performance | `sbo-10-performance.mdc` | `10-performance-review.json` | `performance-review` |
| 10 | DevOps | `sbo-11-devops.mdc` | `11-deployment-runbook.json` | `deployment-runbook` |
| 11 | Documentação | `sbo-12-documentation.mdc` | `12-docs-outline.json` | `docs-outline` |
| 12 | Code Review | `sbo-13-code-review.mdc` | `13-code-review-report.json` | `code-review-report` |
| 12 | Release | `sbo-14-release.mdc` | `14-release-checklist.json` | `release-checklist` |

**Exemplo de prompt (fase 0)**  
> System Builder Orchestrator, `run_id`: `2026-05-13-academia-builder`. Fase 0: inventário deste repositório, reuso vs novo, riscos e `00-project-inventory.json` + rascunho de `99-master-plan.json`. Não escrever código de aplicação.

**Exemplo de prompt (uma fase)**  
> Com `depends_on` em `outputs/2026-05-13-academia-builder/02-prd.json`, atue como **UX/UI Agent** (`sbo-03`) e gere `03-ux-spec.json`.

---

## 3. System Audit Orchestrator (auditar sistema existente)

**Regra**: `.cursor/rules/saf-00-audit-orchestrator.mdc`  
**Papel no chat**: *System Audit Orchestrator* (sub-regras `saf-01` … `saf-07`).

### 3.1 O que este orquestrador faz

Inspeciona **software já existente**, orquestra relatórios especializados e consolida tudo em um único relatório. **Não** implementa código de aplicação: entrega **evidência e plano** em JSON.

### 3.2 Execução recomendada

1. Defina `audit_run_id` (ex.: `2026-05-13-meu-sistema-audit`).
2. **Escopo primeiro**: `00-audit-scope.json` (alvo, metas, restrições, o que está fora de escopo). Schema: `schemas/audit-scope.json`.
3. **Especialistas** (em paralelo quando fizer sentido): `saf-01` … `saf-07` → arquivos `20`–`26`.
4. **Consolidação**: `99-audit-consolidated-report.json` (severidades, remoções, roadmap por fases, dívida técnica; opcional ligação ao Builder).

### 3.3 Agentes Audit — referência rápida

| Agente | Regra Cursor | Artefato | `artifact_type` (sugestão) |
|--------|---------------|----------|----------------------------|
| Orquestrador | `saf-00-audit-orchestrator.mdc` | `00-audit-scope.json`, `99-audit-consolidated-report.json` | `audit-scope`, `audit-consolidated-report` |
| Code Quality | `saf-01-code-quality.mdc` | `20-audit-code-quality.json` | `audit-code-quality` |
| Architecture | `saf-02-architecture.mdc` | `21-audit-architecture.json` | `audit-architecture` |
| Performance | `saf-03-performance.mdc` | `22-audit-performance.json` | `audit-performance` |
| Feature Overload | `saf-04-feature-overload.mdc` | `23-audit-feature-overload.json` | `audit-feature-overload` |
| Duplication | `saf-05-duplication.mdc` | `24-audit-duplication.json` | `audit-duplication` |
| Security | `saf-06-security.mdc` | `25-audit-security.json` | `audit-security` |
| Test Coverage | `saf-07-test-coverage.mdc` | `26-audit-test-coverage.json` | `audit-test-coverage` |

**Exemplo de prompt**  
> System Audit Orchestrator, `audit_run_id`: `2026-05-13-api-audit`. Gere `00-audit-scope.json` com alvo = este repo, foco em segurança e duplicação. Depois rode os especialistas e `99-audit-consolidated-report.json`.

**Depois da auditoria**  
Correções no código costumam ir para **Auto Evolution** (PR no repo alvo) ou para **Builder** (atualizar especificações + gate antes de codar), conforme política do time.

---

## 4. Auto Evolution Orchestrator (evoluir via Git / PR)

**Regra**: `.cursor/rules/aef-00-auto-evolution-orchestrator.mdc`  
**Papel no chat**: *Auto Evolution Orchestrator* (sub-regras `aef-01` … `aef-06`).

### 4.1 O que este orquestrador faz

Planeja e descreve mudanças **incrementais** no **repositório alvo**: branches, PRs, risco, verificação, rollback. Respeita `automation_mode` e `safety_policy` do escopo.

### 4.2 Execução recomendada

1. Defina `evolution_run_id` (ex.: `2026-05-13-api-evol`).
2. **Escopo primeiro**: `00-evolution-scope.json` — repo, branch base, prefixo de branch, `automation_mode`, metas, `safety_policy`, opcional `depends_on_audit`. Schema: `schemas/evolution-scope.json`.
3. **Especialistas**: `aef-01` … `aef-06` → `30`–`35`.
4. **Consolidação**: `99-evolution-run-report.json` (PRs sugeridos ou descritos, `risk_level`, `merge_recommendation`, checklist, rollback).

### 4.3 `automation_mode` (resumo)

| Modo | Comportamento esperado |
|------|-------------------------|
| `suggest-only` | Planos e análises em JSON; sem automatizar criação de PR. |
| `pr-low-risk-only` | Automatizar PR apenas para itens de baixo risco compatíveis com a política. |
| `pr-tiered-gates` | Vários PRs; revisão obrigatória conforme risco (`require_review_for_risk`). |

**Regras de ouro**: não push na branch protegida; mudanças pequenas e reversíveis; segurança/multi-tenant → cautela e `needs-human-review` quando não houver evidência forte.

### 4.4 Agentes Evolution — referência rápida

| Agente | Regra Cursor | Artefato | `artifact_type` (sugestão) |
|--------|---------------|----------|----------------------------|
| Orquestrador | `aef-00-auto-evolution-orchestrator.mdc` | `00-evolution-scope.json`, `99-evolution-run-report.json` | `evolution-scope`, `evolution-run-report` |
| Code Analysis | `aef-01-code-analysis.mdc` | `30-evolution-code-analysis.json` | `evolution-code-analysis` |
| Refactor | `aef-02-refactor.mdc` | `31-evolution-refactor.json` | `evolution-refactor` |
| Bug Fix | `aef-03-bugfix.mdc` | `32-evolution-bugfix.json` | `evolution-bugfix` |
| Test Generator | `aef-04-test-generator.mdc` | `33-evolution-test-generation.json` | `evolution-test-generation` |
| Security Patch | `aef-05-security-patch.mdc` | `34-evolution-security-patch.json` | `evolution-security-patch` |
| Performance | `aef-06-performance.mdc` | `35-evolution-performance.json` | `evolution-performance` |

**Exemplo de prompt**  
> Auto Evolution Orchestrator, `evolution_run_id`: `2026-05-13-api-evol`. Gere `00-evolution-scope.json` com `automation_mode`: `suggest-only`, repo alvo = [URL ou pasta], branch base = `main`. Opcional: `depends_on_audit` = `outputs/2026-05-13-api-audit/99-audit-consolidated-report.json`. Depois consolide em `99-evolution-run-report.json`.

---

## 5. Como escolher a fábrica (decisão rápida)

| Intenção | Use |
|----------|-----|
| Especificar um produto/SaaS do zero, PRD, arquitetura, planos de teste/deploy | **System Builder** (`sbo-00`) |
| Diagnosticar código/sistema já existente sem implementar | **System Audit** (`saf-00`) |
| Planejar ou executar melhorias em Git (PRs, patches incrementais) | **Auto Evolution** (`aef-00`) |

---

## 6. Checklist mínimo antes de encerrar um run

- [ ] Todos os JSON gerados estão sob **um** `run_id` coerente.
- [ ] `depends_on` aponta para os artefatos que realmente embasaram cada decisão.
- [ ] Dúvidas ficaram em `open_questions` (não “escondidas” no payload).
- [ ] `npm run validate -- outputs/<run_id>` passou (ajustar até validar).

Para detalhes de cada schema, abra o arquivo correspondente em `schemas/`.
