# NexShape Academia — Release Candidate (v1.8.1)

Estado do app Android **Aluno + Profissional** alinhado à API v1 (`laravel-app`). Clínica permanece no painel web.

## Versão

| Campo | Valor |
|-------|--------|
| `versionName` | 1.8.1 |
| `versionCode` | 4 |
| `applicationId` | `br.com.nexshape.academia` |
| `minSdk` | 26 |
| `targetSdk` | 35 |

## Escopo entregue (Sprints 1–9)

### Aluno
- Login Sanctum, refresh token, perfil `/me`
- Treinos, sync offline de exercícios, nutrição, evolução (medidas + fotos)
- Agenda (`/student/*`), assinatura/checkout + deep link
- Chat NexBot, push FCM

### Profissional
- Painel, alunos, agenda, alertas (marcar lido + push)
- Clínico: treinos, avaliações, fotos de evolução (view + upload)
- Aluno ativo (`X-Active-Patient-Id`)

### Plataforma
- Bloqueio PIN/biometria, telemetria `/client-errors`
- WorkManager sync offline, ProGuard release, ícones adaptativos
- Rede: debug HTTP (XAMPP), release HTTPS only
- Gradle Wrapper + CI (`android-ci.yml`)

## Pré-requisitos de produção

1. **Backend** em HTTPS com `laravel-app/public` como document root
2. **`FCM_SERVER_KEY`** no `.env` Laravel
3. **`google-services.json`** em `android-app/app/`
4. **`keystore.properties`** (ver `keystore.properties.example`)
5. URL release em `app/build.gradle.kts` → `release.buildConfigField("API_BASE_URL", ...)`

## Build release

```bash
cd android-app
./gradlew :app:bundleRelease    # Linux/macOS / Git Bash
gradlew.bat :app:bundleRelease  # Windows (requer JDK 17 no PATH ou JAVA_HOME)
# Saída: app/build/outputs/bundle/release/app-release.aab
```

Sem keystore, o AAB é gerado **não assinado** — use Android Studio → *Generate Signed App Bundle* para produção.

## Testes manuais (gate RC)

| # | Cenário | OK |
|---|---------|-----|
| 1 | Login aluno + `/me` carrega | ☐ |
| 2 | Treino: detalhe + sync offline após reconectar | ☐ |
| 3 | Evolução: foto upload + galeria autenticada | ☐ |
| 4 | Agenda: agendar slot | ☐ |
| 5 | Assinatura: checkout + deep link `nexshape://subscription/success` | ☐ |
| 6 | Modo Pro: selecionar aluno, prescrever treino | ☐ |
| 7 | Pro: upload foto evolução | ☐ |
| 8 | Alerta push ao profissional (HealthAlert) | ☐ |
| 9 | Bloqueio app: PIN/biometria ao voltar | ☐ |
| 10 | Release build: login só HTTPS (sem cleartext) | ☐ |

## API — testes automatizados

```bash
cd laravel-app
php artisan test --filter=ApiV1
# Esperado: 53 passed
```

## Play Console (resumo)

- Formato: **AAB** assinado
- Categoria: Saúde e fitness / Medicina (conforme classificação interna)
- Política de privacidade: URL pública obrigatória
- Permissões declaradas: Internet, notificações, câmera (opcional)
- Data safety: token Sanctum local encriptado; fotos via API autenticada

## Fora de escopo (v1)

- Painel Clínica / admin no app
- Pagamento in-app (Google Play Billing) — checkout via gateway web
- iOS

## CI

GitHub Actions (`.github/workflows/android-ci.yml`): `assembleDebug` + `lintDebug` em alterações em `android-app/`.

## Próximos passos opcionais

- Internal testing → [`PLAY_STORE_INTERNAL.md`](PLAY_STORE_INTERNAL.md)
- Build AAB → [`RELEASE_BUILD.md`](RELEASE_BUILD.md)
- Testes manuais → [`MANUAL_TEST_PLAN.md`](MANUAL_TEST_PLAN.md)
- Listing PT-BR → [`PLAY_STORE_LISTING.pt-BR.md`](PLAY_STORE_LISTING.pt-BR.md)
- `versionCode` +1 a cada upload
