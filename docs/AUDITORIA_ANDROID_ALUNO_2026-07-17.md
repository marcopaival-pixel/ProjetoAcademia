# Auditoria Android Aluno - 2026-07-17

Auditoria cruzada entre `docs/funcionalidade_Aluno.md`, rotas reais da API Laravel e implementacao Android.

## Escopo verificado

- Documento base: `docs/funcionalidade_Aluno.md`.
- Rotas reais: `php artisan route:list --path=api/v1`.
- Android: telas em `android/app/src/main/java/br/com/nexshape/academia/ui`, cliente Retrofit em `data/api/NexShapeApi.kt` e repositories em `data/repository/Repositories.kt`.

## Conclusao executiva

O documento esta parcialmente desatualizado. A inconsistencia de Nutricao IA citada anteriormente ja foi corrigida no arquivo atual: registro por linguagem natural, analise de foto, sugestao de refeicao e auditoria semanal estao marcados como `Ambas` e existem nas rotas `api/v1`.

O maior risco encontrado nao e falta de tela Android, mas desalinhamento entre Android e backend API. O app Android possui telas e cliente Retrofit para varias funcionalidades que nao estao registradas nas rotas reais `api/v1`. Isso pode gerar erro 404 em producao mesmo quando a tela existe.

Tambem ha problema de encoding no documento e no Android: textos acentuados aparecem quebrados, indicando arquivo salvo/lido com charset incorreto.

## Rotas `api/v1` confirmadas

Foram confirmadas 49 rotas `api/v1`, cobrindo:

- autenticacao por token;
- perfil `/me`;
- treino e catalogo de exercicios;
- importacao de treino por foto;
- status de pagamento;
- diario alimentar e quatro fluxos de Nutricao IA;
- sessoes de treino basicas;
- evolucao, fotos, relatorio IA e consentimento;
- analise corporal e comparacao;
- comunidade;
- mensagens internas.

## Endpoints chamados pelo Android, mas ausentes em `api/v1`

Estes aparecem em `NexShapeApi.kt`/repositories, mas nao aparecem em `php artisan route:list --path=api/v1`:

| Area | Endpoint Android | Impacto |
|---|---|---|
| Autenticacao | `auth/google`, `auth/register`, `auth/forgot-password`, `auth/reset-password` | Fluxos mobile podem falhar se nao houver bridge fora de `api/v1`. |
| Nutricao | `nutrition/goal`, `nutrition/meal-templates`, `nutrition/meal-templates/{id}/apply` | Metas e templates no Android podem falhar. |
| Hidratacao | `hydration/status`, `hydration/entries`, `hydration/entries/{id}` | NexHydra existe no Android, mas backend API v1 nao registra as rotas. |
| Creditos IA | `ai/credits` | Carteira aparece no Android, mas saldo pode falhar. |
| Progressao | `load-logs` | Registro de carga no Android pode falhar. |
| Treino | `workout-sessions/active`, `workout-sessions/start`, `workout-sessions/{id}` | Sessao ativa/inicio/atualizacao podem falhar; API v1 so lista e cria sessoes. |
| Push | `devices`, `notifications/unread-counts` | Registro FCM e contadores podem falhar. |
| Avaliacoes | `assessments`, `assessments/summary` | Tela de exames/medidas pode falhar parcialmente. |
| Assinaturas | `subscriptions/plans`, `subscriptions/checkout`, `subscriptions/current`, `subscriptions/cancel` | Compra/gestao de planos por Android pode falhar. |
| Profissionais | `student/professionals/*` | Busca, vinculos, permissoes e solicitacoes podem falhar. |
| Agenda | `student/appointments/*` | Agenda Android existe, mas endpoints nao estao em `api/v1`. |
| Documentos clinicos | `student/medical-documents/*` | Tela Android existe, mas endpoints nao estao em `api/v1`. |
| Gamificacao | `student/gamification` | Ranking/trofeus existem no Android, mas endpoint nao esta em `api/v1`. |
| Descanso ativo | `student/active-rest/*` | Tela Android existe, mas endpoints nao estao em `api/v1`. |
| Upload | `uploads/nutrition-photo` | Upload separado de foto nutricional pode falhar. |
| Erros cliente | `client-errors` | Telemetria de erro mobile pode falhar. |
| Profissional mobile | `professional/*`, `dashboard` | Parte do modo profissional Android depende de rotas ausentes em `api/v1`. |

## Matriz por funcionalidade do aluno

| Funcionalidade | Documento | Android | API v1 real | Parecer |
|---|---|---|---|---|
| Dashboard / visao geral | Ambas | Tela presente | Parcial | Depende de agregacoes fora do trecho confirmado; manter como parcial. |
| Perfil do aluno | Ambas | Presente | Sim: `/me` | Correto. |
| Diario alimentar | Ambas | Presente | Sim | Correto. |
| Nutricao / metas / refeicoes | Ambas | Presente | Parcial | Diario e IA existem; metas/templates nao estao em API v1. |
| Sugestao de refeicao | Ambas | Presente | Sim | Correto. |
| Auditoria nutricional semanal | Ambas | Presente | Sim | Correto. |
| Registro por linguagem natural | Ambas | Presente | Sim | Correto. |
| Analise de foto de refeicao | Ambas | Presente | Sim | Correto. |
| Treinos / registro de treino | Ambas | Presente | Parcial | Planos e criacao de sessao existem; sessao ativa/start/update e load logs faltam em API v1. |
| Sync offline de treino | Android | Presente | Parcial | Fila local existe; flush depende de endpoints de treino/carga completos. |
| Meus planos de treino / progressao | Ambas | Presente | Parcial | Planos existem; progressao de carga esta incompleta na API v1. |
| Importar treino por foto | Ambas | Presente | Sim | Correto. |
| Selecao de alvo muscular por foto | Ambas | Presente | Parcial | Cliente/tela indicam suporte; confirmar fluxo completo de backend antes de prometer. |
| Progressao de carga / graficos | Web | Parcial no Android | Parcial/ausente | Documento subestima Android, mas backend API v1 ainda nao fecha a funcao. |
| Registro de treino RPE | Ambas | Presente | Parcial | Validar contrato de payload em sessoes. |
| Catalogo de exercicios | Web | Presente | Sim | Documento incorreto: deve ser `Ambas`. |
| Avaliacoes fisicas / medidas | Ambas | Presente | Ausente em API v1 | Documento pode estar correto no produto, mas API mobile esta desalinhada. |
| Peso | Ambas | Presente | Ausente em API v1 | Depende de assessments/metrica; marcar parcial. |
| Bioimpedancia em PDF | Web | Nao confirmado | Ausente em API v1 | Manter Web por enquanto. |
| Evolucao / fotos | Ambas | Presente | Sim | Correto. |
| Upload de foto de evolucao | Ambas | Presente | Sim | Correto. |
| Relatorio IA de evolucao | Web | Presente | Sim | Documento incorreto: existe API e repository Android; verificar UX final. |
| Analise corporal / Body Analysis | Web | Presente | Sim | Documento incorreto: deve ser `Ambas` ou `Android parcial` se UX incompleta. |
| Comparacao corporal | Web | Presente | Sim | Documento incorreto: deve ser `Ambas` ou `Android parcial`. |
| Hidratacao / NexHydra | Web | Presente | Ausente em API v1 | Documento subestima Android, mas API precisa ser publicada em v1. |
| Saude / wearables / health metrics | Web | Nao ha Health Connect | Ausente em API v1 | Prioridade real alta; ainda nao implementado no Android. |
| Chat IA / NexBot | Ambas | Presente | Nao confirmado em API v1 | Confirmar rotas do chat IA; mensagens internas existem, NexBot nao aparece no route:list v1. |
| Creditos de IA / carteira | Web | Presente | Ausente em API v1 | Documento incorreto quanto a UI Android; API v1 ausente. |
| Compra de creditos IA | Web | Nao confirmado | Ausente em API v1 | Continua pendente no Android. |
| Descanso ativo | Web | Tela presente | Ausente em API v1 | Documento incorreto quanto a UI; backend API v1 ausente. |
| Mensagens com profissional | Web | Presente | Sim | Documento incorreto: deve ser `Ambas`. |
| Grupos de comunicacao | Web | Nao confirmado | Ausente em API v1 | Manter Web/pendente. |
| Agenda / agendamentos | Ambas | Presente | Ausente em API v1 | Documento correto conceitualmente, mas backend API v1 desalinhada. |
| Calendario do aluno | Web | Parcial via Agenda | Ausente em API v1 | Marcar parcial. |
| Ranking / Arena | Web | Presente em Gamificacao | Ausente em API v1 | Documento incorreto quanto a UI; API v1 ausente. |
| Trofeus / conquistas | Web | Presente em Gamificacao | Ausente em API v1 | Documento incorreto quanto a UI; API v1 ausente. |
| Relatorios / exportacao | Web | Parcial | Parcial | Relatorio de evolucao existe; exportacao geral nao confirmada. |
| Relatorio mensal PDF | Web | Nao confirmado | Ausente em API v1 | Manter Web. |
| Comunidade NexShape | Web | Presente | Sim | Documento incorreto: deve ser `Ambas`. |
| Academia NexShape / aulas | Web | Shopping/treinamento nao confirmado | Ausente em API v1 | Manter Web/pendente. |
| Smart Stacks / suplementacao | Web | Nao confirmado | Ausente em API v1 | Manter Web. |
| Assinatura / pagamentos / planos | Ambas | Presente | Parcial | Status existe; planos/checkout/current/cancel faltam em API v1. |
| Checkout com deep link | Android | Presente no cliente | Ausente em API v1 | Alto risco de quebra se nao houver rota alternativa. |
| Busca de profissionais | Ambas | Presente | Ausente em API v1 | Documento correto no app, mas API v1 ausente. |
| Prontuario / evolucoes clinicas | Web | Tela clinica presente | Ausente em API v1 | Android parece parcial; exigir biometria/permissao. |
| Exames / laudos / documentos clinicos | Web | Presente | Ausente em API v1 | Documento incorreto quanto a UI; API v1 ausente. |
| Atestados e receitas | Web | Presente | Ausente em API v1 | Documento incorreto quanto a UI; API v1 ausente. |
| Logs de acesso LGPD | Web | Nao confirmado | Ausente em API v1 | Manter Web; mobile so resumo futuramente. |
| Push FCM / notificacoes | Android | Presente | Ausente em API v1 | API v1 precisa registrar `devices` e notificacoes. |
| Bloqueio PIN / biometria | Android | Presente | Nao depende de API | Correto. |
| OmniChat antigo | Inativa/Web | Nao priorizar | Nao aplicavel | Correto manter fora. |
| Historico de transferencias | Inativa/parcial Web | Nao priorizar | Nao aplicavel | Correto manter fora. |
| Presenca | Inativa/parcial Web | Nao priorizar | Nao aplicavel | Correto manter fora. |
| Menu Agenda antigo do seeder | Parcial/Web | Nao priorizar | Nao aplicavel | Correto manter fora. |

## Prioridades recomendadas apos auditoria

1. Corrigir o contrato API Android antes de expandir UI: registrar ou remover chamadas para endpoints inexistentes em `api/v1`.
2. Atualizar `docs/funcionalidade_Aluno.md` para refletir o estado real: Catalogo, Relatorio IA, Body Analysis, Comparacao, Mensagens e Comunidade nao devem continuar como apenas `Web`.
3. Publicar API v1 para funcionalidades que ja tem tela Android: hidratacao, creditos, agenda, documentos clinicos, gamificacao, descanso ativo, profissionais e notificacoes.
4. Implementar Health Connect como nova frente real de Android, pois nao ha integracao detectada.
5. Endurecer dados clinicos no Android com biometria, permissao granular e registro de acesso antes de marcar como pronto.
6. Corrigir encoding dos arquivos afetados para UTF-8.

## Atualizacao de execucao - 2026-07-17

Foram publicados adaptadores `api/v1` para reduzir os 404 mais provaveis do Android. A lista real subiu de 49 para 79 rotas confirmadas via `php artisan route:list --path=api/v1`.

Novas areas cobertas:

- NexHydra: `hydration/status`, `hydration/entries` e exclusao de entrada.
- Creditos IA: `ai/credits`.
- NexBot mobile: `chat/history` e `chat/send`.
- Push/telemetria basica: `devices`, `notifications/unread-counts`, `client-errors`.
- Avaliacoes fisicas: `assessments`, `assessments/summary`.
- Progressao: `load-logs`.
- Sessoes de treino: `workout-sessions/active`, `workout-sessions/start` e `PATCH workout-sessions/{session}`.
- Assinaturas: `subscriptions/plans`, `subscriptions/checkout`, `subscriptions/current`, `subscriptions/cancel`.
- Agenda: busca de profissionais, agendamentos, slots e lista de espera.
- Documentos clinicos: indice `student/medical-documents`.
- Gamificacao: `student/gamification`.
- Descanso ativo: listagem, favorito e log.

Rotas que ainda exigem decisao/implementacao complementar:

- ~~`auth/google`, `auth/register`, `auth/forgot-password` e `auth/reset-password` como API mobile pura.~~ (Concluído)
- ~~`nutrition/goal`, `nutrition/meal-templates` e aplicacao de templates.~~ (Concluído)
- ~~`exercise-logs/sync`.~~ (Concluído)
- ~~downloads PDF de documentos clinicos no namespace `api/v1`.~~ (Concluído)
- ~~upload generico `uploads/nutrition-photo`.~~ (Concluído)
- ~~vinculos completos com profissionais: `student/professionals`, solicitacoes, permissoes e revogacao.~~ (Concluído)
- API mobile do modo profissional (`professional/*`).

## Parecer final

O Android esta mais avancado do que o documento indica em varias areas, mas parte desse avanco esta em estado incompleto porque o backend API v1 nao expoe todas as rotas que o app consome. Antes de adicionar novas funcionalidades, a prioridade tecnica deve ser alinhar API, repositories e documentacao. A prioridade de produto continua correta: nutricao, treino, evolucao, comunicacao e saude/wearables sao os pilares do uso diario do aluno.
