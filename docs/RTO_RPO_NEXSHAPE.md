# RTO / RPO — NexShape

Objetivos operacionais recomendados para produção. Ajustar com o negócio após primeiro restore testado.

| Métrica | Alvo | Base |
|---------|------|------|
| **RPO** (perda máxima de dados) | **24 h** | Backup diário Spatie às 02:00 (`routes/console.php`) |
| **RTO** (tempo para restaurar serviço) | **4 h** | Restore manual BD + deploy pacote `dist/` |
| **RTO crítico** (indisponibilidade total) | **8 h** | Inclui validação smoke + DNS/SSL |

## Procedimento resumido

1. Declarar incidente e isolar causa (app vs BD vs rede).
2. Restaurar BD a partir do último backup S3/local (`php artisan backup:list`).
3. Publicar versão estável conhecida (`scripts/prepare-deploy-package.ps1` ou tag Git).
4. `php artisan migrate --force` (se schema divergir).
5. Smoke: `php artisan app:api:smoke --url=...` + login web admin.
6. Registrar incidente em `docs/legal/REGISTRO_TRATAMENTO_LGPD_TEMPLATE.md` se houver dados pessoais afetados.

## Teste periódico

- **Mensal:** restore em homologação (ver `docs/DEPLOY_NEXSHAPE.md` §10).
- **Trimestral:** simulação de falha de fila Redis/worker.

*Última revisão: julho/2026.*
