<?php

namespace App\Models;

use App\Models\Traits\FiltersByRepresentative;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralCode extends Model
{
    use FiltersByRepresentative, HasFactory;

    protected $fillable = [
        'code',
        'representative_id',
        'commercial_proposal_id',
        'clinic_id',
        'status',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    const STATUS_DISPONIVEL = 'DISPONIVEL';
    const STATUS_RESERVADO = 'RESERVADO';
    const STATUS_UTILIZADO = 'UTILIZADO';
    const STATUS_EXPIRADO = 'EXPIRADO';
    const STATUS_CANCELADO = 'CANCELADO';

    public function representative(): BelongsTo
    {
        return $this->belongsTo(User::class, 'representative_id');
    }

    public function commercialProposal(): BelongsTo
    {
        return $this->belongsTo(CommercialProposal::class, 'commercial_proposal_id');
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'clinic_id');
    }

    public function isValid(): bool
    {
        if ($this->status !== self::STATUS_DISPONIVEL && $this->status !== self::STATUS_RESERVADO) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function markAsUsed(int $clinicId): void
    {
        $this->update([
            'status' => self::STATUS_UTILIZADO,
            'used_at' => now(),
            'clinic_id' => $clinicId
        ]);
    }

    public static function generateUniqueCode(User $representative): string
    {
        // Example: REP-MARCO-0001
        $nameParts = explode(' ', $representative->name);
        $firstName = strtoupper(substr($nameParts[0], 0, 10)); // Prevent overly long names
        $baseStr = 'REP-' . $firstName . '-';
        
        $lastCode = self::where('code', 'like', $baseStr . '%')
                        ->orderBy('id', 'desc')
                        ->first();

        $nextNum = 1;
        if ($lastCode) {
            $parts = explode('-', $lastCode->code);
            $lastNum = (int) end($parts);
            $nextNum = $lastNum + 1;
        }

        $code = $baseStr . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        // Guarantee uniqueness in a race condition
        while (self::where('code', $code)->exists()) {
            $nextNum++;
            $code = $baseStr . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
        }

        return $code;
    }
}
