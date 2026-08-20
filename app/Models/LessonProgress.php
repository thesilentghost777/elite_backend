<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use HasFactory;

    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'completed',
        'temps_passe_secondes',
        'date_completion',
    ];

    protected $casts = [
        'completed' => 'boolean',
        'date_completion' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(EliteUser::class, 'user_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }
}
