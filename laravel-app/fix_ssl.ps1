# Script para corrigir erro de SSL no PHP/Composer (Windows/XAMPP)
$certPath = "C:\xampp\php\extras\ssl\cacert.pem"
$iniPath = "C:\xampp\php\php.ini"

# 1. Criar diretório se não existir
if (!(Test-Path "C:\xampp\php\extras\ssl")) {
    New-Item -ItemType Directory -Path "C:\xampp\php\extras\ssl" -Force
}

# 2. Copiar o certificado do workspace para a pasta do PHP
echo "Copiando certificado para a pasta do PHP..."
Copy-Item "cacert.pem" -Destination $certPath -Force

# 3. Atualizar php.ini com barras normais (mais compatível)
echo "Configurando php.ini..."
$certPathForward = $certPath -replace '\\', '/'
$content = Get-Content $iniPath
$content = $content -replace '^;?curl.cainfo\s*=.*', "curl.cainfo = `"$certPathForward`""
$content = $content -replace '^;?openssl.cafile\s*=.*', "openssl.cafile = `"$certPathForward`""
Set-Content $iniPath $content

echo "-------------------------------------------------------"
echo "SUCESSO! O certificado foi instalado e o php.ini atualizado."
echo "IMPORTANTE: Reinicie o APACHE no Painel de Controle do XAMPP."
echo "Depois, tente rodar: composer update"
echo "-------------------------------------------------------"
