#Requires -Version 5.1
<#
.SYNOPSIS
  Prepara release de homologacao (build + checklist + smoke opcional).
#>
param(
    [string]$BaseUrl = $env:APP_URL,
    [switch]$SkipMigrate,
    [switch]$RunSmoke
)

$ErrorActionPreference = 'Stop'
$Backend = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $Backend

Write-Host '=== NexShape staging release ===' -ForegroundColor Cyan

& composer install --no-dev --optimize-autoloader
& npm ci
& npm run build

if (-not $SkipMigrate) {
    & php artisan migrate --force
}

& php artisan config:cache
& php artisan app:deploy:checklist --target=homologacao

if ($RunSmoke -and $BaseUrl) {
    & php artisan app:api:smoke --url=$BaseUrl
}

Write-Host 'Release de homologacao pronta.' -ForegroundColor Green
