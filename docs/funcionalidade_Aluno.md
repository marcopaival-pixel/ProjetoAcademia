# Funcionalidades do Painel do Aluno

Inventario consolidado das funcionalidades do aluno NexShape, indicando uso de IA, status funcional e disponibilidade por plataforma.

| Funcionalidade | Tem IA? | IA/modelo consumido quando tem IA | Status | Plataforma |
|---|---:|---|---|---|
| Dashboard / visao geral | Sim | NexBot quando acionado: `gpt-4o-mini` para suporte/classificacao e `gpt-4o` para agentes principais | Ativa | Ambas |
| Perfil do aluno | Nao | - | Ativa | Ambas |
| Diario alimentar | Sim | Nutrição IA: `gpt-4o`; classificacao auxiliar: `gpt-4o-mini` | Ativa | Ambas |
| Nutricao / metas / refeicoes | Sim | Nutrição IA: `gpt-4o`; foto/visao: `gpt-4o`; classificacao auxiliar: `gpt-4o-mini` | Ativa | Ambas |
| Sugestao de refeicao | Sim | Nutricao IA configuravel via backend/agentes compartilhados | Ativa | Ambas |
| Auditoria nutricional semanal | Sim | Nutricao IA configuravel via backend/agentes compartilhados | Ativa | Ambas |
| Registro por linguagem natural | Sim | Nutricao IA configuravel via backend/agentes compartilhados | Ativa | Ambas |
| Analise de foto de refeicao | Sim | Visao/Nutricao IA configuravel via backend/agentes compartilhados | Ativa | Ambas |
| Treinos / registro de treino | Nao | - | Ativa | Ambas |
| Sync offline de treino | Nao | - | Ativa | Android |
| Meus planos de treino / progressao | Sim | Treino IA: `gpt-4o`; importacao por foto: `gpt-4o` | Ativa | Ambas |
| Importar treino por foto | Sim | Workout Import IA/OCR: `gpt-4o` (`OPENAI_WORKOUT_IMPORT_MODEL`) | Ativa | Ambas |
| Selecao de alvo muscular por foto | Sim | Visão/Treino IA: `gpt-4o` | Ativa | Ambas |
| Progressao de carga / graficos | Nao diretamente | - | Ativa | Ambas |
| Registro de treino RPE | Nao | - | Ativa | Ambas |
| Catalogo de exercicios | Nao | - | Ativa | Ambas |
| Avaliacoes fisicas / medidas | Nao | - | Ativa | Ambas |
| Peso | Nao | - | Ativa | Ambas |
| Bioimpedancia em PDF | Nao | - | Ativa | Ambas |
| Evolucao / fotos | Sim | Visão IA: `gpt-4o`; relatório visual usa `evolution-report-orchestrator:v2` e, se provider OpenAI ativo, `gpt-4o` | Ativa | Ambas |
| Upload de foto de evolucao | Sim | Validador de foto corporal/visão: `gpt-4o-mini` para validação rápida quando acionado; visão avançada: `gpt-4o` | Ativa | Ambas |
| Relatorio IA de evolucao | Sim | `evolution-report-orchestrator:v2`; se provider OpenAI ativo: comparação `gpt-4o` e auditoria `gpt-4o` | Ativa | Ambas |
| Analise corporal / Body Analysis | Sim | Visão corporal: `gpt-4o` | Ativa | Ambas |
| Comparacao corporal | Sim | Comparação local sobre análises salvas; quando gera análise nova usa `gpt-4o` | Ativa | Ambas |
| Hidratacao / NexHydra | Nao | - | Ativa | Ambas |
| Saude / wearables / health metrics | Nao diretamente | - | Ativa | Web |
| Chat IA / NexBot | Sim | NexBot: `gpt-4o-mini` em suporte/classificação e `gpt-4o` para treino/nutrição/clínico | Ativa | Ambas |
| Creditos de IA / carteira | Sim | Não consome modelo; controla saldo para `gpt-4o`, `gpt-4o-mini` e fluxos IA configurados | Ativa | Ambas |
| Compra de creditos IA | Sim | Não consome modelo; adiciona créditos para uso posterior de IA | Ativa | Ambas |
| Descanso ativo | Sim | Sugestão inteligente local/regra de calendário; sem chamada direta a modelo OpenAI identificada | Ativa | Ambas |
| Mensagens com profissional | Nao | - | Ativa | Ambas |
| Grupos de comunicacao | Nao | - | Ativa | Ambas |
| Agenda / agendamentos | Nao | - | Ativa | Ambas |
| Calendario do aluno | Nao | - | Ativa | Web |
| Ranking / Arena | Nao | - | Ativa | Ambas |
| Trofeus / conquistas | Nao | - | Ativa | Ambas |
| Relatorios / exportacao | Nao | - | Ativa | Web |
| Relatorio mensal PDF | Nao | - | Ativa | Ambas |
| Comunidade NexShape | Nao | - | Ativa | Ambas |
| Academia NexShape / aulas | Nao | - | Ativa | Web |
| Smart Stacks / suplementacao | Sim | Nutrição IA: `gpt-4o` | Ativa | Ambas |
| Assinatura / pagamentos / planos | Nao | - | Ativa | Ambas |
| Checkout com deep link | Nao | - | Ativa | Android |
| Busca de profissionais | Nao | - | Ativa | Ambas |
| Prontuario / evolucoes clinicas | Nao | - | Ativa | Ambas |
| Exames / laudos / documentos clinicos | Nao | - | Ativa | Ambas |
| Atestados e receitas | Nao | - | Ativa | Ambas |
| Logs de acesso LGPD | Nao | - | Ativa | Web |
| Push FCM / notificacoes | Nao | - | Ativa | Android |
| Bloqueio PIN / biometria | Nao | - | Ativa | Android |
| OmniChat antigo | Nao | - | Inativa | Web |
| Historico de transferencias | Nao | - | Inativa/parcial | Web |
| Presenca | Nao | - | Inativa/parcial | Web |
| Menu "Agenda" antigo do seeder | Nao | - | Parcial | Web |

## Resumo

Com a recente expansão da API (v1), o Android agora cobre de forma abrangente o ecossistema do aluno: login, dashboard, perfil, treino (incluindo progresso e sync offline), evolução com fotos/medidas e laudos em PDF, nutrição (IA, metas e templates), agenda, assinatura, NexBot, push, além de recursos engajadores como Hidratação (NexHydra), Gamificação (Arena e Troféus), Suplementação (Smart Stacks) e Grupos de Comunicação.

A Web permanece como o acesso primário para tarefas administrativas pesadas, análises profundas de gráficos herdados e logs de acesso LGPD, mas compartilha quase a totalidade dos módulos vitais com o Mobile.

## Atualizacao Android - Nutricao IA

As funcionalidades de registro por linguagem natural, analise de foto de refeicao, sugestao de refeicao e auditoria nutricional semanal ficam disponiveis tambem no Android usando a mesma API, os mesmos agentes de Nutricao e o mesmo pacote de creditos de IA da Web. O Android atua apenas como interface de captura, confirmacao e exibicao dos resultados.
