<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Concours extends Model
{
    use HasFactory;

    protected $table = 'concours';

    protected $fillable = [
        'titre',
        'description',
        'organisateur',
        'date_debut',
        'date_fin',
        'date_limite_inscription',
        'conditions',
        'lien_inscription',
        'active',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_limite_inscription' => 'date',
        'active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeEnCours($query)
    {
        return $query->where('date_debut', '<=', now())
                     ->where('date_fin', '>=', now());
    }

    public function scopeInscriptionOuverte($query)
    {
        return $query->where('date_limite_inscription', '>=', now());
    }
}
