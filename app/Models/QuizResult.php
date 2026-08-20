<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'note',
        'total_questions',
        'bonnes_reponses',
        'reussi',
        'reponses_utilisateur',
        'tentative',
        'action_requise',
        'parrainages_effectues',
    ];

    protected $casts = [
        'note' => 'decimal:2',
        'reussi' => 'boolean',
        'reponses_utilisateur' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(EliteUser::class, 'user_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function getNoteOn20Attribute(): float
    {
        $maxPoints = $this->quiz->max_points;
        return $maxPoints > 0 ? round(($this->note / $maxPoints) * 20, 2) : 0;
    }
}
