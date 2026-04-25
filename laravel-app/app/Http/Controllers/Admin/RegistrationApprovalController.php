<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\User;
use App\Services\MessagingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $pending = User::query()
            ->with('userRole')
            ->where('registration_approval_status', 'pending')
            ->where('is_admin', false)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        foreach ($request->user()->unreadNotifications as $notification) {
            if (($notification->data['type'] ?? '') === 'registration_pending') {
                $notification->markAsRead();
            }
        }

        return view('admin.registrations.pending', compact('pending'));
    }

    public function approve(Request $request, User $user): RedirectResponse
    {
        $this->authorizePendingUser($user);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'registration_approval_status' => 'approved',
                'registration_reviewed_at' => now(),
                'registration_rejection_note' => null,
            ]);

            AdminLog::create([
                'user_id' => $request->user()->id,
                'action' => "Aprovou cadastro do utilizador #{$user->id} ({$user->email})",
                'ip_address' => $request->ip(),
                'payload' => ['user_id' => $user->id],
            ]);
        });

        try {
            MessagingService::sendSystemMessage(
                $user->id,
                'Cadastro aprovado',
                'O seu cadastro na NexShape foi aprovado. Pode continuar a utilizar a plataforma após confirmar o seu e-mail, se ainda não o fez.'
            );
        } catch (\Throwable $e) {
            // Correio interno opcional
        }

        return redirect()
            ->route('admin.registrations.pending')
            ->with('success', 'Cadastro de '.$user->name.' aprovado.');
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $this->authorizePendingUser($user);

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $user, $data) {
            $user->update([
                'registration_approval_status' => 'rejected',
                'registration_reviewed_at' => now(),
                'registration_rejection_note' => $data['note'] ?? null,
            ]);

            AdminLog::create([
                'user_id' => $request->user()->id,
                'action' => "Recusou cadastro do utilizador #{$user->id} ({$user->email})",
                'ip_address' => $request->ip(),
                'payload' => ['user_id' => $user->id, 'note' => $data['note'] ?? null],
            ]);
        });

        try {
            $note = trim((string) ($data['note'] ?? ''));
            $body = 'O seu pedido de acesso à plataforma não foi aprovado.'.($note !== '' ? ' Nota: '.$note : ' Contacte o suporte se precisar de esclarecimentos.');
            MessagingService::sendSystemMessage(
                $user->id,
                'Cadastro não aprovado',
                $body
            );
        } catch (\Throwable $e) {
        }

        return redirect()
            ->route('admin.registrations.pending')
            ->with('success', 'Cadastro de '.$user->name.' recusado.');
    }

    private function authorizePendingUser(User $user): void
    {
        if ($user->is_admin || $user->registration_approval_status !== 'pending') {
            abort(404);
        }
    }
}
