# Changelog

Todas as alterações relevantes do ProjetoAcademia seguem [Semantic Versioning](https://semver.org/lang/pt-BR/).

## [Unreleased]

## [1.1.0-rc1] - 2026-06-06

### Adicionado
- Trait `FiltersByRepresentative` e isolamento em referral, saques e financeiro profissional.
- Testes: comissão no checkout, saque representante, financeiro profissional, smoke de release, LGPD operacional.
- Comandos `app:smoke:test` e `app:release:verify` para homologação e go-live.
- Documento `docs/GO_LIVE_CHECKLIST.md` (fases A–D).

### Corrigido
- Taxa de comissão no checkout com código de indicação (perfil do representante não carregava via `User` scoped).
- `ReportsModuleTest` alinhado ao redirect para `patient.reports.index` (não premium).
- Isolamento tenant: `PatientAccessToken`, `ExerciseSet`; allowlist de logs operacionais.

### Alterado
- `app:deploy:checklist` valida `APP_DEBUG`, `APP_ENV`, `MP_WEBHOOK_SECRET` e `APP_PUBLIC_URL` em produção.

## [1.0.0] - 2026-05-29

### Adicionado
- Painel administrativo de deploy e releases (`deploy_releases`).
- Comando `php artisan app:version` para consulta e bump semver.
- CI GitHub Actions (testes PHPUnit + PHPStan).

### Adicionado
- Versão inicial rastreada via arquivo `VERSION` e painel de deploy.
