<?php

namespace App\Services\Lgpd;

use App\Jobs\ProcessLgpdDeletionRequestJob;
use App\Models\User;
use App\Models\UserConsent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LgpdDeletionWorkflowService
{
    public const CONSENT_DELETION_REQUEST = 'account_deletion_request';

    public function pendingUsersQuery(): Builder
    {
        return User::query()
            ->where('status', '!=', LgpdUserAnonymizationService::STATUS_ANONYMIZED)
            ->where('is_admin', false)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_consents')
                    ->whereColumn('user_consents.user_id', 'users.id')
                    ->where('consent_type', self::CONSENT_DELETION_REQUEST);
            });
    }

    /**
     * @return Collection<int, User>
     */
    public function pendingUsers(int $limit = 100): Collection
    {
        return $this->pendingUsersQuery()
            ->with(['consents' => fn ($q) => $q->where('consent_type', self::CONSENT_DELETION_REQUEST)->orderByDesc('created_at')])
            ->orderBy('id')
            ->limit($limit)
            ->get();
    }

    public function pendingCount(): int
    {
        return $this->pendingUsersQuery()->count();
    }

    /**
     * @return array{processed: int, skipped: int, failed: int, errors: list<string>}
     */
    public function processUsers(array $userIds, ?User $actor, string $reason = 'Processamento LGPD (pedido de exclusão)'): array
    {
        $result = [
            'processed' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $uniqueIds = array_values(array_unique(array_map('intval', $userIds)));

        foreach ($uniqueIds as $userId) {
            $outcome = $this->processUserById($userId, $actor, $reason);

            if ($outcome === 'processed') {
                $result['processed']++;
            } elseif ($outcome === 'skipped') {
                $result['skipped']++;
            } else {
                $result['failed']++;
                $result['errors'][] = $outcome;
            }
        }

        return $result;
    }

    /**
     * @return 'processed'|'skipped'|string Error message
     */
    public function processUserById(int $userId, ?User $actor, string $reason = 'Processamento LGPD (pedido de exclusão)'): string
    {
        $user = User::find($userId);

        if ($user === null) {
            return "Utilizador #{$userId} não encontrado.";
        }

        return $this->processUser($user, $actor, $reason);
    }

    /**
     * @return 'processed'|'skipped'|string Error message
     */
    public function processUser(User $user, ?User $actor, string $reason = 'Processamento LGPD (pedido de exclusão)'): string
    {
        if ($user->isAnonymized()) {
            return 'skipped';
        }

        if ($user->is_admin || $user->isAdministrator()) {
            return "Utilizador #{$user->id} é administrador — ignorado.";
        }

        if (! $this->hasPendingDeletionRequest($user)) {
            return "Utilizador #{$user->id} não possui pedido de exclusão pendente.";
        }

        try {
            app(LgpdUserAnonymizationService::class)->anonymize($user->fresh(), $actor, $reason);

            return 'processed';
        } catch (\Throwable $e) {
            Log::error('Falha ao processar pedido LGPD', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            return "Utilizador #{$user->id}: {$e->getMessage()}";
        }
    }

    public function hasPendingDeletionRequest(User $user): bool
    {
        if ($user->isAnonymized()) {
            return false;
        }

        return UserConsent::query()
            ->where('user_id', $user->id)
            ->where('consent_type', self::CONSENT_DELETION_REQUEST)
            ->exists();
    }

    /**
     * Enfileira pedidos pendentes (opcionalmente só após N dias do pedido).
     */
    public function queuePending(int $olderThanDays = 0, ?int $actorId = null): int
    {
        $query = $this->pendingUsersQuery();

        if ($olderThanDays > 0) {
            $cutoff = now()->subDays($olderThanDays);
            $query->whereExists(function ($sub) use ($cutoff) {
                $sub->select(DB::raw(1))
                    ->from('user_consents')
                    ->whereColumn('user_consents.user_id', 'users.id')
                    ->where('consent_type', self::CONSENT_DELETION_REQUEST)
                    ->where('created_at', '<=', $cutoff);
            });
        }

        $count = 0;

        $query->pluck('id')->each(function (int $userId) use ($actorId, &$count) {
            ProcessLgpdDeletionRequestJob::dispatch($userId, $actorId);
            $count++;
        });

        return $count;
    }
}
