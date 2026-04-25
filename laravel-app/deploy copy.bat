@echo off
chcp 65001 >nul
echo.
echo ============================================
echo   NexShape — Deploy / Atualização do Sistema
echo ============================================
echo.

echo [1/5] Instalando dependencias PHP (sem dev)...
composer install --no-dev --optimize-autoloader
if %ERRORLEVEL% neq 0 (
    echo ERRO: Falha no composer install.
    pause
    exit /b 1
)

echo.
echo [2/5] Limpando e reconstruindo cache de configuracao...
php artisan config:cache
if %ERRORLEVEL% neq 0 ( echo AVISO: config:cache falhou. )

echo.
echo [3/5] Limpando e reconstruindo cache de rotas...
php artisan route:cache
if %ERRORLEVEL% neq 0 ( echo AVISO: route:cache falhou. )

echo.
echo [4/5] Limpando e reconstruindo cache de views...
php artisan view:cache
if %ERRORLEVEL% neq 0 ( echo AVISO: view:cache falhou. )

echo.
echo [5/5] Executando migrations pendentes...
php artisan migrate --force
if %ERRORLEVEL% neq 0 (
    echo ERRO: Falha nas migrations.
    pause
    exit /b 1
)

echo.
echo ============================================
echo   Deploy concluido com sucesso!
echo ============================================
echo.
pause
