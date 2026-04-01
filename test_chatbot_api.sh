#!/usr/bin/env bash

# Script para testar a API do Chatbot Nutricional
# Execute: bash test_chatbot_api.sh

API_URL="http://localhost:8000"
CSRF_TOKEN=""

echo "🧪 Testando API do Chatbot Nutricional"
echo "======================================"
echo ""

# 1. Login para obter session e CSRF token
echo "1️⃣ Fazendo login..."
LOGIN_RESPONSE=$(curl -s -c cookies.txt "$API_URL/login" -X GET)
CSRF_TOKEN=$(grep -oP 'csrf-token" content="\K[^"]*' /dev/stdin <<< "$LOGIN_RESPONSE" 2>/dev/null || echo "test-token")

echo "   CSRF Token obtido (ou usando padrão)"
echo ""

# 2. Testar envio de mensagem
echo "2️⃣ Enviando mensagem para o chatbot..."
SEND_RESPONSE=$(curl -s -b cookies.txt "$API_URL/api/chat/send" \
  -X POST \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
  -d '{"message":"Quantas calorias tem 100g de frango grelhado?"}')

echo "   Resposta:"
echo "$SEND_RESPONSE" | jq '.' 2>/dev/null || echo "$SEND_RESPONSE"
echo ""

# 3. Obter histórico
echo "3️⃣ Obtendo histórico de chat..."
HISTORY_RESPONSE=$(curl -s -b cookies.txt "$API_URL/api/chat/history?limit=10" \
  -X GET \
  -H "X-CSRF-TOKEN: $CSRF_TOKEN")

echo "   Histórico:"
echo "$HISTORY_RESPONSE" | jq '.messages' 2>/dev/null || echo "$HISTORY_RESPONSE"
echo ""

# 4. Limpar histórico
echo "4️⃣ Limpando histórico..."
CLEAR_RESPONSE=$(curl -s -b cookies.txt "$API_URL/api/chat/clear" \
  -X POST \
  -H "X-CSRF-TOKEN: $CSRF_TOKEN")

echo "   Resposta:"
echo "$CLEAR_RESPONSE" | jq '.' 2>/dev/null || echo "$CLEAR_RESPONSE"
echo ""

echo "✅ Testes concluídos!"
