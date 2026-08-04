# Subagentes — Bug Surgeon

| Subagente | Tipo Cursor | Fase | Responsabilidade |
|-----------|-------------|------|------------------|
| Bug Intake | (agente principal) | RECEIVED | Organizar contexto do incidente |
| Log Investigator | `explore` | INVESTIGATING | laravel.log, system_errors, filas |
| Root Cause Analyzer | `explore` / principal | INVESTIGATING → DIAGNOSIS_READY | Stack trace, sintoma vs causa |
| Impact Analyzer | `explore` | IMPACT_ANALYZED | Rotas, perfis, tenancy, dependências |
| Patch Planner | principal | PATCH_PROPOSED | Diff mínimo, sem aplicar |
| Human Approval Gate | humano | WAITING_APPROVAL | APROVAR / REJEITAR / NOVA ANÁLISE |
| Patch Executor | principal | APPROVED → PATCH_APPLIED | Aplica diff autorizado |
| Test Agent | `shell` | TESTING | php artisan test direcionado |
| Security Auditor | `security-review` | pós-PATCH_APPLIED | Auth, tenancy, LGPD |
| Deployment Guardian | humano + runbook | staging/prod | docs/DEPLOY_NEXSHAPE.md |

## Delegação read-only

Durante `INVESTIGATING`–`PATCH_PROPOSED`, subagentes **explore** devem receber instrução explícita:

> Modo somente leitura. Não editar arquivos. Retornar evidências numeradas.

## Exemplo de prompt para Log Investigator

```
Incidente BUG-2026-00871. Usuário 145, rota /aluno/avaliacoes, ~22:10.
Investigue somente leitura:
1. backend/storage/logs/laravel.log (janela 22:00-22:30)
2. SystemError model e registros correlatos
3. Rota → Controller → Service
Retorne: stack trace, arquivo:linha, hipótese de causa, evidências.
```

## Integração com admin NexShape

Painel existente: **Admin → Logs de Erros** (`system_errors`).

Campos úteis: `message`, `stack_trace`, `url`, `method`, `user_id`, `payload`.

Painel futuro (backend): estender `system_errors` ou nova tabela `bug_incidents` com estados deste fluxo — ver Fase 3 do plano de implementação.
