#Requires -Version 5.1
<#
.SYNOPSIS
  Prepara release de homologacao (build + checklist + smoke opcional).
#>
param(
    [string]$BaseUrl = $env:APP_URL,
    [switch]$SkipMigrate,
    [switch]$RunSmoke,
    [string]$Branch = (git rev-parse --abbrev-ref HEAD 2>$null)
)

$ErrorActionPreference = 'Stop'
$Backend = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $Backend

if ($Branch) {
    $env:BUGFIX_BRANCH = $Branch
}

Write-Host '=== NexShape staging release ===' -ForegroundColor Cyan

& composer install --no-dev --optimize-autoloader
& npm ci
& npm run build

if (-not $SkipMigrate) {
    & php artisan migrate --force
}

& php artisan config:cache
& php artisan app:deploy:checklist --target=homologacao

if ($Branch -and (git rev-parse --is-inside-work-tree 2>$null)) {
    & php artisan bug-surgeon:deploy-gate --target=homologacao --branch=$Branch
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
}

if ($RunSmoke -and $BaseUrl) {
    & php artisan app:api:smoke --url=$BaseUrl
}

Write-Host 'Release de homologacao pronta.' -ForegroundColor Green
