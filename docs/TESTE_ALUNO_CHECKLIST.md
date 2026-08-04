# Checklist executável — Testes finais do perfil Aluno

Use este documento junto com o plano completo de QA. Rotas reais do NexShape já estão preenchidas.

**Ambiente:** Homologação / Local  
**Versão:** _(preencher)_  
**Responsável:** _(preencher)_  
**Data:** _(preencher)_

---

## Preparação (executar uma vez)

```powershell
cd c:\Projetos\PaivatechSolutions\NexShape
.\scripts\qa-aluno-setup.ps1 -RunSmoke
cd backend
php artisan serve --host=0.0.0.0 --port=8000
```

**Senha de todos os usuários QA:** `Teste@123`

| Chave | E-mail | Cenário |
|-------|--------|---------|
| `free` | `aluno.free@test.nexshape` | Free, sem vínculo |
| `premium` | `aluno.premium@test.nexshape` | Premium, sem vínculo |
| `linked_pro` | `aluno.vinculado.pro@test.nexshape` | Vinculado a profissional |
| `linked_clinic` | `aluno.vinculado.clinica@test.nexshape` | Vinculado a clínica |
| `aluno_paciente` | `aluno.paciente@test.nexshape` | Aluno + paciente |
| `expired` | `aluno.expirado@test.nexshape` | Assinatura expirada |
| `incomplete` | `aluno.incompleto@test.nexshape` | Cadastro incompleto |
| `new` | `aluno.novo@test.nexshape` | Sem histórico |
| `complete` | `aluno.completo@test.nexshape` | Histórico completo |
| profissional | `prof.qa@test.nexshape` | Profissional de apoio |

**Não usar** contas admin (`master@academia.com`, `teste@nexshape.com.br`).

---

## Legenda

- [ ] Pendente
- [x] Aprovado
- [!] Reprovado — registrar ID `ALU-XXX` no final

**Severidade:** B = Bloqueador · C = Crítico · M = Médio · L = Baixo

---

## 1. Cadastro e autenticação

| # | Teste | Usuário | Web | API | OK |
|---|-------|---------|-----|-----|----|
| 1.1 | Cadastro novo aluno | _(novo)_ | `GET/POST /register` | `POST /api/v1/auth/register` | [ ] |
| 1.2 | Login e-mail/senha | `complete` | `GET/POST /login` | `POST /api/v1/auth/token` | [ ] |
| 1.3 | Recuperação de senha | qualquer | `GET/POST /forgot-password` | `POST /api/v1/auth/forgot-password` | [ ] |
| 1.4 | Reset de senha | token e-mail | `GET/POST /reset-password/{token}` | `POST /api/v1/auth/reset-password` | [ ] |
| 1.5 | Login Google | _(conta Google)_ | `GET /auth/google` | `POST /api/v1/auth/google` | [ ] |
| 1.6 | E-mail duplicado | existente | `POST /register` | `POST /api/v1/auth/register` → 409/422 | [ ] |
| 1.7 | Senha fraca | novo | validação frontend + API | 422 | [ ] |
| 1.8 | Cadastro incompleto bloqueia fluxo | `incomplete` | redireciona onboarding | `GET /api/v1/me` | [ ] |

**Erros encontrados:** _______________________________________________

---

## 2. Login, sessão e segurança

| # | Teste | Usuário | Resultado esperado | OK |
|---|-------|---------|-------------------|----|
| 2.1 | Senha incorreta | `complete` | Mensagem clara, sem vazar dados | [ ] |
| 2.2 | Logout web | `complete` | `POST /logout` → login | [ ] |
| 2.3 | Revogar token API | `complete` | `DELETE /api/v1/auth/token` → 401 depois | [ ] |
| 2.4 | Refresh token | `complete` | `POST /api/v1/auth/token/refresh` | [ ] |
| 2.5 | Ping sessão | `complete` | `POST /api/v1/session/ping` | [ ] |
| 2.6 | Página interna sem auth | anônimo | redirect `/login` ou 401 | [ ] |
| 2.7 | Aluno → rota profissional | `complete` | `GET /api/v1/professional/patients` → 403 | [ ] |
| 2.8 | Aluno → rota admin | `complete` | painel admin → 403/redirect | [ ] |
| 2.9 | Aluno → dados de outro aluno | `complete` | IDOR bloqueado → 403/404 | [ ] |

**Erros encontrados:** _______________________________________________

---

## 3. Perfil e dados pessoais

| # | Teste | Web | API | OK |
|---|-------|-----|-----|----|
| 3.1 | Ver perfil | menu Perfil | `GET /api/v1/me` | [ ] |
| 3.2 | Editar dados | formulário perfil | `PATCH /api/v1/me` | [ ] |
| 3.3 | Onboarding | fluxo pós-login | `POST /api/v1/onboarding/profile` | [ ] |
| 3.4 | Upload foto inválida | perfil | rejeita MIME/tamanho | [ ] |
| 3.5 | Cadastro incompleto | `incomplete` | força completar perfil | [ ] |
| 3.6 | Consentimentos LGPD | perfil/config | registros em BD | [ ] |

---

## 4. Tela inicial (Dashboard)

| # | Teste | Usuário | Web | API relacionada | OK |
|---|-------|---------|-----|-----------------|----|
| 4.1 | Carregamento dashboard | `complete` | `/dashboard` | widgets carregam | [ ] |
| 4.2 | Aluno sem dados | `new` | `/dashboard` | cards vazios, sem erro | [ ] |
| 4.3 | Plano Free vs Premium | `free` / `premium` | badges corretos | `GET /api/v1/subscriptions/current` | [ ] |
| 4.4 | Créditos IA visíveis | `premium` | card créditos | `GET /api/v1/ai/credits` | [ ] |
| 4.5 | Sem botões admin | `complete` | nenhum link admin | — | [ ] |
| 4.6 | Console sem erros | todos | DevTools limpo | — | [ ] |

---

## 5. Treinos

| # | Teste | Usuário | Web | API | OK |
|---|-------|---------|-----|-----|----|
| 5.1 | Listar planos | `complete` | `/progression/plans` | `GET /api/v1/training-plans` | [ ] |
| 5.2 | Criar treino próprio | `free` | wizard criação | `POST /api/v1/training-plans` | [ ] |
| 5.3 | Editar treino | `free` | editar plano | `PUT /api/v1/training-plans/{id}` | [ ] |
| 5.4 | Excluir treino | `free` | excluir | `DELETE /api/v1/training-plans/{id}` | [ ] |
| 5.5 | Ficha do profissional | `linked_pro` | plano prescrito visível | plano com `professional_id` | [ ] |
| 5.6 | Registrar execução | `complete` | `/exercise` | `POST /api/v1/load-logs` | [ ] |
| 5.7 | Iniciar sessão | `complete` | execução | `POST /api/v1/workout-sessions/start` | [ ] |
| 5.8 | Histórico | `complete` | histórico | `GET /api/v1/workout-sessions` | [ ] |
| 5.9 | Importação por foto | `premium` | import IA | `POST /api/v1/workout-import/*` | [ ] |
| 5.10 | Aluno novo sem treinos | `new` | lista vazia | `GET /api/v1/training-plans` → [] | [ ] |

---

## 6. Nutrição

| # | Teste | Web | API | OK |
|---|-------|-----|-----|----|
| 6.1 | Diário do dia | `/nutrition`, `/diary` | `GET /api/v1/nutrition/diary` | [ ] |
| 6.2 | Adicionar refeição | formulário | `POST /api/v1/nutrition/diary` | [ ] |
| 6.3 | Editar / excluir | diário | `PUT/DELETE /api/v1/nutrition/diary/{id}` | [ ] |
| 6.4 | Metas nutricionais | config | `GET/PUT /api/v1/nutrition/goal` | [ ] |
| 6.5 | Análise IA refeição | premium | `POST /api/v1/nutrition/analyze-meal` | [ ] |
| 6.6 | Free bloqueado em IA | `free` | mensagem upgrade | 403 ou limite | [ ] |
| 6.7 | Histórico completo | `complete` | 7 dias de refeições | dados visíveis | [ ] |

---

## 7. Hidratação

| # | Teste | Web | API | OK |
|---|-------|-----|-----|----|
| 7.1 | Status do dia | `/hydration` | `GET /api/v1/hydration/status` | [ ] |
| 7.2 | Registrar consumo | botão +ml | `POST /api/v1/hydration/entries` | [ ] |
| 7.3 | Excluir registro | lista | `DELETE /api/v1/hydration/entries/{id}` | [ ] |
| 7.4 | Duplo clique | `complete` | sem duplicata indevida | [ ] |
| 7.5 | Atualiza dashboard | `complete` | widget hidratação | [ ] |

---

## 8. Avaliações e evolução

| # | Teste | Web | API | OK |
|---|-------|-----|-----|----|
| 8.1 | Listar avaliações | `/assessments` | `GET /api/v1/assessments` | [ ] |
| 8.2 | Resumo | dashboard evolução | `GET /api/v1/assessments/summary` | [ ] |
| 8.3 | Fotos evolução | `/evolution` | `GET /api/v1/evolution-photos` | [ ] |
| 8.4 | Upload foto | galeria | `POST /api/v1/evolution-photos` | [ ] |
| 8.5 | PDF avaliação | download | `GET /api/v1/student/assessments/{id}/pdf` | [ ] |
| 8.6 | Histórico completo | `complete` | 2 avaliações seed | [ ] |
| 8.7 | Não edita avaliação do prof | `linked_pro` | somente leitura | [ ] |

---

## 9. Agenda

| # | Teste | Usuário | Web | API | OK |
|---|-------|---------|-----|-----|----|
| 9.1 | Ver consultas | `linked_pro` | `/agenda` | `GET /api/v1/student/appointments` | [ ] |
| 9.2 | Horários disponíveis | `linked_pro` | calendário | `GET /api/v1/student/appointments/slots` | [ ] |
| 9.3 | Agendar | `linked_pro` | form | `POST /api/v1/student/appointments` | [ ] |
| 9.4 | Buscar profissionais | `free` | busca | `GET /api/v1/student/professionals/search` | [ ] |
| 9.5 | Lista de vínculos | `linked_pro` | — | `GET /api/v1/student/professionals` | [ ] |

---

## 10. Chat e comunicação

| # | Teste | Web | API | OK |
|---|-------|-----|-----|----|
| 10.1 | NexBot (IA) | `/chat` | `GET /api/v1/chat/history`, `POST /api/v1/chat/send` | [ ] |
| 10.2 | Mensagens profissional | mensagens | `GET /api/v1/messages/conversations` | [ ] |
| 10.3 | Enviar mensagem | chat | `POST /api/v1/messages/conversations/{id}` | [ ] |
| 10.4 | Sem vínculo bloqueado | `free` | não envia ao prof sem link | [ ] |
| 10.5 | Chat separado do dashboard | UI | área própria na navegação | [ ] |

---

## 11. Assinatura e planos

| # | Teste | Usuário | Web | API | OK |
|---|-------|---------|-----|-----|----|
| 11.1 | Identificar Free | `free` | badge Free | `GET /api/v1/subscriptions/current` | [ ] |
| 11.2 | Identificar Premium | `premium` | badge Premium | plano ativo | [ ] |
| 11.3 | Assinatura expirada | `expired` | downgrade/bloqueio | status expired | [ ] |
| 11.4 | Listar planos | qualquer | `/patient/subscription/*` | `GET /api/v1/subscriptions/plans` | [ ] |
| 11.5 | Checkout | `free` | fluxo pagamento | `POST /api/v1/subscriptions/checkout` | [ ] |
| 11.6 | Cancelamento | `premium` | cancelar | `POST /api/v1/subscriptions/cancel` | [ ] |
| 11.7 | Free tenta recurso premium | `free` | bloqueio UI + API 403 | [ ] |

---

## 12. Créditos de IA

| # | Teste | Usuário | API | OK |
|---|-------|---------|-----|----|
| 12.1 | Ver saldo | `premium` | `GET /api/v1/ai/credits` | [ ] |
| 12.2 | Consumo import treino | `premium` | decrementa após `workout-import` | [ ] |
| 12.3 | Saldo zerado bloqueia | `free` (10 créditos) | 403 após esgotar | [ ] |
| 12.4 | Custo exibido antes | UI | confirmação prévia | [ ] |
| 12.5 | Cobrança no servidor | DevTools | frontend não decrementa sozinho | [ ] |

---

## 13. Perfil combinado aluno + paciente

| # | Teste | Usuário | OK |
|---|-------|---------|----|
| 13.1 | Alternar perfil/modo | `aluno_paciente` | [ ] |
| 13.2 | Menus diferentes por perfil | `aluno_paciente` | [ ] |
| 13.3 | Portal paciente (leitura) | `aluno_paciente` | `/patient/portal` | [ ] |
| 13.4 | Funcionalidades aluno mantidas | `aluno_paciente` | `/dashboard`, treinos | [ ] |
| 13.5 | Sem acesso profissional/clínica | `aluno_paciente` | [ ] |

---

## 14. Navegação web

| Tela | Rota web real | API principal | Resultado | Erro |
|------|---------------|---------------|-----------|------|
| Login | `/login` | `POST /api/v1/auth/token` | [ ] | |
| Dashboard | `/dashboard` | widgets diversos | [ ] | |
| Treinos | `/progression/plans` | `GET /api/v1/training-plans` | [ ] | |
| Nutrição | `/nutrition` | `GET /api/v1/nutrition/diary` | [ ] | |
| Hidratação | `/hydration` | `GET /api/v1/hydration/status` | [ ] | |
| Chat | `/chat` | `GET /api/v1/chat/history` | [ ] | |
| Mensagens | `/chat` ou mensagens | `GET /api/v1/messages/conversations` | [ ] | |
| Perfil | menu perfil | `GET /api/v1/me` | [ ] | |
| Portal paciente | `/patient/portal` | links paciente | [ ] | |
| 404 | rota inexistente | página 404 | [ ] | |
| F5 em rota interna | qualquer | recarrega sem 404 | [ ] | |

---

## 15. Android (Persona A)

Seguir `android/MANUAL_TEST_PLAN.md` com `API_BASE_URL` apontando para o backend.

| # | Seção | Usuário sugerido | OK |
|---|-------|------------------|----|
| A1 | Autenticação | `complete` | [ ] |
| A2 | Treino | `complete` / `linked_pro` | [ ] |
| A3 | Evolução | `complete` | [ ] |
| A4 | Agenda | `linked_pro` | [ ] |
| A5 | Assinatura | `free` + `premium` | [ ] |
| A6 | Chat / Nutrição | `complete` | [ ] |

---

## 16. Testes automatizados (regressão)

```powershell
cd backend
composer test
php artisan app:api:smoke --url=http://localhost:8000 --email=aluno.completo@test.nexshape --password=Teste@123
composer phpstan
```

| Comando | Resultado | OK |
|---------|-----------|----|
| `composer test -- --filter=StudentApiAccessTest` | 8 testes aluno (auth, treinos, hidratacao, permissoes) | [ ] |
| `app:api:smoke` | SUCCESS | [ ] |
| `composer phpstan` | sem erros | [ ] |

---

## 17. Registro de erros

Copie para cada falha:

```text
ID: ALU-___
Módulo:
Perfil:
Ambiente:
Dispositivo:
Tela/Rota:
Endpoint:
Severidade: B / C / M / L

Descrição:

Passos:
1.
2.

Resultado atual:
Resultado esperado:
Status HTTP:
Console/Log:
Evidência:
Status: Aberto
```

---

## 18. Parecer final

```text
Perfil avaliado: Aluno
Versão do sistema:
Ambiente:
Data da avaliação:
Responsável:

Total de funcionalidades avaliadas:
Funcionalidades aprovadas:
Funcionalidades reprovadas:
Erros bloqueadores:
Erros críticos:
Erros médios:
Erros baixos:

Status geral:
[ ] Aprovado para produção
[ ] Aprovado com ressalvas
[ ] Reprovado para produção

Itens obrigatórios antes da publicação:
1.
2.
3.

Melhorias pós-publicação:
1.
2.
3.

Conclusão:
```

---

## Referências no repositório

| Documento | Uso |
|-----------|-----|
| `docs/AUDITORIA_COMPLETA_PAINEL_ALUNO.md` | Inventário de módulos web |
| `docs/manual_portal_aluno_nexshape.md` | Rotas e regras de negócio |
| `android/MANUAL_TEST_PLAN.md` | Testes Android Persona A/B |
| `backend/routes/api.php` | Fonte da verdade — API v1 |
| `backend/docs/GO_LIVE_CHECKLIST.md` | Gate de produção |
