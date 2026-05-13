<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-unverified-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove usuários que não confirmaram o e-mail em até 24 horas.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando limpeza de usuários não verificados...');

        // Busca usuários pendentes de verificação cujo token expirou
        $expiredUsers = User::where('status', 'pending_email_verification')
            ->where('email_verification_expires_at', '<', now())
            ->whereNotNull('email_verification_expires_at')
            ->get();

        $count = $expiredUsers->count();

        if ($count === 0) {
            $this->info('Nenhum usuário expirado encontrado.');
            return;
        }

        foreach ($expiredUsers as $user) {
            $this->warn("Removendo usuário: {$user->email} (Expirou em: {$user->email_verification_expires_at})");
            
            // Registra no log do sistema antes de apagar
            Log::channel('admin')->info("Limpeza Automática: Usuário removido por falta de verificação de e-mail.", [
                'email' => $user->email,
                'created_at' => $user->created_at,
                'expired_at' => $user->email_verification_expires_at
            ]);

            // Remove o usuário e seus dados relacionados (o banco deve ter on cascade se possível, mas faremos manual se necessário)
            // Aqui, como são usuários recém-criados e não verificados, dificilmente terão dados profundos.
            $user->delete();
        }

        $this->info("Limpeza concluída. {$count} usuários removidos.");
    }
}
