# 🛡️ Plano de Homologação Exaustivo — NexShape SaaS (V1.0)

Este documento é o guia definitivo para validar cada engrenagem do ecossistema NexShape antes do lançamento oficial.

---

## 🏗️ 1. INFRAESTRUTURA & NÚCLEO (SISTEMA)
*Objetivo: Garantir que o motor do sistema está rodando sem vazamentos.*

- [ ] **Ambiente (.env):** Validar `APP_ENV=production` e `APP_DEBUG=false` para teste final de erro (deve mostrar tela amigável, não código).
- [ ] **HTTPS/SSL:** Forçar redirecionamento e validar selo de segurança.
- [ ] **Caminhos de Pasta:** No XAMPP/Apache, garantir que arquivos na raiz (como `.env`) não são acessíveis via URL direta.
- [ ] **Sessões Persistent:** Logar, fechar o navegador, abrir de novo. A sessão deve ser mantida se "Lembrar-me" foi marcado.

---

## 🔐 2. AUTENTICAÇÃO & CONTROLE DE ACESSO (RBAC)
*Objetivo: Ninguém acessa o que não deve.*

- [ ] **Cadastro de Profissional:** Preencher registro, especialidade e documentos. Validar se cai na fila de aprovação (se ativa).
- [ ] **Cadastro de Aluno:** Validar fluxo de dados biométricos obrigatórios.
- [ ] **Login Social (Google):** Testar se o vínculo via Google cria a conta ou loga na conta existente com o mesmo email.
- [ ] **Esqueci Senha:** Testar o envio do link de recuperação e a troca efetiva da senha.
- [ ] **Multi-Perfis:** Usuário que é Profissional E Aluno. Testar se o "Seletor de Perfil" no Topbar troca o Dashboard e as permissões de menu instantaneamente.

---

## 🏥 3. MÓDULO CLÍNICA & GESTÃO (B2B)
*Objetivo: Validar a hierarquia administrativa.*

- [ ] **Configurações de Branding:** Trocar Logo e Cor. Verificar se os botões e detalhes do sistema mudam para a cor escolhida (CSS dinâmico).
- [ ] **Gestão de Unidades:** Criar Unidade A e Unidade B.
- [ ] **Transferência de Paciente:** Tentar mover um paciente entre unidades.
- [ ] **Controle de Equipe:** Adicionar um profissional. Testar se ele recebe o email de convite ou acesso.
- [ ] **Faturamento da Clínica:** Validar se os gráficos de receita mostram apenas os dados dos pacientes vinculados àquela organização.

---

## 👨‍⚕️ 4. MÓDULO PROFISSIONAL (OPERAÇÕES CLÍNICAS)
*Objetivo: Validar a ferramenta de trabalho do especialista.*

- [ ] **Prontuário Digital:** Criar evolução, salvar rascunho e finalizar.
- [ ] **Prescrições & Receitas:** Gerar PDF. O PDF deve conter o QR Code de validação (se ativo).
- [ ] **Galeria NexShape Evolution:** Subir fotos de evolução. Testar o comparador (Antes/Depois lado a lado).
- [ ] **Exames & Laudos:** Upload de PDFs de exames. Testar se o aluno consegue baixar no portal dele.
- [ ] **Agenda Inteligente:** Marcar consulta, trocar status (Confirmado, Atendido, Falta). Verificar se o dashboard atualiza o contador de atendimentos do dia.

---

## 🎓 5. PAINEL DO ALUNO (EXPERIÊNCIA PREMIUM)
*Objetivo: Retenção e engajamento do cliente final.*

- [ ] **Dashboard Unificado:** Ver o resumo da saúde, próximo treino e última evolução.
- [ ] **Academia NexShape (Treinos):** Abrir uma ficha de treino. Iniciar cronômetro (se houver). Marcar exercício como concluído.
- [ ] **Progresso Biométrico:** Ver o gráfico de evolução de peso/gordura. Validar se os dados batem com o que o profissional inseriu.
- [ ] **PWA / Instalação:** Instalar no celular. Testar navegação offline na lista de treinos já carregada.

---

## 🤖 6. INTELIGÊNCIA ARTIFICIAL & COMUNICAÇÃO
*Objetivo: Validar os diferenciais tecnológicos.*

- [ ] **NexNeural (Chatbot IA):** Fazer uma pergunta sobre saúde/treino. Validar se os créditos são descontados corretamente.
- [ ] **OmniChat / Mensagens:** Enviar mensagem do Profissional para o Aluno. Verificar se aparece a notificação (ponto vermelho) no menu.
- [ ] **Notificações Internas:** Validar se o sistema avisa sobre novos documentos ou mensagens recebidas.
- [ ] **AI Pulse:** Verificar o monitoramento de uso de IA no painel administrativo.

---

## 💳 7. FINANCEIRO & ASSINATURAS (SAAS)
*Objetivo: Garantir que o dinheiro entra e o acesso é controlado.*

- [ ] **Checkout Mercado Pago/Stripe:** Realizar uma compra em modo Sandbox. Validar se o plano ativa na hora.
- [ ] **Upgrade/Downgrade:** Mudar de plano. Verificar se o sistema ajusta as funcionalidades disponíveis imediatamente.
- [ ] **Bloqueio de Inadimplência:** Simular uma assinatura vencida. O sistema deve redirecionar para a tela de pagamento.

---

## 🛡️ 8. BLINDAGEM DE SEGURANÇA & LGPD
*Objetivo: Proteção jurídica e técnica.*

- [ ] **Logs de Acesso:** Verificar se o sistema registra cada vez que um profissional abre um prontuário (Auditoria).
- [ ] **Termos de Uso:** Garantir que o usuário precise aceitar os termos no primeiro login.
- [ ] **Exclusão de Dados (Direito ao Esquecimento):** Testar se ao deletar um usuário, todos os dados sensíveis são removidos ou anonimizados.
- [ ] **Proteção de IDs:** Tentar mudar o ID na URL (ex: de `/patient/1` para `/patient/2`). O sistema deve barrar se o paciente 2 não for seu.

---

## 🧪 9. TESTE DE ESTRESSE & CARGA (DESEMPENHO)

- [ ] **Tabelas Grandes:** Testar a busca de pacientes com 100+ registros. A paginação deve funcionar e ser rápida.
- [ ] **Uploads Simultâneos:** Subir 5 fotos de alta resolução ao mesmo tempo.
- [ ] **Concorrência:** Abrir o mesmo paciente em dois navegadores diferentes e tentar editar ao mesmo tempo.

---

### ✅ CONCLUSÃO DO TESTE
Se todos os itens acima estiverem marcados com **[x]**, seu sistema está em **Estado de Arte** para produção. Parabéns pela blindagem!
