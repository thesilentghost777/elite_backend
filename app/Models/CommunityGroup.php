<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'description', 'icone',
        'whatsapp_number', 'membres_count', 'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function getWhatsappJoinUrl(): string
    {
        $msg = urlencode("Bonjour, je veux rejoindre le groupe Elite 2.0 de discussion sur {$this->nom}. Merci !");
        return "https://wa.me/{$this->whatsapp_number}?text={$msg}";
    }
}
