<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'entreprise',
        'ville',
        'type_contrat',
        'salaire_min',
        'salaire_max',
        'date_limite',
        'contact_email',
        'contact_telephone',
        'active',
    ];

    protected $casts = [
        'salaire_min' => 'decimal:2',
        'salaire_max' => 'decimal:2',
        'date_limite' => 'date',
        'active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('date_limite')
              ->orWhere('date_limite', '>=', now());
        });
    }
}
