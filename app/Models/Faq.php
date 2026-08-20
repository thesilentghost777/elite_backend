<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'question',
        'reponse',
        'ordre',
        'vues',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'category_id');
    }

    public function incrementVues(): void
    {
        $this->increment('vues');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
