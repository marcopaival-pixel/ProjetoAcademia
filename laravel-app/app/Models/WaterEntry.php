<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterEntry extends Model
{
    use Traits\BelongsToCompany;
    protected $companyColumn = 'user_id';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'entry_date',
        'drank_at',
        'amount_ml',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'drank_at' => 'datetime',
            'amount_ml' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
