<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'texte',
        'est_correcte',
        'ordre',
    ];

    protected $casts = [
        'est_correcte' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'question_id');
    }
}
