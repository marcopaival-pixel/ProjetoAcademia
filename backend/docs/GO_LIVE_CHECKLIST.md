# Go-live NexShape — Checklist (fases A–D)

Checklist operacional para homologação e produção. Complementa `docs/DEPLOY_NEXSHAPE.md`.

---

## Fase A — Pré-deploy (dev/CI)

- [ ] `php artisan test` verde (backend)
- [ ] `./gradlew assembleDebug` OK (Android)
- [ ] `composer phpstan` sem regressões críticas
- [ ] Migrations revisadas (`php artisan migrate:status`)
- [ ] `npm run build` gera `public/build/`
- [ ] `.env.example` atualizado (Sanctum TTL, webhooks, Redis)
- [ ] Sem ficheiros debug no pacote (`scratch_debug.php`, `test_api*.php`)

---

## Fase B — Homologação (beta)

- [ ] Deploy na branch/ambiente `homologacao`
- [ ] `php artisan app:deploy:checklist --target=homologacao`
- [ ] `php artisan app:api:smoke --url=https://beta.seudominio.com.br` (+ credenciais teste)
- [ ] Login web (admin, profissional, paciente)
- [ ] Impersonation admin → paciente (sem IDOR)
- [ ] Webhook Mercado Pago sandbox (pagamento + reembolso)
- [ ] API paciente mobile: links, contexto, documentos PDF
- [ ] API profissional mobile: dashboard, pacientes, requests
- [ ] Refresh token Sanctum (`POST /auth/token/refresh`)
- [ ] Comissões: `php artisan commissions:release-available --dry-run`
- [ ] Registar homologação aprovada em `/admin/deploy`

---

## Fase C — Produção (pré-cutover)

- [ ] Backup BD + `storage/app` validado
- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `LOG_LEVEL=error`
- [ ] `MP_WEBHOOK_SECRET` e `OMNI_WEBHOOK_SECRET` definidos
- [ ] `SESSION_ENCRYPT=true`, cookies seguros em HTTPS
- [ ] Redis para cache/sessão/filas (recomendado)
- [ ] `SANCTUM_TOKEN_EXPIRATION_DAYS=30`
- [ ] Supervisor: `queue:work` + scheduler (`schedule:run`)
- [ ] Pulse restrito a administradores
- [ ] `php artisan app:deploy:checklist --target=production`

---

## Fase D — Pós-deploy (primeiras 24 h)

- [ ] Smoke test produção (`app:api:smoke`)
- [ ] Verificar `storage/logs/laravel.log` e `system_errors`
- [ ] Testar checkout real (valor mínimo) + webhook recebido
- [ ] Confirmar filas/processamento de e-mail
- [ ] Monitorizar `payment_webhook_logs`
- [ ] Rollback documentado se falha crítica

---

## Comandos úteis

```bash
php artisan app:deploy:checklist --target=production
php artisan app:api:smoke --url=https://www.seudominio.com.br
php artisan commissions:release-available
php artisan commissions:cleanup-orphans --dry-run
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

*Última revisão: julho/2026 — alinhado à auditoria 360° NexShape.*
