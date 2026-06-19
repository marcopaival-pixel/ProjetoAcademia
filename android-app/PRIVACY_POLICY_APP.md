# App Android — privacidade e Data safety (Play Console)

Documento de apoio ao preenchimento do Play Console. **Não substitui** revisão jurídica.

## URL oficial (Play Console → Política de privacidade)

Use a rota já existente no Laravel:

```
https://{SEU-DOMINIO}/legal/privacy-policy
```

Exemplo produção: `https://app.nexshape.com.br/legal/privacy-policy`

Rota: `GET /legal/privacy-policy` (`PrivacyController`).

---

## Secção adicional recomendada (web ou anexo)

Publicar no site ou acrescentar à política web um parágrafo sobre o **app móvel**:

### App NexShape Academia (Android)

**Dados recolhidos pelo app**

| Dado | Finalidade | Armazenamento |
|------|------------|---------------|
| E-mail, nome, perfil | Autenticação e personalização | Servidor NexShape + token local encriptado |
| Planos de treino, registos de exercício | Funcionalidade core | Servidor |
| Medidas e fotos de evolução | Acompanhamento (opcional) | Servidor (ficheiros sensíveis) |
| Token FCM | Notificações push | Firebase / servidor |
| PIN de bloqueio (opcional) | Segurança local | Apenas dispositivo (hash, EncryptedSharedPreferences) |
| Erros técnicos (`client-errors`) | Estabilidade do app | Servidor (sem passwords) |

**Permissões Android**

- **Internet** — API REST
- **Notificações** — alertas e push (Android 13+)
- **Câmera / galeria** — upload de fotos de evolução (quando o utilizador escolhe)

**Terceiros**

- **Firebase Cloud Messaging** — entrega de push
- **Gateway de pagamento** (web) — assinaturas; o app abre checkout no browser

**Segurança**

- Token Bearer (Sanctum) em `EncryptedSharedPreferences`
- Release: apenas HTTPS
- Logout revoga token no servidor

**Direitos LGPD**

Os mesmos da plataforma web: acesso, correção, exportação e eliminação via perfil web ou contacto DPO.

**Contacto DPO:** dpo@projetoacademia.com.br (ajustar se diferente em produção)

---

## Play Console — Data safety (guia rápido)

Marque conforme a realidade do vosso contrato:

| Pergunta | Resposta típica |
|----------|-----------------|
| App recolhe dados? | Sim |
| Dados encriptados em trânsito? | Sim |
| Utilizador pode pedir eliminação? | Sim (via web / suporte) |
| Conta obrigatória? | Sim |
| Dados de saúde? | Sim (treino, medidas, fotos — declarar categoria saúde) |
| Identificadores (token dispositivo)? | Sim (FCM) |
| Dados vendidos a terceiros? | Não |

---

## Observabilidade

Erros do app podem ser enviados para `POST /api/v1/client-errors` (mensagem, stack truncado, versão). Não incluir passwords nem tokens nos relatórios.
