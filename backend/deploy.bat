@echo off
setlocal enabledelayedexpansion

echo === NexShape deploy (Windows) ===
cd /d "%~dp0"

where composer >nul 2>&1
if errorlevel 1 (
  echo [ERRO] Composer nao encontrado no PATH.
  exit /b 1
)

where npm >nul 2>&1
if errorlevel 1 (
  echo [ERRO] npm nao encontrado no PATH.
  exit /b 1
)

echo [1/6] composer install --no-dev
call composer install --no-dev --optimize-autoloader
if errorlevel 1 exit /b 1

echo [2/6] npm ci
call npm ci
if errorlevel 1 exit /b 1

echo [3/6] npm run build
call npm run build
if errorlevel 1 exit /b 1

echo [4/6] php artisan migrate --force
call php artisan migrate --force
if errorlevel 1 exit /b 1

echo [5/6] php artisan config:cache
call php artisan config:cache

echo [6/6] php artisan app:deploy:checklist --target=homologacao
call php artisan app:deploy:checklist --target=homologacao

echo.
echo Deploy local concluido. Publique vendor/, public/build/ e demais pastas conforme docs/DEPLOY_NEXSHAPE.md
endlocal
