<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'titre',
        'description',
        'note_totale',
        'duree_minutes',
        'ordre',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id')->orderBy('ordre');
    }

    public function results()
    {
        return $this->hasMany(QuizResult::class, 'quiz_id');
    }

    public function getMaxPointsAttribute(): int
    {
        return $this->questions()->sum('points');
    }

    public function getBestResultFor(EliteUser $user)
    {
        return $this->results()
            ->where('user_id', $user->id)
            ->orderByDesc('note')
            ->first();
    }

    public function getAttemptsCount(EliteUser $user): int
    {
        return $this->results()->where('user_id', $user->id)->count();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
