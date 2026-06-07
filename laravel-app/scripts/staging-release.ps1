# NexShape — release em homologação/staging (Windows)
# Executar na pasta laravel-app: .\scripts\staging-release.ps1

$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot\..

Write-Host "=== NexShape staging release ===" -ForegroundColor Cyan
Write-Host "Versao: $(Get-Content VERSION -Raw)"

Write-Host "`n[1/6] Dependencias PHP..." -ForegroundColor Yellow
composer install --no-interaction --prefer-dist --optimize-autoloader

Write-Host "`n[2/6] Migrations..." -ForegroundColor Yellow
php artisan migrate --force

Write-Host "`n[3/6] Bootstrap homologacao (se aplicavel)..." -ForegroundColor Yellow
php artisan db:seed --class=DeployHomologBootstrapSeeder --force

Write-Host "`n[4/6] Cache de configuracao..." -ForegroundColor Yellow
php artisan config:cache
php artisan route:cache
php artisan view:cache

Write-Host "`n[5/6] Verificacao de release..." -ForegroundColor Yellow
php artisan app:release:verify --target=homologacao
if ($LASTEXITCODE -ne 0) {
    Write-Host "Verificacao falhou. Corrija antes de expor staging." -ForegroundColor Red
    exit 1
}

Write-Host "`n[5b/6] PHPUnit..." -ForegroundColor Yellow
composer test
if ($LASTEXITCODE -ne 0) {
    Write-Host "Testes falharam." -ForegroundColor Red
    exit 1
}

Write-Host "`n[6/6] Smoke manual (checklist):" -ForegroundColor Yellow
@(
    "Login: admin, profissional, paciente, representante"
    "Checkout sandbox Mercado Pago + webhook"
    "2 clinicas com dados isolados"
    "Aprovar homologacao em /admin/deploy"
) | ForEach-Object { Write-Host "  - $_" }

Write-Host "`nStaging release OK." -ForegroundColor Green
