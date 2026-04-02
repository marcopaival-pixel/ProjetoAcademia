# Manual rápido — pedidos à IA (funcionalidade nova ou correção)



Este ficheiro é um **guia curto** com modelos de mensagem. A explicação completa está em **`MANUAL_AGENTE_GOVERNANCA.md`**, secção **2**.



O **mapa do agente** (o que é núcleo, o que é só Cursor ou só `.github`) está em **`AGENTS.md`**, secções **«Estrutura do agente no repositório»** e **«Manuais e replicação»**.



---



## Ritual de 10 segundos (cada conversa nova)



1. Workspace na **raiz** do repo (onde está `AGENTS.md`).

2. Primeira linha: **«Segue o AGENTS.md.»**

3. **Cursor:** use `@AGENTS.md` e ficheiros relevantes; as regras em **`.cursor/rules/`** aplicam-se automaticamente quando edita ficheiros que coincidem com os *globs*.

4. **VS Code + Copilot:** garanta **`.github/copilot-instructions.md`** no projeto; reforce no chat que deve cumprir `AGENTS.md`.

5. **Antigravity:** cite `AGENTS.md` e **`GEMINI.md`** em tarefas maiores.



---



## Modelo — funcionalidade nova



Copie e preencha os `[colchetes]`:



```text

Segue o AGENTS.md.



Objetivo: [uma frase — o que o utilizador passa a conseguir]



Comportamento: [validação, permissões, erros esperados, dados]



Onde encaixa: [rotas, módulos, ecrãs já existentes — referências a ficheiros com @ ou caminhos]



Fora de âmbito: [o que NÃO deve ser alterado neste pedido]



Aceitação: [como testar — passos ou testes automáticos]



Antes de codificares: resume o plano em bullets, lista ficheiros a criar/alterar e confirma que não vais contra o AGENTS.md (BD, remoção de funcionalidades, dependências novas).

```



---



## Modelo — correção de bug / código



```text

Segue o AGENTS.md.



Bug: [sintoma — mensagem, URL, o que falha]



Reproduzir: [passos numerados]



Esperado: [comportamento correto]

Atual: [o que acontece]



Contexto: [@ficheiros ou pastas que suspeitas]



Restrições: correção mínima; sem alterar esquema de BD / sem novas dependências [ajuste conforme necessário].



Antes de alterares vários ficheiros, explica a causa provável e propõe a mudança mínima.

```



---



## Frases úteis durante a conversa



- *«Não inventes tabelas, rotas ou classes — verifica no repositório ou pergunta-me.»*

- *«Migrações ou dados destrutivos: só com a minha autorização explícita.»*

- *«Lista o impacto desta alteração em rotas, permissões e utilizadores.»*



---



## Depois que a IA responder



- Rever o **diff** (só o pedido?).

- Correr **testes / Pint / build** do projeto.

- **Commit** ou PR; mudanças sensíveis: revisão humana.



---



## Onde está o resto

| Documento | Conteúdo |
|-----------|----------|
| **`AGENTS.md`** | **Núcleo** + **Estrutura do agente** + **Manuais e replicação** + **Capacidades de governança** (validação, log, risco, padrões, limites) + regras |
| **`docs/HISTORICO_DECISOES_IA.md`** | Template opcional para **histórico de decisões** da IA |
| **`MANUAL_AGENTE_GOVERNANCA.md`** | Instalação, secção **2** (pedidos), resolução de problemas |
| **`.cursor/rules/*.mdc`** | Regras **só no Cursor** |
| **`.github/copilot-instructions.md`** | **Copilot** → `AGENTS.md` |
| **`GEMINI.md`** | **Antigravity** |
