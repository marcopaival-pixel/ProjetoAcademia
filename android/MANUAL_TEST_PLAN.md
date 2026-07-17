# Plano de testes manual — v1.8.1

Executar num **build release** (AAB/APK release) contra **backend HTTPS** de staging ou produção.

**Testadores:** 1 conta **aluno** + 1 conta **profissional** (ideal: 1 conta com ambos os roles).

Registar: dispositivo, Android version, data, resultado (OK / FALHA + notas).

---

## Pré-condições

- [ ] Contas criadas e activas no Laravel
- [ ] Profissional com aluno vinculado (`pacientes`)
- [ ] `google-services.json` no build (se testar push)
- [ ] App instalado via internal testing ou APK release

---

## Persona A — Aluno

### A1 — Autenticação

| Passo | Acção | Esperado |
|-------|--------|----------|
| 1 | Abrir app | Splash NexShape |
| 2 | Login e-mail/senha válidos | Entra no modo aluno |
| 3 | Perfil → ver nome e e-mail | Dados correctos |
| 4 | Matar app e reabrir (bloqueio ON) | PIN/biometria ou ecrã bloqueio |
| 5 | Perfil → Sair | Volta ao login; relogin funciona |

### A2 — Treino

| Passo | Acção | Esperado |
|-------|--------|----------|
| 1 | Tab Treino | Lista planos |
| 2 | Abrir plano | Exercícios visíveis |
| 3 | Registar série (se disponível) | Grava local/remoto |
| 4 | Modo avião → registar → rede ON | Sync sem perda (WorkManager) |

### A3 — Evolução

| Passo | Acção | Esperado |
|-------|--------|----------|
| 1 | Tab Evolução → Medidas | Lista avaliações |
| 2 | Adicionar medida | 201 / aparece na lista |
| 3 | Tab Fotos → adicionar da galeria | Upload OK; imagem carrega |

### A4 — Agenda

| Passo | Acção | Esperado |
|-------|--------|----------|
| 1 | Tab Agenda | Profissionais vinculados |
| 2 | Ver slots + agendar | Consulta criada |

### A5 — Assinatura

| Passo | Acção | Esperado |
|-------|--------|----------|
| 1 | Perfil → planos | Lista `/subscriptions/plans` |
| 2 | Plano grátis → Ativar | Mensagem sucesso |
| 3 | Plano pago → Assinar (se gateway activo) | Browser abre; voltar ao app |
| 4 | Deep link ou botão "Já paguei" | Mensagem status; sem crash |

### A6 — Chat / Nutrição (smoke)

| Passo | Acção | Esperado |
|-------|--------|----------|
| 1 | Nutrição → dia actual | Carrega ou vazio |
| 2 | Chat → enviar mensagem | Resposta ou histórico |

---

## Persona B — Profissional

### B1 — Modo Pro

| Passo | Acção | Esperado |
|-------|--------|----------|
| 1 | Perfil → Modo Profissional | Tabs Pro visíveis |
| 2 | Painel | Stats carregam |

### B2 — Alunos

| Passo | Acção | Esperado |
|-------|--------|----------|
| 1 | Tab Alunos | Lista vinculados |
| 2 | Seleccionar aluno | Banner "Aluno activo" |
| 3 | Tab Clínico activa | Acompanhamento disponível |

### B3 — Clínico

| Passo | Acção | Esperado |
|-------|--------|----------|
| 1 | Treinos → listar | Planos do aluno |
| 2 | Prescrever treino rápido | Plano criado |
| 3 | Avaliações → nova | Registo OK |
| 4 | Fotos → FAB upload | Foto na grelha |

### B4 — Agenda e alertas

| Passo | Acção | Esperado |
|-------|--------|----------|
| 1 | Tab Agenda | Consultas do dia |
| 2 | Tab Alertas | Lista alertas |
| 3 | Marcar lido | Estado actualiza |

### B5 — Push (requer FCM)

| Passo | Acção | Esperado |
|-------|--------|----------|
| 1 | Criar `HealthAlert` no backend para aluno vinculado | Push no dispositivo Pro |
| 2 | Toque na notificação | App abre (smoke) |

---

## Persona C — Segurança release

| # | Teste | OK |
|---|--------|-----|
| 1 | Build **release** — login só HTTPS (sem XAMPP HTTP) | ☐ |
| 2 | Bloqueio PIN: activar, background, desbloquear | ☐ |
| 3 | Bloqueio biométrico (se hardware) | ☐ |
| 4 | Logout revoga sessão (login noutro device ou token inválido) | ☐ |

---

## Critério de saída (go / no-go internal testing)

**GO** se:

- A1, A2, A3, B1, B2, B3 passam sem falha crítica
- Zero crash em fluxos principais
- Login release HTTPS OK

**NO-GO** se:

- Crash em cold start ou login release
- Perda de dados de treino após sync
- Push obrigatório para negócio e não funciona (documentar workaround)

---

## Registo de execução

| Data | Testador | Build (versionCode) | Backend URL | Resultado |
|------|----------|---------------------|-------------|-----------|
| | | 4 / 1.8.1 | | |

Notas:
