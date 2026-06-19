<?php

namespace App\Models;

use App\Models\Traits\FiltersByProfessionalOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessionalFinanceEntry extends Model
{
    use FiltersByProfessionalOwner;
    protected $fillable = [
        'professional_id',
        'category_id',
        'description',
        'amount',
        'type',
        'status',
        'due_date',
        'payment_date',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'payment_date' => 'date',
    ];

    public function professional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProfessionalFinanceCategory::class, 'category_id');
    }
}
