# NexShape Academia — App Android

Cliente nativo Kotlin + Jetpack Compose para a API REST v1 (`laravel-app`).

## Requisitos

- Android Studio Ladybug (2024.2+) ou superior
- JDK 17
- Backend Laravel acessível (XAMPP ou produção)

## Abrir o projeto

1. Android Studio → **Open** → pasta `android-app/`
2. Aguarde sync Gradle
3. Se faltar wrapper, execute no terminal: `gradle wrapper` (ou use o Gradle embutido do Android Studio)

O repositório inclui **`gradlew`** / **`gradlew.bat`** — prefira:

```bash
cd android-app
./gradlew :app:assembleDebug      # Linux/macOS
gradlew.bat :app:assembleDebug    # Windows
```

## Configurar URL da API

Edite `app/build.gradle.kts` → `defaultConfig.buildConfigField("API_BASE_URL", ...)`:

| Ambiente | URL exemplo |
|----------|-------------|
| Emulador + XAMPP | `http://10.0.2.2/ProjetoAcademia/laravel-app/public/api/v1/` |
| Emulador + `artisan serve` | `http://10.0.2.2:8000/api/v1/` |
| Dispositivo físico (LAN) + XAMPP | `http://192.168.x.x/ProjetoAcademia/laravel-app/public/api/v1/` |
| **Dispositivo físico + `artisan serve` (recomendado dev)** | `http://192.168.x.x:8000/api/v1/` |
| Produção | `https://seu-dominio.com/api/v1/` |

### Teste rápido no celular (recomendado)

1. Na pasta `android-app`, execute:

```powershell
powershell -ExecutionPolicy Bypass -File scripts\serve-for-mobile.ps1
```

2. No telemóvel (mesma Wi‑Fi), abra no browser o link `/api/v1/health` que o script mostrar.
3. Ajuste `API_BASE_URL` em `app/build.gradle.kts` → `defaultConfig` com esse IP.
4. Android Studio → Run no dispositivo USB (depuração USB activa).

Ajuste o caminho conforme a pasta no Apache (`public/` é obrigatório).

## Funcionalidades implementadas (v1.8.1 — Sprint 9)

- **Gradle Wrapper** versionado (`gradlew`, Gradle 8.11.1) — build reproduzível
- **CI GitHub Actions** — `assembleDebug` + `lintDebug` (`.github/workflows/android-ci.yml`)
- **Guia Play Console** — [`PLAY_STORE_INTERNAL.md`](PLAY_STORE_INTERNAL.md)

## Funcionalidades (v1.8.0 — Sprint 8 / RC)

- **Ícones adaptativos** (`mipmap-anydpi-v26`) para Play Store
- **Rede por build:** debug permite HTTP (XAMPP); release só HTTPS
- **Assinatura release:** `keystore.properties` + `bundleRelease`
- **Shrink resources** + documento **`RELEASE_CANDIDATE.md`**

## Funcionalidades (v1.7.0 — Sprint 7)

- **Bloqueio do app:** PIN (4–6 dígitos) + biometria ao sair/voltar (Perfil → Segurança)
- **Splash screen** nativa (Android 12+)
- **ProGuard/R8** com regras para Retrofit, Moshi, Room e Coil
- **Sync periódico:** WorkManager reenvia fila offline a cada 15 min (com rede)
- **Logout corrigido:** revoga token Sanctum no servidor

## Checklist Play Store (release)

1. `google-services.json` em `app/` (FCM)
2. Ajustar `release.buildConfigField` com URL de produção real
3. Gerar keystore e configurar assinatura em Android Studio → Build → Generate Signed Bundle
4. `./gradlew assembleRelease` ou **Build → Generate Signed App Bundle (.aab)**
5. Testar release num dispositivo físico (login, bloqueio, push, checkout deep link)
6. Política de privacidade publicada (URL no Play Console)
7. Screenshots: Aluno (Home, Treino, Evolução) + Profissional (Painel, Clínico)
8. `versionCode` incrementado a cada upload (atual: **4**)

Ver checklist completo: [`RELEASE_CANDIDATE.md`](RELEASE_CANDIDATE.md) · Internal testing: [`PLAY_STORE_INTERNAL.md`](PLAY_STORE_INTERNAL.md)

## Documentação de publicação

| Ficheiro | Uso |
|----------|-----|
| [`RELEASE_BUILD.md`](RELEASE_BUILD.md) | Gerar keystore + AAB + env Laravel |
| [`MANUAL_TEST_PLAN.md`](MANUAL_TEST_PLAN.md) | QA aluno + profissional antes do upload |
| [`PLAY_STORE_LISTING.pt-BR.md`](PLAY_STORE_LISTING.pt-BR.md) | Textos Play Console |
| [`PRIVACY_POLICY_APP.md`](PRIVACY_POLICY_APP.md) | URL privacidade + Data safety |

### Assinatura release

1. Copie `keystore.properties.example` → `keystore.properties`
2. Gere o keystore (comando no ficheiro example)
3. `./gradlew :app:bundleRelease` → `app/build/outputs/bundle/release/`

## Funcionalidades (v1.6.0 — Sprint 6)

- **Upload de fotos (Pro):** profissional registra evolução do aluno ativo na aba Clínico → Fotos
- **Deep link checkout:** `nexshape://subscription/{status}` + redirect web `/app/subscription/return/{status}`
- **Telemetria:** erros do app enviados para `POST /client-errors`
- **Sync offline:** fila de treinos reenviada ao abrir o app / login

## Funcionalidades (v1.5.0 — Sprint 5)

- **Fotos de evolução (Pro):** galeria do aluno ativo com imagens autenticadas
- **Push FCM:** notificações visíveis + alertas disparam push ao profissional
- **Token refresh:** renovação automática do Bearer em respostas 401

## Funcionalidades (v1.4.0 — Sprint 4)

- **Acompanhamento clínico (Pro):** treinos e avaliações do aluno ativo
- **Prescrição rápida** + aplicar protocolo da clínica
- **Alertas:** marcar como lido com toque

## Funcionalidades (v1.3.0 — Sprint 3)

- **Modo Profissional:** painel, alunos vinculados, agenda do dia, alertas
- **Seleção de aluno ativo:** persiste localmente + header `X-Active-Patient-Id`
- **Alternância Aluno/Pro** no perfil (utilizadores com ambos os roles)

## Funcionalidades (v1.2.0 — Sprint 2)

- **Agenda:** profissionais vinculados, consultas, slots e agendamento (`/student/*`)
- **Checkout assinatura:** botão Assinar abre gateway ou ativa plano grátis
- **Treino:** detalhe com séries/reps/descanso, registo offline + botão sincronizar

## Funcionalidades (v1.1.0 — Sprint 1)

- Login Sanctum (`POST /auth/token`) + token encriptado (EncryptedSharedPreferences)
- Desbloqueio biométrico/PIN configurável no Perfil (bloqueio ao background)
- Home com perfil enriquecido (`GET /me` — panels, branding, is_professional)
- Planos de treino (`GET /training-plans`, detalhe)
- **Evolução:** avaliações (`GET/POST /assessments`) + fotos (`GET/POST /evolution-photos`) com imagens autenticadas (Coil)
- Diário nutricional (leitura + adicionar alimento)
- Chat NexBot (`/chat/send`, `/chat/history`)
- **Assinatura:** status de pagamento + listagem de planos (`/payments/status`, `/subscriptions/plans`)
- Fila offline Room para sync de exercícios (`POST /exercise-logs/sync`)
- **Push FCM:** `FirebaseMessagingService` + registo automático após login (`POST /devices`)

## Firebase / Push

1. Criar projeto Firebase e app Android (`br.com.nexshape.academia`)
2. Baixar `google-services.json` → `app/` (o plugin Gradle só é aplicado se o ficheiro existir)
3. Configure `FCM_SERVER_KEY` no `.env` do Laravel
4. Após login, o app regista o token automaticamente via `PushTokenManager`

Sem `google-services.json`, o app funciona normalmente — apenas push fica desativado.

## Estrutura

```
app/src/main/java/br/com/nexshape/academia/
├── data/api/          Retrofit, DTOs, interceptors
├── data/local/        TokenStore, Room (offline queue)
├── data/repository/   Auth, Training, Nutrition, Chat
├── ui/                Compose screens
├── security/          BiometricHelper, PinHasher, AppLockStore
├── sync/              OfflineSyncWorker (WorkManager)
├── observability/     ClientErrorReporter
└── push/              DeviceRegistration
```

## Testar

1. Subir Apache/XAMPP com `laravel-app/public` acessível
2. `php artisan migrate` no Laravel
3. Criar utilizador aluno ou usar conta existente
4. Run no emulador Android 8+ (API 26+)

## Documentação da API

Ver `../laravel-app/docs/API_V1.md` e `openapi-v1.yaml`.
