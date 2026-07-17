# Runbook Operacional de Produção - NexShape

Este documento lista os passos operacionais para subir uma release do NexShape em produção. Use junto com `docs/DEPLOY_NEXSHAPE.md` e `docs/MONITORAMENTO.md`.

## 1. Antes do Deploy

- [ ] Confirmar que a release foi validada em homologação.
- [ ] Confirmar branch/tag que será publicada.
- [ ] Revisar se há migrations novas.
- [ ] Revisar se há mudanças em `.env`, filas, webhooks, storage ou permissões.
- [ ] Confirmar janela de manutenção, se necessário.
- [ ] Avisar responsáveis internos.

Comandos locais recomendados em `backend/`:

```bash
composer install
npm ci
npm run build
php artisan test
php artisan migrate --pretend --no-interaction
```

Para esta release de governança de IA, confirmar que existem as migrations:

- `2026_07_17_000003_create_ai_agent_registry_table.php`
- `2026_07_17_000004_create_ai_feature_routes_table.php`

## 2. Backup Obrigatório

Antes de alterar produção:

```bash
php artisan backup:run --only-db
php artisan backup:list
```

Checklist:

- [ ] Backup do banco gerado.
- [ ] Backup de arquivos críticos validado, se aplicável.
- [ ] Local do backup identificado.
- [ ] Responsável pelo rollback definido.

Se não houver backup válido, não prosseguir.

## 3. Colocar em Manutenção

Se a alteração envolver migrations ou troca grande de código:

```bash
php artisan down --render="errors::503"
```

Para deploys pequenos, pode manter online, mas migrations de IA/governança devem preferencialmente usar janela controlada.

## 4. Publicar Código

No servidor:

```bash
cd /caminho/do/NexShape/backend
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

Se o build for feito em CI/local, publicar também:

- `public/build/`
- `vendor/`, se o servidor não roda composer
- `composer.lock`
- migrations novas

## 5. Conferir `.env` de Produção

Obrigatório:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com.br
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true
QUEUE_CONNECTION=database
OPENAI_API_KEY=...
```

Para IA de evolução:

```env
AI_EVOLUTION_PROVIDER=local
OPENAI_COMPARISON_MODEL=gpt-4o
OPENAI_AUDIT_MODEL=gpt-4o
OPENAI_EVOLUTION_STORE=false
```

Conferir também:

- [ ] `MP_ACCESS_TOKEN`
- [ ] `MP_WEBHOOK_SECRET`
- [ ] `MAIL_*`
- [ ] `GOOGLE_VISION_API_KEY`, se OCR estiver ativo
- [ ] `SENTRY_LARAVEL_DSN`, se usado
- [ ] `REDIS_*`, se filas/cache usarem Redis

## 6. Rodar Migrations e Seeders

Executar:

```bash
php artisan migrate --force
php artisan db:seed --class=AiGovernanceSeeder --force
```

O seeder `AiGovernanceSeeder` é obrigatório para registrar:

- agentes de IA ativos;
- rotas de funcionalidades de IA;
- auditor de segurança de prescrições;
- rotas de treino, nutrição, evolução, chat e Smart Stack.

Sem esse seeder, chamadas de IA podem falhar por agente ou feature não registrada.

## 7. Otimizar Cache Laravel

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Se houver problemas com rotas em produção, limpar:

```bash
php artisan optimize:clear
```

## 8. Permissões e Storage

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

Conferir:

- [ ] Uploads funcionando.
- [ ] `storage/logs/laravel.log` gravável.
- [ ] `storage/app/private` gravável.
- [ ] `bootstrap/cache` gravável.

## 9. Reiniciar Filas e Serviços

Se usar Supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl restart nexshape-worker:*
```

Se usar worker manual:

```bash
php artisan queue:restart
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

Confirmar:

```bash
php artisan queue:failed
```

## 10. Tirar Manutenção

```bash
php artisan up
```

## 11. Smoke Tests Pós-Deploy

Endpoints:

- [ ] `GET /up`
- [ ] `GET /health`
- [ ] `GET /api/v1/health`

Fluxos manuais:

- [ ] Login admin.
- [ ] Login aluno.
- [ ] Dashboard carrega sem erro.
- [ ] Upload/validação de foto corporal.
- [ ] Geração ou consulta de relatório de evolução.
- [ ] Chat IA básico.
- [ ] Prescrição IA de treino/nutrição retorna com auditoria.
- [ ] Importação de treino por foto, se disponível no ambiente.
- [ ] Painel financeiro abre.
- [ ] Webhook Mercado Pago validado em sandbox ou evento controlado.

Consultas úteis:

```sql
SELECT COUNT(*) FROM ai_agent_registry WHERE active = 1;
SELECT COUNT(*) FROM ai_feature_routes WHERE active = 1;
SELECT * FROM ai_orchestrator_logs ORDER BY id DESC LIMIT 10;
SELECT * FROM jobs ORDER BY id DESC LIMIT 10;
```

## 12. Checagens Específicas de IA

Confirmar no banco:

- [ ] `ai_agent_registry` populada.
- [ ] `ai_feature_routes` populada.
- [ ] Existe agente `prescription_safety_auditor`.
- [ ] Existe rota `workout_prescription`.
- [ ] Existe rota `nutrition_prescription`.
- [ ] Existe rota `clinical_prescription`.
- [ ] Existe rota `evolution_report`.
- [ ] Existe rota `workout_image_import`.

Testar comportamento esperado:

- [ ] Uma feature inexistente deve falhar fechada.
- [ ] Uma resposta sensível de treino/nutrição/clínico deve passar por `safety_audit`.
- [ ] Logs de IA devem registrar agente, modelo, tokens e contexto.

## 13. Monitoramento nas Primeiras Horas

Durante as primeiras 2 horas:

- [ ] Verificar `storage/logs/laravel.log`.
- [ ] Verificar tabela `system_errors`.
- [ ] Verificar `ai_orchestrator_logs`.
- [ ] Verificar filas pendentes.
- [ ] Verificar jobs falhados.
- [ ] Verificar consumo de créditos IA.
- [ ] Verificar webhooks de pagamento.

Comandos:

```bash
php artisan queue:failed
php artisan schedule:list
```

## 14. Rollback

Se houver falha crítica:

1. Colocar manutenção:

```bash
php artisan down --render="errors::503"
```

2. Voltar código para release anterior.
3. Restaurar backup do banco se migrations alteraram dados de forma incompatível.
4. Limpar cache:

```bash
php artisan optimize:clear
php artisan config:cache
```

5. Reiniciar filas:

```bash
php artisan queue:restart
sudo supervisorctl restart nexshape-worker:*
```

6. Tirar manutenção:

```bash
php artisan up
```

Não rodar `migrate:rollback` em produção sem validar impacto no banco. Preferir restore do backup quando houver dúvida.

## 15. Registro Final da Release

Registrar:

- [ ] Data/hora do deploy.
- [ ] Versão/tag publicada.
- [ ] Responsável.
- [ ] Migrations executadas.
- [ ] Seeders executados.
- [ ] Resultado dos smoke tests.
- [ ] Incidentes ou observações.
- [ ] Link do backup usado como ponto de restauração.

No painel, registrar em Admin > Deploy & Versões, quando disponível.

