<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Financement extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre', 'description', 'organisme', 'type',
        'montant_min', 'montant_max', 'date_limite',
        'conditions_eligibilite', 'lien_externe',
        'contact_telephone', 'contact_email', 'active',
        'points_requis',
    ];

    protected $casts = [
        'montant_min' => 'decimal:2',
        'montant_max' => 'decimal:2',
        'date_limite' => 'date',
        'active' => 'boolean',
        'points_requis' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
