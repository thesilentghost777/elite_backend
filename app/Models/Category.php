<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'image_url',
        'couleur',
        'ordre',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function packs()
    {
        return $this->hasMany(Pack::class, 'category_id')->orderBy('ordre');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
