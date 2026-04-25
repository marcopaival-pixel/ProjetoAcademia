<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'file_path',
        'category',
        'description',
        'plan_type'
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
