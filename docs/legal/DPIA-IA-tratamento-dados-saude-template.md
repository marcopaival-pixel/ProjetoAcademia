# DPIA — Tratamento de dados de saúde e IA (template)

**Sistema:** NexShape / ProjetoAcademia  
**Versão:** 1.0 (template — revisão jurídica obrigatória)  
**Data:** maio/2026  

> Este documento é um **modelo** para Data Protection Impact Assessment (DPIA / RIPD) conforme LGPD (Art. 38). Não substitui parecer jurídico.

---

## 1. Identificação do tratamento

| Campo | Preencher |
|-------|-----------|
| **Controlador** | Razão social, CNPJ, DPO/contato |
| **Operador(es)** | OpenAI, Mercado Pago, AWS, Google (OAuth/Vision), hospedagem |
| **Finalidade** | Nutrição, treino, prontuário clínico, análise corporal, importação treino por foto |
| **Base legal** | Execução de contrato; consentimento; tutela da saúde (Art. 11 LGPD — validar com advogado) |
| **Categorias de dados** | Dados de saúde, biométricos (fotos), hábitos alimentares, evolução física |
| **Titulares** | Alunos, pacientes, profissionais de saúde |

---

## 2. Descrição dos fluxos com IA

| Fluxo | Dados enviados | Serviço | Retenção |
|-------|----------------|---------|----------|
| Chat nutricional / orquestrador | Mensagens, contexto perfil | OpenAI API | `ai_orchestrator_logs`, `ai_chats` |
| Importação treino (foto) | Imagem, texto OCR | OpenAI / Google Vision | `ai_vision_logs`, `workout_import_logs` |
| Análise corporal | Fotos, landmarks | OpenAI (se aplicável) | `body_analyses` |
| Prescrição IA profissional | Dados clínicos paciente | OpenAI | logs associados |

**Transferência internacional:** OpenAI e AWS podem processar fora do Brasil — documentar cláusulas contratuais / SCCs.

---

## 3. Necessidade e proporcionalidade

- [ ] Dados mínimos necessários para a finalidade?
- [ ] Alternativa sem IA avaliada?
- [ ] Imagens anonimizadas ou pseudonimizadas quando possível?
- [ ] Retenção limitada (`app:purge-old-logs`, política de retenção definida)?

---

## 4. Riscos identificados

| Risco | Probabilidade | Impacto | Mitigação técnica |
|-------|---------------|---------|-------------------|
| Vazamento entre clínicas (multi-tenant) | Média | Alto | `TenantMiddleware`, colunas `academy_company_id` / `clinic_id`, testes isolamento |
| Exfiltração via API OpenAI | Baixa | Alto | Minimizar payload; não enviar CPF/documentos desnecessários |
| Acesso não autorizado a prontuário | Média | Crítico | RBAC, policies, auditoria `audit_logs` |
| Retenção excessiva de imagens | Média | Médio | TTL, exclusão conta (SLA LGPD) |
| Webhook pagamento forjado | Baixa | Alto | HMAC MP, secrets produção |

---

## 5. Medidas técnicas e organizacionais

- Autenticação, CSRF, headers segurança (`SecurityHeaders`)
- Encriptação sessão produção (`SESSION_ENCRYPT`, `SESSION_SECURE_COOKIE`)
- Backup cifrado (`BACKUP_ARCHIVE_PASSWORD`)
- Consentimento cookies (`user_consents`, banner integrado)
- Registo incidentes (`security_incidents`)
- Treino equipa e política de acesso admin

---

## 6. Consulta ao DPO / ANPD

| Item | Data | Resultado |
|------|------|-----------|
| Revisão DPO | | |
| Comunicação ANPD (se incidente grave) | | |

---

## 7. Aprovação

| Papel | Nome | Data | Assinatura |
|-------|------|------|------------|
| Controlador | | | |
| DPO | | | |
| Responsável técnico | | | |

---

## Anexo A — Tabelas relevantes

`body_analyses`, `body_assessments`, `medical_*`, `ai_orchestrator_logs`, `ai_vision_logs`, `workout_import_logs`, `user_consents`, `patient_documents`

Ver `laravel-app/docs/dicionario_dados.md` e suplementos.
