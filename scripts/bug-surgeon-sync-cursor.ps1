#Requires -Version 5.1
<#
.SYNOPSIS
  Exporta session.json e authorization.json do Bug Surgeon para o Cursor.
.EXAMPLE
  .\scripts\bug-surgeon-sync-cursor.ps1 -IncidentCode BUG-2026-00001
#>
param(
    [Parameter(Mandatory = $true)]
    [string]$IncidentCode,
    [string]$DestDir = (Join-Path (Split-Path -Parent $PSScriptRoot) '.cursor\bug-surgeon')
)

$ErrorActionPreference = 'Stop'
$Backend = Join-Path (Split-Path -Parent $PSScriptRoot) 'backend'
Set-Location $Backend

& php artisan bug-surgeon:export-cursor $IncidentCode --path=$DestDir
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }

Write-Host "Cursor sync OK -> $DestDir" -ForegroundColor Green
