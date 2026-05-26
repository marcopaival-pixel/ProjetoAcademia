# Runbook de deploy — NexShape (Laravel)

Documento operacional para publicar e manter a aplicação em `laravel-app/`. Alinhado a `deploy.bat`, `optimize_server.sh` e auditoria 360° (maio/2026).

---

## Pré-requisitos

| Componente | Versão mínima |
|------------|----------------|
| PHP | 8.2+ (`pdo_mysql`, `mbstring`, `openssl`, `curl`, `fileinfo`, `zip`, `json`, `xml`) |
| Composer | 2.x |
| MySQL / MariaDB | 8.0+ / 10.6+ |
| Node.js (build) | 18+ (local ou CI; no servidor só se compilar lá) |
| Apache | `mod_rewrite`, DocumentRoot → `public/` |

**Recomendado em produção:** Redis (cache, sessão, filas), Supervisor (queue + scheduler), SSL válido, OPcache ativo.

---

## 1. Build local (Windows / CI)

Na pasta `laravel-app/`:

```powershell
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

Ou execute `deploy.bat` (inclui migrate local — confirme a base antes de correr).

**Crítico:** enviar `public/build/` para o servidor após o build Vite.

---

## 2. Ficheiros a publicar

### Enviar

- `app/`, `bootstrap/`, `config/`, `routes/`
- `public/` (inclui `build/`, `.htaccess`)
- `resources/views/`
- `artisan`, `composer.json`, `composer.lock`
- `vendor/` **ou** correr `composer install --no-dev` no servidor

### Não enviar

- `.env` (criar no servidor)
- `node_modules/`, `tests/`, `.git/`
- `storage/logs/*` (manter pastas vazias com permissão de escrita)

---

## 3. Configuração `.env` (produção)

```ini
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=error
APP_URL=https://www.seudominio.com.br
APP_PUBLIC_URL=https://www.seudominio.com.br

MP_ACCESS_TOKEN=...
MP_WEBHOOK_SECRET=...   # obrigatório — webhooks retornam 503 sem isto

OMNI_WEBHOOK_SECRET=... # obrigatório para /omnichannel/webhook

QUEUE_CONNECTION=database  # ou redis se disponível
SESSION_DRIVER=database    # ou redis
CACHE_STORE=database       # ou redis

# Redis (quando disponível)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

Referência completa: `laravel-app/.env.example` e [AUDITORIA_360_2026-05-21.md](./AUDITORIA_360_2026-05-21.md).

---

## 4. Primeira instalação no servidor

```bash
cd ~/NexShape   # ajustar caminho

cp .env.example .env
php artisan key:generate
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan db:seed --class=RolesAndPermissionsSeeder   # se ambiente novo
php artisan storage:link
chmod -R 775 storage bootstrap/cache
bash optimize_server.sh
```

---

## 5. Atualização (release)

1. Modo manutenção (opcional): `php artisan down`
2. Backup BD (ver secção 8)
3. Upload/sync dos ficheiros alterados
4. `composer install --no-dev --optimize-autoloader`
5. `php artisan migrate --force`
6. `bash optimize_server.sh`
7. Reiniciar workers: `sudo supervisorctl restart nexshape-worker:*`
8. `php artisan up`
9. Smoke test: `/health`, login, webhook MP (sandbox)

---

## 6. Filas e agendador

### Cron (obrigatório)

```cron
* * * * * cd /caminho/NexShape && php artisan schedule:run >> /dev/null 2>&1
```

Tarefas agendadas (`routes/console.php`): monitorização, backup Spatie, créditos IA, purge de logs, Pulse.

### Queue worker

Com `QUEUE_CONNECTION=database` ou `redis`:

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

**Produção:** usar Supervisor — exemplo em [supervisor-nexshape.conf.example](./supervisor-nexshape.conf.example).

Sem worker ativo, jobs (PDF, Omni, e-mail assíncrono) ficam na tabela `jobs`.

### Redis (opcional, recomendado)

1. Instalar Redis no servidor
2. `.env`: `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`
3. `php artisan config:cache`
4. Validar: `php artisan tinker` → `Cache::put('test', 1, 60)`

---

## 7. Mercado Pago (pós-deploy)

1. Painel MP → Webhooks → URL: `https://<dominio>/mp/webhook`
2. Copiar **chave secreta** → `MP_WEBHOOK_SECRET`
3. **Não** usar `php-app/public/mp_webhook.php` em produção (retorna 410)
4. Teste sandbox: pagamento → log em `payment_webhook_logs`

---

## 8. Backup e restore

- Pacote: `spatie/laravel-backup` — diário 02:00 (scheduler)
- Destino: S3 (`BACKUP_DISK`, variáveis `AWS_*`)
- **Testar restore** mensalmente em ambiente de staging

```bash
php artisan backup:run --only-db
php artisan backup:list
```

---

## 9. Monitorização

| Verificação | Como |
|-------------|------|
| App viva | `GET /health` e `GET /up` |
| Erros HTTP | Tabela `system_errors`, `storage/logs/laravel.log` |
| Pulse | `/pulse` (restringir por IP/auth em produção) |
| Filas | `php artisan queue:monitor` ou tamanho da tabela `jobs` |
| Webhooks | `payment_webhook_logs` |

---

## 10. Backup e teste de restore (obrigatório periódico)

### Backup automático

- Pacote: `spatie/laravel-backup`
- Agendamento: ver `routes/console.php` (limpeza + backup diário)
- Destino recomendado: S3 (`BACKUP_*` / `AWS_*` no `.env`)

### Procedimento de teste de restore (homologação)

1. Gerar backup manual: `php artisan backup:run --only-db` (ou backup completo conforme config).
2. Copiar o ficheiro/zip do destino S3 ou `storage/app/laravel-backup/` para ambiente de **homologação**.
3. Restaurar numa base MySQL vazia:
   - Extrair SQL do backup Spatie (ou usar ferramenta MySQL `mysql < dump.sql`).
4. Subir a app apontando `.env` para essa base restaurada.
5. Smoke test: login admin, listagem de utilizadores, um registo de `payment_webhook_logs`, `/api/v1/health`.
6. Registar data e responsável no ticket interno (não versionar dumps com PII).

### CI

O workflow `.github/workflows/deploy-nexshape.yml` inclui o job `backup-restore-reminder` após validação bem-sucedida.

---

## 11. Rollback rápido

1. Restaurar código anterior (Git/FTP)
2. `php artisan migrate:rollback --step=1` **só** se a release incluiu migração reversível
3. `php artisan optimize:clear && php artisan config:cache`
4. Restaurar BD a partir do backup se migração alterou dados

---

## 12. Redis em produção (recomendado)

```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

Supervisor: ver `docs/supervisor-nexshape.conf.example` (workers `queue:work` + `schedule:run`).

---

## 13. XAMPP (desenvolvimento local)

- Document root: `laravel-app/public/`
- `APP_URL` = URL real usada no browser
- MySQL: `DB_HOST=127.0.0.1`, `DB_PORT=3306`
- Filas: `QUEUE_CONNECTION=sync` ou `database` + `php artisan queue:listen`

Ver também `AGENTS.md` (secção Ambiente XAMPP).

---

## API v1 (opcional)

Integrações externas podem usar tokens Sanctum. Ver [laravel-app/docs/API_V1.md](../laravel-app/docs/API_V1.md).

```bash
php artisan migrate   # personal_access_tokens
```

## Qualidade (CI local)

```bash
composer test
composer phpstan          # app/ completo + baseline legado
composer phpstan-baseline # só após alterações grandes no legado
```

## Referências

- [AUDITORIA_360_2026-05-21.md](./AUDITORIA_360_2026-05-21.md)
- [API_V1.md](../laravel-app/docs/API_V1.md)
- [dicionario_dados_suplemento_2026-05.md](../laravel-app/docs/dicionario_dados_suplemento_2026-05.md)
- `laravel-app/deploy.bat`, `laravel-app/optimize_server.sh`
