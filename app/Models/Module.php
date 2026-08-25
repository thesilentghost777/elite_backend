<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'pack_id',
        'nom',
        'description',
        'type',
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

    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class, 'pack_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'module_id')->orderBy('ordre');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'module_id');
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class, 'module_id')->latestOfMany();
    }

    public function unlocks(): HasMany
    {
        return $this->hasMany(ModuleUnlock::class, 'module_id');
    }

    public function activeQuiz()
    {
        return $this->quizzes()->active()->first();
    }

    public function getTotalLessonsAttribute(): int
    {
        return $this->lessons()->active()->count();
    }

    public function getTotalDurationAttribute(): int
    {
        return $this->lessons()->active()->sum('duree_minutes');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Vérifier si un module est déverrouillé pour un utilisateur
     */
    public function isUnlockedFor(EliteUser $user): bool
    {
        $packId = $this->pack_id;
        if (!$packId) {
            return false;
        }

        $hasPack = UserPack::where('user_id', $user->id)
            ->where('pack_id', $packId)
            ->where('statut', '!=', 'expire')
            ->exists();

        if (!$hasPack) {
            if ($user->partner_id) {
                $hasPack = true;
            } else {
                return false;
            }
        }

        // Le premier module du pack est toujours déverrouillé
        $firstModule = Module::where('pack_id', $packId)
            ->active()
            ->orderBy('ordre')
            ->orderBy('id')
            ->first();

        if ($firstModule && $firstModule->id === $this->id) {
            return true;
        }

        // Pour les autres modules, vérifier s'il est explicitement déverrouillé
        return ModuleUnlock::where('user_id', $user->id)
            ->where('module_id', $this->id)
            ->exists();
    }

    /**
     * Vérifier si toutes les leçons du module sont complétées par l'utilisateur
     */
    public function allLessonsCompletedBy(EliteUser $user): bool
    {
        $totalLessons = $this->lessons()->active()->count();
        if ($totalLessons === 0) {
            return true;
        }

        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $this->lessons()->active()->pluck('id'))
            ->where('completed', true)
            ->count();

        return $completedLessons >= $totalLessons;
    }

    /**
     * Vérifier si l'utilisateur a réussi le quiz du module
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

    /**
     * Vérifier si le module entier est complété :
     * - Si aucun quiz n'est disponible pour le module, validé dès que la dernière leçon est terminée.
     * - Si un quiz est disponible, toutes les leçons terminées ET le quiz réussi.
     */
    public function isCompletedBy(EliteUser $user): bool
    {
        if (!$this->allLessonsCompletedBy($user)) {
            return false;
        }

        $quiz = $this->activeQuiz();
        if (!$quiz) {
            return true;
        }

        return $this->hasPassedQuiz($user);
    }
}
