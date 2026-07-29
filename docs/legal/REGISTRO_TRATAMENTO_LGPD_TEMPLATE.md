# Registro de atividades de tratamento — LGPD (operacional)

Template para preenchimento pelo controlador. Complementa `docs/legal/DPIA-IA-tratamento-dados-saude-template.md`.

---

## 1. Identificação

| Campo | Valor |
|-------|--------|
| Controlador | _Razão social / CNPJ_ |
| Encarregado (DPO) | _Nome, e-mail_ |
| Sistema | NexShape |
| Versão do sistema | _APP_VERSION / tag Git_ |
| Data do registro | _DD/MM/AAAA_ |

---

## 2. Tratamentos em produção

| # | Finalidade | Base legal | Dados | Titulares | Retenção | Operadores |
|---|------------|------------|-------|-----------|----------|------------|
| 1 | Conta e autenticação | Execução de contrato | E-mail, senha hash, perfil | Usuários | Vigência + logs 15–90 dias | Hospedagem |
| 2 | Prontuário / paciente | Tutela da saúde / contrato | Dados clínicos, documentos PDF | Pacientes | Conforme CFM/CRM + política interna | — |
| 3 | Treino / nutrição | Contrato | Hábitos, medidas, fotos evolução | Alunos | Política de retenção NexShape | OpenAI (IA opcional) |
| 4 | Pagamentos | Contrato / obrigação legal | Transações, assinatura | Clientes | 5+ anos (fiscal) | Mercado Pago, Asaas |
| 5 | Comissões representantes | Contrato | Vínculos, valores | Representantes | Enquanto relação ativa | — |

---

## 3. Medidas de segurança implementadas (técnicas)

- [ ] HTTPS obrigatório em produção
- [ ] `APP_DEBUG=false`, secrets de webhook configurados
- [ ] Multi-tenant (`academy_company_id`, impersonation admin auditada)
- [ ] Tokens Sanctum com expiração (`SANCTUM_TOKEN_EXPIRATION_DAYS`)
- [ ] Soft delete em `payments` / `commissions`
- [ ] Audit log CRUD (`User`, `Payment`, `Clinic`)
- [ ] Backup diário (Spatie) + teste de restore mensal documentado
- [ ] Purge de logs (`app:purge-old-logs`)

---

## 4. Direitos dos titulares

| Direito | Canal | SLA interno |
|---------|-------|-------------|
| Acesso / portabilidade | _e-mail DPO / menu privacidade_ | 15 dias |
| Correção | Suporte / perfil | 5 dias |
| Eliminação | Fluxo LGPD anonymization | 15 dias |
| Oposição marketing | Opt-out e-mail | Imediato |

---

## 5. Incidentes

| Data | Descrição | Impacto | Ação | Comunicação ANPD/titulares |
|------|-----------|---------|------|----------------------------|
| | | | | |

---

## 6. Revisão

- **Responsável:** _nome_
- **Próxima revisão:** _data (trimestral recomendado)_
- **Alterações desde última versão:** _resumo_

---

*Documento operacional — não substitui assessoria jurídica.*
