# Build de release — passo a passo (Windows / XAMPP → produção)

Checklist operacional para gerar o **AAB v1.8.1** e validar o backend antes do Play Console.

## 1. Backend Laravel (produção ou staging HTTPS)

```bash
cd laravel-app
php artisan test --filter=ApiV1
# Esperado: 53 passed
```

No `.env` de produção, confirmar:

| Variável | Uso no app |
|----------|------------|
| `APP_URL` | Base dos links (ex.: `https://app.nexshape.com.br`) |
| `FCM_SERVER_KEY` | Push para profissionais/alunos |
| Sanctum / sessão | Login `POST /api/v1/auth/token` |

Rotas usadas pelo app:

- API: `{APP_URL}/api/v1/`
- Retorno checkout: `{APP_URL}/app/subscription/return/success` → deep link `nexshape://subscription/success`
- Política de privacidade (Play Console): `{APP_URL}/legal/privacy-policy`

Teste rápido:

```bash
curl -s https://SEU-DOMINIO/api/v1/health
curl -sI https://SEU-DOMINIO/legal/privacy-policy
```

## 2. URL da API no app (release)

Editar `android-app/app/build.gradle.kts` → bloco `release`:

```kotlin
buildConfigField(
    "String",
    "API_BASE_URL",
    "\"https://SEU-DOMINIO/api/v1/\""
)
```

Deve terminar com `/api/v1/` (barra final).

## 3. Firebase (push)

1. Firebase Console → projeto → app Android `br.com.nexshape.academia`
2. Descarregar `google-services.json` → `android-app/app/google-services.json`
3. Não versionar (já está no `.gitignore`)

## 4. Keystore de release

Na pasta `android-app/`:

```powershell
keytool -genkey -v -keystore nexshape-release.jks -keyalg RSA -keysize 2048 -validity 10000 -alias nexshape
```

Copiar `keystore.properties.example` → `keystore.properties` e preencher:

```properties
storeFile=nexshape-release.jks
storePassword=SUA_SENHA
keyAlias=nexshape
keyPassword=SUA_SENHA
```

**Backup:** guarde `.jks` e passwords num cofre — perda impede atualizações na Play Store.

## 5. JDK 17

Android Studio → **Settings → Build → Gradle → Gradle JDK** → 17.

Ou defina `JAVA_HOME` (ex.: JBR do Android Studio):

```powershell
$env:JAVA_HOME = "C:\Program Files\Android\Android Studio\jbr"
$env:Path = "$env:JAVA_HOME\bin;$env:Path"
java -version
```

## 6. Gerar AAB

```powershell
cd android-app
.\gradlew.bat :app:bundleRelease --no-daemon
```

Saída:

`android-app/app/build/outputs/bundle/release/app-release.aab`

### Alternativa (Android Studio)

**Build → Generate Signed App Bundle / APK** → Android App Bundle → keystore → release.

## 7. Validar AAB localmente (opcional)

Instalar [bundletool](https://github.com/google/bundletool) e gerar APKs para dispositivo:

```powershell
java -jar bundletool.jar build-apks --bundle=app\build\outputs\bundle\release\app-release.aab --output=nexshape.apks --mode=universal
java -jar bundletool.jar install-apks --apks=nexshape.apks
```

Ou enviar directamente ao **Internal testing** (recomendado).

## 8. Antes de upload Play Console

- [ ] `versionCode` incrementado (`app/build.gradle.kts`)
- [ ] Testes manuais: [`MANUAL_TEST_PLAN.md`](MANUAL_TEST_PLAN.md)
- [ ] Listing: [`PLAY_STORE_LISTING.pt-BR.md`](PLAY_STORE_LISTING.pt-BR.md)
- [ ] URL privacidade no Console = `{APP_URL}/legal/privacy-policy`
- [ ] Data safety preenchido: [`PRIVACY_POLICY_APP.md`](PRIVACY_POLICY_APP.md)

## 9. Upload

Seguir [`PLAY_STORE_INTERNAL.md`](PLAY_STORE_INTERNAL.md).

## Problemas comuns

| Sintoma | Causa provável |
|---------|----------------|
| `JAVA_HOME is not set` | JDK 17 não no PATH — ver passo 5 |
| Login falha em release | URL API errada ou HTTP bloqueado (release só HTTPS) |
| Push não chega | Falta `google-services.json` ou `FCM_SERVER_KEY` |
| Checkout não volta ao app | Gateway não redireciona para `/app/subscription/return/...` |
| Crash só em release | ProGuard — rever `app/proguard-rules.pro` e logs `client-errors` |
