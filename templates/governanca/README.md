# Template — pacote de governança para copiar

Este diretório serve para **replicar** o agente de governança (`AGENTS.md`, regras Cursor, Copilot, Antigravity) noutro repositório.

O **mapa oficial** do que é núcleo vs `.cursor/rules` vs `.github` está no início de **`AGENTS.md`** («Estrutura do agente no repositório»).

## Documentação completa

Leia **`MANUAL_AGENTE_GOVERNANCA.md`** na **raiz** deste repositório (não dentro de `templates/`).

## Opção A — Script PowerShell (Windows)

Na pasta deste template:

```powershell
Set-Location "c:\caminho\para\ProjetoPiloto\templates\governanca"
.\copiar-governancia.ps1 -Destino "c:\caminho\para\OOutroProjeto"
```

Parâmetros opcionais:

| Parâmetro | Descrição |
|-----------|-----------|
| `-Destino` | **Obrigatório.** Raiz do projeto de destino. |
| `-Origem` | Raiz do repositório que **contém** `AGENTS.md`. Por omissão: dois níveis acima deste script (`ProjetoPiloto`). |
| `-SemManual` | Não copia `MANUAL_AGENTE_GOVERNANCA.md` nem `MANUAL_PEDIDOS_IA.md` (só pacote “runtime”). |

O script cria `Destino\.cursor\rules\` e `Destino\.github\` se necessário.

## Opção B — Cópia manual

Da **raiz** do repositório fonte, copie para a raiz do destino:

- `AGENTS.md`
- `GEMINI.md`
- `MANUAL_AGENTE_GOVERNANCA.md` (recomendado para a equipa)
- `MANUAL_PEDIDOS_IA.md` (modelos de mensagem à IA)
- `docs\HISTORICO_DECISOES_IA.md` (opcional — histórico de decisões)
- Pasta `.cursor\rules\` (todos os `.mdc`)
- `.github\copilot-instructions.md`

## Ficheiros incluídos pelo script

Ver comentário no topo de `copiar-governancia.ps1` ou a secção “10” do manual.

## Conflitos no destino

Se já existirem `.cursor\rules\`, o script **sobrescreve** ficheiros com o mesmo nome. Faça backup ou use controlo de versões antes de correr.
