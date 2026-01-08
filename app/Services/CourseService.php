<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Chapter;
use App\Models\ChapterUnlock;
use App\Models\EliteUser;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\Pack;
use App\Models\Quiz;
use App\Models\QuizResult;
use App\Models\UserPack;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CourseService
{
    public function getCategories(): array
    {
        $categories = Category::active()
            ->with(['packs' => function ($query) {
                $query->active()->orderBy('ordre');
            }])
            ->orderBy('ordre')
            ->get();

        return $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'nom' => $category->nom,
                'slug' => $category->slug,
                'description' => $category->description,
                'image_url' => $category->image_url,
                'couleur' => $category->couleur,
                'packs_count' => $category->packs->count(),
                'packs' => $category->packs->map(function ($pack) {
                    return [
                        'id' => $pack->id,
                        'nom' => $pack->nom,
                        'slug' => $pack->slug,
                        'prix_points' => $pack->prix_points,
                        'niveau_requis' => $pack->niveau_requis,
                    ];
                }),
            ];
        })->toArray();
    }

    public function getAllPacks(array $filters = []): array
    {
        $query = Pack::active()->with('category');

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['niveau_requis'])) {
            $query->where('niveau_requis', $filters['niveau_requis']);
        }

        $packs = $query->orderBy('ordre')->get();

        return $packs->map(function ($pack) {
            return [
                'id' => $pack->id,
                'nom' => $pack->nom,
                'slug' => $pack->slug,
                'description' => $pack->description,
                'image_url' => $pack->image_url,
                'category' => [
                    'id' => $pack->category->id,
                    'nom' => $pack->category->nom,
                ],
                'niveau_requis' => $pack->niveau_requis,
                'durees_disponibles' => $pack->durees_disponibles,
                'diplomes_possibles' => $pack->diplomes_possibles,
                'prix_points' => $pack->prix_points,
                'debouches' => $pack->debouches,
            ];
        })->toArray();
    }

    public function getRecommendedPacks(EliteUser $user): array
    {
        if (!$user->profile_chosen) {
            return [];
        }

        $profileId = $user->profileChoice->profile_id ?? null;
        
        if (!$profileId) {
            return [];
        }
        
        $packs = Pack::active()
            ->whereHas('profiles', function ($query) use ($profileId) {
                $query->where('career_profiles.id', $profileId);
            })
            ->with('category')
            ->orderByRaw(
                '(SELECT priorite FROM pack_profiles 
                  WHERE pack_profiles.pack_id = packs.id 
                  AND pack_profiles.profile_id = ?) DESC', 
                [$profileId]
            )
            ->get();

        return $packs->map(function ($pack) {
            return [
                'id' => $pack->id,
                'nom' => $pack->nom,
                'slug' => $pack->slug,
                'description' => $pack->description,
                'image_url' => $pack->image_url,
                'category' => [
                    'id' => $pack->category->id,
                    'nom' => $pack->category->nom,
                ],
                'niveau_requis' => $pack->niveau_requis,
                'prix_points' => $pack->prix_points,
                'recommended' => true,
            ];
        })->toArray();
    }

    public function getPackDetails(int $packId): array
    {
        $pack = Pack::with([
            'category',
            'modules' => function ($query) {
                $query->active()->orderBy('ordre')->with([
                    'chapters' => function ($q) {
                        $q->active()->orderBy('ordre');
                    }
                ]);
            }
        ])->findOrFail($packId);

        return [
            'id' => $pack->id,
            'nom' => $pack->nom,
            'slug' => $pack->slug,
            'description' => $pack->description,
            'image_url' => $pack->image_url,
            'category' => [
                'id' => $pack->category->id,
                'nom' => $pack->category->nom,
            ],
            'niveau_requis' => $pack->niveau_requis,
            'durees_disponibles' => $pack->durees_disponibles,
            'diplomes_possibles' => $pack->diplomes_possibles,
            'prix_points' => $pack->prix_points,
            'debouches' => $pack->debouches,
            'total_modules' => $pack->modules->count(),
            'total_chapters' => $pack->modules->sum(fn($m) => $m->chapters->count()),
            'modules' => $pack->modules->map(function ($module) {
                return [
                    'id' => $module->id,
                    'nom' => $module->nom,
                    'description' => $module->description,
                    'type' => $module->type,
                    'chapters_count' => $module->chapters->count(),
                ];
            }),
        ];
    }

    public function getPackModules(EliteUser $user, int $packId): array
    {
        $hasPack = $this->userHasPack($user, $packId);

        $pack = Pack::with([
            'modules' => function ($query) {
                $query->active()->orderBy('ordre')->with([
                    'chapters' => function ($q) {
                        $q->active()->orderBy('ordre')->withCount('lessons');
                    }
                ]);
            }
        ])->findOrFail($packId);

        return $pack->modules->map(function ($module, $moduleIndex) use ($user, $hasPack, $pack) {
            $isFirstModule = $moduleIndex === 0;
            $isModuleUnlocked = $isFirstModule;
            
            if (!$isFirstModule && $hasPack) {
                $previousModule = Module::where('pack_id', $pack->id)
                    ->where('ordre', '<', $module->ordre)
                    ->orderByDesc('ordre')
                    ->first();
                    
                if ($previousModule) {
                    $totalChapters = Chapter::where('module_id', $previousModule->id)
                        ->active()
                        ->count();
                        
                    $completedChapters = ChapterUnlock::where('user_id', $user->id)
                        ->whereIn('chapter_id', Chapter::where('module_id', $previousModule->id)
                            ->active()
                            ->pluck('id'))
                        ->count();
                    
                    $isModuleUnlocked = ($completedChapters >= $totalChapters);
                }
            }

            $chaptersData = $module->chapters->map(function ($chapter, $chapterIndex) use ($user, $hasPack, $moduleIndex, $isModuleUnlocked) {
                $lessonsCount = $chapter->lessons_count;
                $completedCount = 0;
                $isFirstChapter = $chapterIndex === 0;
                
                // ✅ CORRECTION: Premier chapitre du premier module déverrouillé automatiquement
                $isUnlocked = false;

                if ($hasPack && $isModuleUnlocked) {
                    $completedCount = LessonProgress::where('user_id', $user->id)
                        ->whereIn('lesson_id', $chapter->lessons()->pluck('id'))
                        ->where('completed', true)
                        ->count();
                    
                    if ($moduleIndex === 0 && $isFirstChapter) {
                        // Premier chapitre du premier module toujours déverrouillé
                        $isUnlocked = true;
                    } else {
                        $isUnlocked = $chapter->isUnlockedFor($user);
                    }
                }

                return [
                    'id' => $chapter->id,
                    'nom' => $chapter->nom,
                    'description' => $chapter->description,
                    'lessons_count' => $lessonsCount,
                    'completed_count' => $completedCount,
                    'is_unlocked' => $isUnlocked,
                    'is_completed' => $completedCount >= $lessonsCount && $lessonsCount > 0,
                    'has_access' => $hasPack && $isModuleUnlocked,
                    'has_quiz' => $chapter->quizzes()->active()->exists(),
                    'note_passage' => $chapter->note_passage,
                ];
            });

            return [
                'id' => $module->id,
                'nom' => $module->nom,
                'description' => $module->description,
                'type' => $module->type,
                'chapters_count' => $module->chapters->count(),
                'chapters' => $chaptersData,
                'is_unlocked' => $hasPack && $isModuleUnlocked,
                'has_access' => $hasPack,
            ];
        })->toArray();
    }

    public function getModuleChapters(EliteUser $user, int $moduleId): array
    {
        $module = Module::with('pack')->findOrFail($moduleId);
        $hasPack = $this->userHasPack($user, $module->pack_id);

        $chapters = Chapter::where('module_id', $moduleId)
            ->active()
            ->orderBy('ordre')
            ->with(['lessons' => function ($q) {
                $q->active()->orderBy('ordre');
            }, 'quizzes' => function ($q) {
                $q->active()->orderBy('ordre');
            }])
            ->get();

        return $chapters->map(function ($chapter, $index) use ($user, $hasPack) {
            $lessonsProgress = $chapter->lessons->map(function ($lesson) use ($user, $hasPack, $chapter) {
                $progress = null;
                $isUnlocked = false;

                if ($hasPack) {
                    $progress = LessonProgress::where('user_id', $user->id)
                        ->where('lesson_id', $lesson->id)
                        ->first();
                    
                    $isUnlocked = $this->isLessonUnlocked($user, $lesson, $chapter);
                }

                return [
                    'id' => $lesson->id,
                    'titre' => $lesson->titre,
                    'duree_minutes' => $lesson->duree_minutes,
                    'is_completed' => $progress ? $progress->completed : false,
                    'has_video' => !empty($lesson->url_video),
                    'has_web_link' => !empty($lesson->url_web),
                ];
            });

            $quizResult = null;
            $isChapterUnlocked = false;

            if ($hasPack) {
                $quizResult = QuizResult::where('user_id', $user->id)
                    ->whereIn('quiz_id', $chapter->quizzes->pluck('id'))
                    ->orderByDesc('note')
                    ->first();
                
                $isChapterUnlocked = $index === 0 || $chapter->isUnlockedFor($user);
            }

            return [
                'id' => $chapter->id,
                'nom' => $chapter->nom,
                'description' => $chapter->description,
                'is_unlocked' => $isChapterUnlocked,
                'has_access' => $hasPack,
                'note_passage' => $chapter->note_passage,
                'note_parrainage' => $chapter->note_parrainage,
                'parrainages_requis' => $chapter->parrainages_requis,
                'lessons' => $lessonsProgress,
                'has_quiz' => $chapter->quizzes->isNotEmpty(),
                'quiz' => $chapter->quizzes->first() ? [
                    'id' => $chapter->quizzes->first()->id,
                    'titre' => $chapter->quizzes->first()->titre,
                    'duree_minutes' => $chapter->quizzes->first()->duree_minutes,
                    'best_score' => $quizResult ? $quizResult->note : null,
                    'passed' => $quizResult ? $quizResult->reussi : false,
                ] : null,
            ];
        })->toArray();
    }

    public function getChapterLessons(EliteUser $user, int $chapterId): array
    {
        $chapter = Chapter::with('module.pack')->findOrFail($chapterId);
        $hasPack = $this->userHasPack($user, $chapter->module->pack_id);

        if ($hasPack) {
            $this->verifyChapterAccess($user, $chapter);
        }

        $lessons = Lesson::where('chapter_id', $chapterId)
            ->active()
            ->orderBy('ordre')
            ->get();

        return $lessons->map(function ($lesson, $index) use ($user, $hasPack, $chapter) {
            $progress = null;
            $isUnlocked = false;

            if ($hasPack) {
                $progress = LessonProgress::where('user_id', $user->id)
                    ->where('lesson_id', $lesson->id)
                    ->first();
                
                // Première leçon toujours déverrouillée si chapitre déverrouillé
                $isUnlocked = $index === 0 || $this->isLessonUnlocked($user, $lesson, $chapter);
            }

            return [
                'id' => $lesson->id,
                'titre' => $lesson->titre,
                'duree_minutes' => $lesson->duree_minutes,
                'is_completed' => $progress ? $progress->completed : false,
                'temps_passe' => $progress ? $progress->temps_passe_secondes : 0,
                'is_unlocked' => $isUnlocked,
                'has_access' => $hasPack,
                'url_video' => $hasPack && $isUnlocked ? $lesson->url_video : null,
                'url_web' => $hasPack && $isUnlocked ? $lesson->url_web : null,
            ];
        })->toArray();
    }

    public function getLesson(EliteUser $user, int $lessonId): array
    {
        $lesson = Lesson::with(['chapter.module.pack', 'chapter'])->findOrFail($lessonId);
        
        $this->verifyPackAccess($user, $lesson->chapter->module->pack_id);
        $this->verifyChapterAccess($user, $lesson->chapter);
        $this->verifyLessonAccess($user, $lesson);

        $progress = LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->first();

        // Trouver leçon précédente et suivante
        $previousLesson = Lesson::where('chapter_id', $lesson->chapter_id)
            ->where('ordre', '<', $lesson->ordre)
            ->active()
            ->orderByDesc('ordre')
            ->first();

        $nextLesson = Lesson::where('chapter_id', $lesson->chapter_id)
            ->where('ordre', '>', $lesson->ordre)
            ->active()
            ->orderBy('ordre')
            ->first();

        return [
            'id' => $lesson->id,
            'titre' => $lesson->titre,
            'contenu_texte' => $lesson->contenu_texte,
            'url_web' => $lesson->url_web,
            'url_video' => $lesson->url_video,
            'duree_minutes' => $lesson->duree_minutes,
            'is_completed' => $progress ? $progress->completed : false,
            'temps_passe' => $progress ? $progress->temps_passe_secondes : 0,
            'chapter' => [
                'id' => $lesson->chapter->id,
                'nom' => $lesson->chapter->nom,
            ],
            'previous_lesson' => $previousLesson ? [
                'id' => $previousLesson->id,
                'titre' => $previousLesson->titre,
            ] : null,
            'next_lesson' => $nextLesson ? [
                'id' => $nextLesson->id,
                'titre' => $nextLesson->titre,
            ] : null,
        ];
    }

    public function markLessonComplete(EliteUser $user, int $lessonId, int $tempsPasse = 0): void
    {
        $lesson = Lesson::with('chapter.module.pack')->findOrFail($lessonId);
        $this->verifyPackAccess($user, $lesson->chapter->module->pack_id);
        $this->verifyChapterAccess($user, $lesson->chapter);
        $this->verifyLessonAccess($user, $lesson);

        LessonProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lessonId,
            ],
            [
                'completed' => true,
                'temps_passe_secondes' => DB::raw("temps_passe_secondes + {$tempsPasse}"),
                'date_completion' => now(),
            ]
        );

        $userPack = UserPack::where('user_id', $user->id)
            ->where('pack_id', $lesson->chapter->module->pack_id)
            ->first();

        if ($userPack) {
            $userPack->updateProgression();
        }
    }

    public function getChapterQuiz(EliteUser $user, int $chapterId): array
    {
        $chapter = Chapter::with('module.pack')->findOrFail($chapterId);
        $this->verifyPackAccess($user, $chapter->module->pack_id);
        $this->verifyChapterAccess($user, $chapter);
        $this->verifyAllLessonsCompleted($user, $chapter);

        $quiz = Quiz::where('chapter_id', $chapterId)
            ->active()
            ->with(['questions' => function ($q) {
                $q->active()->orderBy('ordre')->with('answers');
            }])
            ->first();

        if (!$quiz) {
            throw ValidationException::withMessages([
                'quiz' => ['Aucun quiz disponible pour ce chapitre.']
            ]);
        }

        $attempts = QuizResult::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->count();

        return [
            'id' => $quiz->id,
            'titre' => $quiz->titre,
            'description' => $quiz->description,
            'duree_minutes' => $quiz->duree_minutes,
            'note_totale' => $quiz->note_totale,
            'note_passage' => $chapter->note_passage,
            'note_parrainage' => $chapter->note_parrainage,
            'attempts' => $attempts,
            'questions' => $quiz->questions->map(function ($question) {
                return [
                    'id' => $question->id,
                    'enonce' => $question->enonce,
                    'image_url' => $question->image_url,
                    'type' => $question->type,
                    'points' => $question->points,
                    'answers' => $question->answers->map(function ($answer) {
                        return [
                            'id' => $answer->id,
                            'texte' => $answer->texte,
                        ];
                    }),
                ];
            }),
        ];
    }

    public function submitQuiz(EliteUser $user, int $quizId, array $responses): array
    {
        $quiz = Quiz::with(['chapter.module.pack', 'questions.answers'])->findOrFail($quizId);
        $chapter = $quiz->chapter;
        $this->verifyPackAccess($user, $chapter->module->pack_id);
        $this->verifyAllLessonsCompleted($user, $chapter);

        $totalPoints = 0;
        $earnedPoints = 0;
        $correctCount = 0;
        $userResponses = [];

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;
            $userAnswer = collect($responses)->firstWhere('question_id', $question->id);
            
            if ($userAnswer) {
                $correctAnswerIds = $question->correctAnswers->pluck('id')->toArray();
                $isCorrect = in_array($userAnswer['answer_id'], $correctAnswerIds);
                
                if ($isCorrect) {
                    $earnedPoints += $question->points;
                    $correctCount++;
                }

                $userResponses[] = [
                    'question_id' => $question->id,
                    'answer_id' => $userAnswer['answer_id'],
                    'is_correct' => $isCorrect,
                    'explication' => $question->explication,
                ];
            }
        }

        $note = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 20 : 0;
        $note = round($note, 2);

        $actionRequise = 'aucune';
        $reussi = false;

        if ($note >= $chapter->note_passage) {
            $reussi = true;
        } elseif ($note >= $chapter->note_parrainage) {
            $actionRequise = 'parrainage';
        } else {
            $actionRequise = 'recommencer';
        }

        $tentative = QuizResult::where('user_id', $user->id)
            ->where('quiz_id', $quizId)
            ->count() + 1;

        $result = QuizResult::create([
            'user_id' => $user->id,
            'quiz_id' => $quizId,
            'note' => $earnedPoints,
            'total_questions' => $quiz->questions->count(),
            'bonnes_reponses' => $correctCount,
            'reussi' => $reussi,
            'reponses_utilisateur' => $userResponses,
            'tentative' => $tentative,
            'action_requise' => $actionRequise,
        ]);

        if ($reussi) {
            $this->unlockNextChapter($user, $chapter, 'score');
        }

        return [
            'note' => $note,
            'note_sur_20' => $note,
            'total_questions' => $quiz->questions->count(),
            'bonnes_reponses' => $correctCount,
            'reussi' => $reussi,
            'action_requise' => $actionRequise,
            'parrainages_requis' => $actionRequise === 'parrainage' ? $chapter->parrainages_requis : 0,
            'corrections' => $userResponses,
            'message' => $this->getResultMessage($note, $reussi, $actionRequise, $chapter),
        ];
    }

    private function unlockNextChapter(EliteUser $user, Chapter $currentChapter, string $method): void
    {
        $nextChapter = Chapter::where('module_id', $currentChapter->module_id)
            ->where('ordre', '>', $currentChapter->ordre)
            ->orderBy('ordre')
            ->first();

        if ($nextChapter) {
            ChapterUnlock::firstOrCreate([
                'user_id' => $user->id,
                'chapter_id' => $nextChapter->id,
            ], [
                'unlock_method' => $method,
            ]);
        }
    }

    public function unlockChapterByReferral(EliteUser $user, int $chapterId): array
    {
        $chapter = Chapter::with('module.pack')->findOrFail($chapterId);
        $this->verifyPackAccess($user, $chapter->module->pack_id);

        $lastResult = QuizResult::where('user_id', $user->id)
            ->whereHas('quiz', function ($q) use ($chapterId) {
                $q->where('chapter_id', $chapterId);
            })
            ->where('action_requise', 'parrainage')
            ->latest()
            ->first();

        if (!$lastResult) {
            throw ValidationException::withMessages([
                'quiz' => ['Vous devez d\'abord passer le quiz avec une note entre 10 et 14.']
            ]);
        }

        $parrainagesDepuisQuiz = $user->referralHistory()
            ->where('created_at', '>=', $lastResult->created_at)
            ->count();

        $parrainagesRequis = $chapter->parrainages_requis;
        $parrainagesEffectues = $lastResult->parrainages_effectues + $parrainagesDepuisQuiz;

        if ($parrainagesEffectues < $parrainagesRequis) {
            return [
                'unlocked' => false,
                'parrainages_effectues' => $parrainagesEffectues,
                'parrainages_requis' => $parrainagesRequis,
                'parrainages_restants' => $parrainagesRequis - $parrainagesEffectues,
                'message' => "Il vous manque encore " . ($parrainagesRequis - $parrainagesEffectues) . " parrainage(s) pour débloquer ce chapitre.",
            ];
        }

        $this->unlockNextChapter($user, $chapter, 'parrainage');

        $lastResult->update([
            'parrainages_effectues' => $parrainagesEffectues,
            'reussi' => true,
            'action_requise' => 'aucune',
        ]);

        return [
            'unlocked' => true,
            'message' => 'Félicitations ! Le chapitre suivant a été débloqué grâce à vos parrainages.',
        ];
    }

    private function userHasPack(EliteUser $user, int $packId): bool
    {
        return UserPack::where('user_id', $user->id)
            ->where('pack_id', $packId)
            ->where('statut', '!=', 'expire')
            ->exists();
    }

    private function verifyPackAccess(EliteUser $user, int $packId): void
    {
        if (!$this->userHasPack($user, $packId)) {
            throw ValidationException::withMessages([
                'pack' => ['Vous n\'avez pas accès à ce pack. Veuillez l\'acheter d\'abord.']
            ]);
        }
    }

    private function verifyChapterAccess(EliteUser $user, Chapter $chapter): void
    {
        // Premier chapitre du pack toujours accessible
        $firstChapter = Chapter::whereHas('module', function($q) use ($chapter) {
            $q->where('pack_id', $chapter->module->pack_id)
              ->where('ordre', 1);
        })->where('ordre', 1)->first();

        if ($firstChapter && $firstChapter->id === $chapter->id) {
            return; // Premier chapitre accessible
        }

        if (!$chapter->isUnlockedFor($user)) {
            throw ValidationException::withMessages([
                'chapter' => ['Ce chapitre est verrouillé. Vous devez terminer le chapitre précédent et réussir son quiz pour y accéder.']
            ]);
        }
    }

    private function isLessonUnlocked(EliteUser $user, Lesson $lesson, Chapter $chapter): bool
    {
        if (!$chapter->isUnlockedFor($user)) {
            return false;
        }

        $firstLesson = Lesson::where('chapter_id', $chapter->id)
            ->active()
            ->orderBy('ordre')
            ->first();

        if ($firstLesson && $firstLesson->id === $lesson->id) {
            return true;
        }

        $previousLesson = Lesson::where('chapter_id', $chapter->id)
            ->where('ordre', '<', $lesson->ordre)
            ->active()
            ->orderByDesc('ordre')
            ->first();

        if (!$previousLesson) {
            return true;
        }

        $previousProgress = LessonProgress::where('user_id', $user->id)
            ->where('lesson_id', $previousLesson->id)
            ->where('completed', true)
            ->exists();

        return $previousProgress;
    }

    private function verifyLessonAccess(EliteUser $user, Lesson $lesson): void
    {
        if (!$this->isLessonUnlocked($user, $lesson, $lesson->chapter)) {
            throw ValidationException::withMessages([
                'lesson' => ['Cette leçon est verrouillée. Vous devez terminer la leçon précédente pour y accéder.']
            ]);
        }
    }

    private function verifyAllLessonsCompleted(EliteUser $user, Chapter $chapter): void
    {
        $totalLessons = Lesson::where('chapter_id', $chapter->id)
            ->active()
            ->count();

        $completedLessons = LessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', Lesson::where('chapter_id', $chapter->id)->active()->pluck('id'))
            ->where('completed', true)
            ->count();

        if ($completedLessons < $totalLessons) {
            throw ValidationException::withMessages([
                'quiz' => ['Vous devez terminer toutes les leçons de ce chapitre avant d\'accéder au quiz.']
            ]);
        }
    }

    private function getResultMessage(float $note, bool $reussi, string $action, Chapter $chapter): string
    {
        if ($reussi) {
            return "Excellent ! Vous avez obtenu {$note}/20. Le chapitre suivant est maintenant débloqué.";
        }

        if ($action === 'parrainage') {
            return "Vous avez obtenu {$note}/20. Pour continuer, parrainez {$chapter->parrainages_requis} personnes ou recommencez le chapitre.";
        }

        return "Vous avez obtenu {$note}/20. La note minimale est de {$chapter->note_parrainage}/20. Révisez et réessayez !";
    }
}