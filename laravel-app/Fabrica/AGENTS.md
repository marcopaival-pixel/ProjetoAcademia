# System Builder — Ecossistema de agentes (Fabrica)

Este repositório define o **System Builder Orchestrator**, o **System Audit Orchestrator** e o **Auto Evolution Orchestrator**, com agentes especializados para **conceber**, **auditar** e **evoluir** software com saídas **estruturadas**, **versionadas** e **reutilizáveis**.

## Princípios

1. **Gate de aprovação**: nenhuma implementação de código antes de análise do que já existe, plano detalhado e **confirmação explícita** do humano.
2. **Um artefato, um schema**: cada agente preenche um payload que valida contra `schemas/*.json`.
3. **Envelope comum**: todo arquivo gerado segue `schemas/_envelope.json` (campos obrigatórios na raiz; `version` do artefato é opcional e distinto de `schema_version`).
4. **Rastreabilidade**: `depends_on` referencia artefatos anteriores; prefira caminhos sob `outputs/<run_id>/…` ou IDs lógicos estáveis.

### Uma pasta por fábrica

Não misture na **mesma pasta** artefatos do **Builder**, da **Audit** e da **Auto Evolution** (colisão de nomes, por exemplo `00-*.json` e `99-*.json`). Use `run_id` distintos ou sufixos claros (`…-builder`, `…-audit`, `…-evol`).

### Matriz de gates (implementação)

| Caminho | O que é “implementação” | Gate humano típico |
|--------|-------------------------|---------------------|
| **System Builder** (SaaS do zero neste fluxo) | Alterar **código de aplicação** conforme plano | Aprovação explícita do plano / `99-master-plan.json` e regra `sbo-00-orchestrator.mdc`. |
| **System Audit** | **Não** implementa código: só diagnóstico e plano em JSON | Revisão dos achados; decisão de **o que** entra no backlog ou na Evolution. |
| **Auto Evolution** | **Commits e PRs** no **repositório alvo** do produto | Revisão de PR, branch protection, CI e `safety_policy` em `00-evolution-scope.json` (`aef-00`). |

Correções sugeridas pela **Audit** podem virar trabalho do **Builder** (atualizar PRD/arquitetura e só então codar com gate do Builder) **ou** trabalho da **Auto Evolution** (PR incremental no repo do produto), conforme política do time. Os dois não se excluem: Evolution mexe no código; Builder mantém especificação alinhada após merges relevantes.

No `99-master-plan.json`, o campo `artifact_index` pode incluir caminhos para runs da **Audit** ou da **Auto Evolution** (ex.: `outputs/<audit_run_id>/99-audit-consolidated-report.json`, `outputs/<evolution_run_id>/99-evolution-run-report.json`) quando o ciclo de produto exigir rastreio formal desses marcos.

## Layout de saídas sugerido

```
outputs/
  <run_id>/
    00-project-inventory.json
    01-business-brief.json
    02-prd.json
    03-ux-spec.json
    04-architecture.json
    05-database-design.json
    06-backend-spec.json        # ver schemas/backend-spec.json
    06-api-spec.openapi.yaml   # opcional; referenciar em openapi_path
    07-frontend-spec.json
    08-test-plan.json
    09-security-review.json
    10-performance-review.json
    11-deployment-runbook.json
    12-docs-outline.json
    13-code-review-report.json
    14-release-checklist.json
    99-master-plan.json
```

Use `run_id` = data + slug (ex.: `2026-05-13-acme-crm`).

## Fluxo do Orchestrator (fases 0 a 12 — 13 fases)

| # | Etapa | Agente principal | Artefato |
|---|--------|------------------|----------|
| 0 | Inventário e plano (sem implementar) | Orchestrator + BA + Architect (visão) | `00-project-inventory.json`, rascunho de `99-master-plan.json` |
| 1 | Ideia → negócio | Product Manager + Business Analyst | `01-business-brief.json`, `02-prd.json` |
| 2 | UX/UI | UX/UI Agent | `03-ux-spec.json` |
| 3 | Arquitetura | Solution Architect | `04-architecture.json` |
| 4 | Dados | Database Architect | `05-database-design.json` |
| 5 | Backend | Backend Agent | `06-backend-spec.json` (+ OpenAPI opcional em `06-api-spec.openapi.yaml`) |
| 6 | Frontend | Frontend Agent | `07-frontend-spec.json` |
| 7 | QA | QA Agent | `08-test-plan.json` |
| 8 | Segurança | Security Agent | `09-security-review.json` |
| 9 | Performance | Performance Agent | `10-performance-review.json` |
| 10 | DevOps / deploy | DevOps Agent | `11-deployment-runbook.json` |
| 11 | Documentação | Documentation Agent | `12-docs-outline.json` |
| 12 | Consolidação | Orchestrator + Code Review + Release | `13-code-review-report.json`, `14-release-checklist.json`, `99-master-plan.json` |

**Implementação de código** só após o gate: revisão humana do `99-master-plan.json` e dos artefatos críticos (PRD, arquitetura, dados, segurança).

**Retroalimentação**: descobertas em etapas tardias (ex.: arquitetura, segurança) podem exigir revisão de artefatos anteriores; atualize `depends_on`, `open_questions` e o `artifact_index` do master plan (status `stale` quando aplicável).

**Revisão de código**: o artefato `13-code-review-report.json` representa um marco de consolidação; em fluxos ágeis, revisões por PR continuam fora desse arquivo, mas devem respeitar o mesmo PRD e contratos.

## `artifact_type` canônico (valor sugerido em `artifact_type`)

| Arquivo em `outputs/<run_id>/` | `artifact_type` sugerido |
|-------------------------------|---------------------------|
| `00-project-inventory.json` | `project-inventory` |
| `01-business-brief.json` | `business-brief` |
| `02-prd.json` | `prd` |
| `03-ux-spec.json` | `ux-spec` |
| `04-architecture.json` | `architecture` |
| `05-database-design.json` | `database-design` |
| `06-backend-spec.json` | `backend-spec` |
| `07-frontend-spec.json` | `frontend-spec` |
| `08-test-plan.json` | `test-plan` |
| `09-security-review.json` | `security-review` |
| `10-performance-review.json` | `performance-review` |
| `11-deployment-runbook.json` | `deployment-runbook` |
| `12-docs-outline.json` | `docs-outline` |
| `13-code-review-report.json` | `code-review-report` |
| `14-release-checklist.json` | `release-checklist` |
| `99-master-plan.json` | `master-plan` |

---

## System Audit Factory (auditoria de sistemas existentes)

Complementa o **System Builder**: enquanto o Builder **concebe** o produto (artefatos `00`–`14`, `99-master-plan`), a **Audit Factory** **inspeciona** código, arquitetura e operação de um sistema já implantado, produzindo um **relatório consolidado** e um **roadmap de refatoração** alinhado à segurança de produção.

### Princípios da auditoria

1. **Simplificar antes de expandir**: priorize remoção, consolidação e redução de superfície.
2. **Risco transparente**: explícito o que é evidência vs. hipótese; use `production_safety` / notas de risco nos achados.
3. **Refatoração gradual**: fases com critérios de saída; evitar “big bang” sem mitigação.
4. **Reuso**: ao eliminar duplicação, aponte **canônico** único (módulo, API, biblioteca).

### Layout de saídas sugerido (auditoria)

Use um `audit_run_id` dedicado (ex.: `2026-05-13-acme-audit`) em `outputs/<audit_run_id>/`:

```
outputs/
  <audit_run_id>/
    00-audit-scope.json
    20-audit-code-quality.json
    21-audit-architecture.json
    22-audit-performance.json
    23-audit-feature-overload.json
    24-audit-duplication.json
    25-audit-security.json
    26-audit-test-coverage.json
    99-audit-consolidated-report.json
```

**Fluxo**: escopo → execução paralela dos sete agentes especializados → `99-audit-consolidated-report.json` (visão geral, severidades, remoções, features redundantes, melhorias, plano por fases, dívida técnica, opcional `builder_integration`).

### `artifact_type` canônico (auditoria)

| Arquivo em `outputs/<audit_run_id>/` | `artifact_type` sugerido |
|--------------------------------------|---------------------------|
| `00-audit-scope.json` | `audit-scope` |
| `20-audit-code-quality.json` | `audit-code-quality` |
| `21-audit-architecture.json` | `audit-architecture` |
| `22-audit-performance.json` | `audit-performance` |
| `23-audit-feature-overload.json` | `audit-feature-overload` |
| `24-audit-duplication.json` | `audit-duplication` |
| `25-audit-security.json` | `audit-security` |
| `26-audit-test-coverage.json` | `audit-test-coverage` |
| `99-audit-consolidated-report.json` | `audit-consolidated-report` |

### Ciclo com o System Builder

1. **Builder** gera/atualiza especificações (`02-prd`, `04-architecture`, `08-test-plan`, `09-security-review`, etc.).
2. **Audit** analisa o sistema real e produz `99-audit-consolidated-report.json`.
3. **Builder** incorpora mudanças via revisão de artefatos e **gate humano** antes de implementação.
4. Repetir conforme evolução contínua do produto.

---

## Auto Evolution Factory (evolução incremental e Pull Requests)

Transforma achados (incluindo os da **Audit Factory**) em **mudanças de código** no repositório alvo, via **branches** e **Pull Requests** documentados, com políticas de **risco**, **verificação** e **rollback**. Complementa o **Builder** (especificação) e o **Audit** (diagnóstico): a Auto Evolution foca na **execução segura e incremental** no Git.

### Princípios da evolução automática

1. **Incremental**: PRs pequenos e reversíveis; evitar reescrita grande sem milestones.
2. **Trunk git saudável**: sem push direto em branch protegida; usar prefixo de branch configurado no escopo.
3. **Risco explícito**: cada item e PR classifica `risk_level`; `merge_recommendation` reflete CI, cobertura e revisão humana.
4. **Compatível com Audit/Builder**: opcionalmente `depends_on_audit`; após merge, atualizar artefatos do Builder quando o comportamento do produto mudar.

### Layout de saídas sugerido (evolução)

Use um `evolution_run_id` dedicado (ex.: `2026-05-13-acme-evol`) em `outputs/<evolution_run_id>/`:

```
outputs/
  <evolution_run_id>/
    00-evolution-scope.json
    30-evolution-code-analysis.json
    31-evolution-refactor.json
    32-evolution-bugfix.json
    33-evolution-test-generation.json
    34-evolution-security-patch.json
    35-evolution-performance.json
    99-evolution-run-report.json
```

**Fluxo sugerido**: escopo e política de automação → agentes `aef-01`–`aef-06` (paralelo quando possível) geram `30`–`35` → `99-evolution-run-report.json` consolida **PRs** (título, descrição, risco, recomendação de merge), verificação e rollback.

### `artifact_type` canônico (evolução)

| Arquivo em `outputs/<evolution_run_id>/` | `artifact_type` sugerido |
|------------------------------------------|---------------------------|
| `00-evolution-scope.json` | `evolution-scope` |
| `30-evolution-code-analysis.json` | `evolution-code-analysis` |
| `31-evolution-refactor.json` | `evolution-refactor` |
| `32-evolution-bugfix.json` | `evolution-bugfix` |
| `33-evolution-test-generation.json` | `evolution-test-generation` |
| `34-evolution-security-patch.json` | `evolution-security-patch` |
| `35-evolution-performance.json` | `evolution-performance` |
| `99-evolution-run-report.json` | `evolution-run-report` |

### Ciclo com Audit e Builder

1. **Audit** prioriza problemas e simplificações (`99-audit-consolidated-report.json`).
2. **Auto Evolution** implementa o acordado em PRs (`99-evolution-run-report.json`).
3. **Builder** atualiza especificações e plano mestre quando o produto mudar de contrato ou escopo.
4. **Audit** pode rodar novamente no resultado para fechar o ciclo.

## Validação de artefatos

Valide JSON contra `schemas/_envelope.json` (raiz) e contra o schema do `payload` correspondente (ex.: `schemas/prd.json` para `02-prd.json`).

Neste repositório:

```bash
npm install
npm run validate -- outputs/<run_id>
```

O script `scripts/validate-artifacts.mjs` usa [AJV](https://ajv.js.org/) e valida apenas arquivos cujo nome corresponde ao layout oficial do **Builder**, da **Audit Factory** ou da **Auto Evolution Factory** (demais `.json` na pasta são ignorados com aviso). Exemplos mínimos: `outputs/_smoke/` (Builder), `outputs/_smoke-audit/` (Audit), `outputs/_smoke-evolution/` (Auto Evolution).

Também é possível validar no editor com extensão JSON Schema ou integrar o mesmo comando em CI no repositório do produto.

## Como invocar no Cursor

1. Ative a regra **System Builder Orchestrator** (`.cursor/rules/sbo-00-orchestrator.mdc`) ou mencione explicitamente o papel *System Builder Orchestrator*.
2. Para um passo específico, ative ou cite a regra do agente (ex.: `sbo-02-business-analyst.mdc`) ou peça ao orquestrador para atuar com aquele sub-papel.
3. Peça ao orquestrador para **iniciar a fase 0** com a ideia do produto.
4. Aprove ou ajuste o plano; só então autorize as fases seguintes.

**System Audit Factory**: ative `.cursor/rules/saf-00-audit-orchestrator.mdc` ou mencione *System Audit Orchestrator*; use `saf-01`–`saf-07`. O relatório final fica em `99-audit-consolidated-report.json`. A Audit **não** substitui revisão de PR: ela produz evidência e recomendações (ver **Matriz de gates** acima).

**Auto Evolution Factory**: ative `.cursor/rules/aef-00-auto-evolution-orchestrator.mdc` ou mencione *Auto Evolution Orchestrator*; use `aef-01`–`aef-06`. O consolidado fica em `99-evolution-run-report.json`. Mudanças são no **repositório alvo** via Git/PR; obedeça `automation_mode` e `safety_policy` do `00-evolution-scope.json`.

## Reutilização de componentes

Na fase 0 o Orchestrator deve:

- Mapear stacks, pastas, pacotes, integrações e padrões já adotados.
- Listar o que será **reaproveitado** vs **criado do zero**.
- Registrar riscos de acoplamento e dívidas já existentes.

## Contrato de envelope (todos os JSON)

Todo artefato deve ser um objeto com:

- `schema_version`, `run_id`, `agent`, `artifact_type`, `created_at`
- `version` (opcional): revisão do artefato (ex.: `1.0.0` ou `draft-2`)
- `depends_on`: array de strings
- `assumptions`, `open_questions`: arrays de strings
- `payload`: objeto conforme schema do tipo

Detalhes em `schemas/_envelope.json`.
