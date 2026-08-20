<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bibliotheque extends Model
{
    use HasFactory;

    protected $table = 'bibliotheque';

    protected $fillable = [
        'titre', 'auteur', 'description', 'categorie',
        'fichier_pdf', 'cover_image', 'nombre_pages',
        'vues', 'telechargements', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeCategorie($query, $cat)
    {
        return $query->where('categorie', $cat);
    }

    public function incrementVues()
    {
        $this->increment('vues');
    }

    public function incrementTelechargements()
    {
        $this->increment('telechargements');
    }
}
