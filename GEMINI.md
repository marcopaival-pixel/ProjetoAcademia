# Antigravity — extensão ao agente de governança

O **`AGENTS.md`** na raiz continua a **fonte única** de princípios, regras obrigatórias e anti-alucinação. A tabela **«Estrutura do agente no repositório»** no início de `AGENTS.md` explica o papel de cada pasta (`GEMINI.md`, `.cursor/rules/`, `.github/`). Este ficheiro **reforça** o comportamento no **Antigravity**; as regras **`.mdc`** do Cursor **não** são carregadas aqui — todo o critério essencial deve estar em `AGENTS.md`.

Para instalar este pacote em projeto **novo ou existente** e usar em VS Code / Cursor / Antigravity, consulte **`MANUAL_AGENTE_GOVERNANCA.md`**. Para **cada pedido** de funcionalidade nova ou correção, use os modelos em **`MANUAL_PEDIDOS_IA.md`** (ou a secção **2** do manual completo). Em Windows, pode usar o script **`templates/governanca/copiar-governancia.ps1`** para copiar ficheiros para outra pasta (ver `templates/governanca/README.md`).

## Antes de alterar o projeto

1. Carregar e seguir `AGENTS.md` (não contornar regras de segurança ou de BD).
2. Preferir leitura de ficheiros reais do workspace a suposições sobre rotas, modelos ou esquema.
3. Comandos de terminal ou alterações destrutivas: alinhar com `AGENTS.md` (confirmação do utilizador quando aplicável).

## Transversal (qualquer stack)

A secção **«Código limpo e seguro (transversal a stacks)»** em `AGENTS.md` aplica-se a **toda** alteração (PHP, JavaScript, Docker, YAML de CI, etc.): segredos, dependências, validação, logs, CI e qualidade estática.

## Laravel / PHP neste repositório

Quando `composer.json` indicar Laravel, aplicar as boas práticas já descritas em `AGENTS.md` e **reutilizar** padrões existentes no código (namespaces, pastas `app/`, convenções do projeto).
