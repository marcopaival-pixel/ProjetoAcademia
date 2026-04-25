<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'order',
    ];

    public function articles()
    {
        return $this->hasMany(KnowledgeBaseArticle::class, 'category_id')->latest();
    }
}
