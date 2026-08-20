<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'nom',
        'description',
        'ordre',
        'note_passage',
        'note_parrainage',
        'parrainages_requis',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'ordre' => 'integer',
        'note_passage' => 'integer',
        'note_parrainage' => 'integer',
        'parrainages_requis' => 'integer',
    ];

    /**
     * Relations
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public function unlocks(): HasMany
    {
        return $this->hasMany(ChapterUnlock::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * ✅ CORRECTION: Vérifier si un chapitre est déverrouillé pour un utilisateur
     */
    public function isUnlockedFor(EliteUser $user): bool
    {
        // Vérifier si l'utilisateur a le pack
        $hasPack = UserPack::where('user_id', $user->id)
            ->where('pack_id', $this->module->pack_id)
            ->where('statut', '!=', 'expire')
            ->exists();

        if (!$hasPack) {
            return false;
        }

        // Le premier chapitre du premier module est toujours déverrouillé
        $firstModule = Module::where('pack_id', $this->module->pack_id)
            ->active()
            ->orderBy('ordre')
            ->first();

        if ($firstModule && $firstModule->id === $this->module_id) {
            $firstChapter = Chapter::where('module_id', $this->module_id)
                ->active()
                ->orderBy('ordre')
                ->first();

            if ($firstChapter && $firstChapter->id === $this->id) {
                return true;
            }
        }

        // Pour les autres chapitres, vérifier s'il est explicitement déverrouillé
        return ChapterUnlock::where('user_id', $user->id)
            ->where('chapter_id', $this->id)
            ->exists();
    }

    /**
     * Vérifier si toutes les leçons sont complétées
     */
    public function allLessonsCompletedBy(EliteUser $user): bool
    {
        $totalLessons = $this->lessons()->active()->count();
        
        if ($totalLessons === 0) {
            return false;
        }

        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $this->lessons()->active()->pluck('id'))
            ->where('completed', true)
            ->count();

        return $completedLessons >= $totalLessons;
    }

    /**
     * Obtenir le quiz actif du chapitre
     */
    public function activeQuiz()
    {
        return $this->quizzes()->active()->first();
    }

    /**
     * Vérifier si l'utilisateur a passé le quiz
     */
    public function hasPassedQuiz(EliteUser $user): bool
    {
        $quiz = $this->activeQuiz();
        
        if (!$quiz) {
            return false;
        }

        return QuizResult::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('reussi', true)
            ->exists();
    }
}