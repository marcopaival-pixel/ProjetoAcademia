<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ManageUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:user {action : create or reset} 
                            {email : O email do utilizador} 
                            {--name= : Nome do utilizador (apenas para create)} 
                            {--password= : Nova senha} 
                            {--admin : Definir como administrador} 
                            {--premium : Definir como premium}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria ou reseta a senha de um utilizador do sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $email = $this->argument('email');
        $password = $this->option('password');

        if ($action === 'create') {
            $name = $this->option('name') ?: 'Utilizador';
            
            if (!$password) {
                $password = $this->ask('Por favor, informe a senha');
            }

            if (User::where('email', $email)->exists()) {
                $this->error("Erro: O utilizador com o email {$email} já existe.");
                return 1;
            }

            $user = new User([
                'name' => $name,
                'email' => $email,
                'is_admin' => $this->option('admin'),
                'is_premium' => $this->option('premium'),
                'status' => 'active'
            ]);
            $user->password_hash = Hash::make($password);
            $user->save();

            $this->info("Utilizador {$name} ({$email}) criado com sucesso!");
        } 
        
        elseif ($action === 'reset') {
            $user = User::where('email', $email)->first();

            if (!$user) {
                $this->error("Erro: Utilizador {$email} não encontrado.");
                return 1;
            }

            if (!$password) {
                $password = $this->ask('Informe a nova senha');
            }

            $user->password_hash = Hash::make($password);
            
            if ($this->option('admin')) {
                $user->is_admin = true;
            }

            if ($this->option('premium')) {
                $user->is_premium = true;
            }

            $user->save();

            $this->info("Senha do utilizador {$email} resetada com sucesso!");
        }

        return 0;
    }
}
