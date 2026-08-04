# Setup QA - Perfil Aluno (NexShape)
# Prepara ambiente local + 9 usuarios de teste + custos IA + smoke da API
#
# Uso:
#   .\scripts\qa-aluno-setup.ps1
#   .\scripts\qa-aluno-setup.ps1 -SkipMigrate
#   .\scripts\qa-aluno-setup.ps1 -RunSmoke

param(
    [switch]$SkipMigrate,
    [switch]$RunSmoke,
    [int]$Port = 8000
)

$ErrorActionPreference = "Stop"
$Root = Split-Path $PSScriptRoot -Parent
$Backend = Join-Path $Root "backend"

Set-Location $Backend

Write-Host "== NexShape QA - Perfil Aluno ==" -ForegroundColor Cyan
Write-Host "Backend: $Backend" -ForegroundColor DarkGray
Write-Host ""

if (-not (Test-Path ".env")) {
    Write-Host "Copiando .env.example -> .env" -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
    php artisan key:generate
}

if (-not $SkipMigrate) {
    Write-Host "== Migrations ==" -ForegroundColor Cyan
    php artisan migrate --force
}

Write-Host "== Seed base (roles, planos, clinica) ==" -ForegroundColor Cyan
php artisan db:seed --class=RolesAndPermissionsSeeder --force
php artisan db:seed --class=PlanSeeder --force
php artisan db:seed --class=AcademyCompanySeeder --force

Write-Host "== Seed custos IA (fail-closed sem feature costs) ==" -ForegroundColor Cyan
php artisan db:seed --class=AiFeatureCostSeeder --force

if ($LASTEXITCODE -ne 0) {
    Write-Host "Falha ao executar AiFeatureCostSeeder." -ForegroundColor Red
    exit $LASTEXITCODE
}

Write-Host "== Seed QA - 9 perfis de aluno ==" -ForegroundColor Cyan
php artisan db:seed --class=StudentProfileTestSeeder --force

if ($LASTEXITCODE -ne 0) {
    Write-Host "Falha ao executar StudentProfileTestSeeder." -ForegroundColor Red
    exit $LASTEXITCODE
}

Write-Host ""
Write-Host "== Credenciais QA ==" -ForegroundColor Green
Write-Host "Senha de todos os alunos QA: Teste@123"
Write-Host ""
Write-Host "  free            aluno.free@test.nexshape"
Write-Host "  premium         aluno.premium@test.nexshape"
Write-Host "  linked_pro      aluno.vinculado.pro@test.nexshape"
Write-Host "  linked_clinic   aluno.vinculado.clinica@test.nexshape"
Write-Host "  aluno_paciente  aluno.paciente@test.nexshape"
Write-Host "  expired         aluno.expirado@test.nexshape"
Write-Host "  incomplete      aluno.incompleto@test.nexshape"
Write-Host "  new             aluno.novo@test.nexshape"
Write-Host "  complete        aluno.completo@test.nexshape"
Write-Host "  profissional    prof.qa@test.nexshape"
Write-Host ""
Write-Host "Web login:     http://localhost:$Port/login" -ForegroundColor Yellow
Write-Host "Creditos IA:   http://localhost:$Port/ai-credits" -ForegroundColor Yellow
Write-Host "Demo:          http://localhost:$Port/demo/start?profile=aluno" -ForegroundColor Yellow
Write-Host "Checklist:     docs/TESTE_ALUNO_CHECKLIST.md" -ForegroundColor Yellow
Write-Host "Checklist IA:  C:\Manual\NexShape\AVALIACAO_IA_SEGURANCA.md" -ForegroundColor Yellow
Write-Host "Regressao:     cd backend; composer test   # 67 testes" -ForegroundColor Yellow
Write-Host ""

if ($RunSmoke) {
    Write-Host "== Smoke test API ==" -ForegroundColor Cyan
    php artisan app:api:smoke `
        --url="http://localhost:$Port" `
        --email="aluno.completo@test.nexshape" `
        --password="Teste@123"
}

Write-Host "Para iniciar o servidor:" -ForegroundColor Cyan
Write-Host "  cd backend; php artisan serve --host=0.0.0.0 --port=$Port" -ForegroundColor White
Write-Host ""
Write-Host "Concluido." -ForegroundColor Green
