# Estados do Bug Surgeon

## Máquina de estados

```
RECEIVED
  → INVESTIGATING
  → DIAGNOSIS_READY
  → IMPACT_ANALYZED
  → PATCH_PROPOSED
  → WAITING_APPROVAL
  → APPROVED
  → PATCH_APPLIED
  → TESTING
  → READY_FOR_STAGING
  → WAITING_DEPLOY_APPROVAL
  → DEPLOYED
  → MONITORING
  → CLOSED
```

Estado alternativo: `ROLLED_BACK` (após falha pós-deploy).

Transições **proibidas**:

- `INVESTIGATING` → `PATCH_APPLIED` (pular aprovação)
- `WAITING_APPROVAL` → `PATCH_APPLIED` (sem `APPROVED`)
- `DIAGNOSIS_READY` → `PATCH_PROPOSED` (sem análise de impacto)

## Estados que permitem edição de código

Somente: `APPROVED`, `PATCH_APPLIED`.

Em `PATCH_APPLIED`, edições adicionais exigem nova autorização (exceto arquivos de teste se explicitamente incluídos em `approved_files`).

## Schema: authorization.json

Gravar somente após `[APROVAR]` explícito do usuário:

```json
{
  "authorization_id": "BUG-2026-00871",
  "approved_at": "2026-08-03T22:45:00-03:00",
  "approved_by": "human",
  "approved_files": [
    "backend/app/Services/StudentAssessmentService.php"
  ],
  "approved_lines": {
    "backend/app/Services/StudentAssessmentService.php": {
      "start": 87,
      "end": 87
    }
  },
  "approved_diff": "- $professionalId = $student->professional->id;\n+ $professionalId = $student->professional?->id;",
  "approved_change_hash": "sha256-do-diff-apresentado",
  "max_files_changed": 1,
  "max_lines_changed": 10,
  "allow_new_files": false,
  "allow_delete_files": false,
  "allow_database_changes": false,
  "allow_dependency_changes": false,
  "forbidden_patterns": [
    "tenant_id",
    "Gate::",
    "middleware(",
    "Schema::"
  ]
}
```

### Cálculo do hash

SHA-256 do campo `approved_diff` (UTF-8, LF). O hook recalcula a partir de cada `StrReplace`/`Write` e compara.

## Níveis de aprovação

| Nível | Campo em session.json | Autoriza |
|-------|----------------------|----------|
| 1 — Correção | `approval_patch: true` | Editar arquivos no authorization.json |
| 2 — Staging | `approval_staging: true` | Deploy em homologação |
| 3 — Produção | `approval_production: true` | Deploy em produção |

## Rollback

```json
{
  "rollback_available": true,
  "commit_hash": "7a93fd2",
  "command": "git revert 7a93fd2"
}
```

Gravar em `session.json` após commit da correção.
