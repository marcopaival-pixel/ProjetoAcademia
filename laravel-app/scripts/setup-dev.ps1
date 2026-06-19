# Setup local — ProjetoAcademia
$ErrorActionPreference = "Stop"
Set-Location (Split-Path $PSScriptRoot -Parent)

Write-Host "== Composer ==" -ForegroundColor Cyan
composer install --no-interaction

Write-Host "== Banco testing (PHPUnit) ==" -ForegroundColor Cyan
php artisan app:db:prepare-testing

Write-Host "== Menus admin (Deploy / Backup) ==" -ForegroundColor Cyan
php artisan db:seed --class=AdminPortalMenusSeeder

Write-Host "== Homolog bootstrap (checklist producao) ==" -ForegroundColor Cyan
php artisan db:seed --class=DeployHomologBootstrapSeeder

Write-Host "== Auditoria tenant ==" -ForegroundColor Cyan
php artisan app:audit:tenant
if ($LASTEXITCODE -ne 0) {
    Write-Host "Ainda existem models a revisar (veja tabela acima)." -ForegroundColor Yellow
}

Write-Host "== Git (se instalado) ==" -ForegroundColor Cyan
$git = Get-Command git -ErrorAction SilentlyContinue
if (-not $git) {
    $gitExe = "C:\Program Files\Git\bin\git.exe"
    if (Test-Path $gitExe) { $git = $gitExe } else { $git = $null }
}
if ($git) {
    if (-not (Test-Path ".git")) {
        & $git init
        & $git checkout -b desenvolvimento
        Write-Host "Repositorio Git inicializado na branch desenvolvimento." -ForegroundColor Green
    } else {
        Write-Host "Git ja inicializado." -ForegroundColor Green
    }
} else {
    Write-Host "Git nao encontrado — instale https://git-scm.com/" -ForegroundColor Yellow
}

Write-Host "Concluido." -ForegroundColor Green
