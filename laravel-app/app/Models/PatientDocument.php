<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\BelongsToCompany;

class PatientDocument extends Model
{
    use BelongsToCompany, HasFactory, Traits\HasClinic;


    protected $fillable = [
        'patient_id',
        'professional_id',
        'title',
        'category',
        'file_path',
        'file_type',
        'file_size'
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function professional()
    {
        return $this->belongsTo(User::class, 'professional_id');
    }
}
