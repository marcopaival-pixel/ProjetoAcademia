# Ecossistema de Agentes NexShape (Produção)

Este diretório contém as definições operacionais e prompts base para os agentes de IA do sistema NexShape.

## Arquitetura de Agentes

O sistema utiliza um modelo de **Orchestrator** que gerencia as solicitações e delega para agentes especialistas.

### Lista de Agentes
1. [**Orchestrator**](orchestrator.md): Coordenador central.
2. [**Training Agent**](training-agent.md): Prescrição de treinos.
3. [**Nutrition Agent**](nutrition-agent.md): Estratégias alimentares.
4. [**Clinical Insights Agent**](clinical-insights-agent.md): Análise de limitações físicas.
5. [**Support Agent**](support-agent.md): Suporte ao uso do sistema.
6. [**Analytics Agent**](analytics-agent.md): Insights e relatórios de dados.
7. [**Finance Agent**](finance-agent.md): Gestão financeira e cobranças.
8. [**Sales Agent**](sales-agent.md): Conversão e upgrades.
9. [**Retention Agent**](retention-agent.md): Prevenção de cancelamentos (Churn).

## Regras Globais
- **Isolamento**: Todos os agentes operam sob a restrição de `clinic_id`.
- **Segurança**: Respeitar permissões de usuário e LGPD.
- **Limites**: Validar planos (Free vs Premium) antes da execução.
- **Logs**: Todas as interações devem ser registradas para fins de auditoria e métricas de uso de IA.

---
*Gerado automaticamente para o ecossistema NexShape.*
