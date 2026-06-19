<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Commission;
use App\Models\WithdrawalRequest;
use App\Models\AdminLog;
use App\Notifications\RepresentativeApproved;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RepresentativeAdminController extends Controller
{
    public function index(Request $request): View
    {
        $representatives = User::whereHas('roles', function($q) {
                $q->where('name', 'representative');
            })
            ->whereIn('status', ['PENDENTE_APROVACAO', 'pending', 'PENDENTE', 'active', 'ATIVO', 'APROVADO', 'approved'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.representatives.index', compact('representatives'));
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        if (!$user->hasRole('representative')) {
            abort(403, 'Este usuário não é um representante.');
        }

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'status' => 'APROVADO',
                'registration_approval_status' => 'approved',
                'registration_reviewed_at' => now(),
            ]);

            AdminLog::create([
                'user_id' => $request->user()->id,
                'action' => "Aprovou representante #{$user->id} ({$user->email})",
                'ip_address' => $request->ip(),
                'payload' => ['user_id' => $user->id],
            ]);
        });

        try {
            $user->notify(new RepresentativeApproved());
        } catch (\Exception $e) {
            \Log::error("Erro ao enviar e-mail de aprovação para representante: " . $e->getMessage());
        }

        return back()->with('success', "O representante {$user->name} foi aprovado com sucesso.");
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        if (!$user->hasRole('representative')) {
            abort(403, 'Este usuário não é um representante.');
        }

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'status' => 'REPROVADO',
                'registration_approval_status' => 'rejected',
                'registration_reviewed_at' => now(),
            ]);

            AdminLog::create([
                'user_id' => $request->user()->id,
                'action' => "Recusou representante #{$user->id} ({$user->email})",
                'ip_address' => $request->ip(),
                'payload' => ['user_id' => $user->id],
            ]);
        });

        return back()->with('success', "O representante {$user->name} foi recusado.");
    }
    public function withdrawals(Request $request): View
    {
        $withdrawals = WithdrawalRequest::with('representative')
            ->latest()
            ->paginate(20);

        return view('admin.representatives.withdrawals', compact('withdrawals'));
    }

    public function updateWithdrawal(Request $request, WithdrawalRequest $withdrawal)
    {
        $request->validate([
            'status' => 'required|in:PENDENTE,APROVADO,PAGO,RECUSADO',
            'admin_notes' => 'nullable|string',
        ]);

        $oldStatus = $withdrawal->status;
        $newStatus = $request->input('status');

        $withdrawal->update([
            'status' => $newStatus,
            'admin_notes' => $request->input('admin_notes'),
            'paid_at' => $newStatus === WithdrawalRequest::STATUS_PAGO ? now() : $withdrawal->paid_at,
        ]);

        // Se o status mudar para PAGO, devemos marcar as comissões correspondentes como PAGAS?
        // Na verdade, o controle de saldo já considera as solicitações. 
        // Mas para fins de auditoria, seria bom marcar as comissões que compuseram esse saque.
        // Como não rastreamos quais comissões específicas (apenas o montante), 
        // podemos marcar as comissões DISPONÍVEIS mais antigas até atingir o valor do saque.
        
        if ($newStatus === WithdrawalRequest::STATUS_PAGO && $oldStatus !== WithdrawalRequest::STATUS_PAGO) {
            app(\App\Services\CommissionWithdrawalService::class)->settleWithdrawal($withdrawal);

            // Auditoria
            \App\Models\RepresentativeAudit::create([
                'user_id' => auth()->id(),
                'action' => 'liberou_pagamento',
                'entity_type' => WithdrawalRequest::class,
                'entity_id' => $withdrawal->id,
                'new_values' => ['amount' => $withdrawal->amount, 'representative_id' => $withdrawal->representative_id],
            ]);
        }

        return back()->with('success', 'Solicitação de saque atualizada com sucesso.');
    }

    public function linkClinic(Request $request, \App\Models\Clinic $clinic)
    {
        $request->validate([
            'representative_id' => 'required|exists:users,id',
            'commission_type' => 'required|in:percentual,fixo',
            'commission_value' => 'required|numeric|min:0',
        ]);

        $oldValues = $clinic->only(['representative_id', 'commission_type', 'commission_value']);

        $clinic->update([
            'representative_id' => $request->representative_id,
            'commission_type' => $request->commission_type,
            'commission_value' => $request->commission_value,
            'sale_date' => now(),
        ]);

        \App\Models\RepresentativeAudit::create([
            'user_id' => auth()->id(),
            'action' => 'vinculou_clinica',
            'entity_type' => \App\Models\Clinic::class,
            'entity_id' => $clinic->id,
            'old_values' => $oldValues,
            'new_values' => $clinic->only(['representative_id', 'commission_type', 'commission_value']),
        ]);

        return back()->with('success', 'Clínica vinculada ao representante com sucesso.');
    }

    public function updateCommission(Request $request, Commission $commission)
    {
        $request->validate([
            'status' => 'required|in:PENDENTE,DISPONIVEL,PAGO,CANCELADO',
            'commission_amount' => 'required|numeric',
        ]);

        $oldValues = $commission->only(['status', 'commission_amount']);

        $commission->update([
            'status' => $request->status,
            'commission_amount' => $request->commission_amount,
        ]);

        \App\Models\RepresentativeAudit::create([
            'user_id' => auth()->id(),
            'action' => 'alterou_comissao',
            'entity_type' => Commission::class,
            'entity_id' => $commission->id,
            'old_values' => $oldValues,
            'new_values' => $commission->only(['status', 'commission_amount']),
        ]);

        return back()->with('success', 'Comissão atualizada com sucesso.');
    }
}
