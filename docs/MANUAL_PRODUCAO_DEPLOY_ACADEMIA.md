# Manual de produção — Projeto Academia (Laravel)

Este documento lista **procedimentos obrigatórios e recomendados** ao colocar o sistema em **produção**. Complementa os guias já existentes no repositório:

- `docs/GUIA_DEPLOY_LARAVEL_HOSTGATOR.md` — hospedagem compartilhada (cPanel, `public_html`, `index.php`).
- `docs/MANUAL_TECNICO_DEPLOY_LARAVEL_SHARED.md` — padrão técnico, checklist e ideias de script pós-deploy.

**Stack verificada no repositório:** PHP **^8.2**, Laravel **11.x**, aplicação em `laravel-app/`. Ajuste caminhos se o deploy usar apenas essa pasta como raiz do projeto.

---

## 1. Antes do deploy (máquina de build ou CI)

1. **Código versionado** sem segredos (nunca commitar `.env`, chaves de API, tokens Mercado Pago, etc.).
2. **Dependências PHP (produção):**
   ```bash
   cd laravel-app
   composer install --no-dev --optimize-autoloader
   ```
3. **Front-end (Vite), se aplicável ao que estiver em uso:**
   ```bash
   npm ci
   npm run build
   ```
4. **Testes / verificação local** (recomendado): fluxos críticos (login, admin, geração de PDF se for prioridade).
5. **Gerar `APP_KEY` de produção** (se ainda não existir no `.env` de produção):
   ```bash
   php artisan key:generate --show
   ```
   Copie o valor para o `.env` de produção **uma única vez**; não rode `key:generate` em produção se já houver utilizadores e dados cifrados dependentes da chave.

---

## 2. Infraestrutura mínima

| Componente | Notas |
|------------|--------|
| **PHP** | >= 8.2; extensões típicas Laravel: `openssl`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `curl`, `zip`, `bcmath`. |
| **MySQL/MariaDB** | Base dedicada, utilizador com privilégios adequados, charset `utf8mb4`. |
| **HTTPS** | Certificado válido; obrigatório para cookies seguros e confiança em links públicos (validação de PDF, pagamentos). |
| **Document root** | Deve apontar para `public/` (ou equivalente em hospedagem compartilhada, conforme guias). **Não** expor a raiz do Laravel. |
| **Redis** (opcional mas recomendado em VPS) | O `.env.example` usa `SESSION_DRIVER=redis`, `CACHE_STORE=redis`, `QUEUE_CONNECTION=redis`. Em **hospedagem compartilhada** muitas vezes **não** há Redis: use `file` ou `database` (ver secção 6). |

---

## 3. Variáveis de ambiente (`.env`) em produção

Criar o `.env` **no servidor** a partir de `laravel-app/.env.example` e preencher com valores reais. Checklist:

### 3.1 Aplicação

- `APP_NAME` — nome exibido.
- `APP_ENV=production`
- `APP_DEBUG=false` (**obrigatório** em produção).
- `APP_URL=https://seu-dominio.tld` — URL **exata** do site (afeta redirects, alguns links e coerência com o utilizador).
- `APP_TIMEZONE` — ex.: `America/Sao_Paulo`.
- `APP_KEY` — chave base64 gerada (ver secção 1).

### 3.2 URL pública e subpastas

- `APP_PUBLIC_URL` — usado em fluxos alinhados ao projeto legado / webhooks (ver `config/projeto.php`). Deve ser a URL pública até a pasta **`public`** (sem barra final desnecessária), conforme comentários no `.env.example`.
- `APP_BASE_PATH` — se a aplicação correr num subcaminho (ex.: `/academia/public`), definir conforme documentação interna do projeto; caso contrário deixar vazio.

### 3.3 Base de dados

- `DB_*` — host (muitas vezes `127.0.0.1` em shared hosting), porta, base, utilizador, palavra-passe fortes.

### 3.4 Sessão, cache e filas

Ajustar conforme o hosting:

- **VPS com Redis:** manter Redis coerente com `SESSION_DRIVER`, `CACHE_STORE`, `QUEUE_CONNECTION`.
- **Shared hosting sem Redis:** exemplos comuns:
  - `SESSION_DRIVER=file` ou `database`
  - `CACHE_STORE=file` ou `database`
  - `QUEUE_CONNECTION=sync` **só se** aceitar que e-mails/envios em fila executem no mesmo pedido HTTP (limitado); idealmente `database` + worker (ver secção 7).

### 3.5 E-mail

- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, credenciais, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`.
- Testar envio (ex.: recuperação de password ou e-mail de teste no painel, se existir).

### 3.6 Mercado Pago

- `MP_ACCESS_TOKEN` — token de produção (não usar token de teste em produção sem querer).
- Confirmar URLs de retorno/webhook com a URL **HTTPS** real e o caminho configurado no projeto (`routes/web.php` referencia webhook em `/mp_webhook.php`).

### 3.7 PDF oficial, histórico e validação (módulo SaaS)

Conforme `laravel-app/.env.example` e `config/pdf.php`:

- `PDF_HISTORICO_DISK` — disco Laravel (`local`, `public`, `s3`, etc.); em produção com múltiplos servidores, **evitar** `local` sem partilha; preferir **S3** (ou NFS) coerente com `config/filesystems.php`.
- `PDF_HISTORICO_DIRECTORY` — subpasta no disco escolhido.
- `PDF_VALIDATION_PATH` — segmento da rota pública de validação (ex.: `validar-documento`); deve corresponder ao que se divulga nos QR codes.
- `PDF_DEFAULT_TTL_DAYS` — `0` = sem expiração automática; outro valor = política de “expirado” no portal público.

Garantir que **`APP_URL`** (e qualquer URL usada na geração do QR) reflete o domínio **real** de produção, para os links de validação funcionarem.

### 3.8 WhatsApp (opcional)

- `WHATSAPP_DRIVER` — ex.: `none` se não houver gateway; ou valor suportado pelo código (`config/services.php`).
- `WHATSAPP_API_URL`, `WHATSAPP_TOKEN` — conforme o fornecedor; o contrato HTTP pode variar.

### 3.9 Outros serviços

- **OpenAI** (`OPENAI_API_KEY`, etc.) — se usar chat nutricional.
- **Omnichannel** — `OMNI_WEBHOOK_SECRET`: definir valor forte em produção; o webhook deve enviar o header acordado na documentação do `.env.example`.
- **AWS / S3** — se `FILESYSTEM_DISK` ou PDFs usarem S3.

### 3.10 Seed e administrador

- `ADMIN_EMAIL` — opcional; usado pelo `DatabaseSeeder` para marcar utilizador existente como admin **apenas** quando corre seed (não substitui gestão correta de perfis em produção).

---

## 4. Estrutura no servidor e segurança de ficheiros

1. Colocar o código **fora** da pasta pública, exceto o conteúdo de `public/`, conforme `GUIA_DEPLOY_LARAVEL_HOSTGATOR.md` / `MANUAL_TECNICO_DEPLOY_LARAVEL_SHARED.md`.
2. Ajustar `public/index.php` (ou `public_html/index.php`) para apontar para `vendor/autoload.php` e `bootstrap/app.php` no caminho real do projeto.
3. **Permissões** (Linux): pastas como `storage/` e `bootstrap/cache/` graváveis pelo utilizador do PHP/web server.
4. **Ligação simbólica do storage** (ficheiros públicos):
   ```bash
   php artisan storage:link
   ```
5. Não deixar ficheiros de manutenção públicos (`deploy-fix.php`, instaladores, `phpinfo`, etc.) após uso.

---

## 5. Base de dados: migrações e dados iniciais

1. **Migrações (produção):**
   ```bash
   php artisan migrate --force
   ```
   O `--force` é necessário quando `APP_ENV=production`.

2. **Seeders:** o `DatabaseSeeder` atual chama `RolesAndPermissionsSeeder`, `AcademyCompanySeeder` e `MasterUserSeeder` (entre outros).  
   - **Primeira instalação:** pode fazer sentido correr seed **uma vez** para perfis/permissões e empresa padrão.  
   - **Produção com dados reais:** **não** volte a correr seed cegamente — pode criar duplicados ou alterar utilizadores. Preferir migrações + scripts controlados ou seeders idempotentes já previstos (`firstOrCreate`, etc.).  
   - Rever `MasterUserSeeder`: cria utilizador master com palavra-passe predefinida no código — **altere a password imediatamente** ou desative esse utilizador após o primeiro acesso se usar este seed em produção.

3. **Backup:** antes de `migrate`, fazer backup da base (e dos ficheiros de storage relevantes).

---

## 6. Otimização Laravel

Após `.env` correto e migrations aplicadas:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Em **desenvolvimento** ou ao alterar `.env` frequentemente, usar `php artisan config:clear` etc.

---

## 7. Filas e envio automático de PDF (e-mail / WhatsApp)

O módulo de PDF pode agendar envios via **jobs** na fila. Se `QUEUE_CONNECTION` não for `sync`, é necessário **processar a fila**:

- **VPS:** Supervisor (ou systemd) a correr continuamente:
  ```bash
  php artisan queue:work --sleep=3 --tries=3
  ```
- **Shared hosting:** muitas vezes só é viável `QUEUE_CONNECTION=sync` ou cron a cada minuto com `queue:work --stop-when-empty` (limitado; avaliar carga).

Sem worker (e com fila não síncrona), e-mails e integrações assíncronas **ficam pendentes**.

---

## 8. Tarefas agendadas (Cron)

Se no futuro existirem tarefas em `routes/console.php` ou `app/Console/Kernel.php` (consoante a versão), o servidor deve ter uma entrada cron:

```text
* * * * * cd /caminho/absoluto/laravel-app && php artisan schedule:run >> /dev/null 2>&1
```

No estado atual do `routes/console.php` fornecido no repositório, o agendamento pode ser mínimo; confirme no código antes de ativar.

---

## 9. Verificação pós-deploy (checklist)

- [ ] Site abre em HTTPS sem avisos de certificado.
- [ ] Login e painel admin acessíveis.
- [ ] Uploads e logos (storage público) visíveis após `storage:link`.
- [ ] Mercado Pago: fluxo de pagamento e webhook em ambiente real (valor simbólico primeiro).
- [ ] E-mail transacional (registo, recuperação, PDF por e-mail se ativo).
- [ ] **PDF:** gerar documento oficial, verificar ficheiro no disco configurado, QR aponta para `APP_URL`/`PDF_VALIDATION_PATH` corretos, página pública de validação responde.
- [ ] **Filas:** job de envio executado (logs de mail / `failed_jobs` vazios ou tratados).
- [ ] `APP_DEBUG=false` e logs (`storage/logs`) com nível adequado (`LOG_LEVEL` em produção frequentemente `error` ou `warning`).
- [ ] Revisão de permissões RBAC (perfis Gerente, Instrutor, etc.) após `RolesAndPermissionsSeeder`.

---

## 10. Manutenção contínua

1. **Backups:** base de dados + `storage/app` (PDFs históricos, uploads) + eventual S3.
2. **Atualizações:** `composer update` / patches de segurança; repetir `migrate`, caches e testes.
3. **Segredos:** rotação se vazamento; nunca commitar `.env`.
4. **Monitorização:** espaço em disco (PDFs crescem com o tempo), filas falhadas, logs de erro PHP e Laravel.

---

## 11. Referência rápida de comandos

```bash
cd laravel-app

composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Opcional: worker em terminal dedicado (VPS)
php artisan queue:work --sleep=3 --tries=3
```

---

*Documento gerado com base na estrutura e ficheiros do repositório ProjetoAcademia. Ajuste caminhos, domínios e políticas internas da sua organização.*
