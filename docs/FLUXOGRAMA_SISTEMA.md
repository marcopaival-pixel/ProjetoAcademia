# Fluxograma do Sistema - Projeto Academia

Este documento apresenta o fluxo completo do sistema, desde o ponto de entrada do usuário até as funcionalidades principais e o encerramento da sessão. O objetivo é fornecer uma visão clara tanto para desenvolvedores quanto para gestores sobre como os dados e as interações fluem no ecossistema da aplicação (Laravel + PHP Legado).

## Fluxo de Processo (Mermaid)

```mermaid
graph TD
    %% Nós de Início e Fim
    START([Início: Acesso /index.php ou /])
    END([Fim: Logout / Sessão Encerrada])

    %% Decisões Iniciais
    START --> CHECK_AUTH{Usuário Autenticado?}
    CHECK_AUTH -- Não --> LOGIN_PAGE[Página de Login / Cadastro]
    CHECK_AUTH -- Sim --> DASH_REDIRECT[Redirecionar para Dashboard]

    %% Processo de Login
    LOGIN_PAGE --> INPUT_CRED[Inserir Credenciais / Dados]
    INPUT_CRED --> VALIDATE_FIELDS{Dados Válidos?}
    
    VALIDATE_FIELDS -- Não --> SHOW_VALID_ERROR[Exibir Erros de Validação: 422]
    SHOW_VALID_ERROR --> LOGIN_PAGE

    VALIDATE_FIELDS -- Sim --> ATTEMPT_AUTH{Sucesso na Autenticação?}
    ATTEMPT_AUTH -- Não --> SHOW_AUTH_ERROR[Erro: Email/Senha Incorretos]
    SHOW_AUTH_ERROR --> LOGIN_PAGE

    %% Direcionamento por Perfil
    ATTEMPT_AUTH -- Sim --> CHECK_ADMIN{É Administrador?}
    DASH_REDIRECT --> CHECK_ADMIN

    %% Fluxo Administrativo
    CHECK_ADMIN -- Sim --> ADMIN_PANEL[Painel Administrativo]
    subgraph Painel_Adm [Gestão e Monitoramento]
        ADMIN_PANEL --> ADM_USERS[Gerenciar Usuários / Edit]
        ADMIN_PANEL --> ADM_LOGS[Monitorar Logs e Interações IA]
        ADMIN_PANEL --> ADM_EXERCISES[Catálogo de Exercícios]
        ADMIN_PANEL --> ADM_ANNOUNCE[Publicar Avisos Globais]
    end

    %% Fluxo de Usuário Comum
    CHECK_ADMIN -- Não --> USER_DASHBOARD[Dashboard do Usuário]
    
    subgraph Dashboard_Central [Hub de Funcionalidades]
        USER_DASHBOARD --> VIEW_METRICS[Visualizar Calorias, Água e Peso]
        USER_DASHBOARD --> NAV_FEATURES{Escolher Funcionalidade}
    end

    %% Funcionalidades Principais
    subgraph Funcionalidades_Core [Funcionalidades do Sistema]
        NAV_FEATURES --> DIARY[Diário Alimentar: Registro de Macros]
        NAV_FEATURES --> EXERCISES[Treinos: Log de Cargas e Séries]
        NAV_FEATURES --> WEIGHT[Peso: Evolução e Gráficos]
        NAV_FEATURES --> REPORTS[Relatórios: PDF/CSV Mensal]
        NAV_FEATURES --> AI_CHAT[AI Coach: Chat de Consultoria]
        NAV_FEATURES --> BODY_ANALYSIS[Análise Corporal: IA e Medidas]
        NAV_FEATURES --> PAYMENTS[Upgrade Premium: MercadoPago]
    end

    %% Persistência e Banco de Dados
    DIARY --> DB[(Banco de Dados: MySQL)]
    EXERCISES --> DB
    WEIGHT --> DB
    BODY_ANALYSIS --> DB
    ADM_EXERCISES --> DB

    %% Tratamento de Erros e Feedback
    DB -.-> ERROR_HANDLING{Ocorreu Erro de BD/Servidor?}
    ERROR_HANDLING -- Sim --> LOG_ERROR[Registrar Log / Notificar Usuário]
    LOG_ERROR --> NAV_FEATURES
    ERROR_HANDLING -- Não --> SUCCESS_FEEDBACK[Atualizar Interface / Dashboard]
    SUCCESS_FEEDBACK --> USER_DASHBOARD

    %% Finalização
    USER_DASHBOARD --> LOGOUT[Solicitar Logout]
    ADMIN_PANEL --> LOGOUT
    LOGOUT --> SESSION_CLEAR[Limpar Sessão / Invalidar Token]
    SESSION_CLEAR --> END

    %% Estilização (Opcional no Mermaid)
    style START fill:#4f46e5,color:#fff
    style END fill:#ef4444,color:#fff
    style DB fill:#f59e0b,color:#000
    style ADMIN_PANEL fill:#10b981,color:#fff
    style USER_DASHBOARD fill:#3b82f6,color:#fff
```

## Legenda dos Símbolos

| Símbolo | Descrição | Uso no Sistema |
| :--- | :--- | :--- |
| **Oval (Início/Fim)** | Pontos de entrada e saída. | Acesso inicial e Logout. |
| **Retângulo** | Processo ou operação. | Registro de alimentos, cálculos de macros, edição de perfil. |
| **Losango** | Decisão ou bifurcação. | Validação de credenciais, checagem de nível (Admin vs User). |
| **Cilindro** | Banco de Dados. | MySQL (Tabelas de usuários, logs, diário, exercícios). |
| **Linha Contínua** | Fluxo principal de execução. | Sequência normal de navegação. |
| **Linha Tracejada** | Fluxo de erro ou processo secundário. | Tratamento de exceções e logs. |

## Resumo das Etapas Críticas

1.  **Validação de Dados:** O sistema utiliza `Laravel FormRequests` e validação em linha para garantir a integridade antes de qualquer inserção no banco de dados.
2.  **Tratamento de Erros:** Implementado via middlewares e blocos `try-catch`, redirecionando usuários para páginas de erro amigáveis em caso de falha no servidor (500) ou permissão negada (403).
3.  **Segurança:** Acesso protegido por `Middleware:auth`, garantindo que funcionalidades sensíveis não sejam expostas a usuários anônimos.
4.  **Encerramento:** O logout invalida a sessão PHP e regenera o token CSRF para prevenir ataques de fixação de sessão.
