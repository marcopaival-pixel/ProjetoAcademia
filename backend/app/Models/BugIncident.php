<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BugIncident extends Model
{
    public const STATE_RECEIVED = 'RECEIVED';

    public const STATE_INVESTIGATING = 'INVESTIGATING';

    public const STATE_DIAGNOSIS_READY = 'DIAGNOSIS_READY';

    public const STATE_IMPACT_ANALYZED = 'IMPACT_ANALYZED';

    public const STATE_PATCH_PROPOSED = 'PATCH_PROPOSED';

    public const STATE_WAITING_APPROVAL = 'WAITING_APPROVAL';

    public const STATE_APPROVED = 'APPROVED';

    public const STATE_PATCH_APPLIED = 'PATCH_APPLIED';

    public const STATE_TESTING = 'TESTING';

    public const STATE_READY_FOR_STAGING = 'READY_FOR_STAGING';

    public const STATE_WAITING_DEPLOY_APPROVAL = 'WAITING_DEPLOY_APPROVAL';

    public const STATE_DEPLOYED = 'DEPLOYED';

    public const STATE_MONITORING = 'MONITORING';

    public const STATE_CLOSED = 'CLOSED';

    public const STATE_ROLLED_BACK = 'ROLLED_BACK';

    public const ORDERED_STATES = [
        self::STATE_RECEIVED,
        self::STATE_INVESTIGATING,
        self::STATE_DIAGNOSIS_READY,
        self::STATE_IMPACT_ANALYZED,
        self::STATE_PATCH_PROPOSED,
        self::STATE_WAITING_APPROVAL,
        self::STATE_APPROVED,
        self::STATE_PATCH_APPLIED,
        self::STATE_TESTING,
        self::STATE_READY_FOR_STAGING,
        self::STATE_WAITING_DEPLOY_APPROVAL,
        self::STATE_DEPLOYED,
        self::STATE_MONITORING,
        self::STATE_CLOSED,
    ];

    /** Rótulos exibidos no painel (≠ “corrigido” até CLOSED). */
    public const STATUS_LABELS = [
        self::STATE_RECEIVED => 'Novo',
        self::STATE_INVESTIGATING => 'Em análise',
        self::STATE_DIAGNOSIS_READY => 'Diagnóstico pronto',
        self::STATE_IMPACT_ANALYZED => 'Impacto calculado',
        self::STATE_PATCH_PROPOSED => 'Correção proposta',
        self::STATE_WAITING_APPROVAL => 'Aguardando aprovação',
        self::STATE_APPROVED => 'Aprovado para correção',
        self::STATE_PATCH_APPLIED => 'Correção aplicada',
        self::STATE_TESTING => 'Testes em execução',
        self::STATE_READY_FOR_STAGING => 'Aguardando homologação',
        self::STATE_WAITING_DEPLOY_APPROVAL => 'Homologado',
        self::STATE_DEPLOYED => 'Publicado em produção',
        self::STATE_MONITORING => 'Monitorando',
        self::STATE_CLOSED => 'Corrigido',
        self::STATE_ROLLED_BACK => 'Rollback executado',
    ];

    protected $fillable = [
        'incident_code',
        'fingerprint',
        'title',
        'state',
        'severity',
        'environment',
        'module',
        'system_error_id',
        'affected_user_id',
        'affected_role',
        'tenant_id',
        'clinic_id',
        'route_path',
        'http_method',
        'method_name',
        'user_message',
        'error_summary',
        'diagnosis',
        'root_cause',
        'stack_trace',
        'first_occurred_at',
        'last_occurred_at',
        'occurrence_count',
        'affected_users_count',
        'affected_clinics_count',
        'log_reference',
        'evidence',
        'impact_analysis',
        'agent_context',
        'analysis_checklist',
        'test_results',
        'production_commit',
        'approved_commit',
        'file_path',
        'line_start',
        'line_end',
        'code_snippet',
        'proposed_diff',
        'approved_change_hash',
        'authorization',
        'confidence',
        'error_reproduced',
        'risk_level',
        'is_critical',
        'requires_dual_approval',
        'ignored_at',
        'approval_patch',
        'approval_staging',
        'approval_production',
        'staging_status',
        'production_status',
        'monitoring_status',
        'commit_hash',
        'branch_name',
        'rollback_command',
        'opened_by',
        'approved_by',
        'approved_at',
        'deployed_at',
        'closed_at',
        'resolved_at',
    ];

    protected $casts = [
        'evidence' => 'array',
        'impact_analysis' => 'array',
        'agent_context' => 'array',
        'analysis_checklist' => 'array',
        'test_results' => 'array',
        'authorization' => 'array',
        'approval_patch' => 'boolean',
        'approval_staging' => 'boolean',
        'approval_production' => 'boolean',
        'error_reproduced' => 'boolean',
        'is_critical' => 'boolean',
        'requires_dual_approval' => 'boolean',
        'first_occurred_at' => 'datetime',
        'last_occurred_at' => 'datetime',
        'approved_at' => 'datetime',
        'deployed_at' => 'datetime',
        'closed_at' => 'datetime',
        'resolved_at' => 'datetime',
        'ignored_at' => 'datetime',
    ];

    public function systemError(): BelongsTo
    {
        return $this->belongsTo(SystemError::class);
    }

    public function affectedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'affected_user_id');
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(BugIncidentEvent::class)->orderByDesc('created_at');
    }

    public function statusLabel(): string
    {
        if ($this->ignored_at) {
            return 'Ignorado';
        }

        return self::STATUS_LABELS[$this->state] ?? $this->state;
    }

    public function isOpen(): bool
    {
        return $this->ignored_at === null
            && ! in_array($this->state, [self::STATE_CLOSED, self::STATE_ROLLED_BACK], true);
    }

    public function isWaitingApproval(): bool
    {
        return $this->state === self::STATE_WAITING_APPROVAL;
    }

    public function canApprovePatch(): bool
    {
        return $this->isWaitingApproval()
            && filled($this->proposed_diff)
            && filled($this->file_path)
            && ! $this->approval_patch
            && $this->ignored_at === null;
    }

    public function uiActionGroup(): string
    {
        if ($this->ignored_at) {
            return 'ignored';
        }

        return match ($this->state) {
            self::STATE_RECEIVED, self::STATE_INVESTIGATING => 'new',
            self::STATE_DIAGNOSIS_READY, self::STATE_IMPACT_ANALYZED,
            self::STATE_PATCH_PROPOSED, self::STATE_WAITING_APPROVAL => 'analyzed',
            self::STATE_APPROVED, self::STATE_PATCH_APPLIED, self::STATE_TESTING => 'patch_applied',
            self::STATE_READY_FOR_STAGING, self::STATE_WAITING_DEPLOY_APPROVAL => 'homologated',
            self::STATE_CLOSED => 'resolved',
            default => 'in_progress',
        };
    }
}
