<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
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
        'note_totale' => 'integer',
        'duree_minutes' => 'integer',
        'ordre' => 'integer',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class, 'chapter_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id')->orderBy('ordre');
    }

    public function results(): HasMany
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
