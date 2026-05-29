# Fabrica

Ecossistema de agentes para **especificar** (System Builder), **auditar** (System Audit) e **evoluir** (Auto Evolution) software, com artefatos JSON validados por schema.

## Início rápido

```bash
npm install
npm run validate -- outputs/_smoke
npm run validate -- outputs/_smoke-audit
npm run validate -- outputs/_smoke-evolution
```

Documentação completa: [AGENTS.md](AGENTS.md). **Guia passo a passo** (orquestradores e cada agente): [docs/GUIA-EXECUCAO-ORQUESTRADORES-E-AGENTES.md](docs/GUIA-EXECUCAO-ORQUESTRADORES-E-AGENTES.md). CI no GitHub Actions: [.github/workflows/validate.yml](.github/workflows/validate.yml).

## Regras no Cursor

| Fábrica | Orquestrador (regra) |
|--------|----------------------|
| System Builder | `.cursor/rules/sbo-00-orchestrator.mdc` |
| System Audit | `.cursor/rules/saf-00-audit-orchestrator.mdc` |
| Auto Evolution | `.cursor/rules/aef-00-auto-evolution-orchestrator.mdc` |

Agentes especializados: `sbo-01`…`sbo-14`, `saf-01`…`saf-07`, `aef-01`…`aef-06` em `.cursor/rules/`.

## Pastas de saída

Use **uma pasta por run** e **não misture** artefatos de Builder, Audit e Evolution no mesmo diretório (há colisão de nomes como `00-*.json` / `99-*.json`). Ex.: `outputs/2026-05-13-produto-builder/`, `outputs/2026-05-13-produto-audit/`, `outputs/2026-05-13-produto-evol/`.

## Schemas

Contrato de envelope: `schemas/_envelope.json`. Payloads: `schemas/*.json`.
