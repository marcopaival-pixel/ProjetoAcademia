@echo off
chcp 65001 >nul
echo.
echo ============================================
echo   NexShape — Deploy / Atualização do Sistema
echo ============================================
echo.

echo [1/6] Instalando dependencias PHP (sem dev)...
composer install --no-dev --optimize-autoloader
if %ERRORLEVEL% neq 0 (
    echo ERRO: Falha no composer install.
    pause
    exit /b 1
)

echo.
echo [2/6] Gerando build dos assets Vite (CSS + JS)...
call npm run build
if %ERRORLEVEL% neq 0 (
    echo ERRO: Falha no npm run build.
    pause
    exit /b 1
)
echo IMPORTANTE: Envie a pasta public\build\ para o servidor apos este script!
echo   Caminho local : public\build\
echo   Caminho remoto: /home2/marc9796/NexShape/public/build/

echo.
echo [3/6] Limpando e reconstruindo cache de configuracao...
php artisan config:cache
if %ERRORLEVEL% neq 0 ( echo AVISO: config:cache falhou. )

echo.
echo [4/6] Limpando e reconstruindo cache de rotas...
php artisan route:cache
if %ERRORLEVEL% neq 0 ( echo AVISO: route:cache falhou. )

echo.
echo [5/6] Limpando e reconstruindo cache de views...
php artisan view:cache
if %ERRORLEVEL% neq 0 ( echo AVISO: view:cache falhou. )

echo.
echo [6/6] Executando migrations pendentes (requer acesso ao servidor)...
echo AVISO: Execute no servidor: php artisan migrate --force
echo        O comando abaixo usa DB local — confirme se e isso que pretende:
php artisan migrate --force
if %ERRORLEVEL% neq 0 (
    echo ERRO: Falha nas migrations.
    pause
    exit /b 1
)

echo.
echo ============================================
echo   Passos OBRIGATORIOS apos este script:
echo.
echo   1. Envie os arquivos via FTP para o servidor:
echo      - public\build\         -> /NexShape/public/build/
echo      - public\css\           -> /NexShape/public/css/
echo      - resources\views\      -> /NexShape/resources/views/
echo.
echo   2. Confira o .env no servidor (via cPanel ou FTP):
echo      APP_ENV=production
echo      APP_DEBUG=false
echo      LOG_LEVEL=error
echo.
echo   3. Execute via SSH no servidor:
echo      bash ~/NexShape/optimize_server.sh
echo      (ou manualmente: php artisan optimize:clear ^&^& php artisan config:cache ^&^& php artisan route:cache ^&^& php artisan view:cache)
echo ============================================
echo.
pause
