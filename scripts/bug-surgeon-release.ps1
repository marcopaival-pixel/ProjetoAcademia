#Requires -Version 5.1
<#
.SYNOPSIS
  Pipeline Bug Surgeon: testes direcionados + gate + staging release.
.EXAMPLE
  .\scripts\bug-surgeon-release.ps1 -Branch bugfix/BUG-2026-00001-patch
#>
param(
    [string]$Branch = (git rev-parse --abbrev-ref HEAD 2>$null),
    [string]$IncidentCode,
    [string]$BaseUrl = $env:APP_URL,
    [switch]$SkipMigrate,
    [switch]$RunSmoke,
    [switch]$SkipGate
)

$ErrorActionPreference = 'Stop'
$Root = Split-Path -Parent $PSScriptRoot
$Backend = Join-Path $Root 'backend'
Set-Location $Backend

if (-not $Branch) {
    Write-Error 'Branch não detectada. Use -Branch bugfix/BUG-YYYY-NNNNN-descricao'
}

$env:BUGFIX_BRANCH = $Branch
Write-Host "=== Bug Surgeon release ===" -ForegroundColor Cyan
Write-Host "Branch: $Branch"

$planPath = Join-Path $Root '.cursor\bug-surgeon\test-plan.json'
$filters = @('BugIncidentServiceTest')
if (Test-Path $planPath) {
    $plan = Get-Content $planPath -Raw | ConvertFrom-Json
    if ($plan.tests) { $filters = @($plan.tests) }
}

foreach ($f in $filters) {
    Write-Host "Test --filter=$f" -ForegroundColor Yellow
    & php artisan test --filter=$f
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
}

if ($IncidentCode) {
    & php artisan bug-surgeon:ci-passed $IncidentCode --branch=$Branch
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
}

if (-not $SkipGate) {
    & php artisan bug-surgeon:deploy-gate --target=homologacao --branch=$Branch
    if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
}

& (Join-Path $Backend 'scripts\staging-release.ps1') -BaseUrl $BaseUrl -SkipMigrate:$SkipMigrate -RunSmoke:$RunSmoke
exit $LASTEXITCODE
