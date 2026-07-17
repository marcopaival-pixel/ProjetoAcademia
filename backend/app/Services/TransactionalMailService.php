<?php

namespace App\Services;

use App\Models\ConfiguracaoEmail;
use App\Models\LogEnvioEmail;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class TransactionalMailService
{
    /**
     * Valida se o utilizador pode receber e-mail transacional (coluna users.email, ativo).
     *
     * @return string|null Mensagem de erro ou null se OK
     */
    public function validateUserForOutgoingMail(User $user): ?string
    {
        $email = trim((string) $user->email);
        if ($email === '') {
            return 'Usuário não possui email cadastrado.';
        }
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'E-mail do usuário é inválido.';
        }
        if (! $user->isActive()) {
            return 'Usuário inativo.';
        }

        return null;
    }

    /**
     * Envia para o e-mail registado em users.email (nunca aceitar outro endereço quando há utilizador).
     *
     * @param  array<int, string>  $additionalCc
     * @param  array<int, string>  $additionalBcc
     */
    public function sendToUser(
        Mailable $mailable,
        User|int $user,
        ?int $empresaId,
        string $tipoEnvio,
        string $assuntoLog,
        string $mensagemResumo = '-',
        array $additionalCc = [],
        array $additionalBcc = [],
        bool $applyDedupe = true
    ): bool {
        $userModel = $user instanceof User ? $user : User::query()->find($user);
        if ($userModel === null) {
            $this->persistLog(
                empresaId: $empresaId,
                usuarioId: null,
                tipoEnvio: $tipoEnvio,
                emailDestino: '-',
                assuntoLog: $assuntoLog,
                mensagemResumo: $mensagemResumo,
                status: LogEnvioEmail::STATUS_FALHA,
                erro: 'Utilizador não encontrado.'
            );

            return false;
        }

        $err = $this->validateUserForOutgoingMail($userModel);
        if ($err !== null) {
            $this->persistLog(
                empresaId: $empresaId,
                usuarioId: $userModel->id,
                tipoEnvio: $tipoEnvio,
                emailDestino: (string) ($userModel->email ?? ''),
                assuntoLog: $assuntoLog,
                mensagemResumo: $mensagemResumo,
                status: LogEnvioEmail::STATUS_FALHA,
                erro: $err
            );

            return false;
        }

        return $this->send(
            mailable: $mailable,
            to: $userModel->email,
            empresaId: $empresaId,
            usuarioId: $userModel->id,
            assuntoLog: $assuntoLog,
            mensagemResumo: $mensagemResumo,
            tipoEnvio: $tipoEnvio,
            additionalCc: $additionalCc,
            additionalBcc: $additionalBcc,
            applyDedupe: $applyDedupe
        );
    }

    /**
     * Envia mailable aplicando configuração da empresa (se existir e ativa), regista log e respeita limite por hora.
     *
     * @param  string|array<int, string|\Illuminate\Mail\Mailables\Address>  $to
     * @param  array<int, string>  $additionalCc
     * @param  array<int, string>  $additionalBcc
     */
    public function send(
        Mailable $mailable,
        string|array $to,
        ?int $empresaId,
        ?int $usuarioId,
        string $assuntoLog,
        string $mensagemResumo = '-',
        ?string $tipoEnvio = null,
        array $additionalCc = [],
        array $additionalBcc = [],
        bool $applyDedupe = true
    ): bool {
        $ip = request()?->ip();

        [$mergedCc, $mergedBcc] = $this->mergeCopyAddresses($additionalCc, $additionalBcc);

        $cfg = null;
        if ($empresaId !== null) {
            $cfg = ConfiguracaoEmail::where('empresa_id', $empresaId)->first();
        }

        $emailDestinoLog = is_string($to) ? $to : json_encode($to);

        if ($cfg !== null && ! $cfg->ativo) {
            $this->persistLog($empresaId, $usuarioId, $tipoEnvio, $emailDestinoLog, $assuntoLog, $mensagemResumo, LogEnvioEmail::STATUS_FALHA, 'Envio desativado para esta empresa.', $ip);

            return false;
        }

        if ($cfg !== null && $cfg->ativo) {
            $limit = max(1, (int) $cfg->limite_envio_por_hora);
            $key = 'mail-hourly:'.$empresaId;
            if (RateLimiter::tooManyAttempts($key, $limit)) {
                $this->persistLog($empresaId, $usuarioId, $tipoEnvio, $emailDestinoLog, $assuntoLog, $mensagemResumo, LogEnvioEmail::STATUS_FALHA, 'Limite de envios por hora excedido.', $ip);

                return false;
            }
        }

        $dedupeTtl = max(0, (int) config('mail.dedupe_seconds', 60));
        if ($applyDedupe && $dedupeTtl > 0) {
            $dedupeKey = 'mail-send-dedupe:'.sha1(($usuarioId ?? '0').'|'.($tipoEnvio ?? '').'|'.$assuntoLog);
            if (RateLimiter::tooManyAttempts($dedupeKey, 1)) {
                $this->persistLog($empresaId, $usuarioId, $tipoEnvio, $emailDestinoLog, $assuntoLog, $mensagemResumo, LogEnvioEmail::STATUS_FALHA, 'Envio duplicado bloqueado (janela de '.$dedupeTtl.'s).', $ip);

                return false;
            }
        }

        try {
            MailConfigService::apply($empresaId);
            $pending = Mail::to($to);
            if ($mergedCc !== []) {
                $pending->cc($mergedCc);
            }
            if ($mergedBcc !== []) {
                $pending->bcc($mergedBcc);
            }
            $pending->send($mailable);

            if ($cfg !== null && $cfg->ativo) {
                RateLimiter::hit('mail-hourly:'.$empresaId, 3600);
            }
            if ($applyDedupe && $dedupeTtl > 0) {
                $dedupeKey = 'mail-send-dedupe:'.sha1(($usuarioId ?? '0').'|'.($tipoEnvio ?? '').'|'.$assuntoLog);
                RateLimiter::hit($dedupeKey, $dedupeTtl);
            }

            $this->persistLog($empresaId, $usuarioId, $tipoEnvio, $emailDestinoLog, $assuntoLog, $mensagemResumo, LogEnvioEmail::STATUS_ENVIADO, null, $ip);

            return true;
        } catch (\Throwable $e) {
            $this->persistLog($empresaId, $usuarioId, $tipoEnvio, $emailDestinoLog, $assuntoLog, $mensagemResumo, LogEnvioEmail::STATUS_FALHA, mb_substr($e->getMessage(), 0, 2000), $ip);

            return false;
        }
    }

    /**
     * @param  array<int, string>  $additionalCc
     * @param  array<int, string>  $additionalBcc
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    private function mergeCopyAddresses(array $additionalCc, array $additionalBcc): array
    {
        $cc = array_merge(config('mail.transactional_copy.cc', []), $additionalCc);
        $bcc = array_merge(config('mail.transactional_copy.bcc', []), $additionalBcc);

        $norm = function (array $list): array {
            $out = [];
            foreach ($list as $e) {
                $e = is_string($e) ? trim($e) : '';
                if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL)) {
                    $out[] = $e;
                }
            }

            return array_values(array_unique($out));
        };

        return [$norm($cc), $norm($bcc)];
    }

    private function persistLog(
        ?int $empresaId,
        ?int $usuarioId,
        ?string $tipoEnvio,
        string $emailDestino,
        string $assuntoLog,
        string $mensagemResumo,
        string $status,
        ?string $erro,
        ?string $ip = null
    ): void {
        LogEnvioEmail::create([
            'empresa_id' => $empresaId,
            'usuario_id' => $usuarioId,
            'tipo_envio' => $tipoEnvio,
            'email_destino' => mb_substr($emailDestino, 0, 255),
            'assunto' => $assuntoLog,
            'mensagem' => $mensagemResumo,
            'status' => $status,
            'erro' => $erro !== null ? mb_substr($erro, 0, 2000) : null,
            'ip' => $ip ?? request()?->ip(),
            'data_envio' => now(),
        ]);
    }
}
