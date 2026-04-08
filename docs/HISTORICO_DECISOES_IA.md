# Histórico de decisões da IA (opcional)

Use este ficheiro para registar **decisões relevantes** tomadas com apoio de IA (arquitetura, segurança, alterações amplas), além do que já fica em **commits** e **PRs**.

Apague este bloco de instruções quando começar a usar, ou mova-o para o README interno da equipa.

---

## Entradas (modelo)

Copie o bloco abaixo para cada registo:

```markdown
### YYYY-MM-DD — [título curto]

- **Pedido / contexto:** …
- **Ferramenta:** Cursor | Copilot | Antigravity | outro
- **Decisão ou resultado:** …
- **Ficheiros / PR:** …
- **Riscos / notas:** …
- **Validado por:** … (opcional)
```

---

## Registos

### 2026-04-07 — Início da Transformação para SaaS Pro

- **Pedido / contexto:** Transformar o ProjetoAcademia em plataforma SaaS profissional para nutrólogos e personals.
- **Ferramenta:** Antigravity
- **Decisão ou resultado:** Definida visão estratégica (Multi-tenancy, Módulos especializados, IA Hub). Implementado o primeiro "Portal do Profissional" com interface premium (Glassmorphism/Dark Mode).
- **Ficheiros / PR:** `docs/SAAS_VISION_ARCH.md`, `Professional/DashboardController.php`, `resources/views/professional/dashboard.blade.php`, `web.php`.
- **Riscos / notas:** Necessidade futura de refatorar o Model User para suportar Tenancy real.
- **Validado por:** —

---

### (exemplo) 2026-04-01 — Estrutura inicial do agente de governança

- **Pedido / contexto:** Definir pacote AGENTS + regras Cursor + manuais.
- **Ferramenta:** Cursor
- **Decisão ou resultado:** Adotado `AGENTS.md` como núcleo; `.cursor/rules` e `.github/copilot-instructions.md` como extensões por ferramenta.
- **Ficheiros / PR:** raiz do repo `ProjetoPiloto`
- **Riscos / notas:** Revisar quando o stack da aplicação Laravel existir no mesmo repo ou noutro.
- **Validado por:** —

---

*(Adicione novas entradas acima desta linha, do mais recente para o mais antigo, ou inverta a convenção da equipa.)*
