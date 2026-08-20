<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoadmapStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'roadmap_id',
        'titre',
        'description',
        'ordre',
        'duree_semaines',
        'type',
        'pack_recommande_id',
        'obligatoire',
    ];

    protected $casts = [
        'obligatoire' => 'boolean',
    ];

    public function roadmap()
    {
        return $this->belongsTo(Roadmap::class, 'roadmap_id');
    }

    public function packRecommande()
    {
        return $this->belongsTo(Pack::class, 'pack_recommande_id');
    }
}
