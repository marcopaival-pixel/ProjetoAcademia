# Manual Técnico: Padrão de Deploy Laravel em Hospedagem Compartilhada (HostGator)

Este documento estabelece o padrão técnico para a implantação de sistemas Laravel em ambientes de hospedagem compartilhada, visando segurança, performance e facilidade de manutenção.

---

## 📋 1. Checklist de Pré-Instalação

Antes de iniciar o upload, certifique-se de que:
- [ ] Versão do PHP no cPanel é >= 8.2.
- [ ] Extensões `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml` estão ativas.
- [ ] Banco de Dados MySQL e Usuário criados com privilégios totais.
- [ ] Certificado SSL (Let's Encrypt) ativo para o domínio.
- [ ] A pasta `vendor` e `node_modules` foram removidas para o upload (serão instaladas via SSH ou enviadas separadamente).
- [ ] O comando `npm run build` foi executado localmente (se usar Vite).

---

## 📂 2. Estrutura de Diretórios Padrão

Para isolar o código-fonte da raiz pública e evitar exposição de arquivos `.env` e logs:

```text
/home/usuario/
├── repositories/
│   └── nome-do-projeto/           <-- Código fonte (Arquivos do Laravel)
│       ├── app/
│       ├── bootstrap/
│       ├── config/
│       ├── database/
│       ├── storage/
│       ├── .env
│       └── ... (exceto a pasta public)
└── public_html/                   <-- Acesso Público
    ├── index.php                  <-- Alterado para apontar para /repositories/
    ├── .htaccess                  <-- Configurações de redirecionamento
    └── assets/ (css, js, imagens)
```

---

## 🛠️ 3. Script de Configuração Inicial (`deploy-fix.php`)

Crie este arquivo na raiz da `public_html/` para executar tarefas de manutenção sem necessidade de SSH. **Exclua-o após o uso.**

```php
<?php
/**
 * Script de Ajuste Pós-Deploy - EXCLUIR APÓS O USO
 */
$projectPath = __DIR__ . '/../repositories/nome-do-projeto';

// 1. Criar link simbólico para o Storage
if (!file_exists(__DIR__ . '/storage')) {
    symlink($projectPath . '/storage/app/public', __DIR__ . '/storage');
    echo "Link simbólico criado com sucesso.<br>";
}

// 2. Limpar Caches
echo "Limpando caches...<br>";
exec("php82 {$projectPath}/artisan config:cache");
exec("php82 {$projectPath}/artisan route:cache");
exec("php82 {$projectPath}/artisan view:cache");

echo "Configuração concluída.";
```

---

## 📄 4. Modelo de Arquivo `.env` (Produção)

```env
APP_NAME=NomeDoSistema
APP_ENV=production
APP_KEY=base64:GENERATE_ON_LOCAL_AND_COPY
APP_DEBUG=false
APP_URL=https://seudominio.com.br

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prefixo_nome_bd
DB_USERNAME=prefixo_usuario_bd
DB_PASSWORD=senha_segura_aqui

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

---

## 🚀 5. Procedimento de Implantação

### 5.1 Upload via FTP/Gerenciador de Arquivos
1. Compacte a pasta do projeto (sem `vendor`).
2. Envie para `/home/usuario/repositories/nome-do-projeto`.
3. Extraia o conteúdo da pasta `public` original para `public_html`.
4. Ajuste `public_html/index.php`:
   - Linha 34: `require __DIR__.'/../repositories/nome-do-projeto/vendor/autoload.php';`
   - Linha 47: `$app = require_once __DIR__.'/../repositories/nome-do-projeto/bootstrap/app.php';`

### 5.2 Configuração de Permissões
- Pastas dentro de `storage/` e `bootstrap/cache/`: **775**.
- Arquivos PHP e outros: **644**.
- Links simbólicos: **777**.

---

## 🔐 6. Banco de Dados, SSL e Logs

### Banco de Dados
- Utilize o **MySQL Database Wizard** no cPanel.
- Importe o dump `.sql` local via **phpMyAdmin** ou execute `php artisan migrate` via SSH.

### Configuração de SSL
- Acesse **SSL/TLS Status** no cPanel.
- Clique em **Run AutoSSL** para garantir que o Let's Encrypt está ativo.

### Monitoramento de Logs
- Os erros do sistema ficam em: `repositories/nome-do-projeto/storage/logs/laravel.log`.
- Os erros do servidor (Apache/PHP) ficam em: `public_html/error_log`.

---

## 💾 7. Rotina de Backup e Atualização

### Backup Semanal (Manual)
1. cPanel > Backups > Download a MySQL Database Backup.
2. cPanel > File Manager > Compactar a pasta `repositories/nome-do-projeto`.

### Procedimento de Atualização
1. Coloque o site em modo manutenção: `php artisan down`.
2. Envie apenas os arquivos alterados via FTP.
3. Se houver novas migrations: `php artisan migrate --force`.
4. Limpe o cache: `php artisan config:clear`.
5. Saia do modo manutenção: `php artisan up`.

---

## 🛠️ 8. Solução de Problemas Comuns

| Sintoma | Causa Provável | Solução |
| :--- | :--- | :--- |
| **Erro 500** | Permissões ou Versão PHP | Verificar logs em `storage/logs/` e versão do PHP no cPanel. |
| **Erro 403** | Falta de `.htaccess` ou Index | Garantir que o conteúdo da `public/` foi movido para `public_html`. |
| **Mist de Assets (Vite)** | APP_URL incorreta | Verificar `APP_URL` no `.env` e se o build foi gerado com `npm run build`. |
| **Storage não funciona** | Link simbólico quebrado | Executar o script `deploy-fix.php` ou criar link via SSH. |

---

## 🏁 9. Padrão para Ambientes de Produção

1. **Minificação:** Sempre envie assets compilados (`build/` ou `dist/`).
2. **Segurança:** Nunca versionar o arquivo `.env` no Git.
3. **Optimização:** Sempre utilize `php artisan config:cache` para acelerar o carregamento.
4. **Isolamento:** Cada projeto Laravel deve residir em sua própria pasta dentro de `repositories/`.
