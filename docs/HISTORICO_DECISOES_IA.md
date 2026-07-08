# Histórico de Decisões da IA

Este documento regista as decisões arquiteturais e correções implementadas de forma autónoma ou assistida, para fins de governança e rastreabilidade da equipa.

## 2026-07-02
**Contexto:** Auditoria de Segurança e Infraestrutura (Android & Backend)
**Agente Responsável:** Agente de Governança (Antigravity)

**Decisões e Implementações:**

1. **Correção de Vazamento de Dados (Android - Offline Sync):**
   - **Problema:** A função `logout()` apenas limpava o token do `TokenStore`, mas não apagava o banco de dados `Room` (onde residem registos pendentes do utilizador) nem cancelava o `WorkManager`. Isto permitia vazamento de dados se outro paciente fizesse login na mesma máquina.
   - **Solução:** Adicionado `AppDatabase.get(appContext).clearAllTables()` e `WorkManager.getInstance(appContext).cancelAllWork()` no método `logout()` de `AuthRepository.kt`.

2. **Infraestrutura de Testes de UI (Android):**
   - **Decisão:** Criação de ambiente base de testes (Jetpack Compose).
   - **Implementação:** Injeção das bibliotecas `androidx-compose-ui-test-junit4` no `libs.versions.toml`, criação do `LoginScreenTest.kt` no diretório `androidTest` e integração de um emulador *Headless* no GitHub Actions (`android-ci.yml`) usando `macos-latest` para aceleração de hardware gratuita (HAXM).

3. **Resolução de Relações e N+1 (Backend - Laravel):**
   - **Problema:** O *PHPStan* acusou a ausência da relação Eloquent `professional()` nos modelos `TrainingPlan`, `BodyAssessment` e `PatientDocument`. Isto causaria erros graves durante o *Eager Loading* e potenciais *N+1 queries*.
   - **Solução:** Adição da tipagem e método `professional(): BelongsTo` nas três classes.
   - **Falsos Positivos:** O pacote `barryvdh/laravel-ide-helper` foi instalado e o ficheiro `_ide_helper_models.php` foi gerado para resolver mais de 150 falsos alarmes de propriedades no PHPStan.
