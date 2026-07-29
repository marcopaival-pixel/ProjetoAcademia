# App iOS NexShape — status

**Estado (jul/2026):** não iniciado / fora do escopo deste repositório.

O monorepo contém:

- `backend/` — Laravel (API + web)
- `android/` — app Kotlin/Compose (aluno, profissional, paciente)
- `docs/` — documentação e auditorias

Não existe pasta `ios/` nem target Xcode no Git. O roadmap de produto prevê iOS como fase futura; até lá:

- Utilizar **PWA** ou **Android** para mobile
- API REST v1 (`docs/API_V1.md`, `docs/openapi-v1.yaml`) já preparada para clientes nativos

**Quando iniciar o iOS:**

1. Criar módulo `ios/` com SwiftUI + URLSession/Alamofire
2. Reutilizar contratos Sanctum (`POST /auth/token`, refresh, `X-Active-Context` paciente)
3. Publicar na App Store apenas após smoke test em homolog (`php artisan app:api:smoke`)
