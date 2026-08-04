<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiCreditTransaction extends Model
{
    use Traits\BelongsToUserCompany;

    protected $fillable = [
        'user_id',
        'type',
        'credits',
        'balance_before',
        'balance_after',
        'feature_code',
        'reference_id',
        'description',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
