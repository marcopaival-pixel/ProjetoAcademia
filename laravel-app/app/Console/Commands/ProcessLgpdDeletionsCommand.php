<?php

namespace App\Console\Commands;

use App\Services\Lgpd\LgpdDeletionWorkflowService;
use Illuminate\Console\Command;

class ProcessLgpdDeletionsCommand extends Command
{
    protected $signature = 'app:lgpd:process-deletions
                            {--user-id= : Processa um único utilizador}
                            {--older-than-days=0 : Só pedidos com pelo menos N dias}
                            {--queue : Enfileira em vez de processar de forma síncrona}
                            {--dry-run : Lista pedidos pendentes sem alterar dados}';

    protected $description = 'Processa pedidos de exclusão de conta (LGPD) pendentes.';

    public function handle(LgpdDeletionWorkflowService $workflow): int
    {
        $userId = $this->option('user-id');
        $olderThanDays = max(0, (int) $this->option('older-than-days'));
        $useQueue = (bool) $this->option('queue');
        $dryRun = (bool) $this->option('dry-run');

        if ($userId !== null && $userId !== '') {
            if ($dryRun) {
                $this->info("Dry-run: processaria utilizador #{$userId}.");

                return self::SUCCESS;
            }

            if ($useQueue) {
                \App\Jobs\ProcessLgpdDeletionRequestJob::dispatch((int) $userId);
                $this->info("Pedido enfileirado para utilizador #{$userId}.");

                return self::SUCCESS;
            }

            $outcome = $workflow->processUserById((int) $userId, null);

            if ($outcome === 'processed') {
                $this->info("Utilizador #{$userId} anonimizado com sucesso.");

                return self::SUCCESS;
            }

            if ($outcome === 'skipped') {
                $this->warn("Utilizador #{$userId} ignorado (já anonimizado).");

                return self::SUCCESS;
            }

            $this->error($outcome);

            return self::FAILURE;
        }

        $query = $workflow->pendingUsersQuery();

        if ($olderThanDays > 0) {
            $cutoff = now()->subDays($olderThanDays);
            $query->whereExists(function ($sub) use ($cutoff) {
                $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                    ->from('user_consents')
                    ->whereColumn('user_consents.user_id', 'users.id')
                    ->where('consent_type', LgpdDeletionWorkflowService::CONSENT_DELETION_REQUEST)
                    ->where('created_at', '<=', $cutoff);
            });
        }

        $userIds = $query->pluck('id')->all();
        $total = count($userIds);

        if ($total === 0) {
            $this->info('Nenhum pedido de exclusão pendente.');

            return self::SUCCESS;
        }

        $this->info("Pedidos pendentes encontrados: {$total}");

        if ($dryRun) {
            foreach ($userIds as $id) {
                $this->line(" - user_id={$id}");
            }

            return self::SUCCESS;
        }

        if ($useQueue) {
            $queued = $workflow->queuePending($olderThanDays);
            $this->info("{$queued} pedido(s) enfileirado(s).");

            return self::SUCCESS;
        }

        $result = $workflow->processUsers($userIds, null);

        $this->info("Processados: {$result['processed']} | Ignorados: {$result['skipped']} | Falhas: {$result['failed']}");

        foreach ($result['errors'] as $error) {
            $this->error($error);
        }

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
