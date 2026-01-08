<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'enonce',
        'image_url',
        'type',
        'explication',
        'ordre',
        'points',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'question_id')->orderBy('ordre');
    }

    public function correctAnswers()
    {
        return $this->answers()->where('est_correcte', true);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
