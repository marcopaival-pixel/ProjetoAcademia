<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PrepareTestingDatabaseCommand extends Command
{
    protected $signature = 'app:db:prepare-testing';

    protected $description = 'Cria o banco MySQL "testing" para PHPUnit (CREATE DATABASE IF NOT EXISTS).';

    public function handle(): int
    {
        $database = 'testing';

        if (config('database.default') !== 'mysql') {
            $this->warn('Conexão padrão não é mysql. Ajuste DB_* no .env.');

            return self::FAILURE;
        }

        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);
        $user = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        try {
            $pdo = new \PDO(
                "mysql:host={$host};port={$port}",
                (string) $user,
                (string) $password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->info("Banco `{$database}` pronto para testes.");
            $this->line('Execute: composer test');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            $this->line('Crie manualmente: CREATE DATABASE testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');

            return self::FAILURE;
        }
    }
}
