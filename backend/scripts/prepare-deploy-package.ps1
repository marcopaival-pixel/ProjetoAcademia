#Requires -Version 5.1
<#
.SYNOPSIS
  Gera pacote ZIP de deploy em backend/dist/
#>
param(
    [string]$OutputName = "nexshape-deploy.zip"
)

$ErrorActionPreference = 'Stop'
$Backend = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $Backend

Write-Host '=== Preparando pacote de deploy ===' -ForegroundColor Cyan

& composer install --no-dev --optimize-autoloader
& npm ci
& npm run build

$dist = Join-Path $Backend 'dist'
New-Item -ItemType Directory -Force -Path $dist | Out-Null
$zipPath = Join-Path $dist $OutputName
if (Test-Path $zipPath) { Remove-Item $zipPath -Force }

$items = @(
    'app', 'bootstrap', 'config', 'database', 'public', 'resources', 'routes',
    'artisan', 'composer.json', 'composer.lock', 'vendor'
)

Compress-Archive -Path $items -DestinationPath $zipPath -Force

Write-Host "Pacote gerado: $zipPath" -ForegroundColor Green
