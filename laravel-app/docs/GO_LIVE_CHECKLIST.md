# Checklist Go-Live — NexShape v1.1

Runbook operacional para homologação, piloto e comercialização. Complementa `docs/DEPLOY_NEXSHAPE.md` e `docs/MONITORAMENTO.md`.

---

## Comandos automatizados

```bash
# Homologação (staging)
php artisan app:release:verify --target=homologacao
composer test

# Produção (após homologação aprovada em /admin/deploy)
php artisan app:release:verify --target=production
composer test

# Registar homologação aprovada (bootstrap)
php artisan db:seed --class=DeployHomologBootstrapSeeder
```

| Comando | Função |
|---------|--------|
| `app:audit:tenant` | Models sem isolamento multi-tenant |
| `app:deploy:checklist` | Versão, migrations, health, secrets |
| `app:smoke:test` | Rotas críticas, RBAC, demo em produção |
| `app:release:verify` | Orquestra os três acima + PHPUnit opcional |

Testes marcados `@group release`: `php artisan test --group=release`

---

## Fase A — Estabilização (desenvolvimento)

- [ ] Versão congelada em `VERSION` e `APP_VERSION` (ex.: `1.1.0-rc1`)
- [ ] `composer test` verde
- [ ] `composer phpstan` sem erros novos
- [ ] `php artisan app:audit:tenant` exit 0
- [ ] Isolamento tenant validado (referral, financeiro, representante)
- [ ] CHANGELOG atualizado

---

## Fase B — Homologação (staging)

### Deploy

- [ ] `APP_ENV=homologacao` ou staging dedicado
- [ ] `php artisan migrate --force`
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `npm ci && npm run build` (se front alterado)
- [ ] `php artisan config:cache` / `route:cache` / `view:cache`
- [ ] Queue worker + `schedule:run` ativos (Supervisor)

### Secrets (staging espelhando produção)

- [ ] `MP_ACCESS_TOKEN` sandbox ou produção conforme teste
- [ ] `MP_WEBHOOK_SECRET` configurado no painel MP
- [ ] `APP_PUBLIC_URL` correto
- [ ] `OPERATIONAL_ALERT_EMAIL`
- [ ] `SENTRY_LARAVEL_DSN` (recomendado)

### Smoke manual

- [ ] Login: admin, profissional, paciente, representante
- [ ] Checkout sandbox → webhook → assinatura ativa
- [ ] 2 clínicas: dados isolados (treino, nutrição)
- [ ] Saque representante (valor ≤ saldo)
- [ ] Export LGPD JSON + pedido de exclusão
- [ ] `/up`, `/health`, `/api/v1/health` → 200

### Aprovação

- [ ] Registo em `/admin/deploy` com homologação **aprovada**
- [ ] `php artisan app:deploy:checklist --target=production` OK

### Backup

- [ ] Backup BD + `storage/` executado
- [ ] **Restore testado** e documentado (data, responsável, RTO)

---

## Fase C — Piloto fechado (3–5 clínicas)

- [ ] Contrato manual / proposta assinada
- [ ] Canal de suporte definido (e-mail/WhatsApp, horário)
- [ ] Onboarding assistido por clínica
- [ ] Monitoramento diário: `storage/logs`, Sentry, `/health`
- [ ] Revisão semanal: erros, custos IA (`ai:cost-audit`), comissões
- [ ] Feedback LGPD (export, exclusão, consentimentos)

**Critério de saída do piloto:** 2–4 semanas sem incidente **crítico** (pagamento, vazamento de dados, indisponibilidade > 1 h).

---

## Fase D — Comercialização aberta

### Jurídico / LGPD (obrigatório antes de marketing público)

- [ ] Textos em `/legal/*` revistos por advogado/DPO
- [ ] ROPA / DPA com subprocessadores (MP, OpenAI, hospedagem)
- [ ] DPIA para módulos IA e imagens (Vision)
- [ ] SLA de exclusão de conta documentado

### Produto e operações

- [ ] Precificação e planos validados financeiramente
- [ ] Processo de comissões e saques de representantes
- [ ] UptimeRobot (ou equivalente) em `/up` — intervalo 5 min
- [ ] Runbook de incidentes (`docs/MONITORAMENTO.md`)
- [ ] CI verde na branch de release

### Marketing

- [ ] Checkout público testado em produção
- [ ] Webhook MP apenas no Laravel (`/mp/webhook`), não `php-app`
- [ ] Demo `/demo/*` retorna 404 em produção

---

## Matriz de decisão

| Cenário | Pré-requisito |
|---------|----------------|
| Staging interno | Fase A |
| Piloto 3–5 clínicas | Fases A + B |
| Venda B2B (representantes) | Piloto + testes comissão/saque |
| Checkout + marketing público | Fases A + B + D (jurídico) |

---

## Contactos e responsáveis (preencher)

| Papel | Nome | Contacto |
|-------|------|----------|
| Deploy / DevOps | | |
| DPO / Jurídico | | |
| Suporte piloto | | |
| Aprovação go-live | | |
