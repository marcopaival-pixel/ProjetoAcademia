# Levantamento de Funcionalidades - Projeto Academia

Este documento apresenta a listagem completa de menus, submenus e funcionalidades mapeadas no sistema.

---

## 1. Portal do Profissional (Menu do Profissional)

Estas são as funcionalidades que o profissional utiliza para gerenciar seus alunos.

| Nome do Menu | Submenu | Descrição da Funcionalidade | Perfil de Acesso | Módulo |
| :--- | :--- | :--- | :--- | :--- |
| Dashboard | - | Visão geral de alunos e indicadores. | Profissional | Core / Geral |
| Pacientes | - | Gestão dos alunos vinculados ao profissional. | Profissional | Profissional |
| Treinos | - | Criação e gestão de treinos. | Profissional | Treinos |
| Progressão | Planos | Planejamento e periodização de treinos. | Profissional | Treinos |
| Gráficos | - | Análise de evolução dos alunos. | Profissional | Treinos |
| Nutrição | - | Gestão de dieta e metas nutricionais. | Profissional | Nutrição |
| Avaliações | - | Registro de avaliações físicas. | Profissional | Avaliações |
| Peso | - | Monitoramento do peso dos alunos. | Profissional | Evolução |
| Hidratação | - | Monitoramento da ingestão de água. | Profissional | Saúde |
| Mensagens | Mensagens Diretas | Comunicação com alunos. | Profissional | Comunicação |
| Correio Interno | - | Envio de mensagens internas. | Profissional | Comunicação |
| Agenda | - | Gestão de atendimentos e horários. | Profissional | Agenda |
| Relatórios | - | Emissão de relatórios em PDF. | Profissional | Relatórios |
| IA Wizard | - | Geração automática de treinos e prescrições. | Profissional | IA / Pro |
| Branding | - | Personalização da marca do profissional. | Profissional | Profissional |

---

## 2. Painel Administrativo (Portal Admin)

Destinado à gestão global da plataforma e controle de subscrições SaaS.

| Nome do Menu | Submenu | Descrição da Funcionalidade | Perfil de Acesso | Módulo |
| :--- | :--- | :--- | :--- | :--- |
| **Visão Geral** | - | Dashboard administrativo com estatísticas de negócio e saúde do servidor. | Admin | Gestão |
| **Gestão de Usuários**| - | Controle total de contas: criar, banir, reset de senha e exportação. | Admin | Segurança |
| **Cadastros Pendentes**| - | Fluxo de moderação para aprovação de novos profissionais registados. | Admin | Gestão |
| **Perfis e Permissões**| - | Configuração de Roles (RBAC) e matriz de visibilidade de menus. | Admin | Segurança |
| **Planos Assinatura** | - | Manutenção do catálogo de planos para profissionais. | Admin | Comercial |
| **Config. Gerais** | - | Definições de sistema, temas, logs e manutenção global. | Admin | Sistema |
| **Config. Financeiras**| - | Configuração de Gateways de Pagamento (Mercado Pago, Stripe). | Admin | Financeiro |
| **Config. E-mail** | **Provedores** | Configuração de SMTP e APIs de envio (SendGrid/Mailgun). | Admin | Comunicação |
| | **Templates** | Editor de modelos para e-mails transacionais. | Admin | Comunicação |
| | **Logs** | Monitorização de entregas, falhas e bounced emails. | Admin | Comunicação |
| **APIs Externas** | - | Gestão de chaves e testes de serviços externos. | Admin | Integração |
| **PDF Suite** | **Gerador** | Ferramenta para emissão manual de documentos assinados. | Admin | Documentos |
| | **Templates** | Gestão de layouts de documentos (Dompdf). | Admin | Documentos |
| | **Histórico** | Auditoria de todos os ficheiros PDF gerados pelo sistema. | Admin | Documentos |
| **Segurança / LGPD** | - | Gestão de consentimentos, incidentes e descarga de dados (PII). | Admin | Segurança |
| **Dash Comercial** | - | Indicadores de vendas, conversão de leads e faturamento. | Admin | Comercial |
| **Gestão de Leads** | - | Fluxo de CRM: Funil de vendas e acompanhamento de leads. | Admin | Comercial |
| **Propostas** | - | Criação de propostas comerciais enviadas por token ao cliente. | Admin | Comercial |
| **Gestão Academia** | **Módulos** | Organização da "Academia NexShape" (Conteúdo de Vídeo). | Admin | Treinamento |
| | **Aulas** | Gestão de vídeo-lições e materiais didáticos por módulo. | Admin | Treinamento |
| **Exercícios** | - | Catálogo mestre de exercícios disponível para todos os treinos da app. | Admin | Treinos |
| **Logs de Erros** | - | Visualizador em tempo real de exceções e erros críticos. | Admin | Monitoramento |
| **Monitoramento IA** | - | Dashboard de consumo de tokens e latência das APIs de IA. | Admin | IA |
| **OmniChannel** | - | Painel multi-canal (WhatsApp/Messenger) com gestão de bots. | Admin | Comunicação |

---

👤 Menu do Cliente (Aluno)

Essas são as funcionalidades que o cliente/aluno acessa no aplicativo ou portal.

Nome do Menu	Submenu	Descrição da Funcionalidade	Perfil de Acesso	Módulo
Dashboard	-	Visão geral de métricas, progresso físico e atalhos rápidos.	Aluno	Core / Geral
Diário	-	Registo diário de alimentação e atividades extra-treino.	Aluno	Diário
Treinos	-	Visualização e execução de treinos e histórico.	Aluno	Treinos
Progressão	Planos	Seleção de metas e acompanhamento de evolução.	Aluno	Treinos
Gráficos	-	Visualização da evolução física.	Aluno	Treinos
Nutrição	-	Controle de dieta e macronutrientes.	Aluno	Nutrição
Avaliações	-	Visualização das avaliações físicas.	Aluno	Avaliações
Peso	-	Registro e acompanhamento do peso.	Aluno	Evolução
Hidratação	-	Controle da ingestão de água.	Aluno	Saúde
Chat IA	-	Assistente virtual para treinos e dieta.	Aluno	IA
Ranking	-	Classificação gamificada entre alunos.	Aluno	Gamification
Descanso Ativo	-	Sugestões de recuperação física.	Aluno	Saúde
Mensagens	Mensagens Diretas	Comunicação com profissionais.	Aluno	Comunicação
Correio Interno	-	Sistema interno de mensagens formais.	Aluno	Comunicação
Agenda	-	Visualização de horários e eventos.	Aluno	Agenda
Pagamentos	-	Consulta e pagamento de mensalidade.	Aluno	Financeiro
Presença	-	Registro de presença na academia.	Aluno	Gestão
*Documento atualizado em 18/04/2026 para refletir o mapeamento total do sistema.*
