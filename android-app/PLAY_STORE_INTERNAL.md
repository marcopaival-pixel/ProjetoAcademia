# Internal testing — Google Play Console

Guia para publicar o **NexShape Academia v1.8.x** na faixa *Internal testing* antes da produção.

## Pré-requisitos

1. Conta [Google Play Console](https://play.google.com/console) (taxa única de registo)
2. AAB assinado (`./gradlew :app:bundleRelease` com `keystore.properties`)
3. `google-services.json` no build de produção (FCM)
4. Política de privacidade em URL pública — ver [`PRIVACY_POLICY_APP.md`](PRIVACY_POLICY_APP.md) (`/legal/privacy-policy`)

Documentação relacionada:

| Documento | Conteúdo |
|-----------|----------|
| [`RELEASE_BUILD.md`](RELEASE_BUILD.md) | Keystore, AAB, Firebase, JDK |
| [`MANUAL_TEST_PLAN.md`](MANUAL_TEST_PLAN.md) | Testes aluno + profissional |
| [`PLAY_STORE_LISTING.pt-BR.md`](PLAY_STORE_LISTING.pt-BR.md) | Textos da loja |
| [`PRIVACY_POLICY_APP.md`](PRIVACY_POLICY_APP.md) | Data safety + URL privacidade |

## 1. Criar a app

1. Play Console → **Criar app**
2. Nome: **NexShape** (ou NexShape Academia)
3. Idioma predefinido: **Português (Brasil)**
4. Tipo: App · Gratuito (ou conforme modelo de negócio)

## 2. Configuração da loja (rascunho)

Preencher o mínimo para internal testing:

| Secção | Conteúdo sugerido |
|--------|-------------------|
| Descrição curta / completa | Copiar de [`PLAY_STORE_LISTING.pt-BR.md`](PLAY_STORE_LISTING.pt-BR.md) |
| Ícone | Exportar 512×512 do adaptive icon (Android Studio → Image Asset) |
| Screenshots | Telefone: Home, Treino, Evolução, Modo Pro (mín. 2) |
| Categoria | Saúde e fitness |
| E-mail de contacto | suporte@nexshape.com.br (ajustar) |
| Política de privacidade | `{APP_URL}/legal/privacy-policy` — ver [`PRIVACY_POLICY_APP.md`](PRIVACY_POLICY_APP.md) |

## 3. App content (declarações)

- **Privacidade:** dados de conta, fotos de evolução, tokens FCM — declarar conforme formulário Data safety
- **Permissões:** Internet, notificações, câmera (opcional para fotos)
- **Público-alvo:** 18+ ou conforme orientação jurídica
- **Anúncios:** Não (se aplicável)

## 4. Upload Internal testing

1. **Testing → Internal testing**
2. **Criar nova versão**
3. Upload do ficheiro `app-release.aab`
4. Notas da versão (exemplo):

   ```
   v1.8.1 — Release candidate
   - Aluno: treino, evolução, agenda, assinatura
   - Profissional: painel, clínico, alertas push
   - Bloqueio PIN/biometria
   ```

5. **Rever e publicar** na faixa internal

## 5. Testadores

1. Internal testing → **Testers**
2. Criar lista de e-mails (equipa QA)
3. Partilhar **link opt-in** com testadores
4. Instalar via Play Store (não sideload do AAB)

## 6. Deep link checkout

Configurar no gateway de pagamento redirect para:

`https://SEU-DOMINIO/app/subscription/return/success`

(confirma abertura do app via `nexshape://subscription/success`)

## 7. Checklist pós-upload

- [ ] Login aluno em produção (HTTPS)
- [ ] Push FCM recebido
- [ ] Modo profissional + aluno ativo
- [ ] Bloqueio ao background
- [ ] Sem crash em release (R8/ProGuard)

## 8. Promover para produção

Após internal + closed testing estável:

1. **Production → Criar versão**
2. Reutilizar o mesmo AAB (ou novo `versionCode`)
3. Rollout gradual (ex.: 10% → 50% → 100%)

## CI

O workflow `.github/workflows/android-ci.yml` valida `assembleDebug` e `lintDebug` em cada push que altere `android-app/`.
