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
echo "[6/6] Verificando permissões de storage e bootstrap/cache..."
chmod -R 775 storage bootstrap/cache
echo "OK"

echo ""
echo "============================================"
echo "  Otimização concluída com sucesso!"
echo "  Verifique o site agora: https://www.nexshape.com.br"
echo "============================================"
echo ""
echo "  Dicas adicionais:"
echo "  - Verifique o .env do servidor:"
echo "    APP_ENV=production"
echo "    APP_DEBUG=false"
echo "    LOG_LEVEL=error"
echo "    CACHE_STORE=file"
echo ""
