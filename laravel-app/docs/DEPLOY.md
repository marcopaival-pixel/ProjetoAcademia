# Deploy — ProjetoAcademia (NexShape)

## Fluxo Git

```
desenvolvimento  →  homologacao  →  main (produção)
       ↑                  ↑
  feature/* fix/*    validar + aprovar no painel
```

Branches recomendadas:

| Branch | Uso |
|--------|-----|
| `desenvolvimento` | Integração contínua |
| `homologacao` | Ambiente beta / testes |
| `main` | Produção |
| `feature/nome` | Nova funcionalidade |
| `fix/nome` | Correção |
| `hotfix/nome` | Correção urgente (a partir de `main`) |

## Homologação

Configure no `.env` de homolog (ex.: `beta.seudominio.com.br`):

```env
APP_ENV=staging
APP_URL=https://beta.seudominio.com.br
```

Processo:

1. Merge na branch `homologacao` e deploy no servidor beta.
2. Registrar release em **Admin → Deploy & Versões** (ambiente: Homologação).
3. Testar checklist manual (login, financeiro, 2 empresas, webhooks).
4. **Aprovar** homologação no painel.
5. Só então merge em `main` e deploy produção.

## Comandos

```bash
# Versão
php artisan app:version
php artisan app:version --patch --note="Correção X"

# Auditoria multiempresa (models sem trait)
php artisan app:audit:tenant

# Checklist automático pré-deploy
php artisan app:deploy:checklist --target=production

# Banco para PHPUnit
php artisan app:db:prepare-testing
composer test
composer phpstan
```

## Checklist produção

Automático: `php artisan app:deploy:checklist --target=production`

Manual:

- [ ] Backup banco e arquivos
- [ ] `composer install` / `npm install` se dependências mudaram
- [ ] `php artisan migrate --force`
- [ ] `php artisan config:cache` / `route:cache` (produção)
- [ ] Testes críticos em homolog aprovados
- [ ] Registrar deploy em `/admin/deploy`

## CI

GitHub Actions: `.github/workflows/laravel-ci.yml` (push/PR nas branches principais).

## Versionamento

- Arquivo `VERSION` (semver)
- `CHANGELOG.md` atualizado via `app:version --patch|--minor|--major`
