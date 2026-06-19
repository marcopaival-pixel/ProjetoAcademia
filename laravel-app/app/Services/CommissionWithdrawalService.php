<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\DB;

class CommissionWithdrawalService
{
    /**
     * Vincula comissões DISPONÍVEIS (FIFO) ao saque e marca como PAGO.
     */
    public function settleWithdrawal(WithdrawalRequest $withdrawal): int
    {
        if ($withdrawal->status !== WithdrawalRequest::STATUS_PAGO) {
            return 0;
        }

        $existing = DB::table('commission_withdrawal')
            ->where('withdrawal_request_id', $withdrawal->id)
            ->count();

        if ($existing > 0) {
            return 0;
        }

        return DB::transaction(function () use ($withdrawal) {
            $amountRemaining = (float) $withdrawal->amount;
            $linked = 0;

            $commissions = Commission::where('representative_id', $withdrawal->representative_id)
                ->where('status', Commission::STATUS_DISPONIVEL)
                ->orderBy('created_at', 'asc')
                ->lockForUpdate()
                ->get();

            foreach ($commissions as $commission) {
                if ($amountRemaining <= 0) {
                    break;
                }

                $commissionAmount = (float) $commission->commission_amount;
                if ($commissionAmount <= 0 || $commissionAmount > $amountRemaining) {
                    break;
                }

                DB::table('commission_withdrawal')->insert([
                    'withdrawal_request_id' => $withdrawal->id,
                    'commission_id' => $commission->id,
                    'amount_applied' => $commissionAmount,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $commission->update([
                    'status' => Commission::STATUS_PAGO,
                    'paid_amount' => $commissionAmount,
                    'pending_amount' => 0,
                    'paid_at' => now(),
                ]);

                $amountRemaining -= $commissionAmount;
                $linked++;
            }

            return $linked;
        });
    }
}
