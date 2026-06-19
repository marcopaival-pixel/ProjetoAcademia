# Sobe a API Laravel acessível na rede local (celular na mesma Wi‑Fi).
# Uso: powershell -ExecutionPolicy Bypass -File scripts\serve-for-mobile.ps1

$ErrorActionPreference = "Stop"
$androidApp = Split-Path $PSScriptRoot -Parent
$repoRoot = Split-Path $androidApp -Parent
$laravel = Join-Path $repoRoot "laravel-app"

if (-not (Test-Path (Join-Path $laravel "artisan"))) {
    Write-Error "Não encontrei laravel-app/ em $laravel"
}

$ip = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object {
    $_.InterfaceAlias -notmatch 'Loopback|vEthernet|WSL' -and $_.IPAddress -notlike '169.254.*'
} | Sort-Object -Property InterfaceMetric | Select-Object -First 1).IPAddress

if (-not $ip) { $ip = "192.168.0.109" }

$apiUrl = "http://${ip}:8000/api/v1/"
$healthUrl = "${apiUrl}health"

Write-Host ""
Write-Host "=== NexShape — teste no celular ===" -ForegroundColor Cyan
Write-Host "1. Celular na MESMA Wi‑Fi que este PC"
Write-Host "2. No Chrome do celular, abra:" -ForegroundColor Yellow
Write-Host "   $healthUrl"
Write-Host "3. Deve aparecer JSON com status ok"
Write-Host ""
Write-Host "4. API_BASE_URL do app (debug) em app/build.gradle.kts:" -ForegroundColor Yellow
Write-Host "   `"$apiUrl`""
Write-Host ""
Write-Host "5. Android Studio -> Run no dispositivo USB (depuração USB activa)"
Write-Host ""
Write-Host "A iniciar: php artisan serve --host=0.0.0.0 --port=8000" -ForegroundColor Green
Write-Host "Ctrl+C para parar."
Write-Host ""

Set-Location $laravel
php artisan serve --host=0.0.0.0 --port=8000
