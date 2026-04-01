# ProjetoAcademia (PHP + MySQL)

Web app responsivo para acompanhamento de alimentação, exercícios e peso.

## Requisitos

- PHP 8.1+ (extensões: `pdo_mysql`, recomendado `mbstring`)
- MySQL 8+ (ou MariaDB compatível)

## Banco de dados

1. Crie o banco e as tabelas:

   ```bash
   mysql -u root -p < sql/schema.sql
   ```

   Se o projeto já existia **antes** das metas de macros, aplique também:

   ```bash
   mysql -u root -p < sql/migrations/002_macro_targets.sql
   ```

2. Copie as credenciais:

   - Copie `.env.example.php` para `.env.php`
   - Ajuste `DB_USER`, `DB_PASS`, etc.

## Rodar localmente

Na pasta `php-app`:

```bash
php -S localhost:8080 -t public
```

Abra `http://localhost:8080/`.

### XAMPP (Windows)

1. Copie ou mantenha a pasta `php-app` acessível pelo Apache. Exemplo em `C:\xampp\htdocs\ProjetoAcademia\php-app` (a partir da raiz do seu repo pode ser um atalho/junction para não duplicar arquivos).
2. **Document root** deve ser a pasta **`public`** (URLs apontando direto para os `.php` públicos):
   - **Opção A — sem virtual host:** acesse  
     `http://localhost/ProjetoAcademia/php-app/public/`  
     (ajuste o caminho se sua pasta em `htdocs` for outra).
   - **Opção B — Virtual Host (recomendado):** crie um vhost com `DocumentRoot` = `.../php-app/public` e `ServerName` tipo `projetoacademia.local`; inclua no `hosts` do Windows `127.0.0.1 projetoacademia.local` e use `http://projetoacademia.local/`.
3. No **`.env.php`**, defina o caminho da URL após o host, **sem barra no final**:
   - Opção A: `'APP_BASE_PATH' => '/ProjetoAcademia/php-app/public',`
   - Opção B com `http://projetoacademia.local/` na raiz: `'APP_BASE_PATH' => '',`
4. **MySQL:** inicie MySQL no painel do XAMPP. Importe `sql/schema.sql` via **phpMyAdmin** (Importar) ou terminal:
   `C:\xampp\mysql\bin\mysql.exe -u root -p < sql\schema.sql` (na pasta `php-app`).
5. Extensões PHP: no `php.ini` do XAMPP, confira `extension=pdo_mysql` e `extension=mysqli` descomentados; reinicie o Apache.

Se o projeto estiver em uma **subpasta** do servidor (ex.: `http://localhost/projetoacademia/public/`), defina em `.env.php` algo como:

```php
'APP_BASE_PATH' => '/projetoacademia/public',
```

## Produção

Configure o virtual host ou o nginx com **document root** apontando para `php-app/public`.

## Estrutura

- `public/` — ponto de entrada (`index.php`, páginas, CSS, JS)
- `includes/` — config, PDO, layout, bootstrap de sessão
- `includes/nutrition.php` — estimativa de TMB (Mifflin–St Jeor), TDEE e meta sugerida no **Perfil** (com peso mais recente)
- `includes/chart_weight.php` — gráfico SVG de evolução do peso (página **Peso**, com 2+ registros)
- `includes/csv_export.php` — download CSV (UTF-8 BOM, separador `;`)
- `public/report.php` — resumo dos últimos 7 dias
- `public/export.php` — exportação CSV (alimentação, exercícios, peso); filtro opcional **De / Até**
- `public/set_theme.php` — alterna tema claro/escuro (cookie + `data-theme` no HTML)
- `includes/theme.php` — leitura do tema e URL segura de retorno (sem cookie, usa `prefers-color-scheme: light` até o usuário fixar Escuro/Claro)
- `includes/export_range.php` — validação do intervalo de datas para exportação
- `sql/schema.sql` — esquema inicial
