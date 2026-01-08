<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPack extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pack_id',
        'duree_choisie',
        'prix_paye',
        'statut',
        'progression',
        'date_achat',
        'date_expiration',
    ];

    protected $casts = [
        'progression' => 'float',
        'prix_paye' => 'float',
        'date_achat' => 'datetime',
        'date_expiration' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(EliteUser::class, 'user_id');
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class, 'pack_id');
    }

    public function isActive(): bool
    {
        return $this->statut === 'actif' && 
               ($this->date_expiration === null || $this->date_expiration->isFuture());
    }

    public function calculateProgression(): float
    {
        $pack = $this->pack()->with('modules.chapters.lessons')->first();
        $totalLessons = 0;
        $completedLessons = 0;

        foreach ($pack->modules as $module) {
            foreach ($module->chapters as $chapter) {
                foreach ($chapter->lessons as $lesson) {
                    $totalLessons++;
                    if ($lesson->isCompletedBy($this->user)) {
                        $completedLessons++;
                    }
                }
            }
        }

        return $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0;
    }

    public function updateProgression(): void
    {
        $this->progression = $this->calculateProgression();
        if ($this->progression >= 100) {
            $this->statut = 'complete';
        }
        $this->save();
    }
}
