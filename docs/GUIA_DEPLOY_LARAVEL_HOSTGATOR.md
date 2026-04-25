# Guia de Instalação e Configuração: Laravel na HostGator (Hospedagem Compartilhada)

Este guia fornece um passo a passo profissional para implantar uma aplicação Laravel em servidores HostGator, garantindo segurança e funcionalidade.

---

## 1. Preparação do Ambiente Local

Antes de subir para o servidor, garanta que seu projeto está pronto.

### Requisitos
*   **PHP:** Versão compatível com o Laravel (ex: 8.2 ou 8.3).
*   **Composer:** Gerenciador de dependências instalado.
*   **Banco de Dados:** MySQL/MariaDB (XAMPP ou Laragon recomendados).

### Criação do Projeto
```powershell
composer create-project laravel/laravel nome-do-projeto
```

### Configuração do `.env` Local
Configure o banco de dados local para desenvolvimento:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=root
DB_PASSWORD=
```

---

## 2. Configuração da Hospedagem na HostGator (cPanel)

### Criar Banco de Dados MySQL
1. Acesse o **cPanel** > **Assistente de Banco de Dados MySQL**.
2. **Passo 1:** Nomeie o banco (ex: `usuario_sistema`).
3. **Passo 2:** Crie um usuário (ex: `usuario_admin`) e uma senha forte.
4. **Passo 3:** Adicione o usuário ao banco com **Todos os Privilégios**.

### Configurar Domínio
*   Se o domínio é o principal, ele apontará para a pasta `public_html`.
*   Para domínios adicionais ou subdomínios, verifique o "Diretório Raiz" no menu **Domínios**.

---

## 3. Upload do Projeto e Estrutura de Pastas

**ERRO COMUM:** Colocar todo o projeto dentro da `public_html`. Isso expõe arquivos sensíveis como o `.env`.

### Estrutura Segura Recomendada
No diretório raiz da sua hospedagem (um nível acima da `public_html`), crie uma pasta chamada `projeto_laravel`.

```
/ (raiz da conta)
├── .bashrc
├── projeto_laravel (coloque todos os arquivos do laravel aqui, EXCETO a pasta public)
│   ├── app/
│   ├── config/
│   ├── .env
│   └── ...
└── public_html (Coloque aqui APENAS o conteúdo da pasta 'public' do Laravel)
    ├── index.php
    ├── .htaccess
    └── css/js/images...
```

### Passo a Passo do Upload
1. No seu PC, compacte todo o projeto (exceto `vendor` e `node_modules`).
2. Via **Gerenciador de Arquivos** ou **FTP (FileZilla)**, envie o `.zip` para o servidor.
3. Extraia o conteúdo na pasta `projeto_laravel`.
4. Mova o conteúdo da pasta `projeto_laravel/public` para dentro da `public_html`.

### Ajuste do `index.php`
Edite o arquivo `public_html/index.php` para refletir os novos caminhos:

```php
// Linha ~34: Ajuste o caminho do autoload
require __DIR__.'/../projeto_laravel/vendor/autoload.php';

// Linha ~47: Ajuste o caminho do bootstrap
$app = require_once __DIR__.'/../projeto_laravel/bootstrap/app.php';
```

---

## 4. Configuração do Banco de Dados no Servidor

### Ajuste do `.env` Remoto
No servidor, edite o arquivo `projeto_laravel/.env`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com.br

DB_CONNECTION=mysql
DB_HOST=localhost  # HostGator usa localhost na maioria dos casos
DB_DATABASE=usuario_nome_do_banco
DB_USERNAME=usuario_nome_do_usuario
DB_PASSWORD=sua_senha_segura
```

---

## 5. Permissões Necessárias

O Laravel precisa escrever em pastas específicas. No Gerenciador de Arquivos do cPanel, clique com o botão direito nas pastas dentro de `projeto_laravel`:

*   **`storage/`**: Permissão 775 (ou 777 se houver erro de escrita).
*   **`bootstrap/cache/`**: Permissão 775.

---

## 6. Configuração do `.htaccess`

Para garantir que o Laravel gerencie as rotas corretamente, verifique se o arquivo `public_html/.htaccess` contém:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Redirecionar para HTTPS (Opcional, mas recomendado)
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Directory...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)/$ /$1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 7. Acesso via SSH (Opcional, mas Altamente Recomendado)

O SSH facilita a execução de comandos Artisan e instalação de dependências.

### Ativação
1. A HostGator geralmente exige que você solicite a ativação via **Chat de Suporte**.
2. Informe o domínio e peça para habilitar o acesso SSH.

### Acesso via PuTTY (Windows)
1. **Host Name:** IP do seu servidor ou seu domínio.
2. **Porta:** Geralmente 2222 (Porta padrão HostGator).
3. **Username/Password:** Mesmos dados do cPanel.

### Comandos Importantes
Navegue até a pasta do projeto: `cd projeto_laravel`
```bash
# Instalar dependências (se não enviou a pasta vendor)
php82 /usr/local/bin/composer install --no-dev

# Limpar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Executar migrations
php artisan migrate
```

---

## 8. Testes Finais e Erros Comuns

### Erros 403 / 404
*   Verifique se o `.htaccess` está na `public_html`.
*   Verifique as permissões de pasta.

### Erro 500 (Internal Server Error)
Geralmente causado por:
1. Versão do PHP incompatível no cPanel.
2. Caminhos incorretos no `index.php`.
3. Falta de permissão na pasta `storage`.

### Acesso aos Logs
Se o erro persistir, o arquivo de log do Laravel dirá exatamente o que houve:
*   Local: `projeto_laravel/storage/logs/laravel.log`

---

## 9. Boas Práticas e Segurança

*   **Segurança:** Nunca deixe `APP_DEBUG=true` em produção.
*   **SSL:** Use o "Let's Encrypt" gratuito no cPanel para habilitar o HTTPS.
*   **Backup:** Utilize a ferramenta de backup do cPanel para baixar o banco e os arquivos semanalmente.
*   **Symbolic Link:** Se usar o `Storage` do Laravel, você precisará criar o link simbólico manualmente via SSH:
    ```bash
    ln -s /home/USUARIO/projeto_laravel/storage/app/public /home/USUARIO/public_html/storage
    ```
