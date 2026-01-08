<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'chapter_id',
        'titre',
        'contenu_texte',
        'url_web',
        'url_video',
        'ordre',
        'duree_minutes',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class, 'lesson_id');
    }

    public function isCompletedBy(EliteUser $user): bool
    {
        return $this->progress()
            ->where('user_id', $user->id)
            ->where('completed', true)
            ->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
