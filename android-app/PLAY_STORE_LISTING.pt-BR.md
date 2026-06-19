# Google Play — textos de listing (PT-BR)

Copiar/colar no Play Console. Ajuste e-mail, URL e nome comercial conforme produção.

---

## Nome da app (máx. 30 caracteres)

```
NexShape Academia
```

Alternativa curta: `NexShape`

---

## Descrição curta (máx. 80 caracteres)

```
Treinos, evolução e agenda — app oficial NexShape para aluno e profissional.
```

---

## Descrição completa (máx. 4000 caracteres)

```
NexShape Academia é o aplicativo oficial da plataforma NexShape para acompanhar sua jornada de saúde e performance — no papel de aluno ou de profissional vinculado.

PARA ALUNOS
• Login seguro e sincronização com sua conta NexShape
• Planos de treino com detalhes de exercícios
• Registro de evolução: medidas corporais e fotos de progresso
• Diário nutricional e chat NexBot (assistente)
• Agenda: consultas com profissionais vinculados
• Assinatura e planos premium via checkout seguro

PARA PROFISSIONAIS
• Painel com indicadores do dia
• Lista de alunos vinculados e seleção de aluno ativo
• Prescrição rápida de treinos e protocolos da clínica
• Avaliações e fotos de evolução do aluno
• Agenda profissional e alertas de saúde com notificações push

SEGURANÇA
• Token encriptado no dispositivo
• Bloqueio opcional por PIN ou biometria ao sair do app
• Comunicação com servidores via HTTPS em produção

A gestão de clínica, administração e funcionalidades avançadas de back-office continuam disponíveis no portal web NexShape.

Requer conta NexShape ativa. Alguns recursos dependem do plano contratado e do vínculo com profissional/clínica.

Suporte: suporte@nexshape.com.br
Política de privacidade: https://app.nexshape.com.br/legal/privacy-policy
```

Substituir domínio e e-mail pelos valores reais.

---

## Notas da versão (Internal testing v1.8.1)

```
Versão 1.8.1 — candidata a testes internos

• App aluno: treino, evolução, nutrição, agenda, assinatura
• Modo profissional: painel, clínico, alertas push
• Bloqueio PIN/biometria
• Melhorias de estabilidade (release)
```

---

## Categoria sugerida

- **Principal:** Saúde e fitness
- **Secundária (opcional):** Medicina (se aplicável à classificação interna)

---

## E-mail de contacto

```
suporte@nexshape.com.br
```

---

## Screenshots (roteiro)

Capture em telefone 1080×1920 ou superior (mín. 2):

| # | Ecrã | Modo |
|---|------|------|
| 1 | Home / dashboard aluno | Aluno |
| 2 | Detalhe do treino | Aluno |
| 3 | Evolução (fotos ou medidas) | Aluno |
| 4 | Agenda ou assinatura | Aluno |
| 5 | Painel profissional | Pro |
| 6 | Clínico (treinos + aluno ativo) | Pro |

Android Studio → **View → Tool Windows → Device Manager** → screenshot, ou botão câmera no emulador.

---

## Feature graphic (1024×500) — texto sugerido

```
NexShape Academia
Treino · Evolução · Profissional
```

Exportar no Canva/Figma ou Android Studio Asset Studio.

---

## Data safety (resumo para formulário)

Ver detalhe em [`PRIVACY_POLICY_APP.md`](PRIVACY_POLICY_APP.md).

- **Dados colectados:** e-mail, nome, dados de saúde/treino, fotos (opcional), token FCM
- **Encriptação em trânsito:** Sim (HTTPS)
- **Conta obrigatória:** Sim
- **Dados partilhados com terceiros:** Gateway de pagamento, Firebase (push) — declarar conforme contratos
