# Monitoramento e observabilidade — NexShape

Runbook complementar a `docs/DEPLOY_NEXSHAPE.md` (secção 9). Alinhado à auditoria P2 (maio/2026).

---

## 1. Health checks (implementado)

| Endpoint | Uso | Monitor externo |
|----------|-----|-----------------|
| `GET /up` | Laravel bootstrap | UptimeRobot / Pingdom — **5 min** |
| `GET /health` | JSON detalhado (BD, filas, disco) | Alerta se HTTP ≠ 200 |
| `GET /api/v1/health` | API mobile/integrações | Mesmo domínio ou subdomínio API |

**Smoke pós-deploy:** os três endpoints devem responder 200 (salvo BD down em `/health` → 503).

---

## 2. Logs da aplicação

| Destino | Config | Produção |
|---------|--------|----------|
| Ficheiro | `storage/logs/laravel.log` | `LOG_LEVEL=warning` ou `error` |
| BD | `system_errors` | Erros HTTP capturados em `bootstrap/app.php` |
| E-mail | `OperationalAlertService` | `OPERATIONAL_ALERT_EMAIL` |

**Rotação:** log diário opcional (`LOG_STACK=daily`); purge BD — `app:purge-old-logs` (scheduler 03:00).

---

## 3. Laravel Pulse (implementado)

- Rota Pulse (pacote Laravel) — restringir por IP ou auth admin.
- Gate `viewPulse` → só administradores (`AppServiceProvider`).
- Scheduler: `pulse:check` e `pulse:work` a cada minuto.
- **Retenção:** `app:purge-pulse` diário às 03:15 (default 7 dias — `LOG_RETENTION_PULSE_DAYS` / `PULSE_STORAGE_KEEP`).
- Diagnóstico: `php artisan app:db:mysql-health` (buffer pool, slow log, contagem `pulse_entries`).

**Produção:** não expor `/pulse` publicamente; usar VPN ou allowlist Apache.

---

## 3.1 MySQL / MariaDB (XAMPP e produção)

Comando read-only: `php artisan app:db:mysql-health`

| Variável | Dev (XAMPP) | Produção |
|----------|-------------|----------|
| `innodb_buffer_pool_size` | ≥ **128 MB** (my.ini) | ≥ 512 MB conforme RAM |
| `slow_query_log` | opcional | **ON** (`long_query_time=2`) |
| `pulse_entries` | monitorizar | scheduler `app:purge-pulse` |

**XAMPP (`my.ini`):** secção `[mysqld]` — aumentar `innodb_buffer_pool_size`; reiniciar MySQL no painel XAMPP.

**Backups nativos:** `php artisan app:backup:native` (CLI) ou painel admin (fallback). `php artisan app:backup:verify` — deteta `.sql` vazios em `storage/app/backups` (scheduler semanal). Ficheiros vazios são rejeitados na geração.

Referência histórica: `docs/AUDITORIA_BANCO_DADOS_20260513.md`.

---

## 4. Sentry (integrado — opcional em runtime)

Pacote instalado: `sentry/sentry-laravel` (^4.25). Config: `config/sentry.php`.

Ativar em produção definindo no `.env`:

```env
SENTRY_LARAVEL_DSN=https://...@sentry.io/...
SENTRY_TRACES_SAMPLE_RATE=0.1
```

Sem DSN, o Sentry **não envia** eventos (comportamento seguro para dev/local).

Publicar/ajustar config (já versionado):

```bash
php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
```

**LGPD:** manter `send_default_pii` = `false` em `config/sentry.php`. O handler Laravel existente (`SystemError`, e-mail operacional) continua ativo em paralelo.

**Sem Sentry:** manter `system_errors` + monitorização de `/health` + revisão diária de `storage/logs/laravel.log`.

---

## 5. Uptime externo

Serviços gratuitos adequados:

- [UptimeRobot](https://uptimerobot.com/) — monitor HTTP `/up`
- [Better Stack](https://betterstack.com/) — uptime + log aggregation (alternativa)

Configurar alertas para:

- HTTP 5xx em `/up` ou `/health`
- Tempo de resposta > 5 s (ajustável)
- Certificado SSL a expirar (< 14 dias)

Variável de referência no `.env` (documentação interna):

```env
UPTIME_MONITOR_URL=https://www.seudominio.com.br/up
```

---

## 6. Filas e jobs

| Verificação | Comando / query |
|-------------|-----------------|
| Jobs pendentes | `SELECT COUNT(*) FROM jobs` ou `php artisan queue:monitor` |
| Jobs falhados | `php artisan queue:failed` |
| Worker ativo | Supervisor `nexshape-worker` — ver `docs/supervisor-nexshape.conf.example` |

**Alerta:** se `jobs` crescer > 1000 ou worker parado > 15 min.

---

## 7. Pagamentos e webhooks

- Tabela `payment_webhook_logs` — falhas repetidas MP/Asaas.
- Mercado Pago: painel → Webhooks → histórico de entregas.

---

## 8. Backup

- Spatie backup diário 02:00 — ver `DEPLOY_NEXSHAPE.md` §10.
- Alerta se `backup:run` falhar (monitorizar log ou e-mail `BACKUP_NOTIFICATION_EMAIL`).

---

## 9. Checklist operacional semanal

- [ ] `/health` e `/up` OK
- [ ] Tamanho tabela `jobs` / worker Supervisor ativo
- [ ] Erros novos em `system_errors` (últimos 7 dias)
- [ ] Webhooks MP sem falhas sustentadas
- [ ] Espaço em disco `storage/` e MySQL

---

## 10. Observabilidade avançada (implementado — maio/2026)

Config central: `laravel-app/config/observability.php` (variáveis em `.env.example`).

| Recurso | Rota admin | Tabela / origem |
|---------|------------|-----------------|
| Logs admin | `/admin/observability/admin-logs` | `admin_logs` |
| Auditoria auth | `/admin/observability/auth-logs` | `auth_audit_logs` |
| Logs API v1 | `/admin/observability/api-logs` | `api_access_logs` |
| Erros JavaScript | `/admin/observability/client-errors` | `client_error_logs` |
| Logs e-mail | `/admin/settings/email/logs` | `log_envio_email` |

**API:** middleware `LogApiAccess` + `AssignRequestId` em rotas `/api/*`.  
**Frontend:** `resources/js/logger.js` → `POST /api/v1/client-errors`.  
**Alertas:** `AlertDispatcher` (e-mail + Slack via `SLACK_OPS_WEBHOOK_URL`).  
**Retenção:** `php artisan app:purge-old-logs --force` (políticas por tabela em `observability.retention_days`).  
**Deploy:** `php artisan app:deploy:checklist` valida health endpoints e `SystemHealthService`.  
**Relatório compliance:** `php artisan app:audit:report --days=7` → JSON em `storage/app/reports/`.  
**Filas nomeadas:** `default`, `pdf`, `ai`, `webhooks` — ver `App\Support\QueueNames` e `docs/supervisor-nexshape.conf.example`.

### Laravel Horizon (produção Linux + Redis)

Requer `ext-pcntl` e `ext-posix` (não disponível no Windows/XAMPP). Em servidor Linux:

```bash
composer require laravel/horizon
php artisan horizon:install
# .env: QUEUE_CONNECTION=redis, HORIZON_ENABLED=true
php artisan horizon
```

Proteger `/horizon` — apenas administradores (`AppServiceProvider` registra `Horizon::auth` quando o pacote está instalado). Não expor publicamente em produção.

---

## Referências

- `config/monitoring.php` — URLs e flags documentadas
- `laravel-app/tests/Feature/HealthEndpointsTest.php`
- `laravel-app/tests/Feature/SecurityHeadersTest.php`
