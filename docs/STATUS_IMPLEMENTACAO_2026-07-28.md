# Status de implementação pós-auditoria — jul/2026

Consolidação do trabalho realizado após `AUDITORIA_360_NEXSHAPE_2026-07-28.md` e auditorias anteriores.

## Concluído no código

| Área | Itens |
|------|--------|
| **Segurança P0** | IDOR admin (`PatientAccessGuard`, policies), audit show tenant |
| **Financeiro P0** | Idempotência webhook IA, lock `Coupon::markAsUsed`, clawback comissões, soft delete payments/commissions |
| **API Paciente** | Contextos, links, dashboard, mensagens, documentos PDF, evolução, agenda, avaliações |
| **Auth mobile** | Sanctum TTL 30d, refresh token, Android alinhado |
| **API Profissional** | `/dashboard`, `/professional/*` (pacientes, requests, agenda, alertas, protocolos, planos, avaliações, fotos) |
| **API Aluno** | Rotas Android alinhadas (requests, links, medical downloads, nutrition goal POST) |
| **Onboarding mobile** | `POST /api/v1/onboarding/profile` |
| **Comissões** | `commissions:release-available`, `commissions:cleanup-orphans` (scheduler) |
| **Operacional** | `deploy.bat`, `optimize_server.sh`, scripts PowerShell, `GO_LIVE_CHECKLIST`, `app:api:smoke`, CI GitHub Actions |
| **Compliance** | Audit CRUD User/Payment/Clinic, template registro LGPD, RTO/RPO doc |
| **Limpeza** | Removidos arquivos debug (`scratch_debug.php`, `test_api*.php`) |
| **Testes** | 40+ PHPUnit (auth, IDOR, clawback, comissões, evolution, body analysis, API profissional) |

## Pendente operacional (infra / processo)

- Provisionar **Redis** em produção (`CACHE/SESSION/QUEUE=redis`)
- Smoke test homolog com credenciais reais (`SMOKE_TEST_*`)
- Restore backup mensal documentado
- Registro LGPD preenchido e assinado juridicamente
- **WCAG** — auditoria acessibilidade web
- **App iOS** — ver `docs/IOS_APP_STATUS.md`

## Comandos úteis

```bash
php artisan app:deploy:checklist --target=production
php artisan app:api:smoke --url=https://beta.seudominio.com.br
php artisan commissions:release-available
php artisan commissions:cleanup-orphans --dry-run
php artisan test
```
