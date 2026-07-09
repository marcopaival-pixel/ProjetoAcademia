# Manual de Homologacao e Testes - NexShape

Este manual define como testar o NexShape antes de liberar uma versao para piloto, producao ou venda publica. Ele deve ser usado junto com `docs/GO_LIVE_CHECKLIST.md`, `docs/DEPLOY.md`, `docs/API_V1.md` e os logs reais da aplicacao.

O objetivo nao e apenas "clicar nas telas". O objetivo e provar, com evidencias, que os fluxos criticos funcionam, que usuarios nao acessam dados indevidos, que pagamentos e agenda se comportam corretamente e que o sistema continua seguro e estavel.

---

## 1. Como usar este manual

### Ordem recomendada

1. Preparar ambiente e usuarios de teste.
2. Executar testes automatizados e checks tecnicos.
3. Testar cadastro, login e permissoes.
4. Testar isolamento multi-tenant.
5. Testar fluxos principais: agenda, alunos/pacientes, prontuario, avaliacoes, treinos e financeiro.
6. Testar IA, logs, notificacoes e integracoes.
7. Testar Android.
8. Testar seguranca, performance, backup e producao.
9. Registrar evidencias e aprovar ou reprovar a homologacao.

### Regra de ouro

Uma versao so deve ser aprovada se:

- Nao houver falha Critica aberta.
- Nao houver falha Alta aberta em login, permissoes, multi-tenant, financeiro, IA, backup ou seguranca.
- Os fluxos principais tiverem evidencias de teste.
- Os logs nao apresentarem erros recorrentes sem explicacao.
- O ambiente de homologacao estiver o mais parecido possivel com producao.

---

## 2. Classificacao de severidade

| Severidade | Quando usar | Exemplo |
|---|---|---|
| Critica | Vazamento de dados, pagamento errado, indisponibilidade total, perda de dados, falha grave de seguranca | Clinica A ve dados da Clinica B |
| Alta | Fluxo essencial quebrado ou permissao incorreta sem vazamento confirmado | Profissional nao consegue acessar agenda |
| Media | Funcionalidade importante com contorno manual aceitavel | Filtro de pacientes retorna resultado incompleto |
| Baixa | Problema visual, texto, usabilidade ou comportamento secundario | Botao pouco claro ou mensagem confusa |

---

## 3. Modelo padrao de registro de teste

Use esta matriz para cada teste manual ou tecnico.

| Campo | Como preencher |
|---|---|
| ID | Codigo unico. Ex.: `AUTH-001`, `TENANT-003`, `FIN-010` |
| Area | Login, Agenda, Financeiro, IA, Android, etc. |
| Perfil | Aluno, Profissional, Clinica, Admin, Super Admin |
| Cenario | Nome curto do que sera testado |
| Pre-condicao | Dados necessarios antes do teste |
| Passos | Acoes executadas pelo testador |
| Resultado esperado | O que o sistema deve fazer |
| Resultado obtido | O que aconteceu de fato |
| Status | Passou, Falhou, Bloqueado ou Nao aplicavel |
| Severidade | Critica, Alta, Media ou Baixa |
| Evidencia | Print, video, log, ID do registro, payload, horario |
| Responsavel | Quem testou |
| Data | Data da execucao |

Exemplo:

| ID | Area | Perfil | Cenario | Resultado esperado | Status | Severidade |
|---|---|---|---|---|---|---|
| AUTH-001 | Login | Aluno | Login valido | Acessar dashboard do aluno |  | Alta |
| TENANT-001 | Multi-tenant | Profissional | Trocar ID na URL | Sistema deve bloquear acesso a dados de outra clinica |  | Critica |
| FIN-001 | Financeiro | Clinica | Pagamento PIX | Pagamento confirmado e registrado uma unica vez |  | Critica |

---

## 4. Preparacao do ambiente

### Ambiente

- Usar homologacao/staging, nao banco de producao real.
- Garantir `APP_ENV` adequado para homologacao.
- Conferir `APP_URL` com o endereco real acessado pelo navegador ou app.
- Conferir conexao com MySQL/MariaDB local ou servidor de homologacao.
- Conferir se filas, agendador e jobs necessarios estao ativos conforme o ambiente.
- Confirmar que o Apache/XAMPP aponta para a pasta `public/` quando usado localmente.

### Dados minimos de teste

Criar pelo menos:

- 2 clinicas diferentes: Clinica A e Clinica B.
- 1 admin.
- 1 super admin.
- 2 profissionais por clinica.
- 3 alunos/pacientes por clinica.
- 1 plano ativo.
- 1 assinatura ativa.
- 1 agenda com horarios livres e bloqueados.
- 1 prontuario com anexos.
- 1 avaliacao fisica.
- 1 conversa de IA por clinica.

### Evidencias obrigatorias

Guardar:

- Prints de telas aprovadas e falhas.
- Data e hora dos testes.
- Usuario usado.
- IDs de registros criados.
- Logs relevantes de `storage/logs`.
- Respostas de API quando aplicavel.
- Comprovantes sandbox de pagamento quando aplicavel.

---

## 5. Checks automatizados antes dos testes manuais

Executar quando aplicavel ao ambiente:

```bash
composer test
composer phpstan
php artisan app:release:verify --target=homologacao
php artisan app:audit:tenant
php artisan app:db:orphans --fail-on-orphans
php artisan app:db:index-explain --fail-on-scan
php artisan app:backup:verify
php artisan app:smoke:test
```

Se algum comando nao existir ou nao puder ser executado no ambiente, registrar como "Nao executado" com motivo.

---

## 6. Cadastro e login

### Cadastro

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| AUTH-001 | Cadastro de aluno com dados validos | Conta criada e aluno direcionado ao fluxo correto | Alta |
| AUTH-002 | Cadastro de profissional com dados validos | Profissional criado e vinculado corretamente | Alta |
| AUTH-003 | Cadastro de clinica com dados validos | Clinica criada sem misturar dados com outras clinicas | Critica |
| AUTH-004 | Cadastro de administrador | Admin criado com permissoes corretas | Alta |
| AUTH-005 | Campos obrigatorios vazios | Sistema bloqueia e mostra mensagens claras | Media |
| AUTH-006 | CPF invalido | Sistema bloqueia cadastro | Alta |
| AUTH-007 | E-mail duplicado | Sistema bloqueia duplicidade | Alta |
| AUTH-008 | Telefone invalido | Sistema valida ou orienta correcao | Media |
| AUTH-009 | Senha abaixo do minimo | Sistema bloqueia e informa criterio | Alta |
| AUTH-010 | Confirmacao de senha diferente | Sistema bloqueia cadastro | Media |

### Login, sessao e senha

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| AUTH-011 | Login valido | Usuario acessa o dashboard correto | Alta |
| AUTH-012 | Login invalido | Acesso negado sem expor detalhes sensiveis | Alta |
| AUTH-013 | Usuario bloqueado | Login impedido | Alta |
| AUTH-014 | Usuario inativo | Login impedido | Alta |
| AUTH-015 | Recuperacao de senha | Link/token funciona e expira corretamente | Alta |
| AUTH-016 | Alteracao de senha | Senha antiga deixa de funcionar | Alta |
| AUTH-017 | Logout | Sessao encerrada e area protegida bloqueada | Alta |
| AUTH-018 | Expiracao de sessao | Usuario precisa autenticar novamente | Alta |
| AUTH-019 | Permanecer conectado | Sessao respeita configuracao sem burlar seguranca | Media |
| AUTH-020 | Login biometrico Android | Biometria autentica apenas usuario autorizado | Alta |

---

## 7. Controle de permissoes

Testar para todos os perfis: Aluno, Profissional, Clinica, Admin e Super Admin.

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| PERM-001 | Acesso por menu | Usuario so ve menus permitidos | Alta |
| PERM-002 | Acesso por URL direta | Sistema bloqueia rotas nao permitidas | Critica |
| PERM-003 | Acesso por API | API retorna 401/403 quando nao autorizado | Critica |
| PERM-004 | Botoes de acao | Usuario nao ve ou nao consegue executar acoes proibidas | Alta |
| PERM-005 | Relatorios | Usuario so ve relatorios permitidos | Alta |
| PERM-006 | Troca manual de IDs | Sistema bloqueia acesso indevido | Critica |
| PERM-007 | Admin vs Super Admin | Admin comum nao executa acoes globais | Critica |

---

## 8. Multi-tenant e privacidade

Esta e uma das areas mais importantes. Qualquer falha confirmada aqui deve bloquear a homologacao.

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| TENANT-001 | Clinica A tenta ver dados da Clinica B pela tela | Acesso bloqueado ou dado nao aparece | Critica |
| TENANT-002 | Clinica A tenta ver dados da Clinica B pela URL | Acesso bloqueado | Critica |
| TENANT-003 | Profissional tenta ver paciente de outro profissional sem permissao | Acesso bloqueado | Critica |
| TENANT-004 | Financeiro entre clinicas | Lancamentos aparecem apenas para a clinica correta | Critica |
| TENANT-005 | Agenda entre clinicas | Horarios e pacientes isolados | Critica |
| TENANT-006 | IA usando contexto da clinica | IA responde apenas com dados autorizados | Critica |
| TENANT-007 | Busca global | Pesquisa nao retorna dados de outro tenant | Critica |
| TENANT-008 | Relatorios e dashboards | Indicadores respeitam tenant e perfil | Critica |

---

## 9. Agenda

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| AGENDA-001 | Criar atendimento | Atendimento criado no horario correto | Alta |
| AGENDA-002 | Editar atendimento | Alteracao salva e registrada | Alta |
| AGENDA-003 | Excluir atendimento | Atendimento removido ou cancelado conforme regra | Alta |
| AGENDA-004 | Cancelar atendimento | Status alterado e notificacoes aplicadas | Alta |
| AGENDA-005 | Reagendar atendimento | Novo horario salvo sem duplicidade | Alta |
| AGENDA-006 | Confirmar presenca | Status atualizado corretamente | Media |
| AGENDA-007 | Registrar falta | Status atualizado e refletido em historico | Media |
| AGENDA-008 | Agenda cheia | Sistema impede novo agendamento ou orienta usuario | Alta |
| AGENDA-009 | Conflito de horario | Sistema impede sobreposicao indevida | Critica |
| AGENDA-010 | Horario de almoco | Sistema respeita bloqueio | Alta |
| AGENDA-011 | Horario bloqueado | Sistema impede agendamento | Alta |
| AGENDA-012 | Feriado | Sistema respeita regra configurada | Media |
| AGENDA-013 | Atendimento recorrente | Recorrencia criada corretamente e sem conflitos | Alta |

---

## 10. Alunos e pacientes

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| PAC-001 | Criar aluno/paciente | Registro criado e vinculado corretamente | Alta |
| PAC-002 | Editar cadastro | Dados atualizados sem perder historico | Alta |
| PAC-003 | Excluir ou arquivar | Regra de exclusao/arquivamento respeitada | Alta |
| PAC-004 | Pesquisar | Busca retorna somente dados permitidos | Alta |
| PAC-005 | Filtrar | Filtros funcionam e respeitam tenant | Media |
| PAC-006 | Historico | Historico completo aparece para perfil autorizado | Alta |
| PAC-007 | Fotos | Upload, visualizacao e remocao funcionam | Alta |
| PAC-008 | Anexos | Arquivos sao armazenados e acessados com permissao | Alta |
| PAC-009 | Observacoes | Observacoes salvas e protegidas | Alta |
| PAC-010 | Evolucoes | Evolucoes aparecem em ordem correta | Media |

---

## 11. Prontuario

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| PRONT-001 | Criar prontuario | Registro criado para o paciente correto | Alta |
| PRONT-002 | Editar prontuario | Alteracoes salvas com seguranca | Alta |
| PRONT-003 | Salvamento automatico | Conteudo nao se perde em falha comum | Media |
| PRONT-004 | Excluir prontuario | Exclusao respeita permissao e auditoria | Alta |
| PRONT-005 | Historico | Alteracoes relevantes ficam rastreaveis | Alta |
| PRONT-006 | Imagens | Upload e visualizacao funcionam | Alta |
| PRONT-007 | Arquivos | Download exige permissao | Critica |
| PRONT-008 | Assinaturas | Assinatura e validade funcionam conforme regra | Alta |
| PRONT-009 | Impressao | Documento impresso mantem dados corretos | Media |
| PRONT-010 | PDF | PDF gerado sem expor dados indevidos | Alta |

---

## 12. Avaliacoes fisicas

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| AVAL-001 | Peso | Valor salvo e exibido corretamente | Media |
| AVAL-002 | Altura | Valor salvo e exibido corretamente | Media |
| AVAL-003 | IMC | Calculo correto conforme dados informados | Alta |
| AVAL-004 | Dobras | Dados salvos sem perda de precisao | Media |
| AVAL-005 | Circunferencias | Dados salvos e comparaveis | Media |
| AVAL-006 | Bioimpedancia | Campos numericos retornam como numeros nas APIs | Alta |
| AVAL-007 | Fotos comparativas | Fotos respeitam privacidade e acesso | Alta |
| AVAL-008 | Graficos | Graficos refletem historico correto | Media |
| AVAL-009 | Historico | Evolucao ordenada corretamente | Media |

---

## 13. Treinos, dietas e evolucao

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| TREINO-001 | Criar plano de treino | Plano vinculado ao aluno correto | Alta |
| TREINO-002 | Editar treino | Alteracoes salvas sem afetar outros alunos | Alta |
| TREINO-003 | Excluir treino | Exclusao respeita permissao | Alta |
| TREINO-004 | Exercicios e series | Dados aparecem corretamente no portal/app | Media |
| TREINO-005 | Sessao concluida | Progresso registrado corretamente | Media |
| TREINO-006 | Historico de evolucao | Historico preservado | Alta |
| TREINO-007 | Importacao ou sugestao por IA | Conteudo revisavel e sem dados de outro aluno | Critica |

---

## 14. Financeiro

Financeiro deve ser tratado como area critica. Nao aprovar homologacao com falha aberta que afete valores, cobrancas, assinaturas ou isolamento.

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| FIN-001 | Criar plano | Plano criado com preco e regras corretas | Alta |
| FIN-002 | Assinatura nova | Assinatura vinculada ao cliente correto | Critica |
| FIN-003 | Mensalidade | Cobranca gerada no valor correto | Critica |
| FIN-004 | PIX | Pagamento confirmado e registrado | Critica |
| FIN-005 | Cartao | Pagamento aprovado/recusado tratado corretamente | Critica |
| FIN-006 | Mercado Pago | Webhook atualiza status correto | Critica |
| FIN-007 | Webhook duplicado | Sistema nao duplica pagamento | Critica |
| FIN-008 | Estorno | Estorno refletido corretamente | Critica |
| FIN-009 | Cancelamento | Assinatura cancelada conforme regra | Critica |
| FIN-010 | Cupom | Desconto aplicado uma unica vez e no valor correto | Alta |
| FIN-011 | Desconto manual | Permissao e auditoria respeitadas | Alta |
| FIN-012 | Nota/recibo | Documento gerado com dados corretos | Alta |
| FIN-013 | Financeiro por tenant | Clinica nao ve valores de outra clinica | Critica |

---

## 15. IA

Testes de IA devem validar resposta, custo, autorizacao e isolamento. Nao basta verificar se "responde".

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| IA-001 | Pergunta simples autorizada | IA responde corretamente dentro do contexto | Media |
| IA-002 | Pergunta sobre outro cliente | IA nao revela dados e informa limite de acesso | Critica |
| IA-003 | Contexto do aluno | IA usa apenas dados do aluno autorizado | Critica |
| IA-004 | Contexto da clinica | IA nao mistura tenants | Critica |
| IA-005 | Resposta sem dados suficientes | IA nao inventa informacoes | Alta |
| IA-006 | Creditos descontados | Consumo registrado corretamente | Critica |
| IA-007 | Compra de creditos | Creditos adicionados apos pagamento confirmado | Critica |
| IA-008 | Historico de conversas | Historico visivel apenas para perfis autorizados | Critica |
| IA-009 | Limite de historico | Payload nao cresce sem controle | Media |
| IA-010 | Cache semantico | Respostas equivalentes usam cache quando aplicavel | Media |
| IA-011 | Imagem/OCR/postura | Upload comprimido e sem expor dados indevidos | Alta |

---

## 16. Dashboards e paineis

Testar todos os paineis: Aluno, Profissional, Clinica, Admin e Super Admin.

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| DASH-001 | Dashboard Aluno | Cards e links mostram dados do aluno | Alta |
| DASH-002 | Dashboard Profissional | Agenda, pacientes e indicadores corretos | Alta |
| DASH-003 | Dashboard Clinica | Indicadores isolados por clinica | Critica |
| DASH-004 | Dashboard Admin | Dados administrativos corretos | Alta |
| DASH-005 | Dashboard Super Admin | Visao global apenas para perfil autorizado | Critica |
| DASH-006 | Graficos | Numeros batem com registros reais | Alta |
| DASH-007 | Filtros | Filtros nao quebram isolamento | Critica |
| DASH-008 | Links e cards | Links levam para telas corretas | Media |

---

## 17. Android

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| AND-001 | Login | Login funciona e abre area correta | Alta |
| AND-002 | Criar conta | Conta criada com validacoes | Alta |
| AND-003 | Login biometrico | Biometria respeita usuario logado | Alta |
| AND-004 | Permanecer conectado | Sessao persiste conforme regra | Media |
| AND-005 | Navegacao | Telas principais acessiveis sem erro | Alta |
| AND-006 | Notificacoes | Notificacoes chegam e abrem destino correto | Media |
| AND-007 | Tema claro | Tela legivel e sem sobreposicao | Baixa |
| AND-008 | Tema escuro | Tela legivel e sem sobreposicao | Baixa |
| AND-009 | Offline | App mostra mensagem ou fila segura | Media |
| AND-010 | Internet lenta | App nao duplica acoes nem trava fluxo critico | Alta |
| AND-011 | Rotacao de tela | Estado da tela preservado | Baixa |
| AND-012 | Upload de foto | Upload funciona e respeita permissao | Alta |
| AND-013 | Download de PDF | Download funciona e arquivo correto | Media |
| AND-014 | Compartilhamento | Compartilha apenas arquivo permitido | Alta |
| AND-015 | Permissoes do aparelho | App pede somente permissoes necessarias | Media |

---

## 18. Seguranca

Qualquer falha critica de seguranca deve bloquear a homologacao.

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| SEC-001 | SQL Injection | Entrada maliciosa nao altera consulta nem dados | Critica |
| SEC-002 | XSS | Scripts nao executam em campos exibidos | Critica |
| SEC-003 | CSRF | Requisicoes sensiveis exigem protecao | Critica |
| SEC-004 | Upload malicioso | Arquivo bloqueado ou armazenado com seguranca | Critica |
| SEC-005 | Limite de tamanho | Upload grande e bloqueado corretamente | Alta |
| SEC-006 | Sessao expirada | Area protegida exige login novamente | Alta |
| SEC-007 | Troca manual de URL | Acesso indevido bloqueado | Critica |
| SEC-008 | Troca de IDs | Dados de outro usuario/tenant nao aparecem | Critica |
| SEC-009 | Rate limit | Tentativas repetidas sao limitadas | Alta |
| SEC-010 | Senha criptografada | Senha nao aparece em texto puro no banco/logs | Critica |
| SEC-011 | Logs sem segredo | Logs nao contem senha, token ou dado sensivel desnecessario | Critica |
| SEC-012 | Headers e HTTPS | Cookies seguros e headers adequados em producao | Alta |

---

## 19. Performance

Testar com volumes progressivos:

- 100 pacientes.
- 1.000 pacientes.
- 10.000 pacientes, se o ambiente suportar massa de teste.

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| PERF-001 | Abrir dashboard | Tempo aceitavel e sem erro | Media |
| PERF-002 | Abrir agenda | Agenda carrega sem travar | Alta |
| PERF-003 | Listar pacientes | Paginacao/filtros funcionam | Alta |
| PERF-004 | Pesquisar pacientes | Busca rapida e isolada por tenant | Alta |
| PERF-005 | Abrir financeiro | Indicadores carregam corretamente | Alta |
| PERF-006 | Abrir prontuario | Historico carrega sem erro | Alta |
| PERF-007 | Consultas lentas | Slow queries identificadas | Media |
| PERF-008 | Uso de memoria | Sem estouro de memoria em fluxos comuns | Alta |

Registrar tempo aproximado, volume usado, perfil, tela e horario.

---

## 20. Usabilidade

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| UX-001 | Usuario novo entende cadastro | Fluxo claro sem ajuda externa | Media |
| UX-002 | Botoes claros | Acoes principais sao obvias | Baixa |
| UX-003 | Mensagens de erro | Mensagens explicam como corrigir | Media |
| UX-004 | Campos intuitivos | Campos tem rotulos e formatos claros | Baixa |
| UX-005 | Fluxo rapido | Tarefa comum nao exige cliques excessivos | Baixa |
| UX-006 | Mobile/responsivo | Telas nao quebram em tamanhos comuns | Media |

---

## 21. Integracoes

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| INT-001 | Mercado Pago sandbox | Pagamento e webhook funcionam | Critica |
| INT-002 | E-mail | E-mails transacionais chegam corretamente | Alta |
| INT-003 | WhatsApp | Mensagens enviadas conforme regra do projeto | Media |
| INT-004 | Upload | Arquivo armazenado e acessivel com permissao | Alta |
| INT-005 | Download | Arquivo baixado e correto | Media |
| INT-006 | IA | Integracao responde sem expor dados indevidos | Critica |
| INT-007 | APIs externas | Erros externos sao tratados sem quebrar sistema | Alta |

---

## 22. Logs e auditoria

Verificar se operacoes importantes geram log ou trilha de auditoria quando aplicavel.

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| LOG-001 | Login | Evento registrado sem senha/token | Alta |
| LOG-002 | Logout | Evento registrado quando aplicavel | Media |
| LOG-003 | Cadastro | Criacao rastreavel | Alta |
| LOG-004 | Exclusao | Quem excluiu, quando e o que foi afetado | Alta |
| LOG-005 | Pagamento | Evento financeiro rastreavel | Critica |
| LOG-006 | Alteracao sensivel | Alteracao importante rastreavel | Alta |
| LOG-007 | Erro | Erro tecnico registrado com contexto seguro | Alta |
| LOG-008 | IA | Uso, custo e contexto rastreaveis sem vazamento | Critica |
| LOG-009 | Compra/cancelamento | Evento financeiro rastreavel | Critica |

---

## 23. Banco de dados

Nao alterar schema ou dados de producao durante homologacao sem autorizacao explicita.

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| DB-001 | Dados orfaos | Nenhum orfao critico encontrado | Alta |
| DB-002 | Duplicidades | Dados unicos nao duplicam | Alta |
| DB-003 | Indices | Consultas criticas usam indices adequados | Media |
| DB-004 | Integridade referencial | Relacionamentos consistentes | Alta |
| DB-005 | Soft delete | Registros removidos seguem regra do sistema | Alta |
| DB-006 | Foreign keys | FKs criticas consistentes | Alta |
| DB-007 | Migrations pendentes | Ambiente sem migrations pendentes | Alta |

---

## 24. Backup, restore e falhas

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| BCK-001 | Backup do banco | Arquivo gerado e nao vazio | Critica |
| BCK-002 | Backup de storage | Arquivos importantes incluidos | Critica |
| BCK-003 | Restore | Ambiente restaurado e validado | Critica |
| BCK-004 | Perda de conexao com banco | Sistema falha de forma controlada | Alta |
| BCK-005 | Queda de energia/interrupcao | Dados nao ficam parcialmente corrompidos | Alta |
| BCK-006 | Rollback de release | Plano de rollback testado ou documentado | Critica |

---

## 25. Producao

Antes de liberar publicamente:

| ID | Cenario | Resultado esperado | Severidade |
|---|---|---|---|
| PROD-001 | HTTPS | Site acessivel apenas com HTTPS | Critica |
| PROD-002 | Cookies | Cookies seguros, HttpOnly e SameSite quando aplicavel | Alta |
| PROD-003 | Sessoes | Sessao expira corretamente | Alta |
| PROD-004 | Cache | Config, rotas e views cacheadas sem erro | Media |
| PROD-005 | CDN | Assets carregam corretamente, se houver CDN | Media |
| PROD-006 | Compressao | Compressao ativa sem quebrar assets | Baixa |
| PROD-007 | Headers | Headers de seguranca revisados | Alta |
| PROD-008 | Demo | Rotas demo bloqueadas em producao, se aplicavel | Alta |
| PROD-009 | Health checks | `/up`, `/health` ou equivalente respondem | Alta |

---

## 26. Cenarios reais de ponta a ponta

Executar pelo menos estes fluxos completos.

### Fluxo aluno

1. Aluno cria conta.
2. Faz login.
3. Atualiza perfil.
4. Agenda avaliacao.
5. Recebe notificacao.
6. Conversa com a IA.
7. Compra plano ou creditos.
8. Visualiza evolucao.
9. Faz logout.

Resultado esperado: nenhum dado de outra clinica aparece, pagamento/creditos ficam corretos e historico permanece consistente.

### Fluxo profissional

1. Profissional faz login.
2. Cadastra paciente.
3. Cria treino/dieta/plano.
4. Registra evolucao.
5. Agenda atendimento.
6. Gera relatorio ou PDF.
7. Consulta financeiro permitido.

Resultado esperado: profissional acessa apenas dados autorizados e todas as alteracoes ficam salvas.

### Fluxo clinica

1. Clinica faz login.
2. Gerencia profissionais.
3. Consulta agendas.
4. Visualiza indicadores.
5. Acompanha pagamentos.
6. Testa relatorios.

Resultado esperado: clinica ve apenas seus dados e indicadores batem com registros reais.

### Fluxo administrador

1. Admin configura planos.
2. Acompanha assinaturas.
3. Monitora logs.
4. Verifica integracoes.
5. Revisa erros de homologacao.

Resultado esperado: admin nao acessa areas restritas ao super admin e nao ve dados indevidos.

---

## 27. Criterios de aprovacao final

A homologacao pode ser aprovada quando:

- 100% dos testes Criticos passaram ou tiveram correcao validada.
- 100% dos testes Altos de login, permissoes, multi-tenant, financeiro, IA e backup passaram.
- Falhas Medias restantes tem contorno conhecido e data de correcao.
- Falhas Baixas restantes foram aceitas pelo responsavel do produto.
- Evidencias foram anexadas.
- Logs foram revisados.
- Backup e restore foram validados.
- Responsavel tecnico e responsavel de negocio aprovaram.

---

## 28. Relatorio final de homologacao

Preencher ao final.

| Campo | Valor |
|---|---|
| Versao testada |  |
| Ambiente |  |
| Periodo de teste |  |
| Responsavel tecnico |  |
| Responsavel de negocio |  |
| Total de testes |  |
| Passaram |  |
| Falharam |  |
| Bloqueados |  |
| Falhas Criticas abertas |  |
| Falhas Altas abertas |  |
| Decisao | Aprovado / Reprovado / Aprovado com ressalvas |
| Observacoes |  |

---

## 29. Checklist rapido de bloqueio

Bloquear release se qualquer item abaixo falhar:

- [ ] Usuario ve dados de outro tenant.
- [ ] Profissional acessa paciente sem permissao.
- [ ] Financeiro mostra valor errado.
- [ ] Pagamento duplica ou nao registra.
- [ ] IA revela dados de outro cliente.
- [ ] Upload permite arquivo perigoso.
- [ ] Senha ou token aparece em log.
- [ ] Backup nao existe ou restore nao foi validado.
- [ ] Login/logout/sessao falham em fluxo comum.
- [ ] API permite acesso sem autorizacao.

