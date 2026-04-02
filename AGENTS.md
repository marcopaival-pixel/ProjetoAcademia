# Agente de governança do projeto

Este ficheiro é a **fonte única de verdade** para orientação da IA. O agente é o conjunto **deste documento** mais os ficheiros indicados abaixo (Cursor e GitHub são opcionais conforme a ferramenta que usar).

---

## Estrutura do agente no repositório

| Local | Papel no agente |
|-------|------------------|
| **`AGENTS.md`** (este ficheiro) | Núcleo: princípios, regras obrigatórias, anti-alucinação, fluxo de trabalho e regras transversais. **Toda a IA deve alinhar-se aqui.** |
| **`GEMINI.md`** (raiz) | Extensão para **Google Antigravity**; reforça este documento quando usar esse IDE. |
| **`.cursor/rules/`** | **Só para Cursor:** ficheiros `*.mdc` com instruções automáticas (governança sempre ativa + regras por tipo/caminho de ficheiro). Se não usar Cursor, esta pasta pode não existir ou ser ignorada; o núcleo continua a ser `AGENTS.md`. |
| **`.github/`** | **VS Code / GitHub Copilot:** tipicamente `copilot-instructions.md`, que manda seguir `AGENTS.md`. Podem existir outros ficheiros (Actions, Dependabot, etc.) alinhados ao projeto — não fazem parte do “texto” do agente, mas da infra do repo. |
| **`docs/HISTORICO_DECISOES_IA.md`** | **Opcional:** registo manual de decisões relevantes assistidas por IA (complementa histórico de chat e Git). |

**Resumo por ferramenta:** **Cursor** → carrega `.cursor/rules/*.mdc` **e** deve cumprir `AGENTS.md`. **Copilot (VS Code)** → seguir `.github/copilot-instructions.md` **e** `AGENTS.md`. **Antigravity** → `AGENTS.md` + `GEMINI.md`. O detalhe de cada regra `.mdc` está nos próprios ficheiros e no **`MANUAL_AGENTE_GOVERNANCA.md`**.

---

## Manuais e replicação

- **Manual de utilização** (instalação por ferramenta): **`MANUAL_AGENTE_GOVERNANCA.md`**
- **Modelos de pedidos à IA** (funcionalidade nova / correção): **`MANUAL_PEDIDOS_IA.md`** ou secção **2** do manual completo
- **Copiar o agente para outro projeto** (PowerShell): **`templates/governanca/`** (`README.md` + `copiar-governancia.ps1`)
- **Histórico opcional de decisões da IA** (template editável): **`docs/HISTORICO_DECISOES_IA.md`**

---

## Capacidades de governança (o que este pacote cobre)

As capacidades abaixo estão **implementadas neste pacote** na forma indicada. Onde couber **automação adicional** (CI, extensões, produto da IA), isso fica a cargo do repositório de aplicação e das ferramentas que utilizar.

| Capacidade | Implementação neste pacote | Limite / complemento típico |
|------------|----------------------------|------------------------------|
| **Validação automática de código** | Políticas em `AGENTS.md` (qualidade, testes, não desligar analisadores); regras **`.cursor/rules/*.mdc`** para ficheiros de config (Pint, PHPStan, ESLint, `phpunit.xml`, CI, etc.) que **orientam a IA** a respeitar o pipeline do projeto. | A **execução** real da validação (Pint, PHPUnit, `npm run build`, workflows em `.github/workflows`) depende do **código da aplicação** e do CI que configurar — este repositório de governança não substitui esses comandos. |
| **Log das respostas da IA** | O **registo técnico** das conversas fica nas **ferramentas** (Cursor, Copilot, Antigravity). Para decisões que afetem o produto, recomenda-se **commits/PRs** com mensagem clara e, se a equipa quiser, o ficheiro **`docs/HISTORICO_DECISOES_IA.md`**. | Não há servidor de log próprio; exporte ou copie trechos relevantes se a política interna o exigir. |
| **Bloqueio de respostas perigosas** | **Regras obrigatórias** e **transversais** (segurança, BD, operações destrutivas, segredos) que a IA **deve** seguir; instruções em `GEMINI.md` e `copilot-instructions.md`. | **Não** existe filtro automático que impeça o modelo de gerar texto: o **bloqueio efetivo** é **revisão humana**, **não aplicar** patches perigosos e **exigir confirmação** para comandos destrutivos (alinhar ao manual de pedidos). |
| **Análise de risco antes de executar** | **Fluxo obrigatório** antes de desenvolver (secção abaixo); modelos em **`MANUAL_PEDIDOS_IA.md`** (plano e ficheiros **antes** de codificar); regra Cursor `governanca-projeto.mdc` a lembrar `AGENTS.md`. | Não é um motor de scoring de risco; é **processo** + **comportamento esperado do modelo** quando segue as regras. |
| **Histórico de decisões da IA** | Template estruturado em **`docs/HISTORICO_DECISOES_IA.md`** para registo manual das decisões relevantes (data, pedido, decisão, ficheiros). | Opcional; a equipa define se preenche e com que frequência. |
| **Verificador de padrão do projeto** | Dezenas de regras **`.mdc`** por pasta/tipo de ficheiro (Laravel, front-end, Docker, CI) que **condicionam** as sugestões da IA no **Cursor**. | No **Copilot/Antigravity** o equivalente é cumprir **`AGENTS.md`**; **linters no disco** continuam a ser os do projeto (`pint`, `phpstan`, ESLint, etc.). |

**Resumo:** este pacote implementa **governança por política, regras de contexto e processo**; **não** substitui CI, moderação em tempo real da API do fornecedor de IA nem aprovação humana.

## Stack e contexto

- **Linguagem / framework**: PHP com **Laravel** (quando aplicável ao repositório).
- **Ambiente local**: código deve ser compatível com execução típica em **XAMPP** (PHP, Apache, MySQL/MariaDB), salvo indicação contrária no projeto.
- **Princípio**: não assumir versões de PHP/Laravel nem pacotes sem verificar `composer.json`, documentação do projeto ou código existente.

---

## Fluxo obrigatório antes de desenvolver

1. **Entender o pedido** e confirmar se está dentro do escopo já definido para o sistema (arquitetura, módulos, ADRs ou documentos de desenho existentes).
2. **Inspecionar o código e a configuração reais** (ficheiros, rotas, modelos, migrações, `composer.json`, `.env.example`) — não inventar APIs, tabelas ou ficheiros.
3. **Aplicar** os princípios, regras obrigatórias e regras anti-alucinação abaixo.
4. Se faltar informação crítica, **pedir esclarecimento** em vez de supor.
5. Antes de alterar código existente relevante, **explicar** o que será mudado e porquê.

---

## Princípios orientadores

- Segurança em primeiro lugar.
- Código simples e legível.
- Reutilização de código.
- Baixo acoplamento.
- Alta coesão.
- Manter compatibilidade com versões e contratos existentes.
- Evitar dependências desnecessárias.
- Manter performance e estabilidade do sistema.

---

## Regras obrigatórias

- Nunca inventar funções, bibliotecas ou tabelas.
- Nunca assumir que algo existe sem verificar no repositório ou na documentação do projeto.
- Nunca alterar código existente sem explicar antes o impacto e a abordagem.
- Nunca remover funcionalidades existentes sem autorização explícita.
- Nunca modificar base de dados (migrações, esquema, dados) sem autorização explícita.
- Nunca gerar código fora do escopo solicitado.
- Nunca criar soluções complexas quando uma simples resolve o problema.
- Nunca sobrescrever ficheiros críticos sem confirmação (ex.: configurações sensíveis, núcleo de segurança).
- Nunca gerar código inseguro (validação, autorização, exposição de segredos, SQL inseguro, etc.).
- Nunca ignorar validação de dados de entrada.
- Nunca executar operações destrutivas sem confirmação explícita do utilizador.
- Nunca gerar código duplicado quando existe abstração ou serviço reutilizável.
- Nunca alterar a estrutura global do sistema sem análise e alinhamento com a arquitetura definida.
- Nunca criar dependências externas sem necessidade comprovada.
- Nunca ignorar tratamento de erros e cenários de falha.
- Nunca gerar código que não funcione ou não seja testável no ambiente **XAMPP** acordado para o projeto.
- Nunca assumir que o sistema usa Laravel (ou outro framework) se isso não estiver evidente no repositório — verificar primeiro.
- Nunca gerar código que quebre compatibilidade com versões ou integrações já acordadas.
- Nunca versionar segredos (`.env` real, `auth.json` com tokens, chaves privadas, passwords em CI).
- Nunca desativar ou contornar controlos de segurança em CI/análise estática sem documentação e acordo explícito.

---

## Código limpo e seguro (transversal a stacks)

Aplicável a **qualquer** sistema ou linguagem no repositório, além das regras Laravel/PHP acima.

- **Segredos e configuração**: cofres, variáveis de CI, `.env` ignorado; rotação se houve vazamento; nunca logar credenciais ou PII desnecessária.
- **Dependências e supply chain**: preferir versões fixas ou ranges conscientes; rever `composer audit` / `npm audit` quando relevante; não adicionar pacotes abandonados ou de origem duvidosa sem validação.
- **Entrada, saída e autorização**: validar entradas; codificar/escapar saídas; menor privilégio em permissões, APIs e contas de serviço.
- **Dados pessoais**: minimizar coleta e retenção; mascarar em logs; respeitar políticas do projeto e legislação aplicável quando indicado.
- **Erros e observabilidade**: respostas genéricas ao utilizador final; detalhes técnicos só em logs seguros; métricas sem expor dados sensíveis.
- **CI/CD**: pipelines reproduzíveis; permissões mínimas nos runners; artefactos sem segredos embutidos.
- **Qualidade**: corrigir causas em vez de desligar linters/analisadores; manter formatação e convenções do repo (EditorConfig, Pint, Prettier, etc.).

---

## Regras anti-alucinação

- Se não souber, dizer que não sabe.
- Se faltar informação, pedir esclarecimento.
- Se houver dúvida sobre estrutura de pastas, módulos ou dados, perguntar antes.
- Se o código ou o comportamento não puder ser garantido, declarar a incerteza.
- Nunca inventar caminhos de ficheiros.
- Nunca inventar nomes de tabelas.
- Nunca inventar nomes de campos ou colunas.
- Nunca inventar endpoints ou APIs.
- Nunca inventar configurações ou variáveis de ambiente.
- Nunca inventar padrões ou convenções inexistentes no projeto.
- Trabalhar com dados e exemplos reais fornecidos pelo utilizador ou constantes no repositório.

---

## Boas práticas Laravel / PHP (alinhamento com governança)

- Respeitar camadas já usadas no projeto (Controllers finos, lógica em Services/Actions, Form Requests para validação, Policies para autorização).
- Usar Eloquent e migrações existentes; não criar tabelas ou colunas “de cabeça”.
- Manter convenções PSR e estilo do projeto (Laravel Pint, PHP-CS-Fixer, etc., se configurados).

---

## Conflitos entre pedido e regras

Se um pedido do utilizador violar segurança, integridade de dados ou regras obrigatórias acima, **explicar o risco** e pedir confirmação ou alternativa segura — não cumprir o pedido de forma insegura apenas para agradar.

---

## Fecho de recomendações (checklist mental)

Antes de considerar uma alteração **concluída**, quando aplicável ao projeto: escopo respeitado; código legível e sem duplicação desnecessária; validação e autorização cobertas; segredos fora do Git; testes ou verificação manual descritos; CI/analisadores alinhados; documentação ou `.env.example` atualizados se mudou contrato ou configuração.
