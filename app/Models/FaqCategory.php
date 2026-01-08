<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'icone',
        'ordre',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function faqs()
    {
        return $this->hasMany(Faq::class, 'category_id')->orderBy('ordre');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
