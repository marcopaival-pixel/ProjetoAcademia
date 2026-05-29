# Gera pacote de deploy NexShape (artefacto FTP/SSH).
# Uso: .\scripts\prepare-deploy-package.ps1 [-OutputDir dist]
param(
    [string]$OutputDir = "dist"
)

$ErrorActionPreference = "Stop"
$Root = Split-Path -Parent $PSScriptRoot
$Laravel = Join-Path $Root "laravel-app"
$Stamp = Get-Date -Format "yyyyMMdd-HHmm"
$ZipName = "nexshape-deploy-$Stamp.zip"
$OutPath = Join-Path $Root $OutputDir

if (-not (Test-Path $Laravel)) {
    Write-Error "Pasta laravel-app nao encontrada em $Root"
}

Push-Location $Laravel
try {
    Write-Host ">> composer install --no-dev"
    composer install --no-dev --optimize-autoloader --no-interaction

    Write-Host ">> npm ci && npm run build"
    npm ci
    npm run build

    if (-not (Test-Path "public\build")) {
        Write-Error "public/build nao encontrado apos npm run build"
    }
}
finally {
    Pop-Location
}

New-Item -ItemType Directory -Force -Path $OutPath | Out-Null
$ZipPath = Join-Path $OutPath $ZipName

$include = @(
    "app", "bootstrap", "config", "database", "public", "resources", "routes",
    "artisan", "composer.json", "composer.lock", "vendor"
)

$temp = Join-Path $env:TEMP "nexshape-deploy-$Stamp"
if (Test-Path $temp) { Remove-Item $temp -Recurse -Force }
New-Item -ItemType Directory -Path $temp | Out-Null

foreach ($item in $include) {
    $src = Join-Path $Laravel $item
    if (Test-Path $src) {
        Copy-Item $src (Join-Path $temp $item) -Recurse -Force
    }
}

# Excluir artefactos indevidos do pacote
@("public\build.zip", "tests", "node_modules", ".env", ".git") | ForEach-Object {
    $p = Join-Path $temp $_
    if (Test-Path $p) { Remove-Item $p -Recurse -Force }
}

Compress-Archive -Path (Join-Path $temp "*") -DestinationPath $ZipPath -Force
Remove-Item $temp -Recurse -Force

Write-Host ""
Write-Host "Pacote criado: $ZipPath"
Write-Host "No servidor: extrair, criar .env, php artisan key:generate, migrate --force, storage:link"
Write-Host "Ver docs/DEPLOY_NEXSHAPE.md"
