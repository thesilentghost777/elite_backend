<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roadmap extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'niveau_depart',
        'titre',
        'description',
        'duree_estimee_mois',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function profile()
    {
        return $this->belongsTo(CareerProfile::class, 'profile_id');
    }

    public function steps()
    {
        return $this->hasMany(RoadmapStep::class, 'roadmap_id')->orderBy('ordre');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
