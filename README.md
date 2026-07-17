# NexShape

Monorepo do ecossistema NexShape.

## Estrutura

```text
NexShape/
├── backend/          # API e painel Laravel
├── android/          # App Android Kotlin/Jetpack Compose
├── ios/              # Reservado para app iOS
├── ai-orchestrator/  # Reservado para orquestracao de IA fora do Laravel
├── ai-agents/        # Prompts e definicoes dos agentes de IA
├── docs/             # Documentacao tecnica e operacional
├── docker/           # Compose e artefatos Docker
└── scripts/          # Scripts auxiliares do monorepo
```

## Backend

```powershell
cd backend
composer install
php artisan serve --host=0.0.0.0 --port=8000
```

## Android

Abra a pasta `android/` no Android Studio. Em desenvolvimento local, a API padrao fica em `http://<ip-do-pc>:8000/api/v1/`.
