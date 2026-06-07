<?php

namespace App\Models;

use App\Models\Traits\FiltersByProfessionalOwner;
use Illuminate\Database\Eloquent\Model;

class ProfessionalFinanceCategory extends Model
{
    use FiltersByProfessionalOwner;
    protected $fillable = [
        'professional_id',
        'name',
        'type',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }

    public function entries()
    {
        return $this->hasMany(ProfessionalFinanceEntry::class, 'category_id');
    }
}
