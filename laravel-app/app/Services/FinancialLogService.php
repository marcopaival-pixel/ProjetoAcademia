<?php

namespace App\Services;

use App\Models\FinancialLog;
use Illuminate\Support\Facades\Request;

class FinancialLogService
{
    /**
     * Registra um evento financeiro.
     */
    public static function log(array $data)
    {
        return FinancialLog::create([
            'user_id' => $data['user_id'] ?? null,
            'academy_company_id' => $data['academy_company_id'] ?? null,
            'action' => $data['action'],
            'amount' => $data['amount'] ?? null,
            'status_before' => $data['status_before'] ?? null,
            'status_after' => $data['status_after'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? null,
            'origin' => $data['origin'] ?? 'system',
            'ip_address' => $data['ip_address'] ?? Request::ip(),
            'observation' => $data['observation'] ?? null,
            'payload' => $data['payload'] ?? null,
        ]);
    }
}
