#!/bin/bash
# ============================================================
#   NexShape — Otimização Pós-Deploy no Servidor HostGator
#   Execute via SSH após enviar os arquivos via FTP
#   Uso: bash optimize_server.sh
# ============================================================

echo ""
echo "============================================"
echo "  NexShape — Otimização do Servidor"
echo "============================================"
echo ""

# Caminho da aplicação no servidor — ajuste se necessário
APP_DIR=~/NexShape

cd "$APP_DIR" || { echo "ERRO: Pasta $APP_DIR não encontrada."; exit 1; }

echo "[1/6] Limpando todos os caches antigos..."
php artisan optimize:clear
echo "OK"

echo ""
echo "[2/6] Reconstruindo cache de configuração..."
php artisan config:cache
echo "OK"

echo ""
echo "[3/6] Reconstruindo cache de rotas..."
php artisan route:cache
echo "OK"

echo ""
echo "[4/6] Reconstruindo cache de views..."
php artisan view:cache
echo "OK"

echo ""
echo "[5/6] Reconstruindo cache de eventos..."
php artisan event:cache
echo "OK"

echo ""
echo "[6/7] Verificando permissões de storage e bootstrap/cache..."
chmod -R 775 storage bootstrap/cache
echo "OK"

echo ""
echo "[7/7] Compilando ativos (JS/CSS) para produção..."
# Nota: Requer node/npm no servidor. Se usar CI/CD, este passo pode ser feito antes do upload.
if command -v npm &> /dev/null
then
    npm install && npm run build
    echo "OK"
else
    echo "Aviso: npm não encontrado. Certifique-se de enviar a pasta /public/build já compilada."
fi

echo ""
echo "============================================"
echo "  Otimização concluída com sucesso!"
echo "  Verifique o site agora: https://www.nexshape.com.br"
echo "============================================"
echo ""
echo "  Dicas adicionais de Alta Performance:"
echo "  - Verifique se o OPcache está ATIVO no seu PHP (obrigatório para Laravel)."
echo "  - Use Redis para CACHE_STORE e SESSION_DRIVER se disponível na sua VPS."
echo "  - Configure Cloudflare para cache de borda e compressão Brotli."
echo "  - Verifique o .env do servidor:"
echo "    APP_ENV=production"
echo "    APP_DEBUG=false"
echo "    LOG_LEVEL=error"
echo ""
